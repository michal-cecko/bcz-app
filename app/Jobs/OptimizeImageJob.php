<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900];

    public int $tries = 3;

    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/avif',
        'image/svg+xml',
    ];

    /**
     * Cap any single dimension at this many pixels. Aspect ratio is preserved
     * and images already smaller than this are left untouched (Fit::Max).
     */
    private const MAX_DIMENSION = 2560;

    /**
     * Mime types we attempt to resize via Spatie\Image (GD-backed). SVG is a
     * vector format with no pixel dimensions, AVIF isn't supported by GD.
     */
    private const RESIZABLE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function __construct(
        public readonly ?string $mediaId = null,
        public readonly ?string $disk = null,
        public readonly ?string $path = null,
    ) {
        $this->onQueue('optimization');
    }

    public static function forMedia(int|string $mediaId): self
    {
        return new self(mediaId: (string) $mediaId);
    }

    public static function forPath(string $disk, string $path): self
    {
        return new self(disk: $disk, path: $path);
    }

    public function handle(): void
    {
        if ($this->mediaId !== null) {
            $this->optimizeMedia();

            return;
        }

        $this->optimizePath();
    }

    private function optimizeMedia(): void
    {
        $media = Media::query()->find($this->mediaId);

        if (! $media) {
            return;
        }

        if (! in_array((string) $media->mime_type, self::IMAGE_MIMES, true)) {
            return;
        }

        $disk = Storage::disk($media->disk);
        $relativePath = $media->getPathRelativeToRoot();

        if (! $disk->exists($relativePath)) {
            Log::warning('OptimizeImageJob: media file missing on disk', [
                'media_id' => $media->id,
                'disk' => $media->disk,
                'path' => $relativePath,
            ]);

            return;
        }

        $result = $this->roundTripOptimize($disk, $relativePath, (string) $media->mime_type);

        if ($result === null) {
            return;
        }

        $media
            ->setCustomProperty('optimized_at', now()->toIso8601String())
            ->setCustomProperty('optimized_size', $result['optimizedSize'])
            ->setCustomProperty('original_size', $result['originalSize'])
            ->save();
    }

    private function optimizePath(): void
    {
        if ($this->disk === null || $this->path === null) {
            return;
        }

        $disk = Storage::disk($this->disk);

        if (! $disk->exists($this->path)) {
            return;
        }

        $mime = $disk->mimeType($this->path);

        if ($mime === false || ! in_array((string) $mime, self::IMAGE_MIMES, true)) {
            return;
        }

        $this->roundTripOptimize($disk, $this->path, (string) $mime);
    }

    /**
     * Download the file from the disk to a temp path, optionally downscale it
     * if either dimension exceeds MAX_DIMENSION, run the optimizer chain, and
     * re-upload only when the resulting bytes are smaller than the original.
     *
     * Required because optimizers (jpegoptim/pngquant/optipng/...) operate on
     * local filesystem paths only, while this app stores media on S3.
     *
     * @return array{originalSize: ?int, optimizedSize: ?int}|null
     */
    private function roundTripOptimize(Filesystem $disk, string $relativePath, string $mime): ?array
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'opt-');

        if ($tmpPath === false) {
            return null;
        }

        // Spatie\Image and the optimizer chain pick the output format from
        // the file extension. tempnam() returns an extensionless path, so
        // append the original extension (or fall back to a mime-derived one).
        $extension = pathinfo($relativePath, PATHINFO_EXTENSION) ?: match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            'image/svg+xml' => 'svg',
            default => 'bin',
        };
        $tmpWithExt = $tmpPath.'.'.$extension;

        if (! @rename($tmpPath, $tmpWithExt)) {
            @unlink($tmpPath);

            return null;
        }

        $tmpPath = $tmpWithExt;

        try {
            if (! $this->downloadToTemp($disk, $relativePath, $tmpPath)) {
                return null;
            }

            clearstatcache(true, $tmpPath);
            $originalSize = filesize($tmpPath) ?: null;

            $this->capDimensions($tmpPath, $mime);

            OptimizerChainFactory::create(config('media-library.image_optimizers') ?? [])->optimize($tmpPath);

            clearstatcache(true, $tmpPath);
            $optimizedSize = filesize($tmpPath) ?: null;

            if (
                $optimizedSize !== null
                && $originalSize !== null
                && $optimizedSize < $originalSize
            ) {
                $this->uploadFromTemp($disk, $relativePath, $tmpPath);
            }

            return ['originalSize' => $originalSize, 'optimizedSize' => $optimizedSize];
        } finally {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    private function downloadToTemp(Filesystem $disk, string $relativePath, string $tmpPath): bool
    {
        $stream = $disk->readStream($relativePath);

        if ($stream === null || $stream === false) {
            return false;
        }

        $tmpHandle = fopen($tmpPath, 'wb');

        if ($tmpHandle === false) {
            if (is_resource($stream)) {
                fclose($stream);
            }

            return false;
        }

        try {
            stream_copy_to_stream($stream, $tmpHandle);
        } finally {
            fclose($tmpHandle);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return true;
    }

    private function uploadFromTemp(Filesystem $disk, string $relativePath, string $tmpPath): void
    {
        try {
            $visibility = $disk->getVisibility($relativePath);
        } catch (Throwable) {
            $visibility = null;
        }

        $stream = fopen($tmpPath, 'rb');

        if ($stream === false) {
            return;
        }

        try {
            $options = $visibility !== null ? ['visibility' => $visibility] : [];
            $disk->writeStream($relativePath, $stream, $options);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * If either dimension exceeds MAX_DIMENSION, downscale in place via
     * Spatie\Image's Fit::Max (preserves aspect ratio, never upsizes). A
     * resize failure is logged but does not abort the optimizer step that
     * follows — the bytes on disk remain untouched in that case.
     */
    private function capDimensions(string $tmpPath, string $mime): void
    {
        if (! in_array($mime, self::RESIZABLE_MIMES, true)) {
            return;
        }

        $info = @getimagesize($tmpPath);

        if ($info === false) {
            return;
        }

        [$width, $height] = $info;

        if ($width <= self::MAX_DIMENSION && $height <= self::MAX_DIMENSION) {
            return;
        }

        try {
            Image::load($tmpPath)
                ->fit(Fit::Max, self::MAX_DIMENSION, self::MAX_DIMENSION)
                ->save($tmpPath);
        } catch (Throwable $e) {
            Log::warning('OptimizeImageJob: dimension cap failed, leaving original size', [
                'path' => $tmpPath,
                'mime' => $mime,
                'width' => $width,
                'height' => $height,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

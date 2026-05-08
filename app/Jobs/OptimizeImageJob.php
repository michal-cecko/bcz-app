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

        $result = $this->roundTripOptimize($disk, $relativePath);

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

        $this->roundTripOptimize($disk, $this->path);
    }

    /**
     * Download the file from the disk to a temp path, run the optimizer chain,
     * and re-upload only when the optimized output is smaller.
     *
     * Required because optimizers (jpegoptim/pngquant/optipng/...) operate on
     * local filesystem paths only, while this app stores media on S3.
     *
     * @return array{originalSize: ?int, optimizedSize: ?int}|null
     */
    private function roundTripOptimize(Filesystem $disk, string $relativePath): ?array
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'opt-');

        if ($tmpPath === false) {
            return null;
        }

        try {
            if (! $this->downloadToTemp($disk, $relativePath, $tmpPath)) {
                return null;
            }

            clearstatcache(true, $tmpPath);
            $originalSize = filesize($tmpPath) ?: null;

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
}

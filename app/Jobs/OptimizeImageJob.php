<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

        $absolutePath = $media->getPath();

        if (! is_file($absolutePath)) {
            Log::warning('OptimizeImageJob: media file missing on disk', [
                'media_id' => $media->id,
                'path' => $absolutePath,
            ]);

            return;
        }

        $originalSize = filesize($absolutePath) ?: null;

        OptimizerChainFactory::create(config('media-library.image_optimizers') ?? [])->optimize($absolutePath);

        clearstatcache(true, $absolutePath);

        $media
            ->setCustomProperty('optimized_at', now()->toIso8601String())
            ->setCustomProperty('optimized_size', filesize($absolutePath) ?: null)
            ->setCustomProperty('original_size', $originalSize)
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

        $absolutePath = $disk->path($this->path);

        if (! is_file($absolutePath)) {
            return;
        }

        OptimizerChainFactory::create(config('media-library.image_optimizers') ?? [])->optimize($absolutePath);
    }
}

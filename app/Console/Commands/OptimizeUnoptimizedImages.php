<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeImageJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OptimizeUnoptimizedImages extends Command
{
    protected $signature = 'media:optimize-images
        {--all : Re-queue even already-optimized Spatie media}
        {--dry-run : Print counts without dispatching jobs}';

    protected $description = 'Queue image optimization for all already-uploaded, not-yet-optimized images (Spatie media + Mason brick files)';

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'svg'];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $all = (bool) $this->option('all');

        $mediaCount = $this->queueSpatieMedia($all, $dryRun);
        $brickCount = $this->queueBrickFiles($dryRun);

        $verb = $dryRun ? 'Would queue' : 'Queued';
        $this->info("{$verb} {$mediaCount} Spatie media + {$brickCount} brick image(s) for optimization.");

        return self::SUCCESS;
    }

    private function queueSpatieMedia(bool $all, bool $dryRun): int
    {
        $query = Media::query()->where('mime_type', 'like', 'image/%');

        if (! $all) {
            $query->whereNull('custom_properties->optimized_at');
        }

        $count = 0;

        $query->chunkById(200, function ($chunk) use (&$count, $dryRun): void {
            foreach ($chunk as $media) {
                if (! $dryRun) {
                    dispatch(OptimizeImageJob::forMedia($media->id));
                }
                $count++;
            }
        });

        return $count;
    }

    private function queueBrickFiles(bool $dryRun): int
    {
        $disk = Storage::disk('public');

        if (! $disk->exists('bricks')) {
            return 0;
        }

        $count = 0;

        foreach ($disk->allFiles('bricks') as $path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (! in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }

            if (! $dryRun) {
                dispatch(OptimizeImageJob::forPath('public', $path));
            }
            $count++;
        }

        return $count;
    }
}

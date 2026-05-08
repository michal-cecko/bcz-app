<?php

namespace Tests\Feature\Commands;

use App\Jobs\OptimizeImageJob;
use App\Models\MediaItem;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OptimizeUnoptimizedImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_dispatches_no_jobs(): void
    {
        Queue::fake();
        Storage::fake('public');
        Storage::disk('public')->putFileAs('bricks', UploadedFile::fake()->image('a.jpg'), 'a.jpg');

        $this->artisan('media:optimize-images', ['--dry-run' => true])
            ->expectsOutputToContain('Would queue')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    public function test_default_run_skips_already_optimized_media(): void
    {
        Storage::fake('public');
        Queue::fake();

        $team = Team::factory()->create();
        $alreadyOptimized = MediaItem::factory()->create(['team_id' => $team->id]);
        $media = $alreadyOptimized->addMedia(UploadedFile::fake()->image('done.jpg'))->toMediaCollection('file');
        $media->setCustomProperty('optimized_at', now()->toIso8601String())->save();

        $unoptimized = MediaItem::factory()->create(['team_id' => $team->id]);
        $unoptimized->addMedia(UploadedFile::fake()->image('pending.jpg'))->toMediaCollection('file');

        // Reset fake so we only assert what the command itself dispatches.
        Queue::fake();

        $this->artisan('media:optimize-images')
            ->expectsOutputToContain('Queued 1 Spatie media')
            ->assertExitCode(0);

        Queue::assertPushed(OptimizeImageJob::class, 1);
    }

    public function test_all_flag_requeues_optimized_media(): void
    {
        Storage::fake('public');
        Queue::fake();

        $team = Team::factory()->create();
        $optimized = MediaItem::factory()->create(['team_id' => $team->id]);
        $media = $optimized->addMedia(UploadedFile::fake()->image('done.jpg'))->toMediaCollection('file');
        $media->setCustomProperty('optimized_at', now()->toIso8601String())->save();

        Queue::fake();

        $this->artisan('media:optimize-images', ['--all' => true])
            ->expectsOutputToContain('Queued 1 Spatie media')
            ->assertExitCode(0);

        Queue::assertPushed(OptimizeImageJob::class, 1);
    }

    public function test_walks_bricks_directory_and_queues_image_files(): void
    {
        Queue::fake();
        Storage::fake('public');

        Storage::disk('public')->putFileAs('bricks', UploadedFile::fake()->image('hero.jpg'), 'hero.jpg');
        Storage::disk('public')->putFileAs('bricks', UploadedFile::fake()->image('logo.png'), 'logo.png');
        Storage::disk('public')->put('bricks/notes.pdf', 'pdf');

        $this->artisan('media:optimize-images')
            ->expectsOutputToContain('+ 2 brick image(s)')
            ->assertExitCode(0);

        Queue::assertPushed(OptimizeImageJob::class, 2);

        // Regression: each dispatched job must carry the configured disk + path.
        Queue::assertPushed(
            OptimizeImageJob::class,
            fn (OptimizeImageJob $job) => $job->disk === 'public' && $job->path === 'bricks/hero.jpg',
        );
        Queue::assertPushed(
            OptimizeImageJob::class,
            fn (OptimizeImageJob $job) => $job->disk === 'public' && $job->path === 'bricks/logo.png',
        );
    }
}

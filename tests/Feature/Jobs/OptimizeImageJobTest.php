<?php

namespace Tests\Feature\Jobs;

use App\Jobs\OptimizeImageJob;
use App\Models\MediaItem;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OptimizeImageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_for_path_optimizes_existing_image_without_throwing(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('bricks', UploadedFile::fake()->image('test.jpg', 800, 600), 'sample.jpg');

        OptimizeImageJob::forPath('public', 'bricks/sample.jpg')->handle();

        // Optimizer is idempotent and silently skips missing binaries — assert the file still exists.
        $this->assertTrue(Storage::disk('public')->exists('bricks/sample.jpg'));
    }

    public function test_for_path_skips_when_file_missing(): void
    {
        Storage::fake('public');

        OptimizeImageJob::forPath('public', 'bricks/missing.jpg')->handle();

        // Should complete without throwing.
        $this->assertFalse(Storage::disk('public')->exists('bricks/missing.jpg'));
    }

    public function test_for_path_skips_non_image_extension(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('bricks/document.pdf', 'fake-pdf-content');

        $sizeBefore = Storage::disk('public')->size('bricks/document.pdf');

        OptimizeImageJob::forPath('public', 'bricks/document.pdf')->handle();

        $this->assertSame($sizeBefore, Storage::disk('public')->size('bricks/document.pdf'));
    }

    public function test_for_media_skips_non_image_mime_without_marking_optimized(): void
    {
        $team = Team::factory()->create();
        $mediaItem = MediaItem::factory()->create(['team_id' => $team->id]);
        $media = $mediaItem->addMedia(UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'))
            ->toMediaCollection('file');

        $media->forgetCustomProperty('optimized_at')->save();

        OptimizeImageJob::forMedia($media->id)->handle();

        $this->assertFalse($media->fresh()->hasCustomProperty('optimized_at'));
    }

    public function test_for_media_marks_image_as_optimized(): void
    {
        $team = Team::factory()->create();
        $mediaItem = MediaItem::factory()->create(['team_id' => $team->id]);
        $media = $mediaItem->addMedia(UploadedFile::fake()->image('photo.jpg', 600, 400))
            ->toMediaCollection('file');

        // The on-upload listener may have already set this — clear it for the explicit test.
        $media->forgetCustomProperty('optimized_at')->save();

        OptimizeImageJob::forMedia($media->id)->handle();

        $fresh = $media->fresh();
        $this->assertTrue($fresh->hasCustomProperty('optimized_at'));
        $this->assertNotNull($fresh->getCustomProperty('optimized_size'));
    }

    public function test_for_media_does_nothing_when_media_id_missing(): void
    {
        OptimizeImageJob::forMedia('00000000-0000-0000-0000-000000000000')->handle();

        // Should complete without throwing.
        $this->assertTrue(true);
    }
}

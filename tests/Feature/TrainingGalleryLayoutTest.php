<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\Training;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingGalleryLayoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var list<string>
     */
    private array $galleryImages = [
        'trainings/gallery/photo-one.jpg',
        'trainings/gallery/photo-two.jpg',
        'trainings/gallery/photo-three.jpg',
        'trainings/gallery/photo-four.jpg',
        'trainings/gallery/photo-five.jpg',
    ];

    private function createTrainingWithGallery(): Training
    {
        $team = Team::factory()->create([
            'name' => ['sk' => 'BCZ Club'],
            'slug' => 'bcz-club',
        ]);

        return Training::factory()->create([
            'team_id' => $team->id,
            'title' => ['sk' => 'Street Workout'],
            'slug' => 'street-workout',
            'gallery_images' => $this->galleryImages,
        ]);
    }

    /**
     * Isolates the gallery block (the Alpine lightbox wrapper up to the teleported
     * lightbox itself) so the assertions cannot accidentally match other sections.
     */
    private function galleryMarkup(string $html): string
    {
        $start = strpos($html, 'lightbox: false');
        $this->assertNotFalse($start, 'Gallery block was not rendered.');

        $end = strpos($html, 'x-teleport="body"', $start);
        $this->assertNotFalse($end, 'Gallery lightbox template was not rendered.');

        return substr($html, $start, $end - $start);
    }

    /**
     * Regression: the gallery grid used to carry a hardcoded `style="height: 500px"`
     * while its column count was responsive (1 / 2 / 3), so below `lg` every column
     * was squeezed into that same 500px box - the tiles collapsed into each other and
     * spilled out of the section. Tile heights must come from the tiles themselves.
     */
    public function test_gallery_does_not_constrain_tiles_with_a_fixed_pixel_height(): void
    {
        $training = $this->createTrainingWithGallery();

        $gallery = $this->galleryMarkup(
            $this->get($training->getLinkUrl())->assertOk()->getContent()
        );

        $this->assertDoesNotMatchRegularExpression(
            '/style="[^"]*height:\s*\d/i',
            $gallery,
            'The gallery must not pin a fixed pixel height on any element - it breaks below the lg breakpoint.'
        );

        // Every tile carries its own aspect ratio, so it has an intrinsic height at
        // any viewport width instead of depending on a fixed-height ancestor.
        $this->assertSame(
            count($this->galleryImages),
            preg_match_all('/class="[^"]*\baspect-[^"\s]+[^"]*"/', $gallery),
            'Each gallery tile must declare its own aspect ratio.'
        );
    }

    /**
     * Regression: images used to be dealt into three column buckets with `$i % 3`
     * while the lightbox `items` array kept the original order, so from the fourth
     * photo on a tile opened somebody else's photo. With 5 photos (as on the live
     * street-workout training) clicking tile #2 opened photo #4.
     */
    public function test_gallery_tiles_open_the_photo_they_display(): void
    {
        $training = $this->createTrainingWithGallery();

        $gallery = $this->galleryMarkup(
            $this->get($training->getLinkUrl())->assertOk()->getContent()
        );

        // Pair every tile's lightbox index with the photo that tile actually shows.
        preg_match_all('/current = (\d+); lightbox = true".*?<img src="([^"]+)"/s', $gallery, $matches, PREG_SET_ORDER);

        $rendered = array_map(
            fn (array $match) => [(int) $match[1], basename($match[2])],
            $matches
        );

        // The lightbox `items` array is in gallery order, so tile N must open item N
        // and must itself display photo N.
        $expected = [];
        foreach ($this->galleryImages as $index => $path) {
            $expected[] = [$index, basename($path)];
        }

        $this->assertSame(
            $expected,
            $rendered,
            'Every gallery tile must open the lightbox on the very photo it displays.'
        );
    }
}

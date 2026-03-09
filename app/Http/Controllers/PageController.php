<?php

namespace App\Http\Controllers;

use App\Mason\Bricks\AboutPreviewBrick;
use App\Mason\Bricks\ContactFormBrick;
use App\Mason\Bricks\CtaBrick;
use App\Mason\Bricks\DividerBrick;
use App\Mason\Bricks\FaqBrick;
use App\Mason\Bricks\FeatureCardsBrick;
use App\Mason\Bricks\FounderSpotlightBrick;
use App\Mason\Bricks\GalleryBrick;
use App\Mason\Bricks\HeadingBrick;
use App\Mason\Bricks\HeroBrick;
use App\Mason\Bricks\ImageBrick;
use App\Mason\Bricks\ImageTextBrick;
use App\Mason\Bricks\NumberedStepsBrick;
use App\Mason\Bricks\PersonCardsBrick;
use App\Mason\Bricks\QuoteBrick;
use App\Mason\Bricks\RichTextBrick;
use App\Mason\Bricks\SkillCardsBrick;
use App\Mason\Bricks\SocialCtaBrick;
use App\Mason\Bricks\SponsorsBrick;
use App\Mason\Bricks\SportCategoriesBrick;
use App\Mason\Bricks\StatsBrick;
use App\Mason\Bricks\TableBrick;
use App\Mason\Bricks\TimelineBrick;
use App\Mason\Bricks\TrainersBrick;
use App\Models\Page;
use Awcodes\Mason\Support\MasonRenderer;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public const array BRICKS = [
        HeroBrick::class,
        RichTextBrick::class,
        ImageBrick::class,
        ImageTextBrick::class,
        FeatureCardsBrick::class,
        CtaBrick::class,
        GalleryBrick::class,
        DividerBrick::class,
        QuoteBrick::class,
        HeadingBrick::class,
        StatsBrick::class,
        TableBrick::class,
        FaqBrick::class,
        TimelineBrick::class,
        PersonCardsBrick::class,
        ContactFormBrick::class,
        NumberedStepsBrick::class,
        SkillCardsBrick::class,
        TrainersBrick::class,
        SportCategoriesBrick::class,
        AboutPreviewBrick::class,
        FounderSpotlightBrick::class,
        SocialCtaBrick::class,
        SponsorsBrick::class,
    ];

    public function show(string $slug = '/'): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $content = $page->content ?: [];

        $renderedContent = MasonRenderer::make($content)
            ->bricks(self::BRICKS)
            ->toUnsafeHtml();

        return view('pages.dynamic', [
            'page' => $page,
            'renderedContent' => $renderedContent,
        ]);
    }
}

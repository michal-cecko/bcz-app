<?php

namespace App\Http\Controllers;

use App\Mason\Bricks\AboutPreviewBrick;
use App\Mason\Bricks\AchievementCardsBrick;
use App\Mason\Bricks\AthletesArchiveBrick;
use App\Mason\Bricks\AthletesShowcaseBrick;
use App\Mason\Bricks\CenteredHeroBrick;
use App\Mason\Bricks\CoachesArchiveBrick;
use App\Mason\Bricks\CompetitionBracketsBrick;
use App\Mason\Bricks\CompetitionCtaBrick;
use App\Mason\Bricks\CompetitionHeroBrick;
use App\Mason\Bricks\CompetitionResultsBrick;
use App\Mason\Bricks\CompetitionsArchiveBrick;
use App\Mason\Bricks\CompetitionTimetableBrick;
use App\Mason\Bricks\ContactFormBrick;
use App\Mason\Bricks\ContactInquiryBrick;
use App\Mason\Bricks\CtaBrick;
use App\Mason\Bricks\DetailsCardBrick;
use App\Mason\Bricks\DividerBrick;
use App\Mason\Bricks\DonationInfoBrick;
use App\Mason\Bricks\EventsArchiveBrick;
use App\Mason\Bricks\EventsShowcaseBrick;
use App\Mason\Bricks\FaqBrick;
use App\Mason\Bricks\FeatureCardsBrick;
use App\Mason\Bricks\FinishedCompetitionsBrick;
use App\Mason\Bricks\FounderSpotlightBrick;
use App\Mason\Bricks\GalleryBrick;
use App\Mason\Bricks\GuideCardsBrick;
use App\Mason\Bricks\HeadingBrick;
use App\Mason\Bricks\HeroBrick;
use App\Mason\Bricks\IconCtaBrick;
use App\Mason\Bricks\ImageBrick;
use App\Mason\Bricks\ImageTextBrick;
use App\Mason\Bricks\JudgesArchiveBrick;
use App\Mason\Bricks\LatestTrainingsBrick;
use App\Mason\Bricks\NumberedStepsBrick;
use App\Mason\Bricks\PersonCardsBrick;
use App\Mason\Bricks\ProfileBioBrick;
use App\Mason\Bricks\ProfileHeroBrick;
use App\Mason\Bricks\ProfileSectionBrick;
use App\Mason\Bricks\QuoteBrick;
use App\Mason\Bricks\RichTextBrick;
use App\Mason\Bricks\SkillCardsBrick;
use App\Mason\Bricks\SocialCtaBrick;
use App\Mason\Bricks\SocialLinksBrick;
use App\Mason\Bricks\SponsorsBrick;
use App\Mason\Bricks\SportCategoriesBrick;
use App\Mason\Bricks\StatsBrick;
use App\Mason\Bricks\StyledQuoteBrick;
use App\Mason\Bricks\TableBrick;
use App\Mason\Bricks\TeamsArchiveBrick;
use App\Mason\Bricks\TimelineBrick;
use App\Mason\Bricks\TrainersBrick;
use App\Mason\Bricks\TrainingCategoriesBrick;
use App\Mason\Bricks\TrainingsArchiveBrick;
use App\Mason\Bricks\VerticalTimelineBrick;
use App\Mason\Bricks\VideoSectionBrick;
use App\Models\Event;
use App\Models\Page;
use Awcodes\Mason\Support\MasonRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
        TrainingCategoriesBrick::class,
        LatestTrainingsBrick::class,
        AboutPreviewBrick::class,
        FounderSpotlightBrick::class,
        SocialCtaBrick::class,
        SponsorsBrick::class,
        DonationInfoBrick::class,
        ProfileHeroBrick::class,
        ProfileBioBrick::class,
        AchievementCardsBrick::class,
        VerticalTimelineBrick::class,
        ProfileSectionBrick::class,
        StyledQuoteBrick::class,
        SocialLinksBrick::class,
        CenteredHeroBrick::class,
        VideoSectionBrick::class,
        DetailsCardBrick::class,
        GuideCardsBrick::class,
        IconCtaBrick::class,
        TrainingsArchiveBrick::class,
        CompetitionsArchiveBrick::class,
        EventsArchiveBrick::class,
        CoachesArchiveBrick::class,
        AthletesArchiveBrick::class,
        JudgesArchiveBrick::class,
        TeamsArchiveBrick::class,
        EventsShowcaseBrick::class,
        ContactInquiryBrick::class,
        CompetitionResultsBrick::class,
        CompetitionBracketsBrick::class,
        CompetitionTimetableBrick::class,
        CompetitionHeroBrick::class,
        FinishedCompetitionsBrick::class,
        AthletesShowcaseBrick::class,
        CompetitionCtaBrick::class,
    ];

    /**
     * Old streetworkoutkysuce.sk blog-post slugs that were renamed when migrated
     * as events; posts whose slug was kept are resolved dynamically in show().
     * Defined here (not as routes) because Symfony rejects non-ASCII route URIs.
     *
     * @var array<string, string>
     */
    private const array LEGACY_EVENT_SLUGS = [
        'majstrovstva-slovenska-freestyle-2023-v-presove' => 'majstrovstva-v-street-workout-e-2023-boli-velkolepe-freestyle-atleti-hviezdili',
        'exhibicia-hotel-dixon-banska-bystrica' => 'street-workout-kysuce-na-plese-sportovcov-v-banskej-bystrici',
        'exhibicia-na-zilinskej-univerzite' => 'exhibicia-pre-fakultu-bezpecnostneho-inzinierstva-uniza',
        'street-workout-kysuce-nadchol-v-cadci-silova-exhibicia-na-60-vyrocie-cvc-ukazala-co-dokaze-ludske-telo' => 'street-workout-kysuce-ohuril-exhibiciou-pri-60-vyroci-cvc-cadca',
        'vylet-z-kruzku-do-gymnastickej-haly-🏆🔥' => 'vylet-z-kruzku-do-gymnastickej-haly',
    ];

    public function show(string $slug = '/'): View|RedirectResponse
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if ($page === null) {
            return $this->redirectLegacyEventSlugOrFail($slug);
        }

        $content = $page->content ?: [];

        $renderedContent = MasonRenderer::make($content)
            ->bricks(self::BRICKS)
            ->toUnsafeHtml();

        return view('pages.dynamic', [
            'page' => $page,
            'renderedContent' => $renderedContent,
        ]);
    }

    /**
     * Old-site blog posts live at root-level slugs; redirect them to their
     * event detail page instead of 404ing so link equity carries over.
     */
    private function redirectLegacyEventSlugOrFail(string $slug): RedirectResponse
    {
        $eventSlug = self::LEGACY_EVENT_SLUGS[$slug] ?? null;

        if ($eventSlug === null) {
            $slugIsPublishedEvent = Event::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->exists();

            abort_unless($slugIsPublishedEvent, 404);

            $eventSlug = $slug;
        }

        return redirect()->route('event.show', ['event' => $eventSlug], 301);
    }
}

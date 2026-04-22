<?php

namespace Database\Seeders;

use App\Enums\PageStatusEnum;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Page;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PageSeeder extends Seeder
{
    /** @var array<string, string> Cache of uploaded media paths keyed by slug */
    private static array $mediaCache = [];

    /** @var array<string, string> Cache of page IDs keyed by system_key */
    private static array $pageCache = [];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('bricks');

        self::seedMedia();

        $pages = [
            [
                'system_key' => 'homepage',
                'title' => ['sk' => 'Domov', 'en' => 'Home', 'cs' => 'Domů'],
                'slug' => '/',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 0,
                'content' => fn () => self::homepageContent(),
            ],
            [
                'system_key' => 'about',
                'title' => ['sk' => 'O nás', 'en' => 'About Us', 'cs' => 'O nás'],
                'slug' => 'o-nas',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 1,
                'content' => fn () => self::aboutContent(),
            ],
            [
                'system_key' => 'contact',
                'title' => ['sk' => 'Kontakt', 'en' => 'Contact', 'cs' => 'Kontakt'],
                'slug' => 'kontakt',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 2,
                'content' => fn () => self::contactContent(),
            ],
            [
                'system_key' => 'faq',
                'title' => ['sk' => 'FAQ', 'en' => 'FAQ', 'cs' => 'FAQ'],
                'slug' => 'faq',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 3,
                'content' => fn () => self::faqContent(),
            ],
            [
                'system_key' => 'support',
                'title' => ['sk' => 'Podporte nás', 'en' => 'Support Us', 'cs' => 'Podpořte nás'],
                'slug' => 'podporte-nas',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 4,
                'content' => fn () => self::supportContent(),
            ],
            [
                'system_key' => 'founder',
                'title' => ['sk' => 'Zakladateľ & CEO — Dominik Klimek', 'en' => 'Founder & CEO — Dominik Klimek', 'cs' => 'Zakladatel & CEO — Dominik Klimek'],
                'slug' => 'zakladatel-ceo-dominik-klimek',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 5,
                'content' => fn () => self::founderContent(),
            ],
            [
                'system_key' => 'tax_donation',
                'title' => ['sk' => '2% z dane', 'en' => '2% Tax Donation', 'cs' => '2% z daní'],
                'slug' => 'dva-percenta-z-dane',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 6,
                'content' => fn () => self::taxDonationContent(),
            ],
            [
                'system_key' => 'trainings',
                'title' => ['sk' => 'Trénuj s nami', 'en' => 'Train With Us', 'cs' => 'Trénuj s námi'],
                'slug' => 'trenuj-s-nami',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 7,
                'content' => fn () => self::trainingsContent(),
            ],
            [
                'system_key' => 'trainings_archive',
                'title' => ['sk' => 'Zoznam tréningov', 'en' => 'Training List', 'cs' => 'Seznam tréninků'],
                'slug' => 'zoznam-treningov',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 8,
                'content' => fn () => self::trainingsArchiveContent(),
            ],
            [
                'system_key' => 'competitions',
                'title' => ['sk' => 'Súťaže', 'en' => 'Competitions', 'cs' => 'Soutěže'],
                'slug' => 'sutaze',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 9,
                'content' => fn () => self::competitionsArchiveContent(),
            ],
            [
                'system_key' => 'events',
                'title' => ['sk' => 'Vystúpenia', 'en' => 'Events', 'cs' => 'Vystoupení'],
                'slug' => 'vystupenia',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 10,
                'content' => fn () => self::eventsArchiveContent(),
            ],
            [
                'system_key' => 'coaches_archive',
                'title' => ['sk' => 'Tréneri', 'en' => 'Coaches', 'cs' => 'Trenéři'],
                'slug' => 'treneri',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 11,
                'content' => fn () => self::coachesArchiveContent(),
            ],
            [
                'system_key' => 'athletes_archive',
                'title' => ['sk' => 'Športovci', 'en' => 'Athletes', 'cs' => 'Sportovci'],
                'slug' => 'atleti',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 12,
                'content' => fn () => self::athletesArchiveContent(),
            ],
            [
                'system_key' => 'judges_archive',
                'title' => ['sk' => 'Rozhodcovia', 'en' => 'Judges', 'cs' => 'Rozhodčí'],
                'slug' => 'rozhodcovia',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 13,
                'content' => fn () => self::judgesArchiveContent(),
            ],
            [
                'system_key' => 'teams_archive',
                'title' => ['sk' => 'Tímy', 'en' => 'Teams', 'cs' => 'Týmy'],
                'slug' => 'timy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 14,
                'content' => fn () => self::teamsArchiveContent(),
            ],
            [
                'system_key' => 'performances',
                'title' => ['sk' => 'Akrobatické Vystúpenia', 'en' => 'Acrobatic Performances', 'cs' => 'Akrobatická Vystoupení'],
                'slug' => 'akrobaticke-vystupenia',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 10,
                'content' => fn () => self::performancesContent(),
            ],
            [
                'system_key' => 'services',
                'title' => ['sk' => 'Vystúpenia, prednášky & workshopy', 'en' => 'Performances, Lectures & Workshops', 'cs' => 'Vystoupení, přednášky & workshopy'],
                'slug' => 'vystupenia-prednasky-workshopy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 10,
                'content' => fn () => self::servicesContent(),
            ],
            [
                'system_key' => 'lectures',
                'title' => ['sk' => 'Inšpiratívne Prednášky', 'en' => 'Inspirational Lectures', 'cs' => 'Inspirativní Přednášky'],
                'slug' => 'prednasky',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 11,
                'content' => fn () => self::lecturesContent(),
            ],
            [
                'system_key' => 'workshops',
                'title' => ['sk' => 'Praktické Workshopy', 'en' => 'Practical Workshops', 'cs' => 'Praktické Workshopy'],
                'slug' => 'workshopy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 12,
                'content' => fn () => self::workshopsContent(),
            ],
            [
                'system_key' => 'parkour',
                'title' => ['sk' => 'Parkour & Freerunning', 'en' => 'Parkour & Freerunning', 'cs' => 'Parkour & Freerunning'],
                'slug' => 'kategoria/parkour-freerunning',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 13,
                'content' => fn () => self::parkourContent(),
            ],
            [
                'system_key' => 'street_workout',
                'title' => ['sk' => 'Street Workout & Kalistenika', 'en' => 'Street Workout & Calisthenics', 'cs' => 'Street Workout & Kalistenika'],
                'slug' => 'kategoria/street-workout',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 14,
                'content' => fn () => self::streetWorkoutContent(),
            ],
            [
                'system_key' => 'pricing',
                'title' => ['sk' => 'Cenník', 'en' => 'Pricing', 'cs' => 'Ceník'],
                'slug' => 'cennik',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 15,
                'content' => fn () => self::pricingContent(),
            ],
            [
                'system_key' => 'privacy_policy',
                'title' => ['sk' => 'Ochrana osobných údajov', 'en' => 'Privacy Policy', 'cs' => 'Ochrana osobních údajů'],
                'slug' => 'ochrana-osobnych-udajov',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 16,
                'content' => fn () => self::privacyPolicyContent(),
            ],
            [
                'system_key' => 'terms_of_use',
                'title' => ['sk' => 'Podmienky používania', 'en' => 'Terms of Use', 'cs' => 'Podmínky používání'],
                'slug' => 'podmienky-pouzivania',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 17,
                'content' => fn () => self::termsOfUseContent(),
            ],
            [
                'system_key' => 'terms_of_commerce',
                'title' => ['sk' => 'Obchodné podmienky', 'en' => 'Terms of Commerce', 'cs' => 'Obchodní podmínky'],
                'slug' => 'obchodne-podmienky',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 18,
                'content' => fn () => self::termsOfCommerceContent(),
            ],
        ];

        Page::query()->where('is_system', true)->forceDelete();

        // First pass: create all pages with empty content, cache their IDs
        foreach ($pages as $page) {
            $contentClosure = $page['content'] instanceof \Closure ? $page['content'] : null;
            $page['content'] = $contentClosure ? [] : $page['content'];
            $created = Page::query()->create($page);
            self::$pageCache[$page['system_key']] = $created->id;

            if ($contentClosure) {
                // Store closure for second pass
                $page['_closure'] = $contentClosure;
                $page['_id'] = $created->id;
                $deferredContent[] = $page;
            }
        }

        // Second pass: resolve deferred content now that all page IDs are available
        foreach ($deferredContent ?? [] as $page) {
            Page::query()->where('id', $page['_id'])->update([
                'content' => ($page['_closure'])(),
            ]);
        }
    }

    private static function brick(string $type, array $config): array
    {
        return [
            'type' => 'masonBrick',
            'attrs' => [
                'id' => $type,
                'config' => $config,
            ],
        ];
    }

    private static function homepageContent(): array
    {
        return [
            self::brick('hero', [
                'badge' => ['sk' => 'BEYOND COMFORT ZONE', 'en' => 'BEYOND COMFORT ZONE', 'cs' => 'BEYOND COMFORT ZONE'],
                'title' => ['sk' => 'PREKONAJ', 'en' => 'PUSH BEYOND', 'cs' => 'PŘEKONEJ'],
                'title_accent' => ['sk' => 'SVOJE LIMITY', 'en' => 'YOUR LIMITS', 'cs' => 'SVÉ LIMITY'],
                'subtitle' => ['sk' => 'Profesionálne tréningy kalisteniky a parkouru, súťaže a vystúpenia.', 'en' => 'Professional calisthenics and parkour training, competitions and performances.', 'cs' => 'Profesionální tréninky kalisteniky a parkouru, soutěže a vystoupení.'],
                'background_image' => self::media('hero-bg'),
                'cta_text' => ['sk' => 'ZAČAŤ TRÉNOVAŤ', 'en' => 'START TRAINING', 'cs' => 'ZAČÍT TRÉNOVAT'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('trainings'),
                'secondary_cta_text' => ['sk' => 'POZRIEŤ VIDEO', 'en' => 'WATCH VIDEO', 'cs' => 'PODÍVAT SE NA VIDEO'],
                'secondary_cta_link_type' => 'custom',
                'secondary_cta_link_url' => ['sk' => '#', 'en' => '#', 'cs' => '#'],
            ]),
            self::brick('sport-categories', [
                'label' => ['sk' => 'ČO ROBÍME', 'en' => 'WHAT WE DO', 'cs' => 'CO DĚLÁME'],
                'title' => ['sk' => 'TRI PILIERE BCZ', 'en' => 'THREE PILLARS OF BCZ', 'cs' => 'TŘI PILÍŘE BCZ'],
                'subtitle' => ['sk' => 'Súťaže. Tréningy. Vystúpenia.', 'en' => 'Competitions. Trainings. Performances.', 'cs' => 'Soutěže. Tréninky. Vystoupení.'],
                'categories' => [
                    [
                        'image' => self::media('cat-competitions'),
                        'title' => ['sk' => 'SÚŤAŽE', 'en' => 'COMPETITIONS', 'cs' => 'SOUTĚŽE'],
                        'description' => ['sk' => 'Profesionálna účasť na medzinárodných a domácich súťažiach. Organizujeme a propagujeme podujatia, pričom naši aktívni členovia dosahujú výnimočné úspechy.', 'en' => 'Professional participation in international and domestic competitions. We organize and promote events, while our active members achieve outstanding results.', 'cs' => 'Profesionální účast na mezinárodních a domácích soutěžích. Organizujeme a propagujeme akce, přičemž naši aktivní členové dosahují výjimečných úspěchů.'],
                        'link_text' => ['sk' => 'ZOBRAZIŤ SÚŤAŽE', 'en' => 'VIEW COMPETITIONS', 'cs' => 'ZOBRAZIT SOUTĚŽE'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('competitions'),
                    ],
                    [
                        'image' => self::media('cat-trainings'),
                        'title' => ['sk' => 'TRÉNINGY', 'en' => 'TRAININGS', 'cs' => 'TRÉNINKY'],
                        'description' => ['sk' => 'Súkromné a skupinové tréningy pre všetky úrovne. Parkour & Freerunning, Freestyle a Kalistenika pre dospelých aj deti s certifikovanými trénermi.', 'en' => 'Private and group training sessions for all levels. Parkour & Freerunning, Freestyle and Calisthenics for adults and children with certified coaches.', 'cs' => 'Soukromé a skupinové tréninky pro všechny úrovně. Parkour & Freerunning, Freestyle a Kalistenika pro dospělé i děti s certifikovanými trenéry.'],
                        'link_text' => ['sk' => 'PRESKÚMAŤ TRÉNINGY', 'en' => 'EXPLORE TRAININGS', 'cs' => 'PROZKOUMAT TRÉNINKY'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('trainings'),
                    ],
                    [
                        'image' => self::media('cat-performances'),
                        'title' => ['sk' => 'VYSTÚPENIA', 'en' => 'PERFORMANCES', 'cs' => 'VYSTOUPENÍ'],
                        'description' => ['sk' => 'Spektakulárne vystúpenia pre školy, škôlky, firmy a verejné podujatia. Dynamické show s profesionálnym vybavením, ktoré inšpirujú a bavia každé publíkum.', 'en' => 'Spectacular performances for schools, kindergartens, companies and public events. Dynamic shows with professional equipment that inspire and entertain every audience.', 'cs' => 'Spektakulární vystoupení pro školy, školky, firmy a veřejné akce. Dynamické show s profesionálním vybavením, které inspirují a baví každé publikum.'],
                        'link_text' => ['sk' => 'OBJEDNAŤ VYSTÚPENIE', 'en' => 'BOOK A PERFORMANCE', 'cs' => 'OBJEDNAT VYSTOUPENÍ'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('services'),
                    ],
                ],
            ]),
            self::brick('about-preview', [
                'label' => ['sk' => 'NÁŠ PRÍBEH', 'en' => 'OUR STORY', 'cs' => 'NÁŠ PŘÍBĚH'],
                'title' => ['sk' => "ZRODENÍ\nZ VÁŠNE", 'en' => "BORN\nFROM PASSION", 'cs' => "ZROZENÍ\nZ VÁŠNĚ"],
                'description' => ['sk' => 'BCZ Club začal ako skupina priateľov, ktorí posúvali hranice a objavovali, čoho je ľudské telo skutočne schopné. Dnes sme profesionálna asociácia venovaná šíreniu pohybovej kultúry prostredníctvom súťaží, svetových tréningov a nezabudnuteľných vystúpení.', 'en' => 'BCZ Club started as a group of friends pushing boundaries and discovering what the human body is truly capable of. Today we are a professional association dedicated to spreading movement culture through competitions, world-class training and unforgettable performances.', 'cs' => 'BCZ Club začal jako skupina přátel, kteří posouvali hranice a objevovali, čeho je lidské tělo skutečně schopné. Dnes jsme profesionální asociace věnovaná šíření pohybové kultury prostřednictvím soutěží, světových tréninků a nezapomenutelných vystoupení.'],
                'cta_text' => ['sk' => 'PREČÍTAŤ CELÝ PRÍBEH', 'en' => 'READ THE FULL STORY', 'cs' => 'PŘEČÍST CELÝ PŘÍBĚH'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('about'),
                'image_main' => self::media('about-main'),
                'image_left' => self::media('about-left'),
                'image_right' => self::media('about-right'),
            ]),
            self::brick('founder-spotlight', [
                'label' => ['sk' => 'ZAKLADATEĽ & CEO', 'en' => 'FOUNDER & CEO', 'cs' => 'ZAKLADATEL & CEO'],
                'name_line1' => ['sk' => 'DOMINIK', 'en' => 'DOMINIK', 'cs' => 'DOMINIK'],
                'name_line2' => ['sk' => 'KLIMEK', 'en' => 'KLIMEK', 'cs' => 'KLIMEK'],
                'subtitle' => ['sk' => 'Majster sveta v street workoute &middot; Tréner &middot; Mentor', 'en' => 'World Champion in street workout &middot; Coach &middot; Mentor', 'cs' => 'Mistr světa ve street workoutu &middot; Trenér &middot; Mentor'],
                'bio' => ['sk' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladateľ BCZ Club a jediný certifikovaný master tréner kalisteniky a street workoute na Slovensku. V roku 2022 sa stal majstrom sveta v street workoute v Rige a trikrát po sebe vyhral majstrovstvá Slovenska.', 'en' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek is the founder of BCZ Club and the only certified master calisthenics and street workout coach in Slovakia. In 2022 he became the world champion in street workout in Riga and won the Slovak championship three times in a row.', 'cs' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladatel BCZ Club a jediný certifikovaný master trenér kalisteniky a street workoutu na Slovensku. V roce 2022 se stal mistrem světa ve street workoutu v Rize a třikrát po sobě vyhrál mistrovství Slovenska.'],
                'bio2' => ['sk' => 'Dnes vedie komunitu mladých ľudí, organizuje workshopy po školách a inšpiruje novú generáciu k pohybu a zdravému životnému štýlu. Jeho víziou je ukázať, že disciplína a tvrdá práca dokážu zmeniť životy.', 'en' => 'Today he leads a community of young people, organizes workshops at schools and inspires the new generation towards movement and a healthy lifestyle. His vision is to show that discipline and hard work can change lives.', 'cs' => 'Dnes vede komunitu mladých lidí, organizuje workshopy po školách a inspiruje novou generaci k pohybu a zdravému životnímu stylu. Jeho vizí je ukázat, že disciplína a tvrdá práce dokážou změnit životy.'],
                'image' => self::media('founder-img'),
                'stats' => [
                    ['number' => '1x', 'label' => ['sk' => 'Majster sveta', 'en' => 'World Champion', 'cs' => 'Mistr světa']],
                    ['number' => '3x', 'label' => ['sk' => 'Majster SR', 'en' => 'Slovak Champion', 'cs' => 'Mistr SR']],
                    ['number' => 'L4', 'label' => ['sk' => 'Conditioning Coach', 'en' => 'Conditioning Coach', 'cs' => 'Conditioning Coach']],
                    ['number' => '500+', 'label' => ['sk' => 'Mentorovaných detí', 'en' => 'Mentored Children', 'cs' => 'Mentorovaných dětí']],
                ],
                'cta_text' => ['sk' => 'SPOZNAJ DOMINIKA', 'en' => 'MEET DOMINIK', 'cs' => 'POZNEJ DOMINIKA'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('founder'),
            ]),
            self::brick('social-cta', [
                'label' => ['sk' => 'SLEDUJTE NAŠU CESTU', 'en' => 'FOLLOW OUR JOURNEY', 'cs' => 'SLEDUJTE NAŠI CESTU'],
                'title' => ['sk' => 'PRIDAJ SA K POHYBU', 'en' => 'JOIN THE MOVEMENT', 'cs' => 'PŘIDEJ SE K POHYBU'],
                'description' => ['sk' => 'Sledujte nás na sociálnych sieťach pre tréningové tipy, novinky zo súťaží a obsah zo zákulisia.', 'en' => 'Follow us on social media for training tips, competition news and behind-the-scenes content.', 'cs' => 'Sledujte nás na sociálních sítích pro tréninkové tipy, novinky ze soutěží a obsah ze zákulisí.'],
                'background_image' => self::media('social-bg'),
                'instagram_url' => '#',
                'facebook_url' => '#',
                'youtube_url' => '#',
            ]),
            self::brick('sponsors', [
                'sponsors' => Sponsor::query()
                    ->where('is_visible', true)
                    ->orderBy('sort_order')
                    ->pluck('id')
                    ->map(fn (string $id) => ['sponsor_id' => $id])
                    ->values()
                    ->all(),
            ]),
        ];
    }

    private static function aboutContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'NÁŠ', 'en' => 'OUR', 'cs' => 'NÁŠ'],
                'title_accent' => ['sk' => 'PRÍBEH', 'en' => 'STORY', 'cs' => 'PŘÍBĚH'],
                'subtitle' => ['sk' => 'Od skupiny priateľov posúvajúcich hranice po profesionálnu asociáciu inšpirujúcu ďalšiu generáciu športovcov.', 'en' => 'From a group of friends pushing boundaries to a professional association inspiring the next generation of athletes.', 'cs' => 'Od skupiny přátel posouvajících hranice po profesionální asociaci inspirující další generaci sportovců.'],
                'background_image' => self::media('about-hero-bg'),
                'scroll_text' => ['sk' => 'SCROLLUJ PRE VIAC', 'en' => 'SCROLL FOR MORE', 'cs' => 'SCROLLUJ PRO VÍCE'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'O NÁS', 'en' => 'ABOUT US', 'cs' => 'O NÁS']],
                ],
            ]),
            self::brick('about-preview', [
                'label' => ['sk' => 'AKO TO VŠETKO ZAČALO', 'en' => 'HOW IT ALL BEGAN', 'cs' => 'JAK TO VŠECHNO ZAČALO'],
                'title' => ['sk' => "Z ULÍC\nNA PÓDIA", 'en' => "FROM STREETS\nTO STAGES", 'cs' => "Z ULIC\nNA PÓDIA"],
                'description' => ['sk' => 'Všetko to začalo v roku 2015, keď malá skupina priateľov objavila parkour cez online videá. To, čo začalo ako neformálne stretnutia v miestnych parkoch, sa rýchlo vyvinulo v niečo omnoho väčšie.', 'en' => 'It all started in 2015 when a small group of friends discovered parkour through online videos. What began as informal meetups in local parks quickly evolved into something much bigger.', 'cs' => 'Všechno to začalo v roce 2015, když malá skupina přátel objevila parkour přes online videa. To, co začalo jako neformální setkání v místních parcích, se rychle vyvinulo v něco mnohem většího.'],
                'image_main' => self::media('about-story-main'),
                'image_caption' => ['sk' => 'Prvé dni tréningu na uliciach, 2016', 'en' => 'Early days of street training, 2016', 'cs' => 'První dny tréninku na ulicích, 2016'],
            ]),
            self::brick('timeline', [
                'items' => [
                    ['year' => '2015', 'title' => ['sk' => 'Začiatok', 'en' => 'The Beginning', 'cs' => 'Začátek'], 'description' => ['sk' => 'Prvé neoficiálne stretnutia v miestnych parkoch. Len priatelia, ktorí sa zabávajú a učia sa spolu.', 'en' => 'First unofficial meetups in local parks. Just friends having fun and learning together.', 'cs' => 'První neoficiální setkání v místních parcích. Jen přátelé, kteří se baví a učí se spolu.']],
                    ['year' => '2017', 'title' => ['sk' => 'Prvá súťaž', 'en' => 'First Competition', 'cs' => 'První soutěž'], 'description' => ['sk' => 'Náš tím sa zúčastnil prvej národnej parkorovej súťaže. Nevyhrali sme, ale naučili sme sa.', 'en' => 'Our team participated in the first national parkour competition. We didn\'t win, but we learned.', 'cs' => 'Náš tým se zúčastnil první národní parkourové soutěže. Nevyhráli jsme, ale naučili jsme se.']],
                    ['year' => '2019', 'title' => ['sk' => 'Oficiálna asociácia', 'en' => 'Official Association', 'cs' => 'Oficiální asociace'], 'description' => ['sk' => 'BCZ Club sa stal oficiálnou neziskovou organizáciou. Začali sme naše prvé tréningové programy.', 'en' => 'BCZ Club became an official non-profit organization. We started our first training programs.', 'cs' => 'BCZ Club se stal oficiální neziskovou organizací. Začali jsme naše první tréninkové programy.']],
                    ['year' => '2024', 'title' => ['sk' => 'Dnes a ďalej', 'en' => 'Today and Beyond', 'cs' => 'Dnes a dále'], 'description' => ['sk' => 'Medzinárodné súťaže, profesionálne tréningy, vystúpenia po celej krajine. Cesta pokračuje.', 'en' => 'International competitions, professional training, performances across the country. The journey continues.', 'cs' => 'Mezinárodní soutěže, profesionální tréninky, vystoupení po celé zemi. Cesta pokračuje.']],
                ],
            ]),
            self::brick('athletes-showcase', [
                'label' => ['sk' => 'ĽUDIA', 'en' => 'PEOPLE', 'cs' => 'LIDÉ'],
                'title' => ['sk' => 'SPOZNAJTE NAŠICH ŠPORTOVCOV', 'en' => 'MEET OUR ATHLETES', 'cs' => 'POZNEJTE NAŠE SPORTOVCE'],
                'description' => ['sk' => 'Talentovaní jednotlivci, ktorí reprezentujú BCZ Club na súťažiach po celom svete.', 'en' => 'Talented individuals who represent BCZ Club in competitions around the world.', 'cs' => 'Talentovaní jednotlivci, kteří reprezentují BCZ Club na soutěžích po celém světě.'],
                'random' => true,
            ]),
            self::brick('person-cards', [
                'label' => ['sk' => 'UČ SA OD NAJLEPŠÍCH', 'en' => 'LEARN FROM THE BEST', 'cs' => 'UČ SE OD NEJLEPŠÍCH'],
                'title' => ['sk' => 'NAŠI TRÉNERI', 'en' => 'OUR COACHES', 'cs' => 'NAŠI TRENÉŘI'],
                'subtitle' => ['sk' => 'Certifikovaní profesionáli oddaní pomáhať ti dosiahnuť tvoj plný potenciál.', 'en' => 'Certified professionals dedicated to helping you reach your full potential.', 'cs' => 'Certifikovaní profesionálové oddaní pomáhat ti dosáhnout tvůj plný potenciál.'],
                'people' => [
                    ['image' => self::media('trainer-1'), 'name' => ['sk' => 'MENO TRÉNERA', 'en' => 'COACH NAME', 'cs' => 'JMÉNO TRENÉRA'], 'role' => ['sk' => 'Hlavný tréner - Parkour & Freerunning', 'en' => 'Head Coach - Parkour & Freerunning', 'cs' => 'Hlavní trenér - Parkour & Freerunning'], 'description' => ['sk' => 'Certifikovaný inštruktor parkouru s 8+ rokmi učiteľských skúseností. Špecializuje sa na progresiu od začiatočníkov po pokročilých a bezpečnú tréningovú metodológiu.', 'en' => 'Certified parkour instructor with 8+ years of teaching experience. Specializes in progression from beginners to advanced and safe training methodology.', 'cs' => 'Certifikovaný instruktor parkouru s 8+ lety učitelských zkušeností. Specializuje se na progresi od začátečníků po pokročilé a bezpečnou tréninkovou metodologii.'], 'tags' => ['ADAPT Level 2', 'First Aid']],
                    ['image' => self::media('trainer-2'), 'name' => ['sk' => 'MENO TRÉNERA', 'en' => 'COACH NAME', 'cs' => 'JMÉNO TRENÉRA'], 'role' => ['sk' => 'Tréner - Kalistenika & Sila', 'en' => 'Coach - Calisthenics & Strength', 'cs' => 'Trenér - Kalistenika & Síla'], 'description' => ['sk' => 'Expert na tréning s vlastnou váhou a rozvoj sily. Pomáha športovcom všetkých úrovní budovať funkčnú silu a dosahovať ich fitness ciele.', 'en' => 'Expert in bodyweight training and strength development. Helps athletes of all levels build functional strength and achieve their fitness goals.', 'cs' => 'Expert na trénink s vlastní vahou a rozvoj síly. Pomáhá sportovcům všech úrovní budovat funkční sílu a dosahovat jejich fitness cíle.'], 'tags' => ['Personal Trainer', 'Nutrition']],
                ],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'ZA ČÍM SI STOJÍME', 'en' => 'WHAT WE STAND FOR', 'cs' => 'ZA ČÍM SI STOJÍME'],
                'title' => ['sk' => 'NAŠE HODNOTY', 'en' => 'OUR VALUES', 'cs' => 'NAŠE HODNOTY'],
                'cards' => [
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'VÁŠEŇ', 'en' => 'PASSION', 'cs' => 'VÁŠEŇ'], 'description' => ['sk' => 'Všetko čo robíme vychádza z hlbokej lásky k pohybu. Táto vášeň nás poháňa posúvať hranice a inšpirovať ostatných.', 'en' => 'Everything we do comes from a deep love for movement. This passion drives us to push boundaries and inspire others.', 'cs' => 'Všechno co děláme vychází z hluboké lásky k pohybu. Tato vášeň nás pohání posouvat hranice a inspirovat ostatní.']],
                    ['icon' => 'heroicon-o-user-group', 'title' => ['sk' => 'KOMUNITA', 'en' => 'COMMUNITY', 'cs' => 'KOMUNITA'], 'description' => ['sk' => 'Sme silnejší spolu. Naša komunita sa navzájom podporuje, motivúje a oslavuje úspechy každého člena.', 'en' => 'We are stronger together. Our community supports, motivates and celebrates each member\'s achievements.', 'cs' => 'Jsme silnější společně. Naše komunita se navzájem podporuje, motivuje a slaví úspěchy každého člena.']],
                    ['icon' => 'heroicon-o-shield-check', 'title' => ['sk' => 'BEZPEČNOSŤ', 'en' => 'SAFETY', 'cs' => 'BEZPEČNOST'], 'description' => ['sk' => 'Progres cez správnu techniku a kalkulované riziko. Veríme v inteligentný tréning, ktorý minimalizuje zranenia a maximalizuje rast.', 'en' => 'Progress through proper technique and calculated risk. We believe in intelligent training that minimizes injuries and maximizes growth.', 'cs' => 'Progres přes správnou techniku a kalkulované riziko. Věříme v inteligentní trénink, který minimalizuje zranění a maximalizuje růst.']],
                    ['icon' => 'heroicon-o-arrow-trending-up', 'title' => ['sk' => 'RAST', 'en' => 'GROWTH', 'cs' => 'RŮST'], 'description' => ['sk' => 'Každý deň je príležitosťou na zlepšenie. Prijímame výzvy a vnímame zlyhania ako odrazové mostíky k úspechu.', 'en' => 'Every day is an opportunity for improvement. We embrace challenges and see failures as stepping stones to success.', 'cs' => 'Každý den je příležitostí ke zlepšení. Přijímáme výzvy a vnímáme selhání jako odrazové můstky k úspěchu.']],
                ],
            ]),
            self::brick('founder-spotlight', [
                'label' => ['sk' => 'ZAKLADATEĽ & CEO', 'en' => 'FOUNDER & CEO', 'cs' => 'ZAKLADATEL & CEO'],
                'name_line1' => ['sk' => 'DOMINIK', 'en' => 'DOMINIK', 'cs' => 'DOMINIK'],
                'name_line2' => ['sk' => 'KLIMEK', 'en' => 'KLIMEK', 'cs' => 'KLIMEK'],
                'subtitle' => ['sk' => 'Majster sveta v street workoute &middot; Tréner &middot; Mentor', 'en' => 'World Champion in street workout &middot; Coach &middot; Mentor', 'cs' => 'Mistr světa ve street workoutu &middot; Trenér &middot; Mentor'],
                'bio' => ['sk' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladateľ BCZ Club a jediný certifikovaný master tréner kalisteniky a street workoute na Slovensku. V roku 2022 sa stal majstrom sveta v street workoute v Rige a trikrát po sebe vyhral majstrovstvá Slovenska.', 'en' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek is the founder of BCZ Club and the only certified master calisthenics and street workout coach in Slovakia. In 2022 he became the world champion in street workout in Riga and won the Slovak championship three times in a row.', 'cs' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladatel BCZ Club a jediný certifikovaný master trenér kalisteniky a street workoutu na Slovensku. V roce 2022 se stal mistrem světa ve street workoutu v Rize a třikrát po sobě vyhrál mistrovství Slovenska.'],
                'bio2' => ['sk' => 'Dnes vedie komunitu mladých ľudí, organizuje workshopy po školách a inšpiruje novú generáciu k pohybu a zdravému životnému štýlu. Jeho víziou je ukázať, že disciplína a tvrdá práca dokážu zmeniť životy.', 'en' => 'Today he leads a community of young people, organizes workshops at schools and inspires the new generation towards movement and a healthy lifestyle. His vision is to show that discipline and hard work can change lives.', 'cs' => 'Dnes vede komunitu mladých lidí, organizuje workshopy po školách a inspiruje novou generaci k pohybu a zdravému životnímu stylu. Jeho vizí je ukázat, že disciplína a tvrdá práce dokážou změnit životy.'],
                'image' => self::media('founder-img'),
                'stats' => [
                    ['number' => '1x', 'label' => ['sk' => 'Majster sveta', 'en' => 'World Champion', 'cs' => 'Mistr světa']],
                    ['number' => '3x', 'label' => ['sk' => 'Majster SR', 'en' => 'Slovak Champion', 'cs' => 'Mistr SR']],
                    ['number' => 'L4', 'label' => ['sk' => 'Conditioning Coach', 'en' => 'Conditioning Coach', 'cs' => 'Conditioning Coach']],
                    ['number' => '500+', 'label' => ['sk' => 'Mentorovaných detí', 'en' => 'Mentored Children', 'cs' => 'Mentorovaných dětí']],
                ],
                'cta_text' => ['sk' => 'SPOZNAJ DOMINIKA', 'en' => 'MEET DOMINIK', 'cs' => 'POZNEJ DOMINIKA'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('founder'),
            ]),
            self::brick('gallery', [
                'label' => ['sk' => 'MOMENTY', 'en' => 'MOMENTS', 'cs' => 'MOMENTY'],
                'title' => ['sk' => 'FOTOGALÉRIA', 'en' => 'PHOTO GALLERY', 'cs' => 'FOTOGALERIE'],
                'images' => [
                    ['image' => self::media('gallery-about-1')],
                    ['image' => self::media('gallery-about-2')],
                    ['image' => self::media('gallery-about-3')],
                    ['image' => self::media('gallery-about-4')],
                    ['image' => self::media('gallery-about-5')],
                ],
            ]),
        ];
    }

    private static function contactContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'left',
                'background_image' => 'https://picsum.photos/seed/contact-hero/1440/800',
                'badge' => ['sk' => 'KONTAKT', 'en' => 'CONTACT', 'cs' => 'KONTAKT'],
                'title' => ['sk' => 'Napíšte nám', 'en' => 'Write to us', 'cs' => 'Napište nám'],
                'subtitle' => ['sk' => 'Máte otázku, chcete si dohodnúť tréning alebo spoluprácu? Sme tu pre vás.', 'en' => 'Have a question, want to arrange a training or collaboration? We are here for you.', 'cs' => 'Máte otázku, chcete si dohodnout trénink nebo spolupráci? Jsme tu pro vás.'],
            ]),
            self::brick('contact-form', [
                'heading' => ['sk' => 'Kontaktný formulár', 'en' => 'Contact form', 'cs' => 'Kontaktní formulář'],
                'show_reason' => true,
                'show_phone' => true,
                'contact_email' => 'info@bfreak.sk',
                'contact_phone' => '+421 900 000 000',
                'contact_location' => 'Žilina, Slovensko',
                'response_text' => 'Zvyčajne odpovedáme do 24 hodín. Pre urgentné záležitosti nás kontaktujte telefonicky.',
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Najčastejšie otázky', 'en' => 'Frequently Asked Questions', 'cs' => 'Nejčastější otázky'],
                'faq_ids' => Faq::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->limit(3)
                    ->pluck('id')
                    ->all(),
                'link_link_type' => 'page',
                'link_link_model_id' => self::pageId('faq'),
                'link_text' => ['sk' => 'Zobraziť všetky často kladené otázky', 'en' => 'View all frequently asked questions', 'cs' => 'Zobrazit všechny často kladené otázky'],
            ]),
        ];
    }

    private static function faqContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'Často kladené otázky', 'en' => 'Frequently Asked Questions', 'cs' => 'Často kladené otázky'],
                'subtitle' => ['sk' => 'Nájdite odpovede na najčastejšie otázky o našich tréningoch, vystúpeniach a workshopoch', 'en' => 'Find answers to the most common questions about our training, performances and workshops', 'cs' => 'Najděte odpovědi na nejčastější otázky o našich trénincích, vystoupeních a workshopech'],
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Všetky otázky', 'en' => 'All Questions', 'cs' => 'Všechny otázky'],
                'show_all' => true,
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Nenašli ste odpoveď?', 'en' => 'Didn\'t Find an Answer?', 'cs' => 'Nenašli jste odpověď?'],
                'description' => ['sk' => 'Kontaktujte nás a radi vám pomôžeme.', 'en' => 'Contact us and we will be happy to help you.', 'cs' => 'Kontaktujte nás a rádi vám pomůžeme.'],
                'button_text' => ['sk' => 'Kontaktovať nás', 'en' => 'Contact Us', 'cs' => 'Kontaktovat nás'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('contact'),
                'background_color' => '#dc2626',
            ]),
        ];
    }

    private static function supportContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'Pomôžte nám rásť', 'en' => 'Help Us Grow', 'cs' => 'Pomozte nám růst'],
                'subtitle' => ['sk' => 'Vaša podpora nám pomáha rozvíjať komunitu a poskytovať kvalitné tréningy pre všetkých. Každý dar má zmysel.', 'en' => 'Your support helps us develop the community and provide quality training for everyone. Every donation matters.', 'cs' => 'Vaše podpora nám pomáhá rozvíjet komunitu a poskytovat kvalitní tréninky pro všechny. Každý dar má smysl.'],
                'cta_text' => ['sk' => 'Darovať', 'en' => 'Donate', 'cs' => 'Darovat'],
                'cta_link_type' => 'custom',
                'cta_link_url' => ['sk' => '#bank', 'en' => '#bank', 'cs' => '#bank'],
            ]),
            self::brick('donation-info', [
                'bank_title' => ['sk' => 'Bankový prevod', 'en' => 'Bank Transfer', 'cs' => 'Bankovní převod'],
                'bank_rows' => [
                    ['label' => ['sk' => 'Názov organizácie', 'en' => 'Organization Name', 'cs' => 'Název organizace'], 'value' => ['sk' => 'BCZ Club, občianske združenie', 'en' => 'BCZ Club, civic association', 'cs' => 'BCZ Club, občanské sdružení']],
                    ['label' => ['sk' => 'IČO', 'en' => 'ID Number', 'cs' => 'IČO'], 'value' => ['sk' => '52 841 235', 'en' => '52 841 235', 'cs' => '52 841 235']],
                    ['label' => ['sk' => 'IBAN', 'en' => 'IBAN', 'cs' => 'IBAN'], 'value' => ['sk' => 'SK89 0900 0000 0051 8742 6513', 'en' => 'SK89 0900 0000 0051 8742 6513', 'cs' => 'SK89 0900 0000 0051 8742 6513']],
                    ['label' => ['sk' => 'SWIFT/BIC', 'en' => 'SWIFT/BIC', 'cs' => 'SWIFT/BIC'], 'value' => ['sk' => 'GIBASKBX', 'en' => 'GIBASKBX', 'cs' => 'GIBASKBX']],
                    ['label' => ['sk' => 'Banka', 'en' => 'Bank', 'cs' => 'Banka'], 'value' => ['sk' => 'Slovenská sporiteľňa, a.s.', 'en' => 'Slovenská sporiteľňa, a.s.', 'cs' => 'Slovenská sporiteľňa, a.s.']],
                    ['label' => ['sk' => 'Variabilný symbol', 'en' => 'Variable Symbol', 'cs' => 'Variabilní symbol'], 'value' => ['sk' => date('Y').' (aktuálny rok)', 'en' => date('Y').' (current year)', 'cs' => date('Y').' (aktuální rok)']],
                ],
                'qr_title' => ['sk' => 'Naskenujte QR kód', 'en' => 'Scan QR Code', 'cs' => 'Naskenujte QR kód'],
                'qr_description' => ['sk' => 'Použite mobilnú aplikáciu vašej banky na rýchlu platbu. QR kód obsahuje všetky potrebné údaje.', 'en' => 'Use your bank\'s mobile app for a quick payment. The QR code contains all the necessary details.', 'cs' => 'Použijte mobilní aplikaci vaší banky pro rychlou platbu. QR kód obsahuje všechny potřebné údaje.'],
                'iban' => ['sk' => 'SK8909000000005187426513', 'en' => 'SK8909000000005187426513', 'cs' => 'SK8909000000005187426513'],
                'qr_recipient_name' => ['sk' => 'BCZ Club, občianske združenie', 'en' => 'BCZ Club, civic association', 'cs' => 'BCZ Club, občanské sdružení'],
                'qr_format' => ['sk' => 'pay_by_square', 'en' => 'pay_by_square', 'cs' => 'pay_by_square'],
                'usage_title' => ['sk' => 'Na čo využívame dary', 'en' => 'How We Use Donations', 'cs' => 'Na co využíváme dary'],
                'usage_description' => ['sk' => 'Všetky získané prostriedky využívame transparentne na rozvoj našej komunity a zlepšovanie tréningových podmienok.', 'en' => 'We use all funds transparently to develop our community and improve training conditions.', 'cs' => 'Všechny získané prostředky využíváme transparentně na rozvoj naší komunity a zlepšování tréninkových podmínek.'],
                'usage_items' => [
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'color' => '#FF2D2D', 'title' => ['sk' => 'Cvičebné pomôcky', 'en' => 'Training Equipment', 'cs' => 'Cvičební pomůcky'], 'description' => ['sk' => 'Nákup nových podložiek, odporových gúm, švihadiel a ďalšieho vybavenia pre tréningy.', 'en' => 'Purchase of new mats, resistance bands, jump ropes and other training equipment.', 'cs' => 'Nákup nových podložek, odporových gum, švihadel a dalšího vybavení pro tréninky.']],
                    ['icon' => 'heroicon-o-squares-2x2', 'color' => '#3B82F6', 'title' => ['sk' => 'Hrazdy a bradlá', 'en' => 'Pull-up Bars and Dip Bars', 'cs' => 'Hrazdy a bradla'], 'description' => ['sk' => 'Inštalácia a údržba street workout prvkov v Čadci a okolí.', 'en' => 'Installation and maintenance of street workout elements in Čadca and surroundings.', 'cs' => 'Instalace a údržba street workout prvků v Čadci a okolí.']],
                    ['icon' => 'heroicon-o-shield-check', 'color' => '#8B5CF6', 'title' => ['sk' => 'Bezpečnostné vybavenie', 'en' => 'Safety Equipment', 'cs' => 'Bezpečnostní vybavení'], 'description' => ['sk' => 'Crash pady, žinenky a ochranné pomôcky pre bezpečný tréning akrobacie.', 'en' => 'Crash pads, mats and protective equipment for safe acrobatics training.', 'cs' => 'Crash pady, žíněnky a ochranné pomůcky pro bezpečný trénink akrobacie.']],
                    ['icon' => 'heroicon-o-calendar-days', 'color' => '#F59E0B', 'title' => ['sk' => 'Workshopy a podujatia', 'en' => 'Workshops and Events', 'cs' => 'Workshopy a akce'], 'description' => ['sk' => 'Organizácia bezplatných workshopov a podujatí pre verejnosť.', 'en' => 'Organizing free workshops and events for the public.', 'cs' => 'Organizace bezplatných workshopů a akcí pro veřejnost.']],
                ],
                'tax_title' => ['sk' => 'Darujte nám 2% z dane', 'en' => 'Donate 2% of Your Tax', 'cs' => 'Darujte nám 2% z daní'],
                'tax_description' => ['sk' => 'Darovaním 2% z dane nám pomôžete bez toho, aby vás to stálo čokoľvek navyše. Tieto prostriedky idú priamo na rozvoj našich aktivít.', 'en' => 'By donating 2% of your tax you help us without any extra cost to you. These funds go directly to developing our activities.', 'cs' => 'Darováním 2% z daní nám pomůžete, aniž by vás to stálo cokoli navíc. Tyto prostředky jdou přímo na rozvoj našich aktivit.'],
                'tax_link_type' => 'page',
                'tax_link_model_id' => self::pageId('tax_donation'),
                'tax_button_text' => ['sk' => 'Zistiť viac o 2% z dane', 'en' => 'Learn More About 2% Tax Donation', 'cs' => 'Zjistit více o 2% z daní'],
                'contact_title' => ['sk' => 'Kontaktujte nás', 'en' => 'Contact Us', 'cs' => 'Kontaktujte nás'],
                'contact_description' => ['sk' => 'Máte otázky ohľadom darovania alebo spolupráce? Neváhajte nás kontaktovať.', 'en' => 'Have questions about donating or partnering? Don\'t hesitate to contact us.', 'cs' => 'Máte otázky ohledně darování nebo spolupráce? Neváhejte nás kontaktovat.'],
                'contact_email' => 'podpora@bczclub.sk',
                'contact_phone' => '+421 907 123 456',
                'contact_address' => 'Palárikova 123, 022 01 Čadca',
            ]),
            self::brick('stats', [
                'badge' => ['sk' => 'TRANSPARENTNOSŤ', 'en' => 'TRANSPARENCY', 'cs' => 'TRANSPARENTNOST'],
                'badge_color' => '#22C55E',
                'title' => ['sk' => 'Zaväzujeme sa k transparentnosti', 'en' => 'We Are Committed to Transparency', 'cs' => 'Zavazujeme se k transparentnosti'],
                'description' => ['sk' => 'Každý rok zverejňujeme výročnú správu o hospodárení, kde nájdete podrobný prehľad o využití všetkých finančných prostriedkov.', 'en' => 'Every year we publish an annual financial report where you can find a detailed overview of how all funds were used.', 'cs' => 'Každý rok zveřejňujeme výroční zprávu o hospodaření, kde najdete podrobný přehled o využití všech finančních prostředků.'],
                'background_color' => '#0D0D0D',
                'items' => [
                    ['number' => '100%', 'color' => '#22C55E', 'label' => ['sk' => 'Využité na rozvoj', 'en' => 'Used for Development', 'cs' => 'Využité na rozvoj']],
                    ['number' => '0%', 'color' => '#FF2D2D', 'label' => ['sk' => 'Administratívne náklady', 'en' => 'Administrative Costs', 'cs' => 'Administrativní náklady']],
                    ['number' => '3+', 'color' => '#3B82F6', 'label' => ['sk' => 'Roky transparentnosti', 'en' => 'Years of Transparency', 'cs' => 'Roky transparentnosti']],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Každý dar má zmysel', 'en' => 'Every Donation Matters', 'cs' => 'Každý dar má smysl'],
                'description' => ['sk' => 'Aj malá suma pomáha. Ďakujeme, že ste súčasťou našej komunity.', 'en' => 'Even a small amount helps. Thank you for being part of our community.', 'cs' => 'I malá částka pomáhá. Děkujeme, že jste součástí naší komunity.'],
                'button_text' => ['sk' => 'Darovať teraz', 'en' => 'Donate Now', 'cs' => 'Darovat nyní'],
                'button_icon' => 'heroicon-o-heart',
                'button_link_type' => 'custom',
                'button_link_url' => '#bank',
                'secondary_text' => ['sk' => 'Kontaktovať nás', 'en' => 'Contact Us', 'cs' => 'Kontaktovat nás'],
                'secondary_link_type' => 'page',
                'secondary_link_model_id' => self::pageId('contact'),
                'background_color' => '#0A0A0A',
            ]),
        ];
    }

    private static function founderContent(): array
    {
        return [
            self::brick('profile-hero', [
                'title' => ['sk' => 'Dominik Klimek', 'en' => 'Dominik Klimek', 'cs' => 'Dominik Klimek'],
                'subtitle' => ['sk' => 'Majster sveta v street workoute · Master tréner · Zakladateľ BCZ Club', 'en' => 'World Champion in street workout · Master coach · Founder of BCZ Club', 'cs' => 'Mistr světa ve street workoutu · Master trenér · Zakladatel BCZ Club'],
                'badge' => ['sk' => 'ZAKLADATEĽ & CEO', 'en' => 'FOUNDER & CEO', 'cs' => 'ZAKLADATEL & CEO'],
                'background_image' => self::media('hero-bg'),
                'breadcrumb' => [
                    ['text' => ['sk' => 'Domov', 'en' => 'Home', 'cs' => 'Domů'], 'url' => '/'],
                    ['text' => ['sk' => 'O nás', 'en' => 'About', 'cs' => 'O nás'], 'url' => '/o-nas'],
                    ['text' => ['sk' => 'Dominik Klimek', 'en' => 'Dominik Klimek', 'cs' => 'Dominik Klimek']],
                ],
            ]),
            self::brick('stats', [
                'items' => [
                    ['number' => '1x', 'label' => ['sk' => 'Majster sveta', 'en' => 'World Champion', 'cs' => 'Mistr světa']],
                    ['number' => '3x', 'label' => ['sk' => 'Majster SR', 'en' => 'Slovak Champion', 'cs' => 'Mistr SR']],
                    ['number' => 'L4', 'label' => ['sk' => 'S&C Coach', 'en' => 'S&C Coach', 'cs' => 'S&C Coach']],
                    ['number' => '500+', 'label' => ['sk' => 'Mentorovaných detí', 'en' => 'Mentored Children', 'cs' => 'Mentorovaných dětí']],
                    ['number' => '30+', 'label' => ['sk' => 'Krajiny', 'en' => 'Countries', 'cs' => 'Země']],
                ],
            ]),
            self::brick('profile-bio', [
                'label' => ['sk' => 'MÔJ PRÍBEH', 'en' => 'MY STORY', 'cs' => 'MŮJ PŘÍBĚH'],
                'title' => ['sk' => "OD DETÍNSKYCH\nSNOV K TITULU\nMAJSTRA SVETA", 'en' => "FROM CHILDHOOD\nDREAMS TO WORLD\nCHAMPION TITLE", 'cs' => "OD DĚTSKÝCH\nSNŮ K TITULU\nMISTRA SVĚTA"],
                'text' => ['sk' => '<p>Od detstva som hľadal svoju cestu cez futbal, volejbal, parkour, klavír aj šachovú ligu. Nič ma však nenaplnilo tak ako street workout, ktorý som objavil, keď som uvidel chlapa robiť variace na hrázdach na ihrisku. Behom niekoľkých mesiacov som zvládol zadnú páku a vedel som — toto je to.</p><p>V roku 2019 som súťažil prvýkrát na majstrovstvách v Žiline a nepostúpil som ani z kvalifikácie. O tri roky neskôr som však stál na najvyššom stupienku na Majstrovstvách sveta v Rige ako majster sveta v strednej váhovej kategórii.</p>', 'en' => '<p>Since childhood I searched for my path through football, volleyball, parkour, piano and chess league. Nothing fulfilled me like street workout, which I discovered when I saw a guy doing variations on bars at a playground. Within a few months I mastered the back lever and I knew — this is it.</p><p>In 2019 I competed for the first time at the championship in Žilina and didn\'t even advance from the qualifications. Three years later, however, I stood on the highest podium at the World Championship in Riga as the world champion in the middleweight category.</p>', 'cs' => '<p>Od dětství jsem hledal svou cestu přes fotbal, volejbal, parkour, klavír i šachovou ligu. Nic mě však nenaplnilo tak jako street workout, který jsem objevil, když jsem uviděl chlapa dělat variace na hrazdách na hřišti. Během několika měsíců jsem zvládl zadní páku a věděl jsem — tohle je to.</p><p>V roce 2019 jsem soutěžil poprvé na mistrovství v Žilině a nepostoupil jsem ani z kvalifikace. O tři roky později jsem však stál na nejvyšším stupínku na Mistrovství světa v Rize jako mistr světa ve střední váhové kategorii.</p>'],
                'image' => self::media('founder-img'),
            ]),
            self::brick('achievement-cards', [
                'label' => ['sk' => 'VÝSLEDKY', 'en' => 'RESULTS', 'cs' => 'VÝSLEDKY'],
                'title' => ['sk' => 'ÚSPECHY NA SÚŤAŽIACH', 'en' => 'COMPETITION ACHIEVEMENTS', 'cs' => 'ÚSPĚCHY NA SOUTĚŽÍCH'],
                'description' => ['sk' => 'Od prvého neúspechu na kvalifikácii až po titul majstra sveta — každá súťaž bola krokom vpred.', 'en' => 'From the first qualification failure to the world championship title — every competition was a step forward.', 'cs' => 'Od prvního neúspěchu na kvalifikaci až po titul mistra světa — každá soutěž byla krokem vpřed.'],
                'cards' => [
                    ['year' => '2022', 'badge_type' => 'gold', 'title' => ['sk' => 'Majster sveta', 'en' => 'World Champion', 'cs' => 'Mistr světa'], 'description' => ['sk' => 'WSWCF World Championship<br>Riga, Lotyšsko<br>Stredná váha (68-80 kg)', 'en' => 'WSWCF World Championship<br>Riga, Latvia<br>Middleweight (68-80 kg)', 'cs' => 'WSWCF World Championship<br>Riga, Lotyšsko<br>Střední váha (68-80 kg)'], 'badge_text' => ['sk' => '1. MIESTO', 'en' => '1st PLACE', 'cs' => '1. MÍSTO']],
                    ['year' => '2020–2022', 'badge_type' => 'gold', 'title' => ['sk' => '3x Majster SR', 'en' => '3x Slovak Champion', 'cs' => '3x Mistr SR'], 'description' => ['sk' => 'Majstrovstvá Slovenska<br>v street workoute<br>Tri po sebe idúce tituly', 'en' => 'Slovak Championship<br>in street workout<br>Three consecutive titles', 'cs' => 'Mistrovství Slovenska<br>ve street workoutu<br>Tři po sobě jdoucí tituly'], 'badge_text' => ['sk' => 'ZLATO', 'en' => 'GOLD', 'cs' => 'ZLATO']],
                    ['year' => '2021', 'badge_type' => 'top10', 'title' => ['sk' => 'MS Moskva', 'en' => 'WC Moscow', 'cs' => 'MS Moskva'], 'description' => ['sk' => 'Majstrovstvá sveta<br>Moskva, Rusko<br>Kvalifikácia 8. / Finále 7.', 'en' => 'World Championship<br>Moscow, Russia<br>Qualification 8th / Final 7th', 'cs' => 'Mistrovství světa<br>Moskva, Rusko<br>Kvalifikace 8. / Finále 7.'], 'badge_text' => ['sk' => 'TOP 10', 'en' => 'TOP 10', 'cs' => 'TOP 10']],
                    ['year' => '2019', 'badge_type' => 'silver', 'title' => ['sk' => 'Vicemajster SR', 'en' => 'Slovak Vice-Champion', 'cs' => 'Vicemistr SR'], 'description' => ['sk' => 'Majstrovstvá Slovenska<br>Trenčín<br>2. miesto', 'en' => 'Slovak Championship<br>Trenčín<br>2nd place', 'cs' => 'Mistrovství Slovenska<br>Trenčín<br>2. místo'], 'badge_text' => ['sk' => 'STRIEBRO', 'en' => 'SILVER', 'cs' => 'STŘÍBRO']],
                    ['year' => '2019', 'badge_type' => 'gold', 'title' => ['sk' => 'SW Games Brno', 'en' => 'SW Games Brno', 'cs' => 'SW Games Brno'], 'description' => ['sk' => 'Street Workout Games<br>Brno, Česko<br>Víťaz', 'en' => 'Street Workout Games<br>Brno, Czechia<br>Winner', 'cs' => 'Street Workout Games<br>Brno, Česko<br>Vítěz'], 'badge_text' => ['sk' => '1. MIESTO', 'en' => '1st PLACE', 'cs' => '1. MÍSTO']],
                    ['year' => '2022', 'badge_type' => 'silver', 'title' => ['sk' => 'Svetový pohár', 'en' => 'World Cup', 'cs' => 'Světový pohár'], 'description' => ['sk' => 'WSWCF World Cup<br>Jurmala, Lotyšsko<br>Striebro', 'en' => 'WSWCF World Cup<br>Jurmala, Latvia<br>Silver', 'cs' => 'WSWCF World Cup<br>Jurmala, Lotyšsko<br>Stříbro'], 'badge_text' => ['sk' => 'STRIEBRO', 'en' => 'SILVER', 'cs' => 'STŘÍBRO']],
                ],
            ]),
            self::brick('vertical-timeline', [
                'label' => ['sk' => 'CESTA', 'en' => 'JOURNEY', 'cs' => 'CESTA'],
                'title' => ['sk' => 'MOJA CESTA', 'en' => 'MY JOURNEY', 'cs' => 'MOJE CESTA'],
                'items' => [
                    ['year' => '2017', 'title' => ['sk' => 'Objav street workoute', 'en' => 'Discovering Street Workout', 'cs' => 'Objev street workoutu'], 'description' => ['sk' => 'Prvý kontakt s kalistenikou na ihrisku. Začiatok samoukého tréningu a nekončiace sa hodiny na hrázdach.', 'en' => 'First contact with calisthenics at a playground. Beginning of self-taught training and endless hours on bars.', 'cs' => 'První kontakt s kalistenikou na hřišti. Začátek samoukého tréninku a nekončící hodiny na hrazdách.']],
                    ['year' => '2019', 'title' => ['sk' => 'Prvé súťaže a vicemajster SR', 'en' => 'First Competitions and Slovak Vice-Champion', 'cs' => 'První soutěže a vicemistr SR'], 'description' => ['sk' => 'Prvá účasť na majstrovstvách SR v Žiline, nepostúpil z kvalifikácie. O niekoľko mesiacov neskôr už 2. miesto na SR v Trenčíne. Víťazstvo na SW Games Brno.', 'en' => 'First participation at the Slovak championship in Žilina, didn\'t advance from qualification. A few months later already 2nd place at the Slovak championship in Trenčín. Victory at SW Games Brno.', 'cs' => 'První účast na mistrovství SR v Žilině, nepostoupil z kvalifikace. O několik měsíců později již 2. místo na SR v Trenčíně. Vítězství na SW Games Brno.']],
                    ['year' => '2020', 'title' => ['sk' => 'Založenie Street Workout Kysuce', 'en' => 'Founding Street Workout Kysuce', 'cs' => 'Založení Street Workout Kysuce'], 'description' => ['sk' => 'Prvý titul majstra Slovenska. Založenie občianskeho združenia Street Workout Kysuce, dnešného BCZ Club.', 'en' => 'First Slovak championship title. Founding of the civic association Street Workout Kysuce, today\'s BCZ Club.', 'cs' => 'První titul mistra Slovenska. Založení občanského sdružení Street Workout Kysuce, dnešního BCZ Club.']],
                    ['year' => '2022', 'title' => ['sk' => 'Majster sveta v Rige', 'en' => 'World Champion in Riga', 'cs' => 'Mistr světa v Rize'], 'description' => ['sk' => 'Titul majstra sveta v strednej váhovej kategórii na MS v Rige. Na tej istej súťaži získali medaily aj bratia Matej (zlato ľahká váha) a Daniel (striebro).', 'en' => 'World championship title in the middleweight category at the WC in Riga. At the same competition, brothers Matej (gold lightweight) and Daniel (silver) also won medals.', 'cs' => 'Titul mistra světa ve střední váhové kategorii na MS v Rize. Na té samé soutěži získali medaile i bratři Matej (zlato lehká váha) a Daniel (stříbro).']],
                    ['year' => '2024', 'title' => ['sk' => 'Medzinárodný tréner a porotca', 'en' => 'International Coach and Judge', 'cs' => 'Mezinárodní trenér a porotce'], 'description' => ['sk' => 'Prestávka od súťaženia. Zameranie na koučšing, medzinárodné workshopy (Hong Kong, Uzbekistan, Švajčiarsko) a porotcovanie na MS v Hong Kongu.', 'en' => 'Break from competing. Focus on coaching, international workshops (Hong Kong, Uzbekistan, Switzerland) and judging at the WC in Hong Kong.', 'cs' => 'Přestávka od soutěžení. Zaměření na koučink, mezinárodní workshopy (Hong Kong, Uzbekistán, Švýcarsko) a porotcování na MS v Hong Kongu.']],
                ],
            ]),
            self::brick('profile-section', [
                'label' => ['sk' => 'MENTOR & TRÉNER', 'en' => 'MENTOR & COACH', 'cs' => 'MENTOR & TRENÉR'],
                'title' => ['sk' => "INŠPIRÁCIA\nPRE MLADÚ\nGENERÁCIU", 'en' => "INSPIRATION\nFOR THE YOUNG\nGENERATION", 'cs' => "INSPIRACE\nPRO MLADOU\nGENERACI"],
                'text' => ['sk' => 'Viac než súťažením sa Dominik venuje práci s mládežou. Spolu s kolegami chodí po školách, kde učí deti o stanovení cieľov, vytrvalosti a dôležitosti pohybu. Jeho cieľom je ukázať mladým ľuďom, že disciplína a tvrdá práca dokážu zmeniť životy.', 'en' => 'More than competing, Dominik dedicates his time to working with youth. Together with colleagues, he visits schools where he teaches children about goal setting, perseverance and the importance of movement. His goal is to show young people that discipline and hard work can change lives.', 'cs' => 'Více než soutěžením se Dominik věnuje práci s mládeží. Spolu s kolegy chodí po školách, kde učí děti o stanovení cílů, vytrvalosti a důležitosti pohybu. Jeho cílem je ukázat mladým lidem, že disciplína a tvrdá práce dokážou změnit životy.'],
                'text2' => ['sk' => 'Ako jediný certifikovaný master tréner kalisteniky na Slovensku viedol medzinárodné workshopy po celom svete — od Hong Kongu cez Uzbekistan až po Švajčiarsko.', 'en' => 'As the only certified master calisthenics coach in Slovakia, he has led international workshops around the world — from Hong Kong through Uzbekistan to Switzerland.', 'cs' => 'Jako jediný certifikovaný master trenér kalisteniky na Slovensku vedl mezinárodní workshopy po celém světě — od Hong Kongu přes Uzbekistán až po Švýcarsko.'],
                'image' => self::media('founder-img'),
            ]),
            self::brick('styled-quote', [
                'quote' => ['sk' => 'Chcem pomáhať rozvíjať street workout na Slovensku aj vo svete a zároveň inšpirovať ostatných, aby nasledovali svoje sny.', 'en' => 'I want to help develop street workout in Slovakia and around the world while inspiring others to follow their dreams.', 'cs' => 'Chci pomáhat rozvíjet street workout na Slovensku i ve světě a zároveň inspirovat ostatní, aby následovali své sny.'],
                'attribution' => ['sk' => 'Dominik Klimek', 'en' => 'Dominik Klimek', 'cs' => 'Dominik Klimek'],
            ]),
            self::brick('social-links', [
                'label' => ['sk' => 'KONTAKT & SOCIÁLNE SIETE', 'en' => 'CONTACT & SOCIAL MEDIA', 'cs' => 'KONTAKT & SOCIÁLNÍ SÍTĚ'],
                'title' => ['sk' => 'SPOJ SA S DOMINIKOM', 'en' => 'CONNECT WITH DOMINIK', 'cs' => 'SPOJ SE S DOMINIKEM'],
                'description' => ['sk' => 'Sleduj Dominika na sociálnych sieťach, navštív jeho osobnú stránku alebo ho kontaktuj priamo.', 'en' => 'Follow Dominik on social media, visit his personal website or contact him directly.', 'cs' => 'Sleduj Dominika na sociálních sítích, navštiv jeho osobní stránku nebo ho kontaktuj přímo.'],
                'socials' => [
                    ['platform' => 'website', 'url' => 'https://dodoworkout.com', 'name' => ['sk' => 'Osobná stránka', 'en' => 'Personal Website', 'cs' => 'Osobní stránka'], 'handle' => ['sk' => 'dodoworkout.com', 'en' => 'dodoworkout.com', 'cs' => 'dodoworkout.com']],
                    ['platform' => 'instagram', 'url' => 'https://instagram.com/dodoworkout', 'name' => ['sk' => 'Instagram', 'en' => 'Instagram', 'cs' => 'Instagram'], 'handle' => ['sk' => '@dodoworkout', 'en' => '@dodoworkout', 'cs' => '@dodoworkout']],
                    ['platform' => 'youtube', 'url' => 'https://youtube.com/@dodoworkout', 'name' => ['sk' => 'YouTube', 'en' => 'YouTube', 'cs' => 'YouTube'], 'handle' => ['sk' => '@dodoworkout', 'en' => '@dodoworkout', 'cs' => '@dodoworkout']],
                    ['platform' => 'tiktok', 'url' => 'https://tiktok.com/@dodoworkout_sk', 'name' => ['sk' => 'TikTok', 'en' => 'TikTok', 'cs' => 'TikTok'], 'handle' => ['sk' => '@dodoworkout_sk', 'en' => '@dodoworkout_sk', 'cs' => '@dodoworkout_sk']],
                    ['platform' => 'facebook', 'url' => 'https://facebook.com/dominikklimek', 'name' => ['sk' => 'Facebook', 'en' => 'Facebook', 'cs' => 'Facebook'], 'handle' => ['sk' => 'Dominik Klimek', 'en' => 'Dominik Klimek', 'cs' => 'Dominik Klimek']],
                ],
                'email' => ['sk' => 'info@dodoworkout.com', 'en' => 'info@dodoworkout.com', 'cs' => 'info@dodoworkout.com'],
                'phone' => ['sk' => '+421 950 451 310', 'en' => '+421 950 451 310', 'cs' => '+421 950 451 310'],
            ]),
        ];
    }

    private static function taxDonationContent(): array
    {
        return [
            self::brick('centered-hero', [
                'badge' => ['sk' => '2% Z DANE', 'en' => '2% TAX', 'cs' => '2% Z DANÍ'],
                'title' => ['sk' => 'Darujte nám 2% z dane', 'en' => 'Donate 2% of Your Tax to Us', 'cs' => 'Darujte nám 2% z daní'],
                'subtitle' => ['sk' => 'Ak sa vám páči naša činnosť a ciele, môžete nás podporiť darovaním 2% z vašej dane. Nestojí vás to nič navyše - tieto peniaze by inak išli štátu.', 'en' => 'If you like our activities and goals, you can support us by donating 2% of your tax. It costs you nothing extra — this money would otherwise go to the state.', 'cs' => 'Pokud se vám líbí naše činnost a cíle, můžete nás podpořit darováním 2% z vaší daně. Nestojí vás to nic navíc — tyto peníze by jinak šly státu.'],
                'highlight' => ['sk' => 'Vaše 2% pomáhajú rozvíjať parkour, street workout a calisthenics komunitu na Slovensku', 'en' => 'Your 2% helps develop the parkour, street workout and calisthenics community in Slovakia', 'cs' => 'Vaše 2% pomáhají rozvíjet parkour, street workout a calisthenics komunitu na Slovensku'],
            ]),
            self::brick('video-section', [
                'title' => ['sk' => 'Spoznajte našu komunitu', 'en' => 'Meet Our Community', 'cs' => 'Poznejte naši komunitu'],
                'subtitle' => ['sk' => 'Pozrite si krátke video o tom, čo robíme a ako pomáhame mladým ľuďom rozvíjať sa cez pohyb', 'en' => 'Watch a short video about what we do and how we help young people develop through movement', 'cs' => 'Podívejte se na krátké video o tom, co děláme a jak pomáháme mladým lidem rozvíjet se přes pohyb'],
                'checkpoints' => [
                    ['text' => ['sk' => 'Trénujeme deti, mládež aj dospelých', 'en' => 'We train children, youth and adults', 'cs' => 'Trénujeme děti, mládež i dospělé']],
                    ['text' => ['sk' => 'Organizujeme súťaže a workshopy', 'en' => 'We organize competitions and workshops', 'cs' => 'Organizujeme soutěže a workshopy']],
                    ['text' => ['sk' => 'Budujeme silnú komunitu pohybu', 'en' => 'We build a strong movement community', 'cs' => 'Budujeme silnou komunitu pohybu']],
                ],
            ]),
            self::brick('details-card', [
                'title' => ['sk' => 'Údaje organizácie', 'en' => 'Organization Details', 'cs' => 'Údaje organizace'],
                'subtitle' => ['sk' => 'Tieto údaje potrebujete vyplniť do formulára alebo daňového priznania', 'en' => 'You need these details for the form or tax return', 'cs' => 'Tyto údaje potřebujete vyplnit do formuláře nebo daňového přiznání'],
                'rows' => [
                    ['label' => ['sk' => 'Obchodné meno (názov)', 'en' => 'Organization Name', 'cs' => 'Obchodní jméno (název)'], 'value' => ['sk' => 'BCZ Club, občianske združenie', 'en' => 'BCZ Club, civic association', 'cs' => 'BCZ Club, občanské sdružení']],
                    ['label' => ['sk' => 'Sídlo', 'en' => 'Registered Office', 'cs' => 'Sídlo'], 'value' => ['sk' => 'Palárikova 123, 022 01 Čadca', 'en' => 'Palárikova 123, 022 01 Čadca', 'cs' => 'Palárikova 123, 022 01 Čadca']],
                    ['label' => ['sk' => 'IČO', 'en' => 'ID Number', 'cs' => 'IČO'], 'value' => ['sk' => '52 841 235', 'en' => '52 841 235', 'cs' => '52 841 235'], 'highlight' => true],
                    ['label' => ['sk' => 'Právna forma', 'en' => 'Legal Form', 'cs' => 'Právní forma'], 'value' => ['sk' => 'Občianske združenie', 'en' => 'Civic Association', 'cs' => 'Občanské sdružení']],
                    ['label' => ['sk' => 'Rok', 'en' => 'Year', 'cs' => 'Rok'], 'value' => ['sk' => '2025', 'en' => '2025', 'cs' => '2025']],
                ],
                'show_copy_button' => true,
            ]),
            self::brick('guide-cards', [
                'title' => ['sk' => 'Ako darovať 2% z dane?', 'en' => 'How to Donate 2% of Your Tax?', 'cs' => 'Jak darovat 2% z daní?'],
                'subtitle' => ['sk' => 'Vyberte si postup podľa toho, či ste zamestnanec, SZČO alebo právnická osoba', 'en' => 'Choose the procedure based on whether you are an employee, self-employed or a legal entity', 'cs' => 'Vyberte si postup podle toho, zda jste zaměstnanec, OSVČ nebo právnická osoba'],
                'cards' => [
                    [
                        'color' => '#3B82F6',
                        'icon' => 'heroicon-o-briefcase',
                        'title' => ['sk' => 'Zamestnanci', 'en' => 'Employees', 'cs' => 'Zaměstnanci'],
                        'subtitle' => ['sk' => 'Ak vám zamestnávateľ robí ročné zúčtovanie dane', 'en' => 'If your employer does your annual tax settlement', 'cs' => 'Pokud vám zaměstnavatel dělá roční zúčtování daně'],
                        'steps' => [
                            ['text' => ['sk' => 'Požiadajte zamestnávateľa o Potvrdenie o zaplatení dane', 'en' => 'Ask your employer for a Tax Payment Confirmation', 'cs' => 'Požádejte zaměstnavatele o Potvrzení o zaplacení daně']],
                            ['text' => ['sk' => 'Vyplňte Vyhlásenie o poukázaní 2% dane', 'en' => 'Fill out the Declaration for 2% tax allocation', 'cs' => 'Vyplňte Prohlášení o poukázání 2% daně']],
                            ['text' => ['sk' => 'Obe tlačivá doručte na daňový úrad do 30. apríla', 'en' => 'Submit both forms to the tax office by April 30', 'cs' => 'Oba formuláře doručte na finanční úřad do 30. dubna']],
                        ],
                        'button_text' => ['sk' => 'Stiahnuť Vyhlásenie', 'en' => 'Download Declaration', 'cs' => 'Stáhnout Prohlášení'],
                        'button_link_type' => 'custom',
                        'button_link_url' => ['sk' => '#', 'en' => '#', 'cs' => '#'],
                    ],
                    [
                        'color' => '#22C55E',
                        'icon' => 'heroicon-o-clipboard-document-list',
                        'title' => ['sk' => 'Fyzické osoby (SZČO)', 'en' => 'Individuals (Self-employed)', 'cs' => 'Fyzické osoby (OSVČ)'],
                        'subtitle' => ['sk' => 'Ak si podávate daňové priznanie sami', 'en' => 'If you file your tax return yourself', 'cs' => 'Pokud si podáváte daňové přiznání sami'],
                        'steps' => [
                            ['text' => ['sk' => 'V daňovom priznaní (typ A alebo B) vyplňte oddiel na poukázanie 2%', 'en' => 'In your tax return (type A or B) fill in the 2% allocation section', 'cs' => 'V daňovém přiznání (typ A nebo B) vyplňte oddíl na poukázání 2%']],
                            ['text' => ['sk' => 'Uveďte naše IČO a názov organizácie', 'en' => 'Enter our ID number and organization name', 'cs' => 'Uveďte naše IČO a název organizace']],
                            ['text' => ['sk' => 'Podajte daňové priznanie do 31. marca', 'en' => 'Submit your tax return by March 31', 'cs' => 'Podejte daňové přiznání do 31. března']],
                        ],
                        'button_text' => ['sk' => 'Daňové priznanie typ A / B', 'en' => 'Tax Return Type A / B', 'cs' => 'Daňové přiznání typ A / B'],
                        'button_link_type' => 'custom',
                        'button_link_url' => ['sk' => '#', 'en' => '#', 'cs' => '#'],
                    ],
                    [
                        'color' => '#8B5CF6',
                        'icon' => 'heroicon-o-building-office-2',
                        'title' => ['sk' => 'Právnické osoby', 'en' => 'Legal Entities', 'cs' => 'Právnické osoby'],
                        'subtitle' => ['sk' => 'Firmy a spoločnosti môžu darovať 1-2%', 'en' => 'Companies can donate 1-2%', 'cs' => 'Firmy a společnosti mohou darovat 1-2%'],
                        'steps' => [
                            ['text' => ['sk' => 'V daňovom priznaní právnickej osoby vyplňte príslušnú časť', 'en' => 'Fill in the relevant section in your corporate tax return', 'cs' => 'V daňovém přiznání právnické osoby vyplňte příslušnou část']],
                            ['text' => ['sk' => 'Môžete uviesť aj viacerých prijímateľov', 'en' => 'You can list multiple recipients', 'cs' => 'Můžete uvést i více příjemců']],
                            ['text' => ['sk' => 'Termín podania: 31. marca (resp. v predĺženej lehote)', 'en' => 'Deadline: March 31 (or extended deadline)', 'cs' => 'Termín podání: 31. března (resp. v prodloužené lhůtě)']],
                        ],
                        'button_text' => ['sk' => 'Daňové priznanie PO', 'en' => 'Corporate Tax Return', 'cs' => 'Daňové přiznání PO'],
                        'button_link_type' => 'custom',
                        'button_link_url' => ['sk' => '#', 'en' => '#', 'cs' => '#'],
                    ],
                ],
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Často kladené otázky', 'en' => 'Frequently Asked Questions', 'cs' => 'Často kladené otázky'],
                'faq_ids' => self::ensureTaxFaqs(),
            ]),
            self::brick('icon-cta', [
                'icon_text' => ['sk' => '2%', 'en' => '2%', 'cs' => '2%'],
                'title' => ['sk' => 'Ďakujeme za vašu podporu!', 'en' => 'Thank You for Your Support!', 'cs' => 'Děkujeme za vaši podporu!'],
                'description' => ['sk' => 'Každé 2% pomáhajú. Vďaka vám môžeme ďalej rozvíjať parkour, street workout a calisthenics komunitu na Slovensku.', 'en' => 'Every 2% helps. Thanks to you, we can continue to develop the parkour, street workout and calisthenics community in Slovakia.', 'cs' => 'Každé 2% pomáhají. Díky vám můžeme dál rozvíjet parkour, street workout a calisthenics komunitu na Slovensku.'],
                'primary_button_text' => ['sk' => 'Stiahnuť tlačivo', 'en' => 'Download Form', 'cs' => 'Stáhnout formulář'],
                'primary_button_link_type' => 'custom',
                'primary_button_link_url' => ['sk' => '#', 'en' => '#', 'cs' => '#'],
                'secondary_button_text' => ['sk' => 'Podporte nás', 'en' => 'Support Us', 'cs' => 'Podpořte nás'],
                'secondary_button_link_type' => 'page',
                'secondary_button_link_model_id' => self::pageId('support'),
            ]),
        ];
    }

    /**
     * @return list<string>
     */
    private static function ensureTaxFaqs(): array
    {
        $maxSort = Faq::query()->max('sort_order') ?? 0;
        $generalCategoryId = FaqCategory::query()
            ->whereRaw("title->>'sk' = ?", ['Všeobecné'])
            ->value('id')
            ?? FaqCategory::query()->first()?->id
            ?? FaqCategory::query()->create([
                'title' => ['sk' => 'Všeobecné', 'en' => 'General', 'cs' => 'Obecné'],
                'sort_order' => 1,
            ])->id;

        $questions = [
            [
                'question' => ['sk' => 'Koľko ma to bude stáť?', 'en' => 'How much will it cost me?', 'cs' => 'Kolik mě to bude stát?'],
                'answer' => ['sk' => 'Nič. Tieto 2% by ste aj tak zaplatili štátu ako daň. Rozhodujete len o tom, kam pôjdu.', 'en' => 'Nothing. You would pay these 2% to the state as tax anyway. You only decide where they go.', 'cs' => 'Nic. Těchto 2% byste stejně zaplatili státu jako daň. Rozhodujete jen o tom, kam půjdou.'],
            ],
            [
                'question' => ['sk' => 'Do kedy musím podať vyhlásenie?', 'en' => 'By when do I need to submit the declaration?', 'cs' => 'Do kdy musím podat prohlášení?'],
                'answer' => ['sk' => 'Zamestnanci do 30. apríla, SZČO a firmy do 31. marca (alebo v predĺženej lehote).', 'en' => 'Employees by April 30, self-employed and companies by March 31 (or extended deadline).', 'cs' => 'Zaměstnanci do 30. dubna, OSVČ a firmy do 31. března (nebo v prodloužené lhůtě).'],
            ],
            [
                'question' => ['sk' => 'Ako zistím, či boli moje 2% poukázané?', 'en' => 'How do I know if my 2% was allocated?', 'cs' => 'Jak zjistím, zda byly moje 2% poukázány?'],
                'answer' => ['sk' => 'Daňový úrad vás informuje, ak o to požiadate v tlačive. My informácie o darcoch nedostávame.', 'en' => 'The tax office will inform you if you request it in the form. We do not receive information about donors.', 'cs' => 'Finanční úřad vás informuje, pokud o to požádáte ve formuláři. My informace o dárcích nedostáváme.'],
            ],
            [
                'question' => ['sk' => 'Môžem darovať aj viac ako 2%?', 'en' => 'Can I donate more than 2%?', 'cs' => 'Mohu darovat i více než 2%?'],
                'answer' => ['sk' => 'Áno, môžete nás podporiť aj priamym finančným darom na náš účet. Navštívte stránku Podporte nás.', 'en' => 'Yes, you can also support us with a direct financial donation to our account. Visit the Support Us page.', 'cs' => 'Ano, můžete nás podpořit i přímým finančním darem na náš účet. Navštivte stránku Podpořte nás.'],
            ],
        ];

        $ids = [];
        foreach ($questions as $index => $q) {
            $faq = Faq::query()->firstOrCreate(
                ['question->sk' => $q['question']['sk']],
                [
                    'question' => $q['question'],
                    'answer' => $q['answer'],
                    'faq_category_id' => $generalCategoryId,
                    'is_published' => true,
                    'sort_order' => $maxSort + $index + 1,
                ],
            );
            $ids[] = $faq->id;
        }

        return $ids;
    }

    private static function servicesContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'Vystúpenia, Workshopy & Prednášky', 'en' => 'Performances, Workshops & Lectures', 'cs' => 'Vystoupení, Workshopy & Přednášky'],
                'subtitle' => ['sk' => 'Prinášame akrobatické umenie, inšpiratívne prednášky a praktické workshopy pre vaše podujatia, školy a fitness centrá.', 'en' => 'We bring acrobatic artistry, inspirational lectures and practical workshops to your events, schools and fitness centers.', 'cs' => 'Přinášíme akrobatické umění, inspirativní přednášky a praktické workshopy pro vaše akce, školy a fitness centra.'],
                'badge' => ['sk' => 'SLUŽBY PRE FIRMY & EVENTY', 'en' => 'SERVICES FOR COMPANIES & EVENTS', 'cs' => 'SLUŽBY PRO FIRMY & EVENTY'],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'ČO PONÚKAME', 'en' => 'WHAT WE OFFER', 'cs' => 'CO NABÍZÍME'],
                'title' => ['sk' => 'Naše služby', 'en' => 'Our Services', 'cs' => 'Naše služby'],
                'subtitle' => ['sk' => 'Vyberajte z troch hlavných kategórií služieb, ktoré prispôsobíme vašim potrebám.', 'en' => 'Choose from three main categories of services that we customize to your needs.', 'cs' => 'Vybírejte ze tří hlavních kategorií služeb, které přizpůsobíme vašim potřebám.'],
                'cards' => [
                    [
                        'image' => self::media('services-performance'),
                        'icon' => 'heroicon-o-sparkles',
                        'border_color' => '#FF2D2D40',
                        'accent_color' => '#FF2D2D',
                        'card_link_type' => 'page',
                        'card_link_model_id' => self::pageId('performances'),
                        'title' => ['sk' => 'Vystúpenia', 'en' => 'Performances', 'cs' => 'Vystoupení'],
                        'card_subtitle' => ['sk' => 'Akrobatické umenie pre divákov', 'en' => 'Acrobatic art for audiences', 'cs' => 'Akrobatické umění pro diváky'],
                        'description' => ['sk' => 'Dynamické akrobatické show pre firemné eventy, festivaly, otvorenia a špeciálne príležitosti. Kombinujeme parkour, freerunning a akrobaciu do nezabudnuteľného vizuálneho zážitku.', 'en' => 'Dynamic acrobatic shows for corporate events, festivals, openings and special occasions. We combine parkour, freerunning and acrobatics into an unforgettable visual experience.', 'cs' => 'Dynamické akrobatické show pro firemní akce, festivaly, otevření a speciální příležitosti. Kombinujeme parkour, freerunning a akrobacii do nezapomenutelného vizuálního zážitku.'],
                        'features' => [
                            ['sk' => 'Firemné eventy a galavečery', 'en' => 'Corporate events and galas', 'cs' => 'Firemní akce a galavečery'],
                            ['sk' => 'Festivaly a open-air podujatia', 'en' => 'Festivals and open-air events', 'cs' => 'Festivaly a open-air akce'],
                            ['sk' => 'TV show a videoklipy', 'en' => 'TV shows and music videos', 'cs' => 'TV show a videoklipy'],
                            ['sk' => 'Otvorenia obchodov a promo akcie', 'en' => 'Store openings and promo events', 'cs' => 'Otevření obchodů a promo akce'],
                        ],
                    ],
                    [
                        'image' => self::media('services-lecture'),
                        'icon' => 'heroicon-o-academic-cap',
                        'border_color' => '#3B82F640',
                        'accent_color' => '#3B82F6',
                        'card_link_type' => 'page',
                        'card_link_model_id' => self::pageId('lectures'),
                        'title' => ['sk' => 'Prednášky', 'en' => 'Lectures', 'cs' => 'Přednášky'],
                        'card_subtitle' => ['sk' => 'Inšpirácia pre školy a organizácie', 'en' => 'Inspiration for schools and organizations', 'cs' => 'Inspirace pro školy a organizace'],
                        'description' => ['sk' => 'Motivačné prednášky o správnom nastavení mysle, hodnotových rebríčkoch a výhodách cvičenia. Učíme mladých ľudí trpezlivosti, tvrdej drine a vytrvalosti cez náš príbeh.', 'en' => 'Motivational lectures about the right mindset, values and benefits of exercise. We teach young people patience, hard work and perseverance through our story.', 'cs' => 'Motivační přednášky o správném nastavení mysli, hodnotových žebříčcích a výhodách cvičení. Učíme mladé lidi trpělivosti, tvrdé dřině a vytrvalosti přes náš příběh.'],
                        'features' => [
                            ['sk' => 'Základné a stredné školy', 'en' => 'Primary and secondary schools', 'cs' => 'Základní a střední školy'],
                            ['sk' => 'Firemné tímové akcie', 'en' => 'Corporate team events', 'cs' => 'Firemní týmové akce'],
                            ['sk' => 'Konferencie a semináre', 'en' => 'Conferences and seminars', 'cs' => 'Konference a semináře'],
                            ['sk' => 'Motivačné programy', 'en' => 'Motivational programs', 'cs' => 'Motivační programy'],
                        ],
                    ],
                    [
                        'image' => self::media('services-workshop'),
                        'icon' => 'heroicon-o-fire',
                        'border_color' => '#22C55E40',
                        'accent_color' => '#22C55E',
                        'card_link_type' => 'page',
                        'card_link_model_id' => self::pageId('workshops'),
                        'title' => ['sk' => 'Workshopy', 'en' => 'Workshops', 'cs' => 'Workshopy'],
                        'card_subtitle' => ['sk' => 'Praktické kurzy pre všetkých', 'en' => 'Practical courses for everyone', 'cs' => 'Praktické kurzy pro všechny'],
                        'description' => ['sk' => 'Workshopy pre fitness centrá, trénerov a podujatia. Učíme základné aj pokročilé prvky — od bezpečného pádu až po kurz stojky.', 'en' => 'Workshops for fitness centers, coaches and events. We teach basic and advanced elements — from safe falling to handstand courses.', 'cs' => 'Workshopy pro fitness centra, trenéry a akce. Učíme základní i pokročilé prvky — od bezpečného pádu až po kurz stojky.'],
                        'features' => [
                            ['sk' => 'Fitness centrá a gymy', 'en' => 'Fitness centers and gyms', 'cs' => 'Fitness centra a gymy'],
                            ['sk' => 'Školské telocvične', 'en' => 'School gymnasiums', 'cs' => 'Školní tělocvičny'],
                            ['sk' => 'Outdoorové podujatia', 'en' => 'Outdoor events', 'cs' => 'Outdoorové akce'],
                            ['sk' => 'Individuálne kurzy', 'en' => 'Individual courses', 'cs' => 'Individuální kurzy'],
                        ],
                    ],
                ],
            ]),
            self::brick('numbered-steps', [
                'label' => ['sk' => 'PROCES', 'en' => 'PROCESS', 'cs' => 'PROCES'],
                'title' => ['sk' => 'Ako spolupracujeme', 'en' => 'How We Collaborate', 'cs' => 'Jak spolupracujeme'],
                'subtitle' => ['sk' => 'Od prvého kontaktu po úspešnú realizáciu — jednoduchý a transparentný proces.', 'en' => 'From first contact to successful execution — a simple and transparent process.', 'cs' => 'Od prvního kontaktu po úspěšnou realizaci — jednoduchý a transparentní proces.'],
                'steps' => [
                    ['title' => ['sk' => 'Kontakt', 'en' => 'Contact', 'cs' => 'Kontakt'], 'description' => ['sk' => 'Napíšte nám cez kontaktný formulár alebo email. Popíšte typ podujatia, dátum a vaše predstavy.', 'en' => 'Write to us via the contact form or email. Describe the type of event, date and your ideas.', 'cs' => 'Napište nám přes kontaktní formulář nebo email. Popište typ akce, datum a vaše představy.']],
                    ['title' => ['sk' => 'Konzultácia', 'en' => 'Consultation', 'cs' => 'Konzultace'], 'description' => ['sk' => 'Preberieme detaily, vaše požiadavky a navrhneme riešenie šité na mieru vášmu eventu.', 'en' => 'We will discuss details, your requirements and propose a tailor-made solution for your event.', 'cs' => 'Probereme detaily, vaše požadavky a navrhneme řešení šité na míru vašemu eventu.']],
                    ['title' => ['sk' => 'Príprava', 'en' => 'Preparation', 'cs' => 'Příprava'], 'description' => ['sk' => 'Pripravíme program, nacvičíme choreografiu a doladíme všetky detaily pred vaším podujatím.', 'en' => 'We will prepare the program, rehearse the choreography and finalize all details before your event.', 'cs' => 'Připravíme program, nacvičíme choreografii a doladíme všechny detaily před vaší akcí.']],
                    ['title' => ['sk' => 'Realizácia', 'en' => 'Execution', 'cs' => 'Realizace'], 'description' => ['sk' => 'Dodáme nezabudnuteľný zážitok pre vás a vašich hostí. Profesionálne, spoľahlivo a s energiou.', 'en' => 'We deliver an unforgettable experience for you and your guests. Professionally, reliably and with energy.', 'cs' => 'Dodáme nezapomenutelný zážitek pro vás a vaše hosty. Profesionálně, spolehlivě a s energií.']],
                ],
            ]),
            self::brick('events-showcase', [
                'label' => ['sk' => 'PORTFÓLIO', 'en' => 'PORTFOLIO', 'cs' => 'PORTFOLIO'],
                'title' => ['sk' => 'Kde sme vystupovali', 'en' => 'Where We Performed', 'cs' => 'Kde jsme vystupovali'],
                'view_all_text' => ['sk' => 'Všetky podujatia', 'en' => 'All Events', 'cs' => 'Všechny akce'],
                'view_all_url' => '/eventy',
                'mode' => 'random',
                'count' => 3,
            ]),
            self::brick('contact-inquiry', [
                'label' => ['sk' => 'KONTAKT', 'en' => 'CONTACT', 'cs' => 'KONTAKT'],
                'title' => ['sk' => "Máte záujem\no spoluprácu?", 'en' => "Interested in\na collaboration?", 'cs' => "Máte zájem\no spolupráci?"],
                'description' => ['sk' => 'Vyplňte formulár a my sa vám ozveme do 24 hodín. Radi vám pripravíme ponuku na mieru.', 'en' => 'Fill out the form and we will get back to you within 24 hours. We will be happy to prepare a custom offer.', 'cs' => 'Vyplňte formulář a my se vám ozveme do 24 hodin. Rádi vám připravíme nabídku na míru.'],
                'contact_email' => 'info@bczclub.sk',
                'contact_phone' => '+421 900 123 456',
            ]),
        ];
    }

    private static function performancesContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'badge' => ['sk' => 'AKROBATICKÉ SHOW', 'en' => 'ACROBATIC SHOW', 'cs' => 'AKROBATICKÁ SHOW'],
                'title' => ['sk' => 'Akrobatické vystúpenia', 'en' => 'Acrobatic Performances', 'cs' => 'Akrobatická vystoupení'],
                'subtitle' => ['sk' => 'Prinášame spektakulárne akrobatické show na vaše eventy, festivaly a firemné podujatia. Dynamické vystúpenia, ktoré zanechajú nezabudnuteľný dojem.', 'en' => 'We bring spectacular acrobatic shows to your events, festivals and corporate events. Dynamic performances that leave an unforgettable impression.', 'cs' => 'Přinášíme spektakulární akrobatické show na vaše eventy, festivaly a firemní akce. Dynamická vystoupení, která zanechají nezapomenutelný dojem.'],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'PONUKA', 'en' => 'OFFER', 'cs' => 'NABÍDKA'],
                'title' => ['sk' => 'Typy vystúpení', 'en' => 'Types of Performances', 'cs' => 'Typy vystoupení'],
                'subtitle' => ['sk' => 'Prispôsobíme vystúpenie presne podľa vašich potrieb a typu podujatia.', 'en' => 'We tailor the performance exactly to your needs and type of event.', 'cs' => 'Přizpůsobíme vystoupení přesně podle vašich potřeb a typu akce.'],
                'cards' => [
                    ['icon' => 'heroicon-o-sparkles', 'border_color' => '#FF2D2D40', 'accent_color' => '#FF2D2D', 'title' => ['sk' => 'Festival Show', 'en' => 'Festival Show', 'cs' => 'Festival Show'], 'description' => ['sk' => 'Veľké pódiové vystúpenia pre festivaly a open-air eventy. Energické show s hudbou a svetlami pre tisícky divákov.', 'en' => 'Large stage performances for festivals and open-air events. Energetic shows with music and lights for thousands of spectators.', 'cs' => 'Velká pódiová vystoupení pro festivaly a open-air eventy. Energické show s hudbou a světly pro tisíce diváků.']],
                    ['icon' => 'heroicon-o-building-office-2', 'border_color' => '#FF2D2D40', 'accent_color' => '#FF2D2D', 'title' => ['sk' => 'Firemné Eventy', 'en' => 'Corporate Events', 'cs' => 'Firemní Eventy'], 'description' => ['sk' => 'Profesionálne corporate show pre firemné akcie, teambuildingy a konferencie. Elegantné a prispôsobené vašej značke.', 'en' => 'Professional corporate shows for company events, team buildings and conferences. Elegant and customized to your brand.', 'cs' => 'Profesionální corporate show pro firemní akce, teambuildingy a konference. Elegantní a přizpůsobené vaší značce.']],
                    ['icon' => 'heroicon-o-scissors', 'border_color' => '#FF2D2D40', 'accent_color' => '#FF2D2D', 'title' => ['sk' => 'Otvorenia & Promá', 'en' => 'Openings & Promos', 'cs' => 'Otevření & Promo'], 'description' => ['sk' => 'Efektné slávnostné otvorenia obchodov, nákupných centier a eventov. Nezabudnuteľný prvý dojem pre vašich zákazníkov.', 'en' => 'Impressive grand openings of stores, shopping centers and events. An unforgettable first impression for your customers.', 'cs' => 'Efektní slavnostní otevření obchodů, nákupních center a eventů. Nezapomenutelný první dojem pro vaše zákazníky.']],
                    ['icon' => 'heroicon-o-gift', 'border_color' => '#FF2D2D40', 'accent_color' => '#FF2D2D', 'title' => ['sk' => 'Súkromné Podujatia', 'en' => 'Private Events', 'cs' => 'Soukromé Akce'], 'description' => ['sk' => 'Unikátne vystúpenia pre narodeniny, svadby, rozlúčky a ďalšie súkromné oslavy. Osobný prístup a show na mieru.', 'en' => 'Unique performances for birthdays, weddings, farewells and other private celebrations. Personal approach and custom shows.', 'cs' => 'Unikátní vystoupení pro narozeniny, svatby, rozloučky a další soukromé oslavy. Osobní přístup a show na míru.']],
                ],
            ]),
            self::brick('numbered-steps', [
                'label' => ['sk' => 'PROCES', 'en' => 'PROCESS', 'cs' => 'PROCES'],
                'title' => ['sk' => 'Ako to funguje', 'en' => 'How It Works', 'cs' => 'Jak to funguje'],
                'steps' => [
                    ['title' => ['sk' => 'Kontakt & Konzultácia', 'en' => 'Contact & Consultation', 'cs' => 'Kontakt & Konzultace'], 'description' => ['sk' => 'Napíšte nám. Popíšte typ podujatia, dátum a vaše predstavy. Preberieme detaily a navrhneme riešenie.', 'en' => 'Write to us. Describe the type of event, date and your ideas. We will discuss details and propose a solution.', 'cs' => 'Napište nám. Popište typ akce, datum a vaše představy. Probereme detaily a navrhneme řešení.']],
                    ['title' => ['sk' => 'Príprava & Choreografia', 'en' => 'Preparation & Choreography', 'cs' => 'Příprava & Choreografie'], 'description' => ['sk' => 'Pripravíme program prispôsobený vášmu eventu — od 5-minútových showtime aktov po 30-minútové programy s hudbou a svetlami.', 'en' => 'We prepare a program tailored to your event — from 5-minute showtime acts to 30-minute programs with music and lights.', 'cs' => 'Připravíme program přizpůsobený vaší akci — od 5minutových showtime aktů po 30minutové programy s hudbou a světly.']],
                    ['title' => ['sk' => 'Realizácia', 'en' => 'Execution', 'cs' => 'Realizace'], 'description' => ['sk' => 'Dodáme nezabudnuteľný zážitok. Profesionálne, spoľahlivo a s energiou, ktorá strhne každé publikum.', 'en' => 'We deliver an unforgettable experience. Professionally, reliably and with energy that captivates every audience.', 'cs' => 'Dodáme nezapomenutelný zážitek. Profesionálně, spolehlivě a s energií, která strhne každé publikum.']],
                    ['title' => ['sk' => 'Spätná väzba', 'en' => 'Feedback', 'cs' => 'Zpětná vazba'], 'description' => ['sk' => 'Po akcii vám zašleme fotky a video z vystúpenia. Radi sa vrátime aj na ďalšie podujatie.', 'en' => 'After the event we will send you photos and video from the performance. We are happy to return for the next event.', 'cs' => 'Po akci vám zašleme fotky a video z vystoupení. Rádi se vrátíme i na další akci.']],
                ],
            ]),
            self::brick('events-showcase', [
                'label' => ['sk' => 'PORTFÓLIO', 'en' => 'PORTFOLIO', 'cs' => 'PORTFOLIO'],
                'title' => ['sk' => 'Kde sme vystupovali', 'en' => 'Where We Performed', 'cs' => 'Kde jsme vystupovali'],
                'view_all_text' => ['sk' => 'Všetky podujatia', 'en' => 'All Events', 'cs' => 'Všechny akce'],
                'view_all_url' => '/eventy',
                'mode' => 'random',
                'count' => 3,
            ]),
            self::brick('contact-inquiry', [
                'label' => ['sk' => 'BOOKING', 'en' => 'BOOKING', 'cs' => 'BOOKING'],
                'title' => ['sk' => "Zarezervujte si\nvystúpenie", 'en' => "Book a\nperformance", 'cs' => "Zarezervujte si\nvystoupení"],
                'description' => ['sk' => 'Chcete oživiť váš event akrobatickou show? Vyplňte formulár a my sa vám ozveme do 24 hodín.', 'en' => 'Want to liven up your event with an acrobatic show? Fill out the form and we will get back to you within 24 hours.', 'cs' => 'Chcete oživit váš event akrobatickou show? Vyplňte formulář a my se vám ozveme do 24 hodin.'],
                'contact_email' => 'info@bczclub.sk',
                'contact_phone' => '+421 900 123 456',
            ]),
        ];
    }

    private static function lecturesContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'badge' => ['sk' => 'PREDNÁŠKY', 'en' => 'LECTURES', 'cs' => 'PŘEDNÁŠKY'],
                'title' => ['sk' => 'Inšpiratívne prednášky', 'en' => 'Inspirational Lectures', 'cs' => 'Inspirativní přednášky'],
                'subtitle' => ['sk' => 'Motivačné prednášky pre školy, firmy a organizácie. Inšpirujeme mladých ľudí príbehom o disciplíne, vytrvalosti a sile pohybu.', 'en' => 'Motivational lectures for schools, companies and organizations. We inspire young people through a story about discipline, perseverance and the power of movement.', 'cs' => 'Motivační přednášky pro školy, firmy a organizace. Inspirujeme mladé lidi příběhem o disciplíně, vytrvalosti a síle pohybu.'],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'O ČOM HOVORÍME', 'en' => 'WHAT WE TALK ABOUT', 'cs' => 'O ČEM MLUVÍME'],
                'title' => ['sk' => 'Témy prednášok', 'en' => 'Lecture Topics', 'cs' => 'Témata přednášek'],
                'subtitle' => ['sk' => 'Každá prednáška je príbeh o disciplíne, vytrvalosti a sile pohybu.', 'en' => 'Every lecture is a story about discipline, perseverance and the power of movement.', 'cs' => 'Každá přednáška je příběh o disciplíně, vytrvalosti a síle pohybu.'],
                'cards' => [
                    ['icon' => 'heroicon-o-light-bulb', 'border_color' => '#3B82F640', 'accent_color' => '#3B82F6', 'title' => ['sk' => 'Správne Nastavenie Mysle', 'en' => 'The Right Mindset', 'cs' => 'Správné Nastavení Mysli'], 'description' => ['sk' => 'Growth mindset a pozitívne myslenie. Ako zmeniť pohľad na prekážky a premeniť ich na príležitosti.', 'en' => 'Growth mindset and positive thinking. How to change your view of obstacles and turn them into opportunities.', 'cs' => 'Growth mindset a pozitivní myšlení. Jak změnit pohled na překážky a proměnit je v příležitosti.']],
                    ['icon' => 'heroicon-o-bolt', 'border_color' => '#3B82F640', 'accent_color' => '#3B82F6', 'title' => ['sk' => 'Hodnota Disciplíny', 'en' => 'The Value of Discipline', 'cs' => 'Hodnota Disciplíny'], 'description' => ['sk' => 'Prečo je disciplína základom úspechu. Denné návyky a rutiny, ktoré formujú charakter a budujú odolnosť.', 'en' => 'Why discipline is the foundation of success. Daily habits and routines that shape character and build resilience.', 'cs' => 'Proč je disciplína základem úspěchu. Denní návyky a rutiny, které formují charakter a budují odolnost.']],
                    ['icon' => 'heroicon-o-heart', 'border_color' => '#3B82F640', 'accent_color' => '#3B82F6', 'title' => ['sk' => 'Sila Pohybu', 'en' => 'The Power of Movement', 'cs' => 'Síla Pohybu'], 'description' => ['sk' => 'Fyzická aktivita ako nástroj osobného rastu. Benefity cvičenia pre telo aj myseľ.', 'en' => 'Physical activity as a tool for personal growth. Benefits of exercise for body and mind.', 'cs' => 'Fyzická aktivita jako nástroj osobního růstu. Benefity cvičení pro tělo i mysl.']],
                    ['icon' => 'heroicon-o-star', 'border_color' => '#3B82F640', 'accent_color' => '#3B82F6', 'title' => ['sk' => 'Od Sna k Realite', 'en' => 'From Dream to Reality', 'cs' => 'Od Snu k Realitě'], 'description' => ['sk' => 'Ako premeniť víziu na skutočnosť. Príbeh BCZ Clubu od garážových tréningov po celoslovenské vystúpenia.', 'en' => 'How to turn a vision into reality. The story of BCZ Club from garage training to nationwide performances.', 'cs' => 'Jak proměnit vizi ve skutečnost. Příběh BCZ Clubu od garážových tréninků po celoslovenská vystoupení.']],
                ],
            ]),
            self::brick('numbered-steps', [
                'label' => ['sk' => 'PRIEBEH', 'en' => 'PROCESS', 'cs' => 'PRŮBĚH'],
                'title' => ['sk' => 'Ako prednáška prebieha', 'en' => 'How the Lecture Works', 'cs' => 'Jak přednáška probíhá'],
                'steps' => [
                    ['title' => ['sk' => 'Dohodnutie témy', 'en' => 'Topic Agreement', 'cs' => 'Dohodnutí tématu'], 'description' => ['sk' => 'Spoločne vyberieme tému a formát prednášky podľa vašej cieľovej skupiny.', 'en' => 'Together we select the topic and format based on your target audience.', 'cs' => 'Společně vybereme téma a formát přednášky podle vaší cílové skupiny.']],
                    ['title' => ['sk' => 'Prednáška', 'en' => 'The Lecture', 'cs' => 'Přednáška'], 'description' => ['sk' => '45-90 minút inšpiratívneho obsahu s interaktívnymi prvkami a praktickými ukážkami.', 'en' => '45-90 minutes of inspirational content with interactive elements and practical demonstrations.', 'cs' => '45-90 minut inspirativního obsahu s interaktivními prvky a praktickými ukázkami.']],
                    ['title' => ['sk' => 'Q&A a diskusia', 'en' => 'Q&A & Discussion', 'cs' => 'Q&A a diskuse'], 'description' => ['sk' => 'Otvorený priestor pre otázky, zdieľanie a osobný kontakt s prednášajúcim.', 'en' => 'Open space for questions, sharing and personal contact with the speaker.', 'cs' => 'Otevřený prostor pro otázky, sdílení a osobní kontakt s přednášejícím.']],
                    ['title' => ['sk' => 'Ukážka', 'en' => 'Demonstration', 'cs' => 'Ukázka'], 'description' => ['sk' => 'Na záver živá akrobatická ukážka, ktorá inšpiruje a motivuje k pohybu.', 'en' => 'At the end, a live acrobatic demonstration that inspires and motivates movement.', 'cs' => 'Na závěr živá akrobatická ukázka, která inspiruje a motivuje k pohybu.']],
                ],
            ]),
            self::brick('events-showcase', [
                'label' => ['sk' => 'REFERENCIE', 'en' => 'REFERENCES', 'cs' => 'REFERENCE'],
                'title' => ['sk' => 'Kde sme prednášali', 'en' => 'Where We Lectured', 'cs' => 'Kde jsme přednášeli'],
                'view_all_text' => ['sk' => 'Všetky podujatia', 'en' => 'All Events', 'cs' => 'Všechny akce'],
                'view_all_url' => '/eventy',
                'mode' => 'random',
                'count' => 3,
            ]),
            self::brick('contact-inquiry', [
                'label' => ['sk' => 'KONTAKT', 'en' => 'CONTACT', 'cs' => 'KONTAKT'],
                'title' => ['sk' => "Máte záujem\no prednášku?", 'en' => "Interested in\na lecture?", 'cs' => "Máte zájem\no přednášku?"],
                'description' => ['sk' => 'Vyplňte formulár a my sa vám ozveme do 24 hodín.', 'en' => 'Fill out the form and we will get back to you within 24 hours.', 'cs' => 'Vyplňte formulář a my se vám ozveme do 24 hodin.'],
                'contact_email' => 'info@bczclub.sk',
                'contact_phone' => '+421 900 123 456',
            ]),
        ];
    }

    private static function workshopsContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'badge' => ['sk' => 'WORKSHOPY', 'en' => 'WORKSHOPS', 'cs' => 'WORKSHOPY'],
                'title' => ['sk' => 'Praktické workshopy', 'en' => 'Practical Workshops', 'cs' => 'Praktické workshopy'],
                'subtitle' => ['sk' => 'Učíme základné aj pokročilé prvky kalisteniky — od bezpečného pádu až po kurz stojky. Prispôsobíme sa vašej úrovni.', 'en' => 'We teach basic and advanced calisthenics elements — from safe falling to handstand courses. We adapt to your level.', 'cs' => 'Učíme základní i pokročilé prvky kalisteniky — od bezpečného pádu až po kurz stojky. Přizpůsobíme se vaší úrovni.'],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'PONUKA', 'en' => 'OFFER', 'cs' => 'NABÍDKA'],
                'title' => ['sk' => 'Typy workshopov', 'en' => 'Workshop Types', 'cs' => 'Typy workshopů'],
                'subtitle' => ['sk' => 'Každý workshop prispôsobíme úrovni a potrebám účastníkov.', 'en' => 'Every workshop is adapted to the level and needs of participants.', 'cs' => 'Každý workshop přizpůsobíme úrovni a potřebám účastníků.'],
                'cards' => [
                    ['icon' => 'heroicon-o-hand-raised', 'border_color' => '#22C55E40', 'accent_color' => '#22C55E', 'title' => ['sk' => 'Kurz Stojky', 'en' => 'Handstand Course', 'cs' => 'Kurz Stojky'], 'description' => ['sk' => 'Od základnej prípravy až po voľnú stojku. Správna technika, posilnenie jadra a progresie.', 'en' => 'From basic preparation to freestanding handstand. Proper technique, core strengthening and progressions.', 'cs' => 'Od základní přípravy až po volnou stojku. Správná technika, posílení jádra a progrese.']],
                    ['icon' => 'heroicon-o-fire', 'border_color' => '#22C55E40', 'accent_color' => '#22C55E', 'title' => ['sk' => 'Základy Kalisteniky', 'en' => 'Calisthenics Basics', 'cs' => 'Základy Kalisteniky'], 'description' => ['sk' => 'Zhyby, kliky, dipy a ich variácie. Správna forma a zostava tréningového plánu.', 'en' => 'Pull-ups, push-ups, dips and their variations. Proper form and training plan structure.', 'cs' => 'Shyby, kliky, dipy a jejich variace. Správná forma a sestava tréninkového plánu.']],
                    ['icon' => 'heroicon-o-shield-check', 'border_color' => '#22C55E40', 'accent_color' => '#22C55E', 'title' => ['sk' => 'Bezpečný Pád', 'en' => 'Safe Falling', 'cs' => 'Bezpečný Pád'], 'description' => ['sk' => 'Techniky bezpečného pádu a základov parkour rolľov. Nevyhnutné pre každého.', 'en' => 'Safe falling techniques and parkour roll basics. Essential for everyone.', 'cs' => 'Techniky bezpečného pádu a základů parkour rollů. Nezbytné pro každého.']],
                    ['icon' => 'heroicon-o-arrow-trending-up', 'border_color' => '#22C55E40', 'accent_color' => '#22C55E', 'title' => ['sk' => 'Pokročilé Prvky', 'en' => 'Advanced Elements', 'cs' => 'Pokročilé Prvky'], 'description' => ['sk' => 'Muscle-up, front lever, planche a ďalšie. Pre tých, čo už ovládajú základy.', 'en' => 'Muscle-up, front lever, planche and more. For those who already master the basics.', 'cs' => 'Muscle-up, front lever, planche a další. Pro ty, co už ovládají základy.']],
                ],
            ]),
            self::brick('numbered-steps', [
                'label' => ['sk' => 'PRIEBEH', 'en' => 'PROCESS', 'cs' => 'PRŮBĚH'],
                'title' => ['sk' => 'Ako workshop prebieha', 'en' => 'How the Workshop Works', 'cs' => 'Jak workshop probíhá'],
                'steps' => [
                    ['title' => ['sk' => 'Úvod & Rozohriatie', 'en' => 'Introduction & Warm-up', 'cs' => 'Úvod & Rozcvičení'], 'description' => ['sk' => 'Zoznámenie s účastníkmi, stanovenie cieľov a dôkladné rozohriatie tela.', 'en' => 'Getting to know participants, setting goals and thorough body warm-up.', 'cs' => 'Seznámení s účastníky, stanovení cílů a důkladné rozcvičení těla.']],
                    ['title' => ['sk' => 'Technika & Progresie', 'en' => 'Technique & Progressions', 'cs' => 'Technika & Progrese'], 'description' => ['sk' => 'Detailný rozklad cvikov, správna forma a individuálne progresie.', 'en' => 'Detailed exercise breakdown, proper form and individual progressions.', 'cs' => 'Detailní rozklad cviků, správná forma a individuální progrese.']],
                    ['title' => ['sk' => 'Prax & Feedback', 'en' => 'Practice & Feedback', 'cs' => 'Praxe & Feedback'], 'description' => ['sk' => 'Praktické precvičovanie s osobným feedbackom trénera.', 'en' => 'Practical practice with personal feedback from the coach.', 'cs' => 'Praktické procvičování s osobním feedbackem trenéra.']],
                    ['title' => ['sk' => 'Plán & Materiály', 'en' => 'Plan & Materials', 'cs' => 'Plán & Materiály'], 'description' => ['sk' => 'Na záver dostanete tréningový plán a materiály na ďalšie cvičenie.', 'en' => 'At the end you will receive a training plan and materials for further practice.', 'cs' => 'Na závěr dostanete tréninkový plán a materiály na další cvičení.']],
                ],
            ]),
            self::brick('events-showcase', [
                'label' => ['sk' => 'REFERENCIE', 'en' => 'REFERENCES', 'cs' => 'REFERENCE'],
                'title' => ['sk' => 'Kde sme workshopovali', 'en' => 'Where We Workshopped', 'cs' => 'Kde jsme workshopovali'],
                'view_all_text' => ['sk' => 'Všetky podujatia', 'en' => 'All Events', 'cs' => 'Všechny akce'],
                'view_all_url' => '/eventy',
                'mode' => 'random',
                'count' => 3,
            ]),
            self::brick('contact-inquiry', [
                'label' => ['sk' => 'KONTAKT', 'en' => 'CONTACT', 'cs' => 'KONTAKT'],
                'title' => ['sk' => "Máte záujem\no workshop?", 'en' => "Interested in\na workshop?", 'cs' => "Máte zájem\no workshop?"],
                'description' => ['sk' => 'Vyplňte formulár a my sa vám ozveme do 24 hodín.', 'en' => 'Fill out the form and we will get back to you within 24 hours.', 'cs' => 'Vyplňte formulář a my se vám ozveme do 24 hodin.'],
                'contact_email' => 'info@bczclub.sk',
                'contact_phone' => '+421 900 123 456',
            ]),
        ];
    }

    private static function parkourContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Parkour & Freerunning', 'en' => 'Parkour & Freerunning', 'cs' => 'Parkour & Freerunning'],
                'subtitle' => ['sk' => 'Umenie pohybu. Sloboda bez hraníc.', 'en' => 'The art of movement. Freedom without limits.', 'cs' => 'Umění pohybu. Svoboda bez hranic.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Parkour je disciplína, ktorá mení spôsob, akým vnímaš svet okolo seba. Každá stena, zábradlie či lavička sa stáva príležitosťou. Každá prekážka výzvou, ktorú môžeš prekonať.</p><p>Vznikol vo Francúzsku v 80. rokoch a od vtedy sa rozšíril po celom svete. Nie je to len šport — je to filozofia efektívneho pohybu, kde sa učíš prekonávať fyzické aj mentálne bariéry.</p>', 'en' => '<p>Parkour is a discipline that changes the way you perceive the world around you. Every wall, railing or bench becomes an opportunity. Every obstacle a challenge you can overcome.</p><p>It originated in France in the 1980s and has since spread around the world. It\'s not just a sport — it\'s a philosophy of efficient movement where you learn to overcome both physical and mental barriers.</p>', 'cs' => '<p>Parkour je disciplína, která mění způsob, jakým vnímáš svět kolem sebe. Každá stěna, zábradlí či lavička se stává příležitostí. Každá překážka výzvou, kterou můžeš překonat.</p><p>Vznikl ve Francii v 80. letech a od té doby se rozšířil po celém světě. Není to jen sport — je to filozofie efektivního pohybu, kde se učíš překonávat fyzické i mentální bariéry.</p>'],
            ]),
            self::brick('quote', [
                'quote' => ['sk' => 'Byť silný, aby si bol užitočný.', 'en' => 'Be strong to be useful.', 'cs' => 'Být silný, abys byl užitečný.'],
                'attribution' => ['sk' => 'David Belle, zakladateľ Parkouru', 'en' => 'David Belle, founder of Parkour', 'cs' => 'David Belle, zakladatel Parkouru'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-globe-alt', 'title' => ['sk' => 'Bez pravidiel', 'en' => 'No Rules', 'cs' => 'Bez pravidel'], 'description' => ['sk' => 'Žiadne ihriská, žiadne vymedzené zóny. Celé mesto je tvoje ihrisko.', 'en' => 'No playgrounds, no designated zones. The entire city is your playground.', 'cs' => 'Žádná hřiště, žádné vymezené zóny. Celé město je tvoje hřiště.']],
                    ['icon' => 'heroicon-o-bolt', 'title' => ['sk' => 'Mentálna sila', 'en' => 'Mental Strength', 'cs' => 'Mentální síla'], 'description' => ['sk' => 'Prekonávaš nielen fyzické prekážky, ale aj strach. Učíš sa dôverovať svojmu telu.', 'en' => 'You overcome not only physical obstacles but also fear. You learn to trust your body.', 'cs' => 'Překonáváš nejen fyzické překážky, ale i strach. Učíš se důvěřovat svému tělu.']],
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'Fyzická kondícia', 'en' => 'Physical Fitness', 'cs' => 'Fyzická kondice'], 'description' => ['sk' => 'Komplexný tréning celého tela. Sila, vytrvalosť, flexibilita a koordinácia.', 'en' => 'Complete full-body training. Strength, endurance, flexibility and coordination.', 'cs' => 'Komplexní trénink celého těla. Síla, vytrvalost, flexibilita a koordinace.']],
                    ['icon' => 'heroicon-o-user-group', 'title' => ['sk' => 'Komunita', 'en' => 'Community', 'cs' => 'Komunita'], 'description' => ['sk' => 'Parkour spája ľudí z celého sveta. Zdieľaš progres, motivúješ sa navzájom.', 'en' => 'Parkour connects people from around the world. You share progress and motivate each other.', 'cs' => 'Parkour spojuje lidi z celého světa. Sdílíš progres, motivuješ se navzájem.']],
                ],
            ]),
            self::brick('skill-cards', [
                'levels' => [
                    [
                        'name' => ['sk' => 'ZÁKLADY', 'en' => 'BASICS', 'cs' => 'ZÁKLADY'],
                        'color' => '#22c55e',
                        'cards' => [
                            ['title' => ['sk' => 'Safety Roll', 'en' => 'Safety Roll', 'cs' => 'Safety Roll'], 'description' => ['sk' => 'Kotúľ — základ bezpečného dopadu.', 'en' => 'Roll — the foundation of safe landing.', 'cs' => 'Kotoul — základ bezpečného dopadu.']],
                            ['title' => ['sk' => 'Precision Jump', 'en' => 'Precision Jump', 'cs' => 'Precision Jump'], 'description' => ['sk' => 'Presný skok na cieľ.', 'en' => 'Precise jump to a target.', 'cs' => 'Přesný skok na cíl.']],
                            ['title' => ['sk' => 'Cat Leap', 'en' => 'Cat Leap', 'cs' => 'Cat Leap'], 'description' => ['sk' => 'Arm Jump — skok a zachytenie sa o hranu.', 'en' => 'Arm Jump — jump and catch onto an edge.', 'cs' => 'Arm Jump — skok a zachycení se o hranu.']],
                            ['title' => ['sk' => 'Balance', 'en' => 'Balance', 'cs' => 'Balance'], 'description' => ['sk' => 'Rovnováha — chôdza po úzkych plochách.', 'en' => 'Balance — walking on narrow surfaces.', 'cs' => 'Rovnováha — chůze po úzkých plochách.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'STREDNÉ (Vaults)', 'en' => 'INTERMEDIATE (Vaults)', 'cs' => 'STŘEDNÍ (Vaults)'],
                        'color' => '#3b82f6',
                        'cards' => [
                            ['title' => ['sk' => 'Speed Vault', 'en' => 'Speed Vault', 'cs' => 'Speed Vault'], 'description' => ['sk' => 'Rýchly prechod cez prekážku jednou rukou.', 'en' => 'Quick passage over an obstacle with one hand.', 'cs' => 'Rychlý přechod přes překážku jednou rukou.']],
                            ['title' => ['sk' => 'Kong Vault', 'en' => 'Kong Vault', 'cs' => 'Kong Vault'], 'description' => ['sk' => 'Preskok cez prekážku s oporou oboch rúk.', 'en' => 'Jump over an obstacle with both hands support.', 'cs' => 'Přeskok přes překážku s oporou obou rukou.']],
                            ['title' => ['sk' => 'Dash Vault', 'en' => 'Dash Vault', 'cs' => 'Dash Vault'], 'description' => ['sk' => 'Preskok nohami vpred cez prekážku.', 'en' => 'Feet-first jump over an obstacle.', 'cs' => 'Přeskok nohama vpřed přes překážku.']],
                            ['title' => ['sk' => 'Wall Run', 'en' => 'Wall Run', 'cs' => 'Wall Run'], 'description' => ['sk' => 'Beh po stene do výšky.', 'en' => 'Running up a wall.', 'cs' => 'Běh po stěně do výšky.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'POKROČILÉ (Freerunning & Flips)', 'en' => 'ADVANCED (Freerunning & Flips)', 'cs' => 'POKROČILÉ (Freerunning & Flips)'],
                        'color' => '#f59e0b',
                        'cards' => [
                            ['title' => ['sk' => 'Front Flip', 'en' => 'Front Flip', 'cs' => 'Front Flip'], 'description' => ['sk' => 'Salto vpred.', 'en' => 'Front somersault.', 'cs' => 'Salto vpřed.']],
                            ['title' => ['sk' => 'Backflip', 'en' => 'Backflip', 'cs' => 'Backflip'], 'description' => ['sk' => 'Salto vzad.', 'en' => 'Back somersault.', 'cs' => 'Salto vzad.']],
                            ['title' => ['sk' => 'Sideflip', 'en' => 'Sideflip', 'cs' => 'Sideflip'], 'description' => ['sk' => 'Bočné salto.', 'en' => 'Side somersault.', 'cs' => 'Boční salto.']],
                            ['title' => ['sk' => 'Webster', 'en' => 'Webster', 'cs' => 'Webster'], 'description' => ['sk' => 'Salto vpred z jednej nohy.', 'en' => 'Front flip from one leg.', 'cs' => 'Salto vpřed z jedné nohy.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'EXPERT', 'en' => 'EXPERT', 'cs' => 'EXPERT'],
                        'color' => '#ef4444',
                        'cards' => [
                            ['title' => ['sk' => 'Gainer', 'en' => 'Gainer', 'cs' => 'Gainer'], 'description' => ['sk' => 'Backflip z rozbehu s pohybom vpred.', 'en' => 'Backflip from a run with forward movement.', 'cs' => 'Backflip z rozběhu s pohybem vpřed.']],
                            ['title' => ['sk' => 'Double Flip', 'en' => 'Double Flip', 'cs' => 'Double Flip'], 'description' => ['sk' => 'Dvojité salto.', 'en' => 'Double somersault.', 'cs' => 'Dvojité salto.']],
                            ['title' => ['sk' => 'Cork', 'en' => 'Cork', 'cs' => 'Cork'], 'description' => ['sk' => 'Corkscrew — rotácia s twistom.', 'en' => 'Corkscrew — rotation with a twist.', 'cs' => 'Corkscrew — rotace s twistem.']],
                            ['title' => ['sk' => 'Wall Spin', 'en' => 'Wall Spin', 'cs' => 'Wall Spin'], 'description' => ['sk' => 'Rotácia o stenu.', 'en' => 'Spin off a wall.', 'cs' => 'Rotace o stěnu.']],
                        ],
                    ],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'ZAČNI S PARKOUROM', 'en' => 'START WITH PARKOUR', 'cs' => 'ZAČNI S PARKOUREM'],
                'description' => ['sk' => 'Naše tréningy sú vhodné pre všetky úrovne — od úplných začiatočníkov po pokročilých.', 'en' => 'Our trainings are suitable for all levels — from complete beginners to advanced.', 'cs' => 'Naše tréninky jsou vhodné pro všechny úrovně — od úplných začátečníků po pokročilé.'],
                'button_text' => ['sk' => 'Pozrieť tréningy', 'en' => 'View Trainings', 'cs' => 'Zobrazit tréninky'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('trainings'),
                'background_color' => '#dc2626',
            ]),
            self::brick('gallery', [
                'images' => [
                    ['image' => self::media('gallery-parkour-1')],
                    ['image' => self::media('gallery-parkour-2')],
                    ['image' => self::media('gallery-parkour-3')],
                    ['image' => self::media('gallery-parkour-4')],
                    ['image' => self::media('gallery-parkour-5')],
                    ['image' => self::media('gallery-parkour-6')],
                ],
            ]),
        ];
    }

    private static function streetWorkoutContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Street Workout & Kalistenika', 'en' => 'Street Workout & Calisthenics', 'cs' => 'Street Workout & Kalistenika'],
                'subtitle' => ['sk' => 'Ovládni svoje telo. Ovládni gravitáciu.', 'en' => 'Master your body. Master gravity.', 'cs' => 'Ovládni své tělo. Ovládni gravitaci.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Street workout, známy aj ako kalistenika, je forma silového tréningu využívajúca vlastnú váhu tela. Cvičíš na hrazdách, bradlách a iných zariadeniach — vonku, v parkoch, kdekoľvek.</p><p>Kombinuje silu, vytrvalosť a estetiku pohybu. Od základných cvikov ako zhyby a kliky, až po pokročilé prvky ako front lever, planche či muscle up.</p>', 'en' => '<p>Street workout, also known as calisthenics, is a form of strength training using your own body weight. You train on pull-up bars, dip bars and other equipment — outside, in parks, anywhere.</p><p>It combines strength, endurance and the aesthetics of movement. From basic exercises like pull-ups and push-ups to advanced elements like front lever, planche or muscle up.</p>', 'cs' => '<p>Street workout, známý také jako kalistenika, je forma silového tréninku využívající vlastní váhu těla. Cvičíš na hrazdách, bradlech a jiných zařízeních — venku, v parcích, kdekoli.</p><p>Kombinuje sílu, vytrvalost a estetiku pohybu. Od základních cviků jako shyby a kliky, až po pokročilé prvky jako front lever, planche či muscle up.</p>'],
            ]),
            self::brick('quote', [
                'quote' => ['sk' => 'Tvoje telo je tvojou posiľňovňou. Jediné čo potrebuješ, je vôľa začať.', 'en' => 'Your body is your gym. All you need is the will to start.', 'cs' => 'Tvoje tělo je tvou posilovnou. Jediné co potřebuješ, je vůle začít.'],
                'attribution' => ['sk' => '', 'en' => '', 'cs' => ''],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-bolt', 'title' => ['sk' => 'Čistá sila', 'en' => 'Pure Strength', 'cs' => 'Čistá síla'], 'description' => ['sk' => 'Vybuduješ funkčnú silu bez strojov a závaží. Tvoje telo je jediné náradie.', 'en' => 'Build functional strength without machines or weights. Your body is the only tool.', 'cs' => 'Vybuduješ funkční sílu bez strojů a závaží. Tvoje tělo je jediné nářadí.']],
                    ['icon' => 'heroicon-o-scale', 'title' => ['sk' => 'Rovnováha', 'en' => 'Balance', 'cs' => 'Rovnováha'], 'description' => ['sk' => 'Naučíš sa ovládať svoje telo v náročných pozíciách.', 'en' => 'Learn to control your body in challenging positions.', 'cs' => 'Naučíš se ovládat své tělo v náročných pozicích.']],
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'Vytrvalosť', 'en' => 'Endurance', 'cs' => 'Vytrvalost'], 'description' => ['sk' => 'High-rep sety a kombinácie cvikov ti dajú vytrvalosť.', 'en' => 'High-rep sets and exercise combinations will give you endurance.', 'cs' => 'High-rep sety a kombinace cviků ti dají vytrvalost.']],
                    ['icon' => 'heroicon-o-sparkles', 'title' => ['sk' => 'Estetika', 'en' => 'Aesthetics', 'cs' => 'Estetika'], 'description' => ['sk' => 'Statické prvky ako planche či front lever nie sú len o sile — sú to diela pohybového umenia.', 'en' => 'Static elements like planche or front lever are not just about strength — they are works of movement art.', 'cs' => 'Statické prvky jako planche či front lever nejsou jen o síle — jsou to díla pohybového umění.']],
                ],
            ]),
            self::brick('skill-cards', [
                'levels' => [
                    [
                        'name' => ['sk' => 'ZÁKLADY', 'en' => 'BASICS', 'cs' => 'ZÁKLADY'],
                        'color' => '#22c55e',
                        'cards' => [
                            ['title' => ['sk' => 'Pull-up', 'en' => 'Pull-up', 'cs' => 'Pull-up'], 'description' => ['sk' => 'Zhyb — základný ťahový cvik.', 'en' => 'Pull-up — the basic pulling exercise.', 'cs' => 'Shyb — základní tahový cvik.']],
                            ['title' => ['sk' => 'Dip', 'en' => 'Dip', 'cs' => 'Dip'], 'description' => ['sk' => 'Klik na bradlách.', 'en' => 'Dip on parallel bars.', 'cs' => 'Klik na bradlech.']],
                            ['title' => ['sk' => 'Push-up', 'en' => 'Push-up', 'cs' => 'Push-up'], 'description' => ['sk' => 'Klik — základ tlakových cvikov.', 'en' => 'Push-up — the foundation of pressing exercises.', 'cs' => 'Klik — základ tlakových cviků.']],
                            ['title' => ['sk' => 'Australian Pull-up', 'en' => 'Australian Pull-up', 'cs' => 'Australian Pull-up'], 'description' => ['sk' => 'Horizontálny zhyb pre začiatočníkov.', 'en' => 'Horizontal pull-up for beginners.', 'cs' => 'Horizontální shyb pro začátečníky.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'STREDNÉ', 'en' => 'INTERMEDIATE', 'cs' => 'STŘEDNÍ'],
                        'color' => '#3b82f6',
                        'cards' => [
                            ['title' => ['sk' => 'Muscle-up', 'en' => 'Muscle-up', 'cs' => 'Muscle-up'], 'description' => ['sk' => 'Kombinácia zhybu a tlaku nad hrazdu.', 'en' => 'Combination of pull-up and push above the bar.', 'cs' => 'Kombinace shybu a tlaku nad hrazdu.']],
                            ['title' => ['sk' => 'L-sit', 'en' => 'L-sit', 'cs' => 'L-sit'], 'description' => ['sk' => 'Statický sed s nohami v uhle 90°.', 'en' => 'Static hold with legs at a 90° angle.', 'cs' => 'Statický sed s nohama v úhlu 90°.']],
                            ['title' => ['sk' => 'Handstand', 'en' => 'Handstand', 'cs' => 'Handstand'], 'description' => ['sk' => 'Stojka na rukách.', 'en' => 'Handstand.', 'cs' => 'Stojka na rukou.']],
                            ['title' => ['sk' => 'Pistol Squat', 'en' => 'Pistol Squat', 'cs' => 'Pistol Squat'], 'description' => ['sk' => 'Drep na jednej nohe.', 'en' => 'Single-leg squat.', 'cs' => 'Dřep na jedné noze.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'POKROČILÉ', 'en' => 'ADVANCED', 'cs' => 'POKROČILÉ'],
                        'color' => '#f59e0b',
                        'cards' => [
                            ['title' => ['sk' => 'Front Lever', 'en' => 'Front Lever', 'cs' => 'Front Lever'], 'description' => ['sk' => 'Horizontálna poloha na hrazde tvárou nahor.', 'en' => 'Horizontal position on the bar face up.', 'cs' => 'Horizontální poloha na hrazdě tváří nahoru.']],
                            ['title' => ['sk' => 'Back Lever', 'en' => 'Back Lever', 'cs' => 'Back Lever'], 'description' => ['sk' => 'Horizontálna poloha na hrazde tvárou nadol.', 'en' => 'Horizontal position on the bar face down.', 'cs' => 'Horizontální poloha na hrazdě tváří dolů.']],
                            ['title' => ['sk' => 'Planche', 'en' => 'Planche', 'cs' => 'Planche'], 'description' => ['sk' => 'Horizontálny stoj na rukách.', 'en' => 'Horizontal handstand.', 'cs' => 'Horizontální stoj na rukou.']],
                            ['title' => ['sk' => 'Human Flag', 'en' => 'Human Flag', 'cs' => 'Human Flag'], 'description' => ['sk' => 'Ľudská vlajka — bočný vodorovný výdrž na tyči.', 'en' => 'Human flag — lateral horizontal hold on a pole.', 'cs' => 'Lidská vlajka — boční vodorovná výdrž na tyči.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'EXPERT', 'en' => 'EXPERT', 'cs' => 'EXPERT'],
                        'color' => '#ef4444',
                        'cards' => [
                            ['title' => ['sk' => 'Iron Cross', 'en' => 'Iron Cross', 'cs' => 'Iron Cross'], 'description' => ['sk' => 'Železný kríž na kruhoch.', 'en' => 'Iron cross on rings.', 'cs' => 'Železný kříž na kruzích.']],
                            ['title' => ['sk' => 'Victorian', 'en' => 'Victorian', 'cs' => 'Victorian'], 'description' => ['sk' => 'Pokročilá variácia front leveru.', 'en' => 'Advanced front lever variation.', 'cs' => 'Pokročilá variace front leveru.']],
                            ['title' => ['sk' => 'Full Planche', 'en' => 'Full Planche', 'cs' => 'Full Planche'], 'description' => ['sk' => 'Planche s nohami úplne vystreté.', 'en' => 'Planche with legs fully extended.', 'cs' => 'Planche s nohama úplně nataženýma.']],
                            ['title' => ['sk' => 'One Arm Pull-up', 'en' => 'One Arm Pull-up', 'cs' => 'One Arm Pull-up'], 'description' => ['sk' => 'Zhyb na jednej ruke.', 'en' => 'Pull-up on one arm.', 'cs' => 'Shyb na jedné ruce.']],
                        ],
                    ],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'POSUŇ SVOJE LIMITY', 'en' => 'PUSH YOUR LIMITS', 'cs' => 'POSUŇ SVÉ LIMITY'],
                'description' => ['sk' => 'Naše tréningy sú vhodné pre všetky úrovne.', 'en' => 'Our trainings are suitable for all levels.', 'cs' => 'Naše tréninky jsou vhodné pro všechny úrovně.'],
                'button_text' => ['sk' => 'Pozrieť tréningy', 'en' => 'View Trainings', 'cs' => 'Zobrazit tréninky'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('trainings'),
                'background_color' => '#dc2626',
            ]),
            self::brick('gallery', [
                'images' => [
                    ['image' => self::media('gallery-sw-1')],
                    ['image' => self::media('gallery-sw-2')],
                    ['image' => self::media('gallery-sw-3')],
                    ['image' => self::media('gallery-sw-4')],
                    ['image' => self::media('gallery-sw-5')],
                    ['image' => self::media('gallery-sw-6')],
                ],
            ]),
        ];
    }

    private static function pricingContent(): array
    {
        return [
            self::brick('feature-cards', [
                'label' => ['sk' => 'PREČO BCZ CLUB', 'en' => 'WHY BCZ CLUB', 'cs' => 'PROČ BCZ CLUB'],
                'title' => ['sk' => 'Všetko čo váš klub potrebuje', 'en' => 'Everything your club needs', 'cs' => 'Vše co váš klub potřebuje'],
                'cards' => [
                    ['icon' => 'heroicon-o-squares-2x2', 'title' => ['sk' => 'All-in-one platforma', 'en' => 'All-in-one platform', 'cs' => 'All-in-one platforma'], 'description' => ['sk' => 'Tréningy, súťaže, platby a členstvá na jednom mieste.', 'en' => 'Trainings, competitions, payments and memberships in one place.', 'cs' => 'Tréninky, soutěže, platby a členství na jednom místě.']],
                    ['icon' => 'heroicon-o-globe-alt', 'title' => ['sk' => 'Pre celý svet', 'en' => 'For the whole world', 'cs' => 'Pro celý svět'], 'description' => ['sk' => 'GoPay platby celosvetovo. QR platby v SK, CZ a ďalších európskych krajinách.', 'en' => 'GoPay payments worldwide. QR payments in SK, CZ and other European countries.', 'cs' => 'GoPay platby celosvětově. QR platby v SK, CZ a dalších evropských zemích.']],
                    ['icon' => 'heroicon-o-credit-card', 'title' => ['sk' => 'Jednoduché platby', 'en' => 'Simple payments', 'cs' => 'Jednoduché platby'], 'description' => ['sk' => 'Platforma rieši výplaty na IBAN tímu. Tímy nepotrebujú vlastný platobný účet.', 'en' => 'Platform handles payouts to team IBAN. Teams don\'t need their own payment account.', 'cs' => 'Platforma řeší výplaty na IBAN týmu. Týmy nepotřebují vlastní platební účet.']],
                    ['icon' => 'heroicon-o-arrow-trending-up', 'title' => ['sk' => 'Začnite free, rastite', 'en' => 'Start free, grow', 'cs' => 'Začněte zdarma, rostěte'], 'description' => ['sk' => 'Začnite zadarmo a škálujte podľa potrieb vášho klubu.', 'en' => 'Start for free and scale according to your club\'s needs.', 'cs' => 'Začněte zdarma a škálujte podle potřeb vašeho klubu.']],
                ],
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Často kladené otázky', 'en' => 'Frequently asked questions', 'cs' => 'Často kladené otázky'],
                'show_all' => false,
                'faq_ids' => Faq::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->limit(4)
                    ->pluck('id')
                    ->all(),
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Vyskúšajte 2 mesiace zadarmo', 'en' => 'Try 2 months for free', 'cs' => 'Vyzkoušejte 2 měsíce zdarma'],
                'description' => ['sk' => 'Vytvorte si účet za pár sekúnd a začnite spravovať váš klub.', 'en' => 'Create an account in seconds and start managing your club.', 'cs' => 'Vytvořte si účet za pár sekund a začněte spravovat váš klub.'],
                'button_text' => ['sk' => 'Začať skúšobnú dobu', 'en' => 'Start free trial', 'cs' => 'Začít zkušební dobu'],
                'button_link_type' => 'custom',
                'button_link_url' => ['sk' => '/admin', 'en' => '/admin', 'cs' => '/admin'],
                'background_color' => '#0A0A0A',
            ]),
        ];
    }

    private static function trainingsContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'background_image' => self::media('trainings-hero-bg'),
                'badge' => ['sk' => 'BEYOND COMFORT ZONE', 'en' => 'BEYOND COMFORT ZONE', 'cs' => 'BEYOND COMFORT ZONE'],
                'title' => ['sk' => 'TRÉNUJ', 'en' => 'TRAIN', 'cs' => 'TRÉNUJ'],
                'title_accent' => ['sk' => 'S NAMI', 'en' => 'WITH US', 'cs' => 'S NÁMI'],
                'subtitle' => ['sk' => 'Profesionálne tréningy parkouru, kalisteniky a street workoutu pre všetky vekové kategórie.', 'en' => 'Professional parkour, calisthenics and street workout training for all age groups.', 'cs' => 'Profesionální tréninky parkouru, kalisteniky a street workoutu pro všechny věkové kategorie.'],
                'scroll_text' => ['sk' => 'SCROLLUJ PRE VIAC', 'en' => 'SCROLL FOR MORE', 'cs' => 'SCROLLUJ PRO VÍCE'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'TRÉNINGY', 'en' => 'TRAININGS', 'cs' => 'TRÉNINKY'], 'url' => ''],
                ],
            ]),
            self::brick('training-categories', [
                'label' => ['sk' => 'ČO PONÚKAME', 'en' => 'WHAT WE OFFER', 'cs' => 'CO NABÍZÍME'],
                'title' => ['sk' => 'TRÉNINGOVÉ KATEGÓRIE', 'en' => 'TRAINING CATEGORIES', 'cs' => 'TRÉNINKOVÉ KATEGORIE'],
                'subtitle' => ['sk' => 'Vyber si disciplínu, ktorá ťa baví najviac', 'en' => 'Choose the discipline you enjoy the most', 'cs' => 'Vyber si disciplínu, která tě baví nejvíc'],
                'show_all' => true,
            ]),
            self::brick('latest-trainings', [
                'label' => ['sk' => 'AKTUÁLNE SKUPINY', 'en' => 'CURRENT GROUPS', 'cs' => 'AKTUÁLNÍ SKUPINY'],
                'title' => ['sk' => 'VYBER SI SVOJU SKUPINU', 'en' => 'CHOOSE YOUR GROUP', 'cs' => 'VYBER SI SVOU SKUPINU'],
                'subtitle' => ['sk' => 'Skupinové tréningy pre deti aj dospelých s obmedzenou kapacitou', 'en' => 'Group trainings for kids and adults with limited capacity', 'cs' => 'Skupinové tréninky pro děti i dospělé s omezenou kapacitou'],
                'show_all' => true,
                'cta_text' => ['sk' => 'ZOBRAZIŤ VŠETKY TRÉNINGY', 'en' => 'VIEW ALL TRAININGS', 'cs' => 'ZOBRAZIT VŠECHNY TRÉNINKY'],
                'cta_link_type' => 'custom',
                'cta_link_url' => ['sk' => '/zoznam-treningov', 'en' => '/en/zoznam-treningov', 'cs' => '/cs/zoznam-treningov'],
            ]),
            self::brick('person-cards', [
                'label' => ['sk' => 'UČ SA OD NAJLEPŠÍCH', 'en' => 'LEARN FROM THE BEST', 'cs' => 'UČ SE OD NEJLEPŠÍCH'],
                'title' => ['sk' => 'NAŠI TRÉNERI', 'en' => 'OUR COACHES', 'cs' => 'NAŠI TRENÉŘI'],
                'subtitle' => ['sk' => 'Certifikovaní profesionáli s rokmi skúseností', 'en' => 'Certified professionals with years of experience', 'cs' => 'Certifikovaní profesionálové s lety zkušeností'],
                'people' => [
                    [
                        'image' => self::media('trainings-coach1'),
                        'name' => ['sk' => 'DOMINIK KLIMEK', 'en' => 'DOMINIK KLIMEK', 'cs' => 'DOMINIK KLIMEK'],
                        'role' => ['sk' => 'Hlavný tréner Parkour & Kalistenika', 'en' => 'Head Coach Parkour & Calisthenics', 'cs' => 'Hlavní trenér Parkour & Kalistenika'],
                        'description' => ['sk' => '10+ rokov skúseností v parkour a kalistenike. Certifikovaný tréner s medzinárodnými úspechmi na súťažiach.', 'en' => '10+ years of experience in parkour and calisthenics. Certified coach with international competition achievements.', 'cs' => '10+ let zkušeností v parkouru a kalistenice. Certifikovaný trenér s mezinárodními úspěchy na soutěžích.'],
                        'tags' => ['Parkour Pro', 'Kalistenika L3'],
                    ],
                    [
                        'image' => self::media('trainings-coach2'),
                        'name' => ['sk' => 'MICHAL ČEČKO', 'en' => 'MICHAL ČEČKO', 'cs' => 'MICHAL ČEČKO'],
                        'role' => ['sk' => 'Tréner Parkour & Street Workout', 'en' => 'Coach Parkour & Street Workout', 'cs' => 'Trenér Parkour & Street Workout'],
                        'description' => ['sk' => '8 rokov aktívneho tréningu a 5 rokov skúseností s vedením skupín. Špecializácia na techniku a bezpečný progres.', 'en' => '8 years of active training and 5 years of group coaching experience. Specialization in technique and safe progression.', 'cs' => '8 let aktivního tréninku a 5 let zkušeností s vedením skupin. Specializace na techniku a bezpečný progres.'],
                        'tags' => ['Freerunning', 'Street Workout'],
                    ],
                ],
                'cta_text' => ['sk' => 'ZOBRAZIŤ VŠETKÝCH TRÉNEROV', 'en' => 'VIEW ALL COACHES', 'cs' => 'ZOBRAZIT VŠECHNY TRENÉRY'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('about'),
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'PRIDAJ SA K NÁM', 'en' => 'JOIN US', 'cs' => 'PŘIDEJ SE K NÁM'],
                'description' => ['sk' => 'Prvá tréningová hodina je zadarmo. Príď si vyskúšať, či je to niečo pre teba.', 'en' => 'The first training session is free. Come try if it is something for you.', 'cs' => 'První tréninková hodina je zdarma. Přijď si vyzkoušet, jestli je to něco pro tebe.'],
                'button_text' => ['sk' => 'REZERVOVAŤ TRÉNING', 'en' => 'BOOK TRAINING', 'cs' => 'REZERVOVAT TRÉNINK'],
                'button_link_type' => 'custom',
                'button_link_url' => ['sk' => '/pridaj-sa', 'en' => '/en/pridaj-sa', 'cs' => '/cs/pridaj-sa'],
                'secondary_text' => ['sk' => 'KONTAKTUJ NÁS', 'en' => 'CONTACT US', 'cs' => 'KONTAKTUJTE NÁS'],
                'secondary_link_type' => 'page',
                'secondary_link_model_id' => self::pageId('contact'),
                'background_color' => '#0A0A0A',
            ]),
        ];
    }

    private static function trainingsArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'ZOZNAM', 'en' => 'TRAINING', 'cs' => 'SEZNAM'],
                'title_accent' => ['sk' => 'TRÉNINGOV', 'en' => 'LIST', 'cs' => 'TRÉNINKŮ'],
                'subtitle' => ['sk' => 'Nájdi si tréning, ktorý ti vyhovuje', 'en' => 'Find a training that suits you', 'cs' => 'Najdi si trénink, který ti vyhovuje'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'TRÉNINGY', 'en' => 'TRAININGS', 'cs' => 'TRÉNINKY'], 'url' => ''],
                ],
            ]),
            self::brick('trainings-archive', []),
        ];
    }

    private static function competitionsArchiveContent(): array
    {
        return [
            self::brick('competition-hero', [
                'headline1' => ['sk' => 'BOJUJEME', 'en' => 'WE FIGHT', 'cs' => 'BOJUJEME'],
                'headline2' => ['sk' => 'ZA VÍŤAZSTVO', 'en' => 'FOR VICTORY', 'cs' => 'ZA VÍTĚZSTVÍ'],
                'subtitle' => ['sk' => 'Reprezentujeme Slovensko na medzinárodných súťažiach v parkour freestyle, speed a skill competition.', 'en' => 'We represent Slovakia in international parkour freestyle, speed and skill competitions.', 'cs' => 'Reprezentujeme Slovensko na mezinárodních soutěžích v parkour freestyle, speed a skill competition.'],
                'badge' => ['sk' => 'SÚŤAŽNÝ TÍM BCZ', 'en' => 'BCZ COMPETITION TEAM', 'cs' => 'SOUTĚŽNÍ TÝM BCZ'],
                'cta_link_type' => 'url',
                'cta_link_url' => ['sk' => '#upcoming', 'en' => '#upcoming', 'cs' => '#upcoming'],
                'cta_text' => ['sk' => 'Najbližšie súťaže', 'en' => 'Upcoming competitions', 'cs' => 'Nejbližší soutěže'],
                'secondary_cta_link_type' => 'url',
                'secondary_cta_link_url' => ['sk' => '/kontakt', 'en' => '/en/kontakt', 'cs' => '/cs/kontakt'],
                'secondary_cta_text' => ['sk' => 'Kontaktujte nás', 'en' => 'Contact us', 'cs' => 'Kontaktujte nás'],
                'stats' => [
                    ['number' => '15+', 'label' => ['sk' => 'Súťaží', 'en' => 'Competitions', 'cs' => 'Soutěží']],
                    ['number' => '20+', 'label' => ['sk' => 'Atlétov', 'en' => 'Athletes', 'cs' => 'Atletů']],
                    ['number' => '5+', 'label' => ['sk' => 'Krajín', 'en' => 'Countries', 'cs' => 'Zemí']],
                ],
            ]),
            self::brick('competitions-archive', [
                'label' => ['sk' => 'NADCHÁDZAJÚCE', 'en' => 'UPCOMING', 'cs' => 'NADCHÁZEJÍCÍ'],
                'title' => ['sk' => 'NAJBLIŽŠIE SÚŤAŽE', 'en' => 'UPCOMING COMPETITIONS', 'cs' => 'NEJBLIŽŠÍ SOUTĚŽE'],
                'description' => ['sk' => 'Sledujte náš kalendár súťaží a príďte nás povzbudiť.', 'en' => 'Follow our competition calendar and come cheer us on.', 'cs' => 'Sledujte náš kalendář soutěží a přijďte nás povzbudit.'],
            ]),
            self::brick('finished-competitions', [
                'label' => ['sk' => 'NAŠE ÚSPECHY', 'en' => 'OUR ACHIEVEMENTS', 'cs' => 'NAŠE ÚSPĚCHY'],
                'title' => ['sk' => 'VÝSLEDKY ZO SÚŤAŽÍ', 'en' => 'COMPETITION RESULTS', 'cs' => 'VÝSLEDKY ZE SOUTĚŽÍ'],
                'description' => ['sk' => 'Najnovšie umiestnenia a medaily našich atlétov.', 'en' => 'Latest placements and medals of our athletes.', 'cs' => 'Nejnovější umístění a medaile našich atletů.'],
            ]),
            self::brick('athletes-showcase', [
                'label' => ['sk' => 'NÁŠ TÍM', 'en' => 'OUR TEAM', 'cs' => 'NÁŠ TÝM'],
                'title' => ['sk' => 'SÚŤAŽIACI ATLÉTI', 'en' => 'COMPETING ATHLETES', 'cs' => 'SOUTĚŽÍCÍ ATLETI'],
                'description' => ['sk' => 'Spoznajte našich reprezentantov, ktorí bojujú o medaily na domácich aj medzinárodných súťažiach.', 'en' => 'Meet our representatives who fight for medals at domestic and international competitions.', 'cs' => 'Poznejte naše reprezentanty, kteří bojují o medaile na domácích i mezinárodních soutěžích.'],
                'random' => true,
            ]),
            self::brick('competition-cta', [
                'title' => ['sk' => 'CHCEŠ SÚŤAŽIŤ S NAMI?', 'en' => 'WANT TO COMPETE WITH US?', 'cs' => 'CHCEŠ SOUTĚŽIT S NÁMI?'],
                'description' => ['sk' => 'Pridaj sa k nášmu tímu a reprezentuj Slovensko na medzinárodných súťažiach v parkour a freerunning.', 'en' => 'Join our team and represent Slovakia in international parkour and freerunning competitions.', 'cs' => 'Přidej se k našemu týmu a reprezentuj Slovensko na mezinárodních soutěžích v parkour a freerunning.'],
                'background_color' => '#FF2D2D',
                'button_link_type' => 'url',
                'button_link_url' => ['sk' => '/kontakt', 'en' => '/en/kontakt', 'cs' => '/cs/kontakt'],
                'button_text' => ['sk' => 'Pridať sa do tímu', 'en' => 'Join the team', 'cs' => 'Přidat se do týmu'],
                'secondary_link_type' => 'url',
                'secondary_link_url' => ['sk' => '/kontakt', 'en' => '/en/kontakt', 'cs' => '/cs/kontakt'],
                'secondary_text' => ['sk' => 'Kontaktovať', 'en' => 'Contact', 'cs' => 'Kontaktovat'],
            ]),
        ];
    }

    private static function eventsArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'PODUJATIA', 'en' => 'EVENTS', 'cs' => 'UDÁLOSTI'],
                'subtitle' => ['sk' => 'Prehľad všetkých našich vystúpení, prednášok a workshopov', 'en' => 'Overview of all our performances, lectures and workshops', 'cs' => 'Přehled všech našich vystoupení, přednášek a workshopů'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'PODUJATIA', 'en' => 'EVENTS', 'cs' => 'UDÁLOSTI'], 'url' => ''],
                ],
            ]),
            self::brick('events-archive', []),
        ];
    }

    private static function coachesArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'NAŠI', 'en' => 'OUR', 'cs' => 'NAŠI'],
                'title_accent' => ['sk' => 'TRÉNERI', 'en' => 'COACHES', 'cs' => 'TRENÉŘI'],
                'subtitle' => ['sk' => 'Zoznámte sa s našimi skúsenými trénermi', 'en' => 'Meet our experienced coaches', 'cs' => 'Seznamte se s našimi zkušenými trenéry'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'TRÉNERI', 'en' => 'COACHES', 'cs' => 'TRENÉŘI'], 'url' => ''],
                ],
            ]),
            self::brick('coaches-archive', []),
        ];
    }

    private static function athletesArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'NAŠI', 'en' => 'OUR', 'cs' => 'NAŠI'],
                'title_accent' => ['sk' => 'ŠPORTOVCI', 'en' => 'ATHLETES', 'cs' => 'SPORTOVCI'],
                'subtitle' => ['sk' => 'Spoznajte talentovaných atlétov BCZ Club', 'en' => 'Meet the talented athletes of BCZ Club', 'cs' => 'Poznejte talentované atlety BCZ Club'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'ŠPORTOVCI', 'en' => 'ATHLETES', 'cs' => 'SPORTOVCI'], 'url' => ''],
                ],
            ]),
            self::brick('athletes-archive', []),
        ];
    }

    private static function judgesArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'NAŠI', 'en' => 'OUR', 'cs' => 'NAŠI'],
                'title_accent' => ['sk' => 'ROZHODCOVIA', 'en' => 'JUDGES', 'cs' => 'ROZHODČÍ'],
                'subtitle' => ['sk' => 'Certifikovaní rozhodcovia zabezpečujúci férovosť na súťažiach', 'en' => 'Certified judges ensuring fairness at competitions', 'cs' => 'Certifikovaní rozhodčí zajišťující férovost na soutěžích'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'ROZHODCOVIA', 'en' => 'JUDGES', 'cs' => 'ROZHODČÍ'], 'url' => ''],
                ],
            ]),
            self::brick('judges-archive', []),
        ];
    }

    private static function teamsArchiveContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'NAŠE', 'en' => 'OUR', 'cs' => 'NAŠE'],
                'title_accent' => ['sk' => 'TÍMY', 'en' => 'TEAMS', 'cs' => 'TÝMY'],
                'subtitle' => ['sk' => 'Spoznajte tímy BCZ Club pôsobiace v rôznych mestách', 'en' => 'Meet BCZ Club teams operating in various cities', 'cs' => 'Poznejte týmy BCZ Club působící v různých městech'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cs' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'TÍMY', 'en' => 'TEAMS', 'cs' => 'TÝMY'], 'url' => ''],
                ],
            ]),
            self::brick('teams-archive', []),
        ];
    }

    /**
     * Pre-upload all placeholder images needed for brick configs.
     */
    private static function seedMedia(): void
    {
        $images = [
            // Homepage
            'hero-bg' => ['url' => 'https://picsum.photos/seed/hero-bg/1920/1080', 'name' => 'hero-background.jpg'],
            'cat-competitions' => ['url' => 'https://picsum.photos/seed/comp/800/600', 'name' => 'category-competitions.jpg'],
            'cat-trainings' => ['url' => 'https://picsum.photos/seed/train/800/600', 'name' => 'category-trainings.jpg'],
            'cat-performances' => ['url' => 'https://picsum.photos/seed/perf/800/600', 'name' => 'category-performances.jpg'],
            'about-main' => ['url' => 'https://picsum.photos/seed/about1/800/1000', 'name' => 'about-main.jpg'],
            'about-left' => ['url' => 'https://picsum.photos/seed/about2/400/500', 'name' => 'about-left.jpg'],
            'about-right' => ['url' => 'https://picsum.photos/seed/about3/400/500', 'name' => 'about-right.jpg'],
            'founder-img' => ['url' => 'https://picsum.photos/seed/founder/800/1000', 'name' => 'founder-dominik.jpg'],
            'social-bg' => ['url' => 'https://picsum.photos/seed/social/1920/1080', 'name' => 'social-cta-bg.jpg'],

            // About page
            'about-hero-bg' => ['url' => 'https://picsum.photos/seed/aboutHero/1920/1080', 'name' => 'about-hero-bg.jpg'],
            'about-story-main' => ['url' => 'https://picsum.photos/seed/story/800/1000', 'name' => 'about-story.jpg'],
            'person-dominik' => ['url' => 'https://picsum.photos/seed/dom/400/500', 'name' => 'person-dominik.jpg'],
            'person-michal' => ['url' => 'https://picsum.photos/seed/mic/400/500', 'name' => 'person-michal.jpg'],
            'person-member1' => ['url' => 'https://picsum.photos/seed/mem1/400/500', 'name' => 'person-member1.jpg'],
            'person-member2' => ['url' => 'https://picsum.photos/seed/mem2/400/500', 'name' => 'person-member2.jpg'],
            'trainer-1' => ['url' => 'https://picsum.photos/seed/tr1/400/500', 'name' => 'trainer-1.jpg'],
            'trainer-2' => ['url' => 'https://picsum.photos/seed/tr2/400/500', 'name' => 'trainer-2.jpg'],

            // Trainings page
            'trainings-hero-bg' => ['url' => 'https://picsum.photos/seed/trainings-hero/1920/1080', 'name' => 'trainings-hero-bg.jpg'],
            'trainings-cat-parkour' => ['url' => 'https://picsum.photos/seed/trainings-pk/800/600', 'name' => 'trainings-cat-parkour.jpg'],
            'trainings-cat-sw' => ['url' => 'https://picsum.photos/seed/trainings-sw/800/600', 'name' => 'trainings-cat-sw.jpg'],
            'trainings-private' => ['url' => 'https://picsum.photos/seed/trainings-priv/800/600', 'name' => 'trainings-private.jpg'],
            'trainings-coach1' => ['url' => 'https://picsum.photos/seed/trainings-c1/600/800', 'name' => 'trainings-coach1.jpg'],
            'trainings-coach2' => ['url' => 'https://picsum.photos/seed/trainings-c2/600/800', 'name' => 'trainings-coach2.jpg'],

            // Services page
            'services-performance' => ['url' => 'https://picsum.photos/seed/svc-perf/800/600', 'name' => 'services-performance.jpg'],
            'services-lecture' => ['url' => 'https://picsum.photos/seed/svc-lect/800/600', 'name' => 'services-lecture.jpg'],
            'services-workshop' => ['url' => 'https://picsum.photos/seed/svc-work/800/600', 'name' => 'services-workshop.jpg'],
        ];

        // Gallery images (about, parkour, street workout)
        for ($i = 1; $i <= 6; $i++) {
            $images["gallery-about-{$i}"] = ['url' => "https://picsum.photos/seed/gal-a{$i}/800/600", 'name' => "gallery-about-{$i}.jpg"];
            $images["gallery-parkour-{$i}"] = ['url' => "https://picsum.photos/seed/gal-p{$i}/800/600", 'name' => "gallery-parkour-{$i}.jpg"];
            $images["gallery-sw-{$i}"] = ['url' => "https://picsum.photos/seed/gal-sw{$i}/800/600", 'name' => "gallery-sw-{$i}.jpg"];
        }

        foreach ($images as $key => $meta) {
            self::$mediaCache[$key] = self::uploadMedia($meta['url'], $meta['name']);
        }
    }

    private static function uploadMedia(string $url, string $filename): string
    {
        $response = Http::get($url);

        $path = "bricks/{$filename}";
        Storage::disk('public')->put($path, $response->body());

        return $path;
    }

    /** @return list<array{type: string, attrs: array}> */
    private static function privacyPolicyContent(): array
    {
        return [
            self::brick('heading', [
                'title' => ['sk' => 'OCHRANA OSOBNÝCH ÚDAJOV', 'en' => 'PRIVACY POLICY', 'cs' => 'OCHRANA OSOBNÍCH ÚDAJŮ'],
                'subtitle' => ['sk' => 'Informácie o spracovaní osobných údajov podľa čl. 13 GDPR', 'en' => 'Information on personal data processing under Art. 13 GDPR', 'cs' => 'Informace o zpracování osobních údajů podle čl. 13 GDPR'],
            ]),
            self::brick('rich-text', [
                'content' => [
                    'sk' => '<h3>1. Prevádzkovateľ</h3><p>Street Workout Kysuce, o.z., IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca (ďalej len „prevádzkovateľ"). Kontakt: info@bczclub.sk</p><h3>2. Aké osobné údaje spracovávame</h3><p>Spracovávame nasledovné kategórie osobných údajov:</p><ul><li><strong>Identifikačné údaje:</strong> meno, priezvisko, dátum narodenia, pohlavie</li><li><strong>Kontaktné údaje:</strong> e-mailová adresa, telefónne číslo</li><li><strong>Údaje o členstve:</strong> typ členstva, platobné údaje, história registrácií</li><li><strong>Údaje z formulárov:</strong> údaje zadané v registračných formulároch na tréningy, súťaže a podujatia</li><li><strong>Technické údaje:</strong> cookies, IP adresa (spracované cez Cookiebot)</li></ul><h3>3. Účel a právny základ spracovania</h3><ul><li><strong>Registrácia a členstvo:</strong> plnenie zmluvy (čl. 6 ods. 1 písm. b) GDPR)</li><li><strong>Organizácia tréningov a podujatí:</strong> oprávnený záujem (čl. 6 ods. 1 písm. f) GDPR)</li><li><strong>Komunikácia a dopyty:</strong> súhlas (čl. 6 ods. 1 písm. a) GDPR)</li><li><strong>Účtovné a daňové povinnosti:</strong> zákonná povinnosť (čl. 6 ods. 1 písm. c) GDPR)</li><li><strong>Verejné profily trénerov/atlétov:</strong> súhlas (čl. 6 ods. 1 písm. a) GDPR)</li></ul><h3>4. Doba uchovávania údajov</h3><p>Osobné údaje uchovávame počas trvania členstva a 3 roky po jeho ukončení. Účtovné doklady uchovávame 10 rokov v súlade so zákonom o účtovníctve. Údaje spracované na základe súhlasu uchovávame do odvolania súhlasu.</p><h3>5. Príjemcovia osobných údajov</h3><p>Vaše údaje môžu byť poskytnuté nasledovným príjemcom:</p><ul><li>GoPay s.r.o. — spracovanie platieb</li><li>Poskytovateľ hostingu a e-mailových služieb</li><li>Orgány verejnej moci — ak to vyžaduje zákon</li></ul><h3>6. Vaše práva</h3><p>Máte právo na:</p><ul><li><strong>Prístup</strong> k vašim osobným údajom</li><li><strong>Opravu</strong> nesprávnych údajov</li><li><strong>Vymazanie</strong> údajov (právo na zabudnutie)</li><li><strong>Obmedzenie</strong> spracovania</li><li><strong>Prenosnosť</strong> údajov</li><li><strong>Námietku</strong> proti spracovaniu</li><li><strong>Odvolanie súhlasu</strong> kedykoľvek</li></ul><p>Svoje práva môžete uplatniť zaslaním e-mailu na info@bczclub.sk. Máte tiež právo podať sťažnosť na Úrad na ochranu osobných údajov SR (www.dataprotection.gov.sk).</p><h3>7. Cookies</h3><p>Táto webová stránka používa cookies. Podrobné informácie o používaných cookies a možnostiach ich nastavenia nájdete v našom Cookiebot paneli, ktorý sa zobrazí pri prvej návšteve stránky.</p><p><em>Posledná aktualizácia: apríl 2026</em></p>',
                    'en' => '<h3>1. Data Controller</h3><p>Street Workout Kysuce, o.z., ID: 54 188 440, registered at: Kukučínova 1322/36, 022 01 Čadca, Slovakia (hereinafter "controller"). Contact: info@bczclub.sk</p><h3>2. Personal Data We Process</h3><p>We process the following categories of personal data:</p><ul><li><strong>Identification data:</strong> name, surname, date of birth, gender</li><li><strong>Contact data:</strong> email address, phone number</li><li><strong>Membership data:</strong> membership type, payment details, registration history</li><li><strong>Form data:</strong> data entered in registration forms for trainings, competitions and events</li><li><strong>Technical data:</strong> cookies, IP address (processed via Cookiebot)</li></ul><h3>3. Purpose and Legal Basis</h3><ul><li><strong>Registration and membership:</strong> performance of contract (Art. 6(1)(b) GDPR)</li><li><strong>Organization of trainings and events:</strong> legitimate interest (Art. 6(1)(f) GDPR)</li><li><strong>Communication and inquiries:</strong> consent (Art. 6(1)(a) GDPR)</li><li><strong>Accounting and tax obligations:</strong> legal obligation (Art. 6(1)(c) GDPR)</li><li><strong>Public profiles of coaches/athletes:</strong> consent (Art. 6(1)(a) GDPR)</li></ul><h3>4. Data Retention Period</h3><p>We retain personal data for the duration of membership and 3 years after its termination. Accounting documents are retained for 10 years in accordance with accounting legislation. Data processed on the basis of consent is retained until consent is withdrawn.</p><h3>5. Recipients of Personal Data</h3><p>Your data may be provided to the following recipients:</p><ul><li>GoPay s.r.o. — payment processing</li><li>Hosting and email service provider</li><li>Public authorities — if required by law</li></ul><h3>6. Your Rights</h3><p>You have the right to:</p><ul><li><strong>Access</strong> your personal data</li><li><strong>Rectification</strong> of inaccurate data</li><li><strong>Erasure</strong> of data (right to be forgotten)</li><li><strong>Restriction</strong> of processing</li><li><strong>Data portability</strong></li><li><strong>Object</strong> to processing</li><li><strong>Withdraw consent</strong> at any time</li></ul><p>You can exercise your rights by sending an email to info@bczclub.sk. You also have the right to lodge a complaint with the Slovak Data Protection Authority (www.dataprotection.gov.sk).</p><h3>7. Cookies</h3><p>This website uses cookies. Detailed information about cookies used and their settings can be found in our Cookiebot panel, which is displayed on your first visit.</p><p><em>Last updated: April 2026</em></p>',
                    'cs' => '<h3>1. Správce údajů</h3><p>Street Workout Kysuce, o.z., IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca, Slovensko (dále jen „správce"). Kontakt: info@bczclub.sk</p><h3>2. Jaké osobní údaje zpracováváme</h3><p>Zpracováváme následující kategorie osobních údajů:</p><ul><li><strong>Identifikační údaje:</strong> jméno, příjmení, datum narození, pohlaví</li><li><strong>Kontaktní údaje:</strong> e-mailová adresa, telefonní číslo</li><li><strong>Údaje o členství:</strong> typ členství, platební údaje, historie registrací</li><li><strong>Údaje z formulářů:</strong> údaje zadané v registračních formulářích na tréninky, soutěže a akce</li><li><strong>Technické údaje:</strong> cookies, IP adresa (zpracované přes Cookiebot)</li></ul><h3>3. Účel a právní základ zpracování</h3><ul><li><strong>Registrace a členství:</strong> plnění smlouvy (čl. 6 odst. 1 písm. b) GDPR)</li><li><strong>Organizace tréninků a akcí:</strong> oprávněný zájem (čl. 6 odst. 1 písm. f) GDPR)</li><li><strong>Komunikace a dotazy:</strong> souhlas (čl. 6 odst. 1 písm. a) GDPR)</li><li><strong>Účetní a daňové povinnosti:</strong> zákonná povinnost (čl. 6 odst. 1 písm. c) GDPR)</li><li><strong>Veřejné profily trenérů/atletů:</strong> souhlas (čl. 6 odst. 1 písm. a) GDPR)</li></ul><h3>4. Doba uchovávání údajů</h3><p>Osobní údaje uchováváme po dobu trvání členství a 3 roky po jeho ukončení. Účetní doklady uchováváme 10 let v souladu se zákonem o účetnictví. Údaje zpracované na základě souhlasu uchováváme do odvolání souhlasu.</p><h3>5. Příjemci osobních údajů</h3><p>Vaše údaje mohou být poskytnuty následujícím příjemcům:</p><ul><li>GoPay s.r.o. — zpracování plateb</li><li>Poskytovatel hostingu a e-mailových služeb</li><li>Orgány veřejné moci — pokud to vyžaduje zákon</li></ul><h3>6. Vaše práva</h3><p>Máte právo na:</p><ul><li><strong>Přístup</strong> k vašim osobním údajům</li><li><strong>Opravu</strong> nesprávných údajů</li><li><strong>Výmaz</strong> údajů (právo být zapomenut)</li><li><strong>Omezení</strong> zpracování</li><li><strong>Přenositelnost</strong> údajů</li><li><strong>Námitku</strong> proti zpracování</li><li><strong>Odvolání souhlasu</strong> kdykoliv</li></ul><p>Svá práva můžete uplatnit zasláním e-mailu na info@bczclub.sk. Máte také právo podat stížnost u slovenského Úřadu na ochranu osobních údajů (www.dataprotection.gov.sk).</p><h3>7. Cookies</h3><p>Tato webová stránka používá cookies. Podrobné informace o používaných cookies a možnostech jejich nastavení najdete v našem Cookiebot panelu, který se zobrazí při první návštěvě stránky.</p><p><em>Poslední aktualizace: duben 2026</em></p>',
                ],
            ]),
        ];
    }

    /** @return list<array{type: string, attrs: array}> */
    private static function termsOfUseContent(): array
    {
        return [
            self::brick('heading', [
                'title' => ['sk' => 'PODMIENKY POUŽÍVANIA', 'en' => 'TERMS OF USE', 'cs' => 'PODMÍNKY POUŽÍVÁNÍ'],
                'subtitle' => ['sk' => 'Pravidlá používania webovej stránky BCZ Club', 'en' => 'Rules for using the BCZ Club website', 'cs' => 'Pravidla používání webové stránky BCZ Club'],
            ]),
            self::brick('rich-text', [
                'content' => [
                    'sk' => '<h3>1. Prevádzkovateľ</h3><p>Prevádzkovateľom webovej stránky bczclub.sk je Street Workout Kysuce, o.z., IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca.</p><h3>2. Všeobecné ustanovenia</h3><p>Tieto podmienky upravujú pravidlá používania webovej stránky bczclub.sk a všetkých jej súčastí. Prístupom na stránku a jej používaním vyjadrujete súhlas s týmito podmienkami.</p><h3>3. Registrácia a používateľský účet</h3><p>Pre využívanie niektorých služieb (registrácia na tréningy, súťaže, členstvo) je potrebné vytvorenie používateľského účtu. Používateľ je povinný uviesť pravdivé a aktuálne údaje. Za bezpečnosť prihlasovacích údajov zodpovedá používateľ.</p><h3>4. Pravidlá správania</h3><p>Používateľ sa zaväzuje:</p><ul><li>Nepoužívať stránku na nelegálne účely</li><li>Neuverejňovať nevhodný, urážlivý alebo zavádzajúci obsah</li><li>Nezasahovať do technického fungovania stránky</li><li>Rešpektovať práva ostatných používateľov</li></ul><h3>5. Duševné vlastníctvo</h3><p>Všetok obsah na stránke (texty, grafika, logá, fotografie, videá) je chránený autorským právom. Akékoľvek kopírovanie, distribúcia alebo úprava obsahu bez písomného súhlasu prevádzkovateľa je zakázaná.</p><h3>6. Členstvo a platby</h3><p>Členstvo v združení je dobrovoľné. Členské príspevky sú považované za dar v prospech občianskeho združenia v súlade so zákonom č. 83/1990 Zb. o združovaní občanov. Prevádzkovateľ si vyhradzuje právo zmeniť výšku členských príspevkov s predchádzajúcim upozornením.</p><h3>7. Zodpovednosť</h3><p>Prevádzkovateľ nenesie zodpovednosť za:</p><ul><li>Škody vzniknuté v dôsledku nesprávneho používania stránky</li><li>Dočasnú nedostupnosť stránky z technických dôvodov</li><li>Obsah externých stránok, na ktoré vedú odkazy z tejto stránky</li></ul><h3>8. Zmeny podmienok</h3><p>Prevádzkovateľ si vyhradzuje právo tieto podmienky kedykoľvek zmeniť. O zmenách budú používatelia informovaní prostredníctvom webovej stránky.</p><h3>9. Záverečné ustanovenia</h3><p>Tieto podmienky sa riadia právnym poriadkom Slovenskej republiky. Prípadné spory budú riešené príslušnými súdmi Slovenskej republiky.</p><p><em>Posledná aktualizácia: apríl 2026</em></p>',
                    'en' => '<h3>1. Operator</h3><p>The operator of the website bczclub.sk is Street Workout Kysuce, o.z., ID: 54 188 440, registered at: Kukučínova 1322/36, 022 01 Čadca, Slovakia.</p><h3>2. General Provisions</h3><p>These terms govern the rules for using the website bczclub.sk and all its components. By accessing and using the site, you agree to these terms.</p><h3>3. Registration and User Account</h3><p>To use certain services (registration for trainings, competitions, membership), it is necessary to create a user account. The user is obliged to provide truthful and current information. The user is responsible for the security of their login credentials.</p><h3>4. Code of Conduct</h3><p>The user agrees to:</p><ul><li>Not use the site for illegal purposes</li><li>Not publish inappropriate, offensive or misleading content</li><li>Not interfere with the technical operation of the site</li><li>Respect the rights of other users</li></ul><h3>5. Intellectual Property</h3><p>All content on the site (texts, graphics, logos, photographs, videos) is protected by copyright. Any copying, distribution or modification of content without the written consent of the operator is prohibited.</p><h3>6. Membership and Payments</h3><p>Membership in the association is voluntary. Membership fees are considered as donations to the civic association in accordance with Act No. 83/1990 Coll. on the association of citizens. The operator reserves the right to change membership fees with prior notice.</p><h3>7. Liability</h3><p>The operator is not liable for:</p><ul><li>Damages resulting from improper use of the site</li><li>Temporary unavailability of the site for technical reasons</li><li>Content of external sites linked from this site</li></ul><h3>8. Changes to Terms</h3><p>The operator reserves the right to change these terms at any time. Users will be informed of changes through the website.</p><h3>9. Final Provisions</h3><p>These terms are governed by the laws of the Slovak Republic. Any disputes will be resolved by the competent courts of the Slovak Republic.</p><p><em>Last updated: April 2026</em></p>',
                    'cs' => '<h3>1. Provozovatel</h3><p>Provozovatelem webové stránky bczclub.sk je Street Workout Kysuce, o.z., IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca, Slovensko.</p><h3>2. Obecná ustanovení</h3><p>Tyto podmínky upravují pravidla používání webové stránky bczclub.sk a všech jejích součástí. Přístupem na stránku a jejím používáním vyjadřujete souhlas s těmito podmínkami.</p><h3>3. Registrace a uživatelský účet</h3><p>Pro využívání některých služeb (registrace na tréninky, soutěže, členství) je nutné vytvoření uživatelského účtu. Uživatel je povinen uvést pravdivé a aktuální údaje. Za bezpečnost přihlašovacích údajů odpovídá uživatel.</p><h3>4. Pravidla chování</h3><p>Uživatel se zavazuje:</p><ul><li>Nepoužívat stránku k nelegálním účelům</li><li>Nezveřejňovat nevhodný, urážlivý nebo zavádějící obsah</li><li>Nezasahovat do technického fungování stránky</li><li>Respektovat práva ostatních uživatelů</li></ul><h3>5. Duševní vlastnictví</h3><p>Veškerý obsah na stránce (texty, grafika, loga, fotografie, videa) je chráněn autorským právem. Jakékoli kopírování, distribuce nebo úprava obsahu bez písemného souhlasu provozovatele je zakázána.</p><h3>6. Členství a platby</h3><p>Členství ve sdružení je dobrovolné. Členské příspěvky jsou považovány za dar ve prospěch občanského sdružení v souladu se zákonem č. 83/1990 Sb. o sdružování občanů. Provozovatel si vyhrazuje právo změnit výši členských příspěvků s předchozím upozorněním.</p><h3>7. Odpovědnost</h3><p>Provozovatel nenese odpovědnost za:</p><ul><li>Škody vzniklé v důsledku nesprávného používání stránky</li><li>Dočasnou nedostupnost stránky z technických důvodů</li><li>Obsah externích stránek, na které vedou odkazy z této stránky</li></ul><h3>8. Změny podmínek</h3><p>Provozovatel si vyhrazuje právo tyto podmínky kdykoliv změnit. O změnách budou uživatelé informováni prostřednictvím webové stránky.</p><h3>9. Závěrečná ustanovení</h3><p>Tyto podmínky se řídí právním řádem Slovenské republiky. Případné spory budou řešeny příslušnými soudy Slovenské republiky.</p><p><em>Poslední aktualizace: duben 2026</em></p>',
                ],
            ]),
        ];
    }

    /** @return list<array{type: string, attrs: array}> */
    private static function termsOfCommerceContent(): array
    {
        return [
            self::brick('heading', [
                'title' => ['sk' => 'OBCHODNÉ PODMIENKY', 'en' => 'TERMS OF COMMERCE', 'cs' => 'OBCHODNÍ PODMÍNKY'],
                'subtitle' => ['sk' => 'Podmienky členstva, registrácie na tréningy, súťaže a podujatia', 'en' => 'Terms of membership, registration for trainings, competitions and events', 'cs' => 'Podmínky členství, registrace na tréninky, soutěže a akce'],
            ]),
            self::brick('rich-text', [
                'content' => [
                    'sk' => '<h3>1. Úvodné ustanovenia</h3><p>Tieto obchodné podmienky upravujú vzťah medzi občianskym združením <strong>Street Workout Kysuce, o.z.</strong>, IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca (ďalej len „prevádzkovateľ" alebo „BCZ Club") a fyzickou osobou, ktorá si prostredníctvom webovej stránky bczclub.sk objedná členstvo, registráciu na tréning, súťaž alebo iné podujatie (ďalej len „objednávateľ").</p><p>Tieto podmienky sú zverejnené v súlade s § 3 zákona č. 102/2014 Z. z. o ochrane spotrebiteľa pri predaji tovaru alebo poskytovaní služieb na základe zmluvy uzavretej na diaľku.</p><h3>2. Predmet zmluvy</h3><p>Predmetom zmluvy je poskytnutie jednej alebo viacerých z nasledujúcich služieb:</p><ul><li><strong>Členstvo v BCZ Club</strong> — ročný alebo sezónny členský príspevok oprávňujúci na účasť na tréningoch a podujatiach klubu</li><li><strong>Registrácia na tréning</strong> — jednorazová alebo opakovaná účasť na tréningovej jednotke</li><li><strong>Registrácia na súťaž</strong> — štartovné na športovú súťaž organizovanú klubom</li><li><strong>Registrácia na podujatie</strong> — vystúpenia, workshopy, prednášky a iné podujatia</li><li><strong>Predplatné pre tímy (SaaS)</strong> — softvérové predplatné pre tímy využívajúce platformu BCZ Club</li></ul><h3>3. Uzavretie zmluvy</h3><p>Zmluva medzi prevádzkovateľom a objednávateľom je uzavretá odoslaním registračného formulára alebo objednávky prostredníctvom webovej stránky a jej potvrdením zo strany prevádzkovateľa (spravidla e-mailom). Pred odoslaním objednávky je objednávateľ povinný oboznámiť sa s týmito obchodnými podmienkami a vyjadriť s nimi súhlas.</p><h3>4. Ceny a platobné podmienky</h3><p>Aktuálne ceny členstva, štartovného a iných služieb sú zverejnené na webovej stránke pri každej konkrétnej službe. Ceny sú uvedené v eurách (EUR) alebo českých korunách (CZK) v závislosti od lokality a typu platby. Prevádzkovateľ nie je platcom DPH.</p><p>Objednávateľ si môže zvoliť jeden z nasledujúcich spôsobov platby:</p><ul><li><strong>Platba kartou cez GoPay</strong> — okamžité spracovanie platby cez zabezpečenú platobnú bránu GoPay s.r.o.</li><li><strong>Bankový prevod</strong> — platba na účet prevádzkovateľa s použitím variabilného symbolu uvedeného v pokynoch</li><li><strong>Platba v hotovosti</strong> — na mieste po dohode s prevádzkovateľom</li></ul><p>Služba sa považuje za uhradenú dňom pripísania platby na účet prevádzkovateľa. Pri platbe bankovým prevodom má objednávateľ povinnosť uhradiť platbu do dátumu splatnosti uvedeného v potvrdzovacom e-maile, inak môže dôjsť k zrušeniu registrácie.</p><h3>5. Právo spotrebiteľa odstúpiť od zmluvy</h3><p>V súlade so zákonom č. 102/2014 Z. z. má objednávateľ ako spotrebiteľ právo odstúpiť od zmluvy bez uvedenia dôvodu v lehote <strong>14 dní</strong> odo dňa uzavretia zmluvy. Odstúpenie od zmluvy je možné uplatniť písomne na e-mailovej adrese info@bczclub.sk.</p><p><strong>Výnimka z práva na odstúpenie:</strong> V súlade s § 7 ods. 6 zákona č. 102/2014 Z. z. objednávateľ nemôže odstúpiť od zmluvy, ktorej predmetom je poskytnutie služieb súvisiacich s voľnočasovými aktivitami (tréningy, súťaže, podujatia), ak sa prevádzkovateľ zaviazal poskytnúť tieto služby v dohodnutom čase alebo v dohodnutej lehote, a tento čas alebo lehota už uplynuli, alebo ak je dátum konania podujatia pevne určený.</p><p>V prípade riadneho odstúpenia od zmluvy prevádzkovateľ vráti objednávateľovi všetky platby do 14 dní odo dňa doručenia oznámenia o odstúpení, rovnakým spôsobom, akým boli platby prijaté.</p><h3>6. Zrušenie registrácie a vrátenie platby</h3><p>Objednávateľ môže zrušiť svoju registráciu na tréning, súťaž alebo podujatie za nasledujúcich podmienok:</p><ul><li><strong>Zrušenie viac ako 7 dní pred konaním:</strong> vrátenie 100 % uhradenej sumy</li><li><strong>Zrušenie 2 — 7 dní pred konaním:</strong> vrátenie 50 % uhradenej sumy (administratívny poplatok)</li><li><strong>Zrušenie menej ako 48 hodín pred konaním:</strong> uhradená suma sa nevracia</li></ul><p>V prípade zrušenia podujatia zo strany prevádzkovateľa (napr. z dôvodu nedostatočného počtu účastníkov alebo vyššej moci) sa objednávateľovi vracia 100 % uhradenej sumy, prípadne je umožnené preniesť platbu na náhradný termín.</p><p>Členský príspevok je nevratný, pokiaľ nie je v konkrétnom prípade dohodnuté inak.</p><h3>7. Reklamácie</h3><p>V prípade, že poskytnutá služba nezodpovedá popisu alebo má zjavné vady, má objednávateľ právo uplatniť reklamáciu. Reklamáciu je potrebné uplatniť bez zbytočného odkladu, najneskôr do 7 dní odo dňa poskytnutia služby, na e-mailovej adrese info@bczclub.sk.</p><p>Prevádzkovateľ je povinný vybaviť reklamáciu najneskôr do 30 dní odo dňa jej doručenia.</p><h3>8. Mimosúdne riešenie sporov</h3><p>V prípade sporu medzi prevádzkovateľom a objednávateľom má objednávateľ právo obrátiť sa na subjekt alternatívneho riešenia sporov, ktorým je Slovenská obchodná inšpekcia (www.soi.sk). Objednávateľ môže tiež využiť platformu RSO (Riešenie sporov online) dostupnú na adrese ec.europa.eu/consumers/odr.</p><h3>9. Ochrana osobných údajov</h3><p>Spracovanie osobných údajov sa riadi samostatným dokumentom <em>Ochrana osobných údajov</em>, ktorý je dostupný na webovej stránke.</p><h3>10. Záverečné ustanovenia</h3><p>Tieto obchodné podmienky sa riadia právnym poriadkom Slovenskej republiky. Vzťahy neupravené týmito podmienkami sa riadia najmä zákonom č. 40/1964 Zb. (Občiansky zákonník), zákonom č. 102/2014 Z. z. a zákonom č. 250/2007 Z. z. o ochrane spotrebiteľa.</p><p>Prevádzkovateľ si vyhradzuje právo tieto obchodné podmienky meniť. Zmeny sú účinné dňom ich zverejnenia na webovej stránke a nemajú vplyv na zmluvy uzavreté pred ich zverejnením.</p><p><em>Posledná aktualizácia: apríl 2026</em></p>',
                    'en' => '<h3>1. Introductory Provisions</h3><p>These Terms of Commerce govern the relationship between the non-profit association <strong>Street Workout Kysuce, o.z.</strong>, ID: 54 188 440, registered at: Kukučínova 1322/36, 022 01 Čadca, Slovakia (hereinafter "operator" or "BCZ Club") and a natural person who, through the website bczclub.sk, orders a membership, registration for a training, competition or other event (hereinafter "customer").</p><p>These terms are published in accordance with Section 3 of Act No. 102/2014 Coll. on consumer protection in distance contracts for the sale of goods or provision of services.</p><h3>2. Subject of Contract</h3><p>The subject of the contract is the provision of one or more of the following services:</p><ul><li><strong>BCZ Club membership</strong> — annual or seasonal membership fee entitling the member to participate in club trainings and events</li><li><strong>Training registration</strong> — one-time or recurring participation in a training session</li><li><strong>Competition registration</strong> — entry fee for a sports competition organized by the club</li><li><strong>Event registration</strong> — performances, workshops, lectures and other events</li><li><strong>Team subscription (SaaS)</strong> — software subscription for teams using the BCZ Club platform</li></ul><h3>3. Conclusion of Contract</h3><p>The contract between the operator and the customer is concluded by submitting the registration form or order through the website and its confirmation by the operator (usually via email). Before submitting the order, the customer is obliged to review these Terms of Commerce and express agreement with them.</p><h3>4. Prices and Payment Terms</h3><p>Current prices of memberships, entry fees and other services are published on the website at each specific service. Prices are stated in euros (EUR) or Czech crowns (CZK) depending on the location and type of payment. The operator is not a VAT payer.</p><p>The customer can choose one of the following payment methods:</p><ul><li><strong>Card payment via GoPay</strong> — instant payment processing through the secure payment gateway GoPay s.r.o.</li><li><strong>Bank transfer</strong> — payment to the operator\'s account using the variable symbol stated in the instructions</li><li><strong>Cash payment</strong> — on site, by arrangement with the operator</li></ul><p>The service is considered paid on the day the payment is credited to the operator\'s account. When paying by bank transfer, the customer is obliged to make the payment by the due date specified in the confirmation email, otherwise the registration may be cancelled.</p><h3>5. Consumer\'s Right of Withdrawal</h3><p>In accordance with Act No. 102/2014 Coll., the customer as a consumer has the right to withdraw from the contract without giving any reason within <strong>14 days</strong> of the conclusion of the contract. Withdrawal from the contract may be exercised in writing at the email address info@bczclub.sk.</p><p><strong>Exception to the right of withdrawal:</strong> In accordance with Section 7(6) of Act No. 102/2014 Coll., the customer cannot withdraw from a contract concerning the provision of services related to leisure activities (trainings, competitions, events) if the operator has undertaken to provide these services at an agreed time or within an agreed period, and that time or period has already elapsed, or if the date of the event is fixed.</p><p>In the event of proper withdrawal from the contract, the operator shall return all payments to the customer within 14 days of receipt of the notice of withdrawal, in the same manner as the payments were received.</p><h3>6. Cancellation of Registration and Refund</h3><p>The customer may cancel their registration for a training, competition or event under the following conditions:</p><ul><li><strong>Cancellation more than 7 days before the event:</strong> refund of 100% of the paid amount</li><li><strong>Cancellation 2 — 7 days before the event:</strong> refund of 50% of the paid amount (administrative fee)</li><li><strong>Cancellation less than 48 hours before the event:</strong> the paid amount is non-refundable</li></ul><p>If the event is cancelled by the operator (e.g. due to insufficient number of participants or force majeure), the customer is refunded 100% of the paid amount, or the payment may be transferred to an alternative date.</p><p>Membership fees are non-refundable unless otherwise agreed in a specific case.</p><h3>7. Complaints</h3><p>If the service provided does not match the description or has obvious defects, the customer has the right to file a complaint. The complaint must be filed without undue delay, no later than 7 days from the day the service was provided, at the email address info@bczclub.sk.</p><p>The operator is obliged to handle the complaint no later than 30 days from the day of its receipt.</p><h3>8. Out-of-Court Dispute Resolution</h3><p>In the event of a dispute between the operator and the customer, the customer has the right to turn to an alternative dispute resolution body, which is the Slovak Trade Inspection (www.soi.sk). The customer may also use the Online Dispute Resolution (ODR) platform available at ec.europa.eu/consumers/odr.</p><h3>9. Personal Data Protection</h3><p>The processing of personal data is governed by a separate document <em>Privacy Policy</em>, which is available on the website.</p><h3>10. Final Provisions</h3><p>These Terms of Commerce are governed by the laws of the Slovak Republic. Relations not regulated by these terms are governed in particular by Act No. 40/1964 Coll. (Civil Code), Act No. 102/2014 Coll. and Act No. 250/2007 Coll. on consumer protection.</p><p>The operator reserves the right to change these Terms of Commerce. Changes are effective on the day of their publication on the website and do not affect contracts concluded before their publication.</p><p><em>Last updated: April 2026</em></p>',
                    'cs' => '<h3>1. Úvodní ustanovení</h3><p>Tyto obchodní podmínky upravují vztah mezi občanským sdružením <strong>Street Workout Kysuce, o.z.</strong>, IČO: 54 188 440, sídlo: Kukučínova 1322/36, 022 01 Čadca, Slovensko (dále jen „provozovatel" nebo „BCZ Club") a fyzickou osobou, která si prostřednictvím webové stránky bczclub.sk objedná členství, registraci na trénink, soutěž nebo jinou akci (dále jen „objednavatel").</p><p>Tyto podmínky jsou zveřejněny v souladu se slovenským zákonem č. 102/2014 Z. z. o ochraně spotřebitele při prodeji zboží nebo poskytování služeb na základě smlouvy uzavřené na dálku.</p><h3>2. Předmět smlouvy</h3><p>Předmětem smlouvy je poskytnutí jedné nebo více z následujících služeb:</p><ul><li><strong>Členství v BCZ Club</strong> — roční nebo sezónní členský příspěvek opravňující k účasti na trénincích a akcích klubu</li><li><strong>Registrace na trénink</strong> — jednorázová nebo opakovaná účast na tréninkové jednotce</li><li><strong>Registrace na soutěž</strong> — startovné na sportovní soutěž organizovanou klubem</li><li><strong>Registrace na akci</strong> — vystoupení, workshopy, přednášky a další akce</li><li><strong>Předplatné pro týmy (SaaS)</strong> — softwarové předplatné pro týmy využívající platformu BCZ Club</li></ul><h3>3. Uzavření smlouvy</h3><p>Smlouva mezi provozovatelem a objednavatelem je uzavřena odesláním registračního formuláře nebo objednávky prostřednictvím webové stránky a jejím potvrzením ze strany provozovatele (zpravidla e-mailem). Před odesláním objednávky je objednavatel povinen seznámit se s těmito obchodními podmínkami a vyjádřit s nimi souhlas.</p><h3>4. Ceny a platební podmínky</h3><p>Aktuální ceny členství, startovného a dalších služeb jsou zveřejněny na webové stránce u každé konkrétní služby. Ceny jsou uvedeny v eurech (EUR) nebo českých korunách (CZK) v závislosti na lokalitě a typu platby. Provozovatel není plátcem DPH.</p><p>Objednavatel si může zvolit jeden z následujících způsobů platby:</p><ul><li><strong>Platba kartou přes GoPay</strong> — okamžité zpracování platby přes zabezpečenou platební bránu GoPay s.r.o.</li><li><strong>Bankovní převod</strong> — platba na účet provozovatele s použitím variabilního symbolu uvedeného v pokynech</li><li><strong>Platba v hotovosti</strong> — na místě po dohodě s provozovatelem</li></ul><p>Služba se považuje za uhrazenou dnem připsání platby na účet provozovatele. Při platbě bankovním převodem má objednavatel povinnost uhradit platbu do data splatnosti uvedeného v potvrzovacím e-mailu, jinak může dojít ke zrušení registrace.</p><h3>5. Právo spotřebitele odstoupit od smlouvy</h3><p>V souladu se slovenským zákonem č. 102/2014 Z. z. má objednavatel jako spotřebitel právo odstoupit od smlouvy bez uvedení důvodu ve lhůtě <strong>14 dnů</strong> ode dne uzavření smlouvy. Odstoupení od smlouvy je možné uplatnit písemně na e-mailové adrese info@bczclub.sk.</p><p><strong>Výjimka z práva na odstoupení:</strong> V souladu s § 7 odst. 6 zákona č. 102/2014 Z. z. objednavatel nemůže odstoupit od smlouvy, jejímž předmětem je poskytnutí služeb souvisejících s volnočasovými aktivitami (tréninky, soutěže, akce), pokud se provozovatel zavázal poskytnout tyto služby v dohodnutém čase nebo v dohodnuté lhůtě, a tento čas nebo lhůta již uplynuly, nebo pokud je datum konání akce pevně určeno.</p><p>V případě řádného odstoupení od smlouvy provozovatel vrátí objednavateli všechny platby do 14 dnů ode dne doručení oznámení o odstoupení, stejným způsobem, jakým byly platby přijaty.</p><h3>6. Zrušení registrace a vrácení platby</h3><p>Objednavatel může zrušit svou registraci na trénink, soutěž nebo akci za následujících podmínek:</p><ul><li><strong>Zrušení více než 7 dnů před konáním:</strong> vrácení 100 % uhrazené částky</li><li><strong>Zrušení 2 — 7 dnů před konáním:</strong> vrácení 50 % uhrazené částky (administrativní poplatek)</li><li><strong>Zrušení méně než 48 hodin před konáním:</strong> uhrazená částka se nevrací</li></ul><p>V případě zrušení akce ze strany provozovatele (např. z důvodu nedostatečného počtu účastníků nebo vyšší moci) se objednavateli vrací 100 % uhrazené částky, případně je umožněno přenést platbu na náhradní termín.</p><p>Členský příspěvek je nevratný, pokud není v konkrétním případě dohodnuto jinak.</p><h3>7. Reklamace</h3><p>V případě, že poskytnutá služba neodpovídá popisu nebo má zjevné vady, má objednavatel právo uplatnit reklamaci. Reklamaci je třeba uplatnit bez zbytečného odkladu, nejpozději do 7 dnů ode dne poskytnutí služby, na e-mailové adrese info@bczclub.sk.</p><p>Provozovatel je povinen vyřídit reklamaci nejpozději do 30 dnů ode dne jejího doručení.</p><h3>8. Mimosoudní řešení sporů</h3><p>V případě sporu mezi provozovatelem a objednavatelem má objednavatel právo obrátit se na subjekt alternativního řešení sporů, kterým je Slovenská obchodní inspekce (www.soi.sk). Objednavatel může také využít platformu ODR (Řešení sporů online) dostupnou na adrese ec.europa.eu/consumers/odr.</p><h3>9. Ochrana osobních údajů</h3><p>Zpracování osobních údajů se řídí samostatným dokumentem <em>Ochrana osobních údajů</em>, který je dostupný na webové stránce.</p><h3>10. Závěrečná ustanovení</h3><p>Tyto obchodní podmínky se řídí právním řádem Slovenské republiky. Vztahy neupravené těmito podmínkami se řídí zejména zákonem č. 40/1964 Sb. (občanský zákoník), zákonem č. 102/2014 Z. z. a zákonem č. 250/2007 Z. z. o ochraně spotřebitele.</p><p>Provozovatel si vyhrazuje právo tyto obchodní podmínky měnit. Změny jsou účinné dnem jejich zveřejnění na webové stránce a nemají vliv na smlouvy uzavřené před jejich zveřejněním.</p><p><em>Poslední aktualizace: duben 2026</em></p>',
                ],
            ]),
        ];
    }

    private static function media(string $key): ?string
    {
        return self::$mediaCache[$key] ?? null;
    }

    private static function pageId(string $systemKey): ?string
    {
        return self::$pageCache[$systemKey] ?? null;
    }
}

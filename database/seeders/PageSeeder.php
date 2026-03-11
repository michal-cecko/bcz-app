<?php

namespace Database\Seeders;

use App\Enums\PageStatusEnum;
use App\Models\MediaLibraryItem;
use App\Models\Page;
use App\Models\Sponsor;
use App\Services\MediaLibraryFolderService;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /** @var array<string, string> Cache of uploaded media IDs keyed by slug */
    private static array $mediaCache = [];

    /** @var array<string, string> Cache of page IDs keyed by system_key */
    private static array $pageCache = [];

    private static ?string $webContentFolderId = null;

    public function run(): void
    {
        $folderService = app(MediaLibraryFolderService::class);
        $folder = $folderService->ensureWebContentFolder();
        self::$webContentFolderId = $folder->id;

        self::seedMedia();

        $pages = [
            [
                'system_key' => 'homepage',
                'title' => ['sk' => 'Domov', 'en' => 'Home', 'cz' => 'Domů'],
                'slug' => '/',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 0,
                'content' => fn () => self::homepageContent(),
            ],
            [
                'system_key' => 'about',
                'title' => ['sk' => 'O nás', 'en' => 'About Us', 'cz' => 'O nás'],
                'slug' => 'o-nas',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 1,
                'content' => fn () => self::aboutContent(),
            ],
            [
                'system_key' => 'contact',
                'title' => ['sk' => 'Kontakt', 'en' => 'Contact', 'cz' => 'Kontakt'],
                'slug' => 'kontakt',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 2,
                'content' => fn () => self::contactContent(),
            ],
            [
                'system_key' => 'faq',
                'title' => ['sk' => 'FAQ', 'en' => 'FAQ', 'cz' => 'FAQ'],
                'slug' => 'faq',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 3,
                'content' => fn () => self::faqContent(),
            ],
            [
                'system_key' => 'support',
                'title' => ['sk' => 'Podporte nás', 'en' => 'Support Us', 'cz' => 'Podpořte nás'],
                'slug' => 'podporte-nas',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 4,
                'content' => fn () => self::supportContent(),
            ],
            [
                'system_key' => 'founder',
                'title' => ['sk' => 'Zakladateľ & CEO — Dominik Klimek', 'en' => 'Founder & CEO — Dominik Klimek', 'cz' => 'Zakladatel & CEO — Dominik Klimek'],
                'slug' => 'zakladatel-ceo-dominik-klimek',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 5,
                'content' => fn () => self::founderContent(),
            ],
            [
                'system_key' => 'tax_donation',
                'title' => ['sk' => '2% z dane', 'en' => '2% Tax Donation', 'cz' => '2% z daní'],
                'slug' => 'dva-percenta-z-dane',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 6,
                'content' => fn () => self::taxDonationContent(),
            ],
            [
                'system_key' => 'trainings',
                'title' => ['sk' => 'Tréningy', 'en' => 'Trainings', 'cz' => 'Tréninky'],
                'slug' => 'treningy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 7,
                'content' => [],
            ],
            [
                'system_key' => 'competitions',
                'title' => ['sk' => 'Súťaže', 'en' => 'Competitions', 'cz' => 'Soutěže'],
                'slug' => 'sutaze',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 8,
                'content' => [],
            ],
            [
                'system_key' => 'events',
                'title' => ['sk' => 'Vystúpenia', 'en' => 'Events', 'cz' => 'Vystoupení'],
                'slug' => 'vystupenia',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 9,
                'content' => [],
            ],
            [
                'system_key' => 'services',
                'title' => ['sk' => 'Vystúpenia & Workshopy', 'en' => 'Performances & Workshops', 'cz' => 'Vystoupení & Workshopy'],
                'slug' => 'vystupenia-workshopy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 10,
                'content' => fn () => self::servicesContent(),
            ],
            [
                'system_key' => 'lectures',
                'title' => ['sk' => 'Inšpiratívne Prednášky', 'en' => 'Inspirational Lectures', 'cz' => 'Inspirativní Přednášky'],
                'slug' => 'prednasky',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 11,
                'content' => fn () => self::lecturesContent(),
            ],
            [
                'system_key' => 'workshops',
                'title' => ['sk' => 'Praktické Workshopy', 'en' => 'Practical Workshops', 'cz' => 'Praktické Workshopy'],
                'slug' => 'workshopy',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 12,
                'content' => fn () => self::workshopsContent(),
            ],
            [
                'system_key' => 'parkour',
                'title' => ['sk' => 'Parkour & Freerunning', 'en' => 'Parkour & Freerunning', 'cz' => 'Parkour & Freerunning'],
                'slug' => 'kategoria/parkour-freerunning',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 13,
                'content' => fn () => self::parkourContent(),
            ],
            [
                'system_key' => 'street_workout',
                'title' => ['sk' => 'Street Workout & Kalistenika', 'en' => 'Street Workout & Calisthenics', 'cz' => 'Street Workout & Kalistenika'],
                'slug' => 'kategoria/street-workout',
                'status' => PageStatusEnum::Published,
                'is_system' => true,
                'published_at' => now(),
                'sort_order' => 14,
                'content' => fn () => self::streetWorkoutContent(),
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
                'badge' => ['sk' => 'BEYOND COMFORT ZONE'],
                'title' => ['sk' => 'PREKONAJ'],
                'title_accent' => ['sk' => 'SVOJE LIMITY'],
                'subtitle' => ['sk' => 'Profesionálne tréningy kalisteniky a parkouru, súťaže a vystúpenia.'],
                'background_image' => self::media('hero-bg'),
                'cta_text' => ['sk' => 'ZAČAŤ TRÉNOVAŤ'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('trainings'),
                'secondary_cta_text' => ['sk' => 'POZRIEŤ VIDEO'],
                'secondary_cta_link_type' => 'custom',
                'secondary_cta_link_url' => ['sk' => '#'],
            ]),
            self::brick('sport-categories', [
                'label' => ['sk' => 'ČO ROBÍME'],
                'title' => ['sk' => 'TRI PILIERE BCZ'],
                'subtitle' => ['sk' => 'Súťaže. Tréningy. Vystúpenia.'],
                'categories' => [
                    [
                        'image' => self::media('cat-competitions'),
                        'title' => ['sk' => 'SÚŤAŽE'],
                        'description' => ['sk' => 'Profesionálna účasť na medzinárodných a domácich súťažiach. Organizujeme a propagujeme podujatia, pričom naši aktívni členovia dosahujú výnimočné úspechy.'],
                        'link_text' => ['sk' => 'ZOBRAZIŤ SÚŤAŽE'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('competitions'),
                    ],
                    [
                        'image' => self::media('cat-trainings'),
                        'title' => ['sk' => 'TRÉNINGY'],
                        'description' => ['sk' => 'Súkromné a skupinové tréningy pre všetky úrovne. Parkour & Freerunning, Freestyle a Kalistenika pre dospelých aj deti s certifikovanými trénermi.'],
                        'link_text' => ['sk' => 'PRESKÚMAŤ TRÉNINGY'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('trainings'),
                    ],
                    [
                        'image' => self::media('cat-performances'),
                        'title' => ['sk' => 'VYSTÚPENIA'],
                        'description' => ['sk' => 'Spektakulárne vystúpenia pre školy, škôlky, firmy a verejné podujatia. Dynamické show s profesionálnym vybavením, ktoré inšpirujú a bavia každé publíkum.'],
                        'link_text' => ['sk' => 'OBJEDNAŤ VYSTÚPENIE'],
                        'link_link_type' => 'page',
                        'link_link_model_id' => self::pageId('services'),
                    ],
                ],
            ]),
            self::brick('about-preview', [
                'label' => ['sk' => 'NÁŠ PRÍBEH'],
                'title' => ['sk' => "ZRODENÍ\nZ VÁŠNE"],
                'description' => ['sk' => 'BCZ Club začal ako skupina priateľov, ktorí posúvali hranice a objavovali, čoho je ľudské telo skutočne schopné. Dnes sme profesionálna asociácia venovaná šíreniu pohybovej kultúry prostredníctvom súťaží, svetových tréningov a nezabudnuteľných vystúpení.'],
                'cta_text' => ['sk' => 'PREČÍTAŤ CELÝ PRÍBEH'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('about'),
                'image_main' => self::media('about-main'),
                'image_left' => self::media('about-left'),
                'image_right' => self::media('about-right'),
            ]),
            self::brick('founder-spotlight', [
                'label' => ['sk' => 'ZAKLADATEĽ & CEO'],
                'name_line1' => ['sk' => 'DOMINIK'],
                'name_line2' => ['sk' => 'KLIMEK'],
                'subtitle' => ['sk' => 'Majster sveta v street workoute &middot; Tréner &middot; Mentor'],
                'bio' => ['sk' => 'Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladateľ BCZ Club a jediný certifikovaný master tréner kalisteniky a street workoute na Slovensku. V roku 2022 sa stal majstrom sveta v street workoute v Rige a trikrát po sebe vyhral majstrovstvá Slovenska.'],
                'bio2' => ['sk' => 'Dnes vedie komunitu mladých ľudí, organizuje workshopy po školách a inšpiruje novú generáciu k pohybu a zdravému životnému štýlu. Jeho víziou je ukázať, že disciplína a tvrdá práca dokážu zmeniť životy.'],
                'image' => self::media('founder-img'),
                'stats' => [
                    ['number' => '1x', 'label' => ['sk' => 'Majster sveta']],
                    ['number' => '3x', 'label' => ['sk' => 'Majster SR']],
                    ['number' => 'L4', 'label' => ['sk' => 'Conditioning Coach']],
                    ['number' => '500+', 'label' => ['sk' => 'Mentorovaných detí']],
                ],
                'cta_text' => ['sk' => 'SPOZNAJ DOMINIKA'],
                'cta_link_type' => 'page',
                'cta_link_model_id' => self::pageId('founder'),
            ]),
            self::brick('social-cta', [
                'label' => ['sk' => 'SLEDUJTE NAŠU CESTU'],
                'title' => ['sk' => 'PRIDAJ SA K POHYBU'],
                'description' => ['sk' => 'Sledujte nás na sociálnych sieťach pre tréningové tipy, novinky zo súťaží a obsah zo zákulisia.'],
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
                'title' => ['sk' => 'NÁŠ'],
                'title_accent' => ['sk' => 'PRÍBEH'],
                'subtitle' => ['sk' => 'Od skupiny priateľov posúvajúcich hranice po profesionálnu asociáciu inšpirujúcu ďalšiu generáciu športovcov.'],
                'background_image' => self::media('about-hero-bg'),
                'scroll_text' => ['sk' => 'SCROLLUJ PRE VIAC'],
                'breadcrumb' => [
                    ['text' => ['sk' => 'DOMOV', 'en' => 'HOME', 'cz' => 'DOMŮ'], 'url' => '/'],
                    ['text' => ['sk' => 'O NÁS', 'en' => 'ABOUT US', 'cz' => 'O NÁS']],
                ],
            ]),
            self::brick('about-preview', [
                'label' => ['sk' => 'AKO TO VŠETKO ZAČALO'],
                'title' => ['sk' => "Z ULÍC\nNA PÓDIA"],
                'description' => ['sk' => 'Všetko to začalo v roku 2015, keď malá skupina priateľov objavila parkour cez online videá. To, čo začalo ako neformálne stretnutia v miestnych parkoch, sa rýchlo vyvinulo v niečo omnoho väčšie.'],
                'image_main' => self::media('about-story-main'),
                'image_caption' => ['sk' => 'Prvé dni tréningu na uliciach, 2016'],
            ]),
            self::brick('timeline', [
                'items' => [
                    ['year' => '2015', 'title' => ['sk' => 'Začiatok'], 'description' => ['sk' => 'Prvé neoficiálne stretnutia v miestnych parkoch. Len priatelia, ktorí sa zabávajú a učia sa spolu.']],
                    ['year' => '2017', 'title' => ['sk' => 'Prvá súťaž'], 'description' => ['sk' => 'Náš tím sa zúčastnil prvej národnej parkorovej súťaže. Nevyhrali sme, ale naučili sme sa.']],
                    ['year' => '2019', 'title' => ['sk' => 'Oficiálna asociácia'], 'description' => ['sk' => 'BCZ Club sa stal oficiálnou neziskovou organizáciou. Začali sme naše prvé tréningové programy.']],
                    ['year' => '2024', 'title' => ['sk' => 'Dnes a ďalej'], 'description' => ['sk' => 'Medzinárodné súťaže, profesionálne tréningy, vystúpenia po celej krajine. Cesta pokračuje.']],
                ],
            ]),
            self::brick('person-cards', [
                'label' => ['sk' => 'ĽUDIA'],
                'title' => ['sk' => 'SPOZNAJTE NAŠICH ŠPORTOVCOV'],
                'subtitle' => ['sk' => 'Talentovaní jednotlivci, ktorí reprezentujú BCZ Club na súťažiach po celom svete.'],
                'people' => [
                    ['image' => self::media('person-dominik'), 'name' => ['sk' => 'DOMINIK KLIMEK'], 'role' => ['sk' => 'Zakladateľ & Športovec'], 'description' => ['sk' => '10+ rokov v parkour. Viaceré medaily z národných majstrovstiev. Špecializuje sa na freestyle a flow.']],
                    ['image' => self::media('person-michal'), 'name' => ['sk' => 'MICHAL ČEČKO'], 'role' => ['sk' => 'Spoluzakladateľ & Športovec'], 'description' => ['sk' => 'Freerunning špecialista s medzinárodnými skúsenosťami zo súťaží. Známy kreatívnymi a technickými pohybmi.']],
                    ['image' => self::media('person-member1'), 'name' => ['sk' => 'ČLEN TÍMU'], 'role' => ['sk' => 'Súťažný športovec'], 'description' => ['sk' => 'Stúpajúci talent na kalistenickej scéne. Súťaží na národných aj medzinárodných podujatiach.']],
                    ['image' => self::media('person-member2'), 'name' => ['sk' => 'ČLEN TÍMU'], 'role' => ['sk' => 'Súťažný športovec'], 'description' => ['sk' => 'Prináša silu a eleganciu do nášho tímu. Zameriava sa na freestyle a akrobatické pohyby.']],
                ],
            ]),
            self::brick('person-cards', [
                'label' => ['sk' => 'UČ SA OD NAJLEPŠÍCH'],
                'title' => ['sk' => 'NAŠI TRÉNERI'],
                'subtitle' => ['sk' => 'Certifikovaní profesionáli oddaní pomáhať ti dosiahnuť tvoj plný potenciál.'],
                'people' => [
                    ['image' => self::media('trainer-1'), 'name' => ['sk' => 'MENO TRÉNERA'], 'role' => ['sk' => 'Hlavný tréner - Parkour & Freerunning'], 'description' => ['sk' => 'Certifikovaný inštruktor parkouru s 8+ rokmi učiteľských skúseností. Špecializuje sa na progresiu od začiatočníkov po pokročilých a bezpečnú tréningovú metodológiu.'], 'tags' => ['ADAPT Level 2', 'First Aid']],
                    ['image' => self::media('trainer-2'), 'name' => ['sk' => 'MENO TRÉNERA'], 'role' => ['sk' => 'Tréner - Kalistenika & Sila'], 'description' => ['sk' => 'Expert na tréning s vlastnou váhou a rozvoj sily. Pomáha športovcom všetkých úrovní budovať funkčnú silu a dosahovať ich fitness ciele.'], 'tags' => ['Personal Trainer', 'Nutrition']],
                ],
            ]),
            self::brick('feature-cards', [
                'label' => ['sk' => 'ZA ČÍM SI STOJÍME'],
                'title' => ['sk' => 'NAŠE HODNOTY'],
                'cards' => [
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'VÁŠEŇ'], 'description' => ['sk' => 'Všetko čo robíme vychádza z hlbokej lásky k pohybu. Táto vášeň nás poháňa posúvať hranice a inšpirovať ostatných.']],
                    ['icon' => 'heroicon-o-user-group', 'title' => ['sk' => 'KOMUNITA'], 'description' => ['sk' => 'Sme silnejší spolu. Naša komunita sa navzájom podporuje, motivúje a oslavuje úspechy každého člena.']],
                    ['icon' => 'heroicon-o-shield-check', 'title' => ['sk' => 'BEZPEČNOSŤ'], 'description' => ['sk' => 'Progres cez správnu techniku a kalkulované riziko. Veríme v inteligentný tréning, ktorý minimalizuje zranenia a maximalizuje rast.']],
                    ['icon' => 'heroicon-o-arrow-trending-up', 'title' => ['sk' => 'RAST'], 'description' => ['sk' => 'Každý deň je príležitosťou na zlepšenie. Prijímame výzvy a vnímame zlyhania ako odrazové mostíky k úspechu.']],
                ],
            ]),
            self::brick('gallery', [
                'label' => ['sk' => 'MOMENTY'],
                'title' => ['sk' => 'FOTOGALÉRIA'],
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
                'badge' => ['sk' => 'KONTAKT', 'en' => 'CONTACT', 'cz' => 'KONTAKT'],
                'title' => ['sk' => 'Napíšte nám', 'en' => 'Write to us', 'cz' => 'Napište nám'],
                'subtitle' => ['sk' => 'Máte otázku, chcete si dohodnúť tréning alebo spoluprácu? Sme tu pre vás.'],
            ]),
            self::brick('contact-form', [
                'heading' => ['sk' => 'Kontaktný formulár', 'en' => 'Contact form', 'cz' => 'Kontaktní formulář'],
                'show_reason' => true,
                'show_phone' => true,
                'contact_email' => 'info@bfreak.sk',
                'contact_phone' => '+421 900 000 000',
                'contact_location' => 'Žilina, Slovensko',
                'response_text' => 'Zvyčajne odpovedáme do 24 hodín. Pre urgentné záležitosti nás kontaktujte telefonicky.',
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Najčastejšie otázky'],
                'faq_ids' => \App\Models\Faq::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->limit(3)
                    ->pluck('id')
                    ->all(),
                'link_link_type' => 'page',
                'link_link_model_id' => self::pageId('faq'),
                'link_text' => ['sk' => 'Zobraziť všetky často kladené otázky'],
            ]),
        ];
    }

    private static function faqContent(): array
    {
        return [
            self::brick('hero', [
                'layout' => 'centered',
                'title' => ['sk' => 'Často kladené otázky'],
                'subtitle' => ['sk' => 'Nájdite odpovede na najčastejšie otázky o našich tréningoch, vystúpeniach a workshopoch'],
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Všetky otázky'],
                'show_all' => true,
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Nenašli ste odpoveď?'],
                'description' => ['sk' => 'Kontaktujte nás a radi vám pomôžeme.'],
                'button_text' => ['sk' => 'Kontaktovať nás'],
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
                'title' => ['sk' => 'Pomôžte nám rásť'],
                'subtitle' => ['sk' => 'Vaša podpora nám pomáha rozvíjať komunitu a poskytovať kvalitné tréningy pre všetkých. Každý dar má zmysel.'],
                'cta_text' => ['sk' => 'Darovať'],
                'cta_link_type' => 'custom',
                'cta_link_url' => ['sk' => '#bank'],
            ]),
            self::brick('donation-info', [
                'bank_title' => ['sk' => 'Bankový prevod'],
                'bank_rows' => [
                    ['label' => ['sk' => 'Názov organizácie'], 'value' => ['sk' => 'BCZ Club, občianske združenie']],
                    ['label' => ['sk' => 'IČO'], 'value' => ['sk' => '52 841 235']],
                    ['label' => ['sk' => 'IBAN'], 'value' => ['sk' => 'SK89 0900 0000 0051 8742 6513']],
                    ['label' => ['sk' => 'SWIFT/BIC'], 'value' => ['sk' => 'GIBASKBX']],
                    ['label' => ['sk' => 'Banka'], 'value' => ['sk' => 'Slovenská sporiteľňa, a.s.']],
                    ['label' => ['sk' => 'Variabilný symbol'], 'value' => ['sk' => date('Y').' (aktuálny rok)']],
                ],
                'qr_title' => ['sk' => 'Naskenujte QR kód'],
                'qr_description' => ['sk' => 'Použite mobilnú aplikáciu vašej banky na rýchlu platbu. QR kód obsahuje všetky potrebné údaje.'],
                'iban' => ['sk' => 'SK8909000000005187426513', 'cs' => 'SK8909000000005187426513'],
                'qr_recipient_name' => ['sk' => 'BCZ Club, občianske združenie', 'cs' => 'BCZ Club, občianske združenie'],
                'qr_format' => ['sk' => 'pay_by_square', 'cs' => 'pay_by_square'],
                'usage_title' => ['sk' => 'Na čo využívame dary'],
                'usage_description' => ['sk' => 'Všetky získané prostriedky využívame transparentne na rozvoj našej komunity a zlepšovanie tréningových podmienok.'],
                'usage_items' => [
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'color' => '#FF2D2D', 'title' => ['sk' => 'Cvičebné pomôcky'], 'description' => ['sk' => 'Nákup nových podložiek, odporových gúm, švihadiel a ďalšieho vybavenia pre tréningy.']],
                    ['icon' => 'heroicon-o-squares-2x2', 'color' => '#3B82F6', 'title' => ['sk' => 'Hrazdy a bradlá'], 'description' => ['sk' => 'Inštalácia a údržba street workout prvkov v Čadci a okolí.']],
                    ['icon' => 'heroicon-o-shield-check', 'color' => '#8B5CF6', 'title' => ['sk' => 'Bezpečnostné vybavenie'], 'description' => ['sk' => 'Crash pady, žinenky a ochranné pomôcky pre bezpečný tréning akrobacie.']],
                    ['icon' => 'heroicon-o-calendar-days', 'color' => '#F59E0B', 'title' => ['sk' => 'Workshopy a podujatia'], 'description' => ['sk' => 'Organizácia bezplatných workshopov a podujatí pre verejnosť.']],
                ],
                'tax_title' => ['sk' => 'Darujte nám 2% z dane'],
                'tax_description' => ['sk' => 'Darovaním 2% z dane nám pomôžete bez toho, aby vás to stálo čokoľvek navyše. Tieto prostriedky idú priamo na rozvoj našich aktivít.'],
                'tax_link_type' => 'page',
                'tax_link_model_id' => self::pageId('tax_donation'),
                'tax_button_text' => ['sk' => 'Zistiť viac o 2% z dane'],
                'contact_title' => ['sk' => 'Kontaktujte nás'],
                'contact_description' => ['sk' => 'Máte otázky ohľadom darovania alebo spolupráce? Neváhajte nás kontaktovať.'],
                'contact_email' => 'podpora@bczclub.sk',
                'contact_phone' => '+421 907 123 456',
                'contact_address' => 'Palárikova 123, 022 01 Čadca',
            ]),
            self::brick('stats', [
                'badge' => ['sk' => 'TRANSPARENTNOSŤ'],
                'badge_color' => '#22C55E',
                'title' => ['sk' => 'Zaväzujeme sa k transparentnosti'],
                'description' => ['sk' => 'Každý rok zverejňujeme výročnú správu o hospodárení, kde nájdete podrobný prehľad o využití všetkých finančných prostriedkov.'],
                'background_color' => '#0D0D0D',
                'items' => [
                    ['number' => '100%', 'color' => '#22C55E', 'label' => ['sk' => 'Využité na rozvoj']],
                    ['number' => '0%', 'color' => '#FF2D2D', 'label' => ['sk' => 'Administratívne náklady']],
                    ['number' => '3+', 'color' => '#3B82F6', 'label' => ['sk' => 'Roky transparentnosti']],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Každý dar má zmysel'],
                'description' => ['sk' => 'Aj malá suma pomáha. Ďakujeme, že ste súčasťou našej komunity.'],
                'button_text' => ['sk' => 'Darovať teraz'],
                'button_icon' => 'heroicon-o-heart',
                'button_link_type' => 'custom',
                'button_link_url' => '#bank',
                'secondary_text' => ['sk' => 'Kontaktovať nás'],
                'secondary_link_type' => 'page',
                'secondary_link_model_id' => self::pageId('contact'),
                'background_color' => '#0A0A0A',
            ]),
        ];
    }

    private static function founderContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Dominik Klimek'],
                'subtitle' => ['sk' => 'Majster sveta v street workoute · Master tréner · Zakladateľ BCZ Club'],
            ]),
            self::brick('stats', [
                'items' => [
                    ['number' => '1x', 'label' => ['sk' => 'Majster sveta']],
                    ['number' => '3x', 'label' => ['sk' => 'Majster SR']],
                    ['number' => 'L4', 'label' => ['sk' => 'S&C Coach']],
                    ['number' => '500+', 'label' => ['sk' => 'Mentorovaných detí']],
                    ['number' => '30+', 'label' => ['sk' => 'Krajiny']],
                ],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<h2>OD DETÍNSKYCH SNOV K TITULU MAJSTRA SVETA</h2><p>Od detstva som hľadal svoju cestu cez futbal, volejbal, parkour, klavír aj šachovú ligu. Nič ma však nenaplnilo tak ako street workout, ktorý som objavil, keď som uvidel chlapa robiť variace na hrázdach na ihrisku. Behom niekoľkých mesiacov som zvládol zadnú páku a vedel som — toto je to.</p><p>V roku 2019 som súťažil prvýkrát na majstrovstvách v Žiline a nepostúpil som ani z kvalifikácie. O tri roky neskôr som však stál na najvyššom stupienku na Majstrovstvách sveta v Rige ako majster sveta v strednej váhovej kategórii.</p>'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['title' => ['sk' => '2022 — Majster sveta'], 'description' => ['sk' => 'WSWCF World Championship, Riga, Lotyšsko. Stredná váha (68-80 kg) — 1. MIESTO']],
                    ['title' => ['sk' => '2020–2022 — 3x Majster SR'], 'description' => ['sk' => 'Tri po sebe idúce tituly majstra Slovenska — ZLATO']],
                    ['title' => ['sk' => '2021 — MS Moskva'], 'description' => ['sk' => 'Majstrovstvá sveta, Moskva, Rusko. Kvalifikácia 8. / Finále 7. — TOP 10']],
                    ['title' => ['sk' => '2019 — Vicemajster SR'], 'description' => ['sk' => 'Majstrovstvá Slovenska, Trenčín. 2. miesto — STRIEBRO']],
                    ['title' => ['sk' => '2019 — SW Games Brno'], 'description' => ['sk' => 'Street Workout Games, Brno, Česko. Víťaz — 1. MIESTO']],
                    ['title' => ['sk' => '2022 — Svetový pohár'], 'description' => ['sk' => 'WSWCF World Cup, Jurmala, Lotyšsko. Striebro — STRIEBRO']],
                ],
            ]),
            self::brick('timeline', [
                'items' => [
                    ['year' => '2017', 'title' => ['sk' => 'Objav street workoute'], 'description' => ['sk' => 'Prvý kontakt s kalistenikou na ihrisku. Začiatok samoukého tréningu a nekončiace sa hodiny na hrázdach.']],
                    ['year' => '2019', 'title' => ['sk' => 'Prvé súťaže a vicemajster SR'], 'description' => ['sk' => 'Prvá účasť na majstrovstvách SR v Žiline, nepostúpil z kvalifikácie. O niekoľko mesiacov neskôr už 2. miesto na SR v Trenčíne. Víťazstvo na SW Games Brno.']],
                    ['year' => '2020', 'title' => ['sk' => 'Založenie Street Workout Kysuce'], 'description' => ['sk' => 'Prvý titul majstra Slovenska. Založenie občianskeho združenia Street Workout Kysuce, dnešného BCZ Club.']],
                    ['year' => '2022', 'title' => ['sk' => 'Majster sveta v Rige'], 'description' => ['sk' => 'Titul majstra sveta v strednej váhovej kategórii na MS v Rige. Na tej istej súťaži získali medaily aj bratia Matej (zlato ľahká váha) a Daniel (striebro).']],
                    ['year' => '2024', 'title' => ['sk' => 'Medzinárodný tréner a porotca'], 'description' => ['sk' => 'Prestávka od súťaženia. Zameranie na koučšing, medzinárodné workshopy (Hong Kong, Uzbekistan, Švajčiarsko) a porotcovanie na MS v Hong Kongu.']],
                ],
            ]),
            self::brick('quote', [
                'quote' => ['sk' => 'Chcem pomáhať rozvíjať street workout na Slovensku aj vo svete a zároveň inšpirovať ostatných, aby nasledovali svoje sny.'],
                'attribution' => ['sk' => 'Dominik Klimek'],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Sledujte Dominika'],
                'description' => ['sk' => '@dodoworkout na Instagrame, YouTube a TikToku'],
                'button_text' => ['sk' => 'Kontaktovať'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('contact'),
                'background_color' => '#1f2937',
            ]),
        ];
    }

    private static function taxDonationContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Darujte nám 2% z dane'],
                'subtitle' => ['sk' => 'Ak sa vám páči naša činnosť a ciele, môžete nás podporiť darovaním 2% z vašej dane. Nestojí vás to nič navyše — tieto peniaze by inak išli štátu.'],
            ]),
            self::brick('table', [
                'headers' => [
                    ['label' => ['sk' => 'Údaj']],
                    ['label' => ['sk' => 'Hodnota']],
                ],
                'rows' => [
                    ['cells' => [['value' => ['sk' => 'Obchodné meno']], ['value' => ['sk' => 'BCZ Club, občianske združenie']]]],
                    ['cells' => [['value' => ['sk' => 'Sídlo']], ['value' => ['sk' => 'Palárikova 123, 022 01 Čadca']]]],
                    ['cells' => [['value' => ['sk' => 'IČO']], ['value' => ['sk' => '52 841 235']]]],
                    ['cells' => [['value' => ['sk' => 'Právna forma']], ['value' => ['sk' => 'Občianske združenie']]]],
                ],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-briefcase', 'title' => ['sk' => 'Zamestnanci'], 'description' => ['sk' => '1. Požiadajte zamestnávateľa o Potvrdenie o zaplatení dane. 2. Vyplňte Vyhlásenie o poukázaní 2% dane. 3. Obe tlačivá doručte na daňový úrad do 30. apríla.']],
                    ['icon' => 'heroicon-o-user', 'title' => ['sk' => 'Fyzické osoby / SZČO'], 'description' => ['sk' => '1. V daňovom priznaní vyplňte oddiel na 2%. 2. Uveďte naše IČO a názov. 3. Podajte do 31. marca.']],
                    ['icon' => 'heroicon-o-building-office-2', 'title' => ['sk' => 'Právnické osoby'], 'description' => ['sk' => '1. V priznaní PO vyplňte príslušnú časť. 2. Môžete uviesť aj viacerých prijímateľov. 3. Termín: 31. marca (resp. v predĺženej lehote).']],
                ],
            ]),
            self::brick('faq', [
                'heading' => ['sk' => 'Často kladené otázky'],
                'show_all' => true,
                'faq_ids' => [],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Vaše 2% pomáhajú'],
                'description' => ['sk' => 'Pomáhajú rozvíjať parkour, street workout a calisthenics komunitu na Slovensku.'],
                'button_text' => ['sk' => 'Podporte nás priamo'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('support'),
                'background_color' => '#dc2626',
            ]),
        ];
    }

    private static function servicesContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Vystúpenia, Workshopy & Prednášky'],
                'subtitle' => ['sk' => 'Prinášame akrobatické umenie, inšpiratívne prednášky a praktické workshopy pre vaše podujatia, školy a fitness centrá.'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-sparkles', 'title' => ['sk' => 'Vystúpenia'], 'description' => ['sk' => 'Dynamické akrobatické show pre firemné eventy, festivaly, otvorenia a špeciálne príležitosti. Kombinujeme parkour, freerunning a akrobaciu do nezabudnuteľného vizuálneho zážitku.']],
                    ['icon' => 'heroicon-o-microphone', 'title' => ['sk' => 'Prednášky'], 'description' => ['sk' => 'Motivačné prednášky o správnom nastavení mysle, hodnotových rebríčkoch a výhodách cvičenia. Učíme mladých ľudí trpezlivosti, tvrdej drine a vytrvalosti cez náš príbeh.']],
                    ['icon' => 'heroicon-o-academic-cap', 'title' => ['sk' => 'Workshopy'], 'description' => ['sk' => 'Workshopy pre fitness centrá, trénerov a podujatia. Učíme základné aj pokročilé prvky — od bezpečného pádu až po kurz stojky.']],
                ],
            ]),
            self::brick('numbered-steps', [
                'steps' => [
                    ['title' => ['sk' => 'Kontakt'], 'description' => ['sk' => 'Napíšte nám o vašej akcii a predstavách.']],
                    ['title' => ['sk' => 'Konzultácia'], 'description' => ['sk' => 'Spoločne navrhneme najlepší formát pre vás.']],
                    ['title' => ['sk' => 'Príprava'], 'description' => ['sk' => 'Pripravíme choreografiu a program na mieru.']],
                    ['title' => ['sk' => 'Realizácia'], 'description' => ['sk' => 'Profesionálne vystúpenie na vašej akcii.']],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Zaujali sme vás?'],
                'description' => ['sk' => 'Kontaktujte nás a dohodneme si podrobnosti vašej akcie.'],
                'button_text' => ['sk' => 'Kontaktovať nás'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('contact'),
                'background_color' => '#dc2626',
            ]),
            self::brick('contact-form', [
                'show_reason' => true,
                'show_phone' => true,
            ]),
        ];
    }

    private static function lecturesContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'INŠPIRATÍVNE PREDNÁŠKY'],
                'subtitle' => ['sk' => 'Motivačné prednášky pre školy, firmy a organizácie. Inšpirujeme mladých ľudí príbehom o disciplíne, vytrvalosti a sile pohybu.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Naše prednášky sú viac než len slová. Sú to skutočné príbehy členov BCZ Clubu, ktorí prostredníctvom street workoutu a kalisteniky objavili silu disciplíny, trpezlivosti a vytrvalosti.</p><p>Prednášame na školách, v firmách aj na konferenciách. Učíme mladých ľudí, že cesta k úspechu vedie cez tvrdú prácu, správne nastavenie mysle a zdravý životný štýl.</p>'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-light-bulb', 'title' => ['sk' => 'Správne Nastavenie Mysle'], 'description' => ['sk' => 'Growth mindset a pozitívne myslenie. Ako zmeniť pohľad na prekážky a premeniť ich na príležitosti.']],
                    ['icon' => 'heroicon-o-bolt', 'title' => ['sk' => 'Hodnota Disciplíny'], 'description' => ['sk' => 'Prečo je disciplína základom úspechu. Denné návyky a rutiny, ktoré formujú charakter a budujú odolnosť.']],
                    ['icon' => 'heroicon-o-heart', 'title' => ['sk' => 'Sila Pohybu'], 'description' => ['sk' => 'Fyzická aktivita ako nástroj osobného rastu. Benefity cvičenia pre telo aj myseľ.']],
                    ['icon' => 'heroicon-o-star', 'title' => ['sk' => 'Od Sna k Realite'], 'description' => ['sk' => 'Ako premeniť víziu na skutočnosť. Príbeh BCZ Clubu od garážových tréningov po celoslovenské vystúpenia a medzinárodné súťaže.']],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'Prineste Inšpiráciu Do Vašej Školy'],
                'description' => ['sk' => 'Kontaktujte nás pre nezáväznú konzultáciu o prednáške na vašej škole alebo podujatí.'],
                'button_text' => ['sk' => 'Kontaktovať nás'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('contact'),
                'background_color' => '#dc2626',
            ]),
        ];
    }

    private static function workshopsContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'PRAKTICKÉ WORKSHOPY'],
                'subtitle' => ['sk' => 'Učíme základné aj pokročilé prvky kalisteniky — od bezpečného pádu až po kurz stojky. Prispôsobíme sa vašej úrovni.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Naše workshopy sú určené pre fitness centrá, trénerov, školy a podujatia. Každý workshop je vedený certifikovaným trénerom s medzinárodnými skúsenosťami.</p>'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-hand-raised', 'title' => ['sk' => 'Kurz Stojky'], 'description' => ['sk' => 'Od základnej prípravy až po voľnú stojku. Naučíme vás správnu techniku, posilnenie jadra a progresiu krok za krokom.']],
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'Základy Kalisteniky'], 'description' => ['sk' => 'Zhyby, kliky, dipy a ich variácie. Správna forma, progresie a zostava tréningového plánu.']],
                    ['icon' => 'heroicon-o-shield-check', 'title' => ['sk' => 'Bezpečný Pád'], 'description' => ['sk' => 'Techniky bezpečného pádu a základov parkour rolľov. Nevyhnutné pre každého, kto chce začať.']],
                    ['icon' => 'heroicon-o-arrow-trending-up', 'title' => ['sk' => 'Pokročilé Prvky'], 'description' => ['sk' => 'Muscle-up, front lever, planche a ďalšie. Pre tých, čo už ovládajú základy.']],
                ],
            ]),
            self::brick('numbered-steps', [
                'steps' => [
                    ['title' => ['sk' => 'Úvod & Rozohriatie'], 'description' => ['sk' => 'Zoznámenie s účastníkmi, stanovenie cieľov a dôkladné rozohriatie tela.']],
                    ['title' => ['sk' => 'Technika & Progresie'], 'description' => ['sk' => 'Detailný rozklad cvikov, správna forma a individuálne progresie.']],
                    ['title' => ['sk' => 'Prax & Feedback'], 'description' => ['sk' => 'Praktické precvičovanie s osobným feedbackom trénera.']],
                    ['title' => ['sk' => 'Plán & Materiály'], 'description' => ['sk' => 'Na záver dostanete tréningový plán a materiály na ďalšie samostatné cvičenie.']],
                ],
            ]),
            self::brick('stats', [
                'items' => [
                    ['number' => '80+', 'label' => ['sk' => 'Workshopov']],
                    ['number' => '2000+', 'label' => ['sk' => 'Účastníkov']],
                    ['number' => '15+', 'label' => ['sk' => 'Krajín']],
                    ['number' => '5', 'label' => ['sk' => 'Typov workshopov']],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'OBJEDNAJTE SI WORKSHOP'],
                'description' => ['sk' => 'Kontaktujte nás a dohodneme si termín a obsah workshopu na mieru.'],
                'button_text' => ['sk' => 'Kontaktovať nás'],
                'button_link_type' => 'page',
                'button_link_model_id' => self::pageId('contact'),
                'background_color' => '#dc2626',
            ]),
        ];
    }

    private static function parkourContent(): array
    {
        return [
            self::brick('hero', [
                'title' => ['sk' => 'Parkour & Freerunning'],
                'subtitle' => ['sk' => 'Umenie pohybu. Sloboda bez hraníc.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Parkour je disciplína, ktorá mení spôsob, akým vnímaš svet okolo seba. Každá stena, zábradlie či lavička sa stáva príležitosťou. Každá prekážka výzvou, ktorú môžeš prekonať.</p><p>Vznikol vo Francúzsku v 80. rokoch a od vtedy sa rozšíril po celom svete. Nie je to len šport — je to filozofia efektívneho pohybu, kde sa učíš prekonávať fyzické aj mentálne bariéry.</p>'],
            ]),
            self::brick('quote', [
                'quote' => ['sk' => 'Byť silný, aby si bol užitočný.'],
                'attribution' => ['sk' => 'David Belle, zakladateľ Parkouru'],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-globe-alt', 'title' => ['sk' => 'Bez pravidiel'], 'description' => ['sk' => 'Žiadne ihriská, žiadne vymedzené zóny. Celé mesto je tvoje ihrisko.']],
                    ['icon' => 'heroicon-o-bolt', 'title' => ['sk' => 'Mentálna sila'], 'description' => ['sk' => 'Prekonávaš nielen fyzické prekážky, ale aj strach. Učíš sa dôverovať svojmu telu.']],
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'Fyzická kondícia'], 'description' => ['sk' => 'Komplexný tréning celého tela. Sila, vytrvalosť, flexibilita a koordinácia.']],
                    ['icon' => 'heroicon-o-user-group', 'title' => ['sk' => 'Komunita'], 'description' => ['sk' => 'Parkour spája ľudí z celého sveta. Zdieľaš progres, motivúješ sa navzájom.']],
                ],
            ]),
            self::brick('skill-cards', [
                'levels' => [
                    [
                        'name' => ['sk' => 'ZÁKLADY'],
                        'color' => '#22c55e',
                        'cards' => [
                            ['title' => ['sk' => 'Safety Roll'], 'description' => ['sk' => 'Kotúľ — základ bezpečného dopadu.']],
                            ['title' => ['sk' => 'Precision Jump'], 'description' => ['sk' => 'Presný skok na cieľ.']],
                            ['title' => ['sk' => 'Cat Leap'], 'description' => ['sk' => 'Arm Jump — skok a zachytenie sa o hranu.']],
                            ['title' => ['sk' => 'Balance'], 'description' => ['sk' => 'Rovnováha — chôdza po úzkych plochách.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'STREDNÉ (Vaults)'],
                        'color' => '#3b82f6',
                        'cards' => [
                            ['title' => ['sk' => 'Speed Vault'], 'description' => ['sk' => 'Rýchly prechod cez prekážku jednou rukou.']],
                            ['title' => ['sk' => 'Kong Vault'], 'description' => ['sk' => 'Preskok cez prekážku s oporou oboch rúk.']],
                            ['title' => ['sk' => 'Dash Vault'], 'description' => ['sk' => 'Preskok nohami vpred cez prekážku.']],
                            ['title' => ['sk' => 'Wall Run'], 'description' => ['sk' => 'Beh po stene do výšky.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'POKROČILÉ (Freerunning & Flips)'],
                        'color' => '#f59e0b',
                        'cards' => [
                            ['title' => ['sk' => 'Front Flip'], 'description' => ['sk' => 'Salto vpred.']],
                            ['title' => ['sk' => 'Backflip'], 'description' => ['sk' => 'Salto vzad.']],
                            ['title' => ['sk' => 'Sideflip'], 'description' => ['sk' => 'Bočné salto.']],
                            ['title' => ['sk' => 'Webster'], 'description' => ['sk' => 'Salto vpred z jednej nohy.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'EXPERT'],
                        'color' => '#ef4444',
                        'cards' => [
                            ['title' => ['sk' => 'Gainer'], 'description' => ['sk' => 'Backflip z rozbehu s pohybom vpred.']],
                            ['title' => ['sk' => 'Double Flip'], 'description' => ['sk' => 'Dvojité salto.']],
                            ['title' => ['sk' => 'Cork'], 'description' => ['sk' => 'Corkscrew — rotácia s twistom.']],
                            ['title' => ['sk' => 'Wall Spin'], 'description' => ['sk' => 'Rotácia o stenu.']],
                        ],
                    ],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'ZAČNI S PARKOUROM'],
                'description' => ['sk' => 'Naše tréningy sú vhodné pre všetky úrovne — od úplných začiatočníkov po pokročilých.'],
                'button_text' => ['sk' => 'Pozrieť tréningy'],
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
                'title' => ['sk' => 'Street Workout & Kalistenika'],
                'subtitle' => ['sk' => 'Ovládni svoje telo. Ovládni gravitáciu.'],
            ]),
            self::brick('rich-text', [
                'content' => ['sk' => '<p>Street workout, známy aj ako kalistenika, je forma silového tréningu využívajúca vlastnú váhu tela. Cvičíš na hrazdách, bradlách a iných zariadeniach — vonku, v parkoch, kdekoľvek.</p><p>Kombinuje silu, vytrvalosť a estetiku pohybu. Od základných cvikov ako zhyby a kliky, až po pokročilé prvky ako front lever, planche či muscle up.</p>'],
            ]),
            self::brick('quote', [
                'quote' => ['sk' => 'Tvoje telo je tvojou posiľňovňou. Jediné čo potrebuješ, je vôľa začať.'],
                'attribution' => ['sk' => ''],
            ]),
            self::brick('feature-cards', [
                'cards' => [
                    ['icon' => 'heroicon-o-bolt', 'title' => ['sk' => 'Čistá sila'], 'description' => ['sk' => 'Vybuduješ funkčnú silu bez strojov a závaží. Tvoje telo je jediné náradie.']],
                    ['icon' => 'heroicon-o-scale', 'title' => ['sk' => 'Rovnováha'], 'description' => ['sk' => 'Naučíš sa ovládať svoje telo v náročných pozíciách.']],
                    ['icon' => 'heroicon-o-fire', 'title' => ['sk' => 'Vytrvalosť'], 'description' => ['sk' => 'High-rep sety a kombinácie cvikov ti dajú vytrvalosť.']],
                    ['icon' => 'heroicon-o-sparkles', 'title' => ['sk' => 'Estetika'], 'description' => ['sk' => 'Statické prvky ako planche či front lever nie sú len o sile — sú to diela pohybového umenia.']],
                ],
            ]),
            self::brick('skill-cards', [
                'levels' => [
                    [
                        'name' => ['sk' => 'ZÁKLADY'],
                        'color' => '#22c55e',
                        'cards' => [
                            ['title' => ['sk' => 'Pull-up'], 'description' => ['sk' => 'Zhyb — základný ťahový cvik.']],
                            ['title' => ['sk' => 'Dip'], 'description' => ['sk' => 'Klik na bradlách.']],
                            ['title' => ['sk' => 'Push-up'], 'description' => ['sk' => 'Klik — základ tlakových cvikov.']],
                            ['title' => ['sk' => 'Australian Pull-up'], 'description' => ['sk' => 'Horizontálny zhyb pre začiatočníkov.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'STREDNÉ'],
                        'color' => '#3b82f6',
                        'cards' => [
                            ['title' => ['sk' => 'Muscle-up'], 'description' => ['sk' => 'Kombinácia zhybu a tlaku nad hrazdu.']],
                            ['title' => ['sk' => 'L-sit'], 'description' => ['sk' => 'Statický sed s nohami v uhle 90°.']],
                            ['title' => ['sk' => 'Handstand'], 'description' => ['sk' => 'Stojka na rukách.']],
                            ['title' => ['sk' => 'Pistol Squat'], 'description' => ['sk' => 'Drep na jednej nohe.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'POKROČILÉ'],
                        'color' => '#f59e0b',
                        'cards' => [
                            ['title' => ['sk' => 'Front Lever'], 'description' => ['sk' => 'Horizontálna poloha na hrazde tvárou nahor.']],
                            ['title' => ['sk' => 'Back Lever'], 'description' => ['sk' => 'Horizontálna poloha na hrazde tvárou nadol.']],
                            ['title' => ['sk' => 'Planche'], 'description' => ['sk' => 'Horizontálny stoj na rukách.']],
                            ['title' => ['sk' => 'Human Flag'], 'description' => ['sk' => 'Ľudská vlajka — bočný vodorovný výdrž na tyči.']],
                        ],
                    ],
                    [
                        'name' => ['sk' => 'EXPERT'],
                        'color' => '#ef4444',
                        'cards' => [
                            ['title' => ['sk' => 'Iron Cross'], 'description' => ['sk' => 'Železný kríž na kruhoch.']],
                            ['title' => ['sk' => 'Victorian'], 'description' => ['sk' => 'Pokročilá variácia front leveru.']],
                            ['title' => ['sk' => 'Full Planche'], 'description' => ['sk' => 'Planche s nohami úplne vystreté.']],
                            ['title' => ['sk' => 'One Arm Pull-up'], 'description' => ['sk' => 'Zhyb na jednej ruke.']],
                        ],
                    ],
                ],
            ]),
            self::brick('cta', [
                'title' => ['sk' => 'POSUŇ SVOJE LIMITY'],
                'description' => ['sk' => 'Naše tréningy sú vhodné pre všetky úrovne.'],
                'button_text' => ['sk' => 'Pozrieť tréningy'],
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
        $item = MediaLibraryItem::create(['folder_id' => self::$webContentFolderId]);

        $item->addMediaFromUrl($url)
            ->usingFileName($filename)
            ->toMediaCollection('library');

        return $item->id;
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

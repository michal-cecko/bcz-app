<?php

namespace Database\Seeders;

use App\Enums\MenuLocationEnum;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $pageIds = [];

    public function run(): void
    {
        $this->pageIds = Page::query()
            ->whereNotNull('system_key')
            ->pluck('id', 'system_key')
            ->all();

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::Header],
            [
                'label' => ['sk' => 'Hlavné menu', 'en' => 'Main Menu', 'cs' => 'Hlavní menu'],
                'items' => [
                    $this->pageItem('O nás', 'About Us', 'O nás', 'about', 0),
                    $this->pageItem('Súťaže', 'Competitions', 'Soutěže', 'competitions', 1),
                    $this->pageItem('Trénuj s nami', 'Train With Us', 'Trénuj s námi', 'trainings', 2),
                    $this->pageItem('Vystúpenia', 'Events', 'Vystoupení', 'events', 3),
                    $this->pageItem('Kontakt', 'Contact', 'Kontakt', 'contact', 4),
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterDiscover],
            [
                'label' => ['sk' => 'Objavte', 'en' => 'Discover', 'cs' => 'Objevte'],
                'items' => [
                    $this->pageItem('O nás', 'About Us', 'O nás', 'about', 0),
                    ['label_sk' => 'Náš tím', 'label_en' => 'Our Team', 'label_cs' => 'Náš tým', 'link_type' => 'custom', 'link_url' => '/o-nas#tim', 'target' => '_self', 'sort_order' => 1],
                    $this->pageItem('Kontakt', 'Contact', 'Kontakt', 'contact', 2),
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterPrograms],
            [
                'label' => ['sk' => 'Programy', 'en' => 'Programs', 'cs' => 'Programy'],
                'items' => [
                    $this->pageItem('Súťaže', 'Competitions', 'Soutěže', 'competitions', 0),
                    $this->pageItem('Trénuj s nami', 'Train With Us', 'Trénuj s námi', 'trainings', 1),
                    $this->pageItem('Vystúpenia', 'Events', 'Vystoupení', 'events', 2),
                    $this->pageItem('Školské workshopy', 'School Workshops', 'Školní workshopy', 'workshops', 3),
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterLegal],
            [
                'label' => ['sk' => 'Právne', 'en' => 'Legal', 'cs' => 'Právní'],
                'items' => [
                    $this->pageItem('Podmienky používania', 'Terms of Use', 'Podmínky používání', 'terms_of_use', 0),
                    $this->pageItem('Obchodné podmienky', 'Terms of Commerce', 'Obchodní podmínky', 'terms_of_commerce', 1),
                    $this->pageItem('Ochrana osobných údajov', 'Privacy Policy', 'Ochrana osobních údajů', 'privacy_policy', 2),
                ],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function pageItem(string $sk, string $en, string $cz, string $systemKey, int $sortOrder): array
    {
        return [
            'label_sk' => $sk,
            'label_en' => $en,
            'label_cs' => $cz,
            'link_type' => 'page',
            'link_model_id' => $this->pageIds[$systemKey] ?? null,
            'target' => '_self',
            'sort_order' => $sortOrder,
        ];
    }
}

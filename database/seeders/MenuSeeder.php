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
                'label' => ['sk' => 'Hlavné menu', 'en' => 'Main Menu', 'cz' => 'Hlavní menu'],
                'items' => [
                    $this->pageItem('O nás', 'About Us', 'O nás', 'about', 0),
                    $this->pageItem('Súťaže', 'Competitions', 'Soutěže', 'competitions', 1),
                    $this->pageItem('Tréningy', 'Trainings', 'Tréninky', 'trainings', 2),
                    $this->pageItem('Vystúpenia', 'Events', 'Vystoupení', 'events', 3),
                    $this->pageItem('Kontakt', 'Contact', 'Kontakt', 'contact', 4),
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterDiscover],
            [
                'label' => ['sk' => 'Objavte', 'en' => 'Discover', 'cz' => 'Objevte'],
                'items' => [
                    $this->pageItem('O nás', 'About Us', 'O nás', 'about', 0),
                    ['label_sk' => 'Náš tím', 'label_en' => 'Our Team', 'label_cz' => 'Náš tým', 'link_type' => 'custom', 'link_url' => '/o-nas#tim', 'target' => '_self', 'sort_order' => 1],
                    $this->pageItem('Kontakt', 'Contact', 'Kontakt', 'contact', 2),
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterPrograms],
            [
                'label' => ['sk' => 'Programy', 'en' => 'Programs', 'cz' => 'Programy'],
                'items' => [
                    $this->pageItem('Súťaže', 'Competitions', 'Soutěže', 'competitions', 0),
                    $this->pageItem('Tréningy', 'Trainings', 'Tréninky', 'trainings', 1),
                    $this->pageItem('Vystúpenia', 'Events', 'Vystoupení', 'events', 2),
                    $this->pageItem('Školské workshopy', 'School Workshops', 'Školní workshopy', 'workshops', 3),
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
            'label_cz' => $cz,
            'link_type' => 'page',
            'link_model_id' => $this->pageIds[$systemKey] ?? null,
            'target' => '_self',
            'sort_order' => $sortOrder,
        ];
    }
}

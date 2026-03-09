<?php

namespace Database\Seeders;

use App\Enums\MenuLocationEnum;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::Header],
            [
                'label' => ['sk' => 'Hlavné menu', 'en' => 'Main Menu', 'cz' => 'Hlavní menu'],
                'items' => [
                    ['label_sk' => 'O nás', 'label_en' => 'About Us', 'label_cz' => 'O nás', 'link_type' => 'custom', 'link_url' => '/o-nas', 'target' => '_self', 'sort_order' => 0],
                    ['label_sk' => 'Súťaže', 'label_en' => 'Competitions', 'label_cz' => 'Soutěže', 'link_type' => 'custom', 'link_url' => '/sutaze', 'target' => '_self', 'sort_order' => 1],
                    ['label_sk' => 'Tréningy', 'label_en' => 'Trainings', 'label_cz' => 'Tréninky', 'link_type' => 'custom', 'link_url' => '/treningy', 'target' => '_self', 'sort_order' => 2],
                    ['label_sk' => 'Vystúpenia', 'label_en' => 'Events', 'label_cz' => 'Vystoupení', 'link_type' => 'custom', 'link_url' => '/vystupenia', 'target' => '_self', 'sort_order' => 3],
                    ['label_sk' => 'Kontakt', 'label_en' => 'Contact', 'label_cz' => 'Kontakt', 'link_type' => 'custom', 'link_url' => '/kontakt', 'target' => '_self', 'sort_order' => 4],
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterDiscover],
            [
                'label' => ['sk' => 'Objavte', 'en' => 'Discover', 'cz' => 'Objevte'],
                'items' => [
                    ['label_sk' => 'O nás', 'label_en' => 'About Us', 'label_cz' => 'O nás', 'link_type' => 'custom', 'link_url' => '/o-nas', 'target' => '_self', 'sort_order' => 0],
                    ['label_sk' => 'Náš tím', 'label_en' => 'Our Team', 'label_cz' => 'Náš tým', 'link_type' => 'custom', 'link_url' => '/o-nas#tim', 'target' => '_self', 'sort_order' => 1],
                    ['label_sk' => 'Kontakt', 'label_en' => 'Contact', 'label_cz' => 'Kontakt', 'link_type' => 'custom', 'link_url' => '/kontakt', 'target' => '_self', 'sort_order' => 2],
                ],
            ],
        );

        Menu::query()->updateOrCreate(
            ['location' => MenuLocationEnum::FooterPrograms],
            [
                'label' => ['sk' => 'Programy', 'en' => 'Programs', 'cz' => 'Programy'],
                'items' => [
                    ['label_sk' => 'Súťaže', 'label_en' => 'Competitions', 'label_cz' => 'Soutěže', 'link_type' => 'custom', 'link_url' => '/sutaze', 'target' => '_self', 'sort_order' => 0],
                    ['label_sk' => 'Tréningy', 'label_en' => 'Trainings', 'label_cz' => 'Tréninky', 'link_type' => 'custom', 'link_url' => '/treningy', 'target' => '_self', 'sort_order' => 1],
                    ['label_sk' => 'Vystúpenia', 'label_en' => 'Events', 'label_cz' => 'Vystoupení', 'link_type' => 'custom', 'link_url' => '/vystupenia', 'target' => '_self', 'sort_order' => 2],
                    ['label_sk' => 'Školské workshopy', 'label_en' => 'School Workshops', 'label_cz' => 'Školní workshopy', 'link_type' => 'custom', 'link_url' => '/workshopy', 'target' => '_self', 'sort_order' => 3],
                ],
            ],
        );
    }
}

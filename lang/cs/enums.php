<?php

use App\Enums\SponsorTagEnum;

return [
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Hlavni sponzor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Medialni sponzor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Podporovatel',
    ],
];

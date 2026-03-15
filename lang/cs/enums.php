<?php

use App\Enums\CoachRoleEnum;
use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\SponsorTagEnum;

return [
    CoachRoleEnum::class => [
        CoachRoleEnum::MAIN->value => 'Hlavni trener',
        CoachRoleEnum::SECONDARY->value => 'Asistent trenera',
    ],
    EventTypeEnum::class => [
        EventTypeEnum::Report->value => 'Report',
        EventTypeEnum::Organized->value => 'Organizovane',
        EventTypeEnum::Competition->value => 'Soutez',
    ],
    EventPricingTypeEnum::class => [
        EventPricingTypeEnum::Free->value => 'Zdarma',
        EventPricingTypeEnum::Paid->value => 'Placene',
    ],
    RegistrationStatusEnum::class => [
        RegistrationStatusEnum::Pending->value => 'Čekající',
        RegistrationStatusEnum::Approved->value => 'Schválená',
        RegistrationStatusEnum::Rejected->value => 'Zamítnutá',
        RegistrationStatusEnum::Cancelled->value => 'Zrušená',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Hlavni sponzor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Medialni sponzor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Podporovatel',
    ],
];

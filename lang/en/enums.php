<?php

use App\Enums\CoachRoleEnum;
use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\SponsorTagEnum;

return [
    CoachRoleEnum::class => [
        CoachRoleEnum::MAIN->value => 'Head Coach',
        CoachRoleEnum::SECONDARY->value => 'Assistant Coach',
    ],
    EventTypeEnum::class => [
        EventTypeEnum::Report->value => 'Report',
        EventTypeEnum::Organized->value => 'Organized',
        EventTypeEnum::Competition->value => 'Competition',
    ],
    EventPricingTypeEnum::class => [
        EventPricingTypeEnum::Free->value => 'Free',
        EventPricingTypeEnum::Paid->value => 'Paid',
    ],
    RegistrationStatusEnum::class => [
        RegistrationStatusEnum::Pending->value => 'Pending',
        RegistrationStatusEnum::Approved->value => 'Approved',
        RegistrationStatusEnum::Rejected->value => 'Rejected',
        RegistrationStatusEnum::Cancelled->value => 'Cancelled',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Main Sponsor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Media Sponsor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Supporter',
    ],
];

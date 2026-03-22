<?php

use App\Enums\BannerTypeEnum;
use App\Enums\CoachRoleEnum;
use App\Enums\DraftStatusEnum;
use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\ProfileTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\SponsorTagEnum;

return [
    BannerTypeEnum::class => [
        BannerTypeEnum::Topbar->value => 'Top Bar',
        BannerTypeEnum::Floating->value => 'Floating',
        BannerTypeEnum::Popup->value => 'Popup',
    ],
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
    RoleEnum::class => [
        RoleEnum::SUPER_ADMIN->value => 'Super Admin',
        RoleEnum::ADMIN->value => 'Admin',
        RoleEnum::TEAM_ADMIN->value => 'Team Admin',
        RoleEnum::COACH->value => 'Coach',
        RoleEnum::ATHLETE->value => 'Athlete',
        RoleEnum::EDITOR->value => 'Editor',
        RoleEnum::JUDGE->value => 'Judge',
        RoleEnum::CUSTOMER->value => 'Customer',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Main Sponsor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Media Sponsor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Supporter',
    ],
    DraftStatusEnum::class => [
        DraftStatusEnum::Pending->value => 'Pending Approval',
        DraftStatusEnum::Rejected->value => 'Rejected',
    ],
    ProfileTypeEnum::class => [
        ProfileTypeEnum::Coach->value => 'Coach',
        ProfileTypeEnum::Athlete->value => 'Athlete',
        ProfileTypeEnum::Judge->value => 'Judge',
    ],
];

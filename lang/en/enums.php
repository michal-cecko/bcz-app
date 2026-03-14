<?php

use App\Enums\SponsorTagEnum;

return [
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Main Sponsor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Media Sponsor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Supporter',
    ],
];

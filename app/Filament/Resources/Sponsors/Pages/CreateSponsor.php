<?php

namespace App\Filament\Resources\Sponsors\Pages;

use App\Filament\Resources\Sponsors\SponsorResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSponsor extends CreateRecord
{
    protected static string $resource = SponsorResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

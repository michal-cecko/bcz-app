<?php

namespace App\Filament\Resources\AthleteCategories\Pages;

use App\Filament\Resources\AthleteCategories\AthleteCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAthleteCategory extends CreateRecord
{
    protected static string $resource = AthleteCategoryResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

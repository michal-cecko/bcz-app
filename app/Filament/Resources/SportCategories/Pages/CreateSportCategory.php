<?php

namespace App\Filament\Resources\SportCategories\Pages;

use App\Filament\Resources\SportCategories\SportCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSportCategory extends CreateRecord
{
    protected static string $resource = SportCategoryResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

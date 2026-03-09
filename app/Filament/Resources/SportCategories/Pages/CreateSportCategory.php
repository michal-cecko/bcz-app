<?php

namespace App\Filament\Resources\SportCategories\Pages;

use App\Filament\Resources\SportCategories\SportCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSportCategory extends CreateRecord
{
    protected static string $resource = SportCategoryResource::class;
}

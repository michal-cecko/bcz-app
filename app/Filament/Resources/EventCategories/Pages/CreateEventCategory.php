<?php

namespace App\Filament\Resources\EventCategories\Pages;

use App\Filament\Resources\EventCategories\EventCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateEventCategory extends CreateRecord
{
    protected static string $resource = EventCategoryResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

<?php

namespace App\Filament\Resources\SportCategories\Pages;

use App\Filament\Resources\SportCategories\SportCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSportCategories extends ListRecords
{
    protected static string $resource = SportCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

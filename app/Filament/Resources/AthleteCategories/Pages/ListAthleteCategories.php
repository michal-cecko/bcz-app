<?php

namespace App\Filament\Resources\AthleteCategories\Pages;

use App\Filament\Resources\AthleteCategories\AthleteCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAthleteCategories extends ListRecords
{
    protected static string $resource = AthleteCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

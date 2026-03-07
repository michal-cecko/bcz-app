<?php

namespace App\Filament\Resources\Disciplines\Pages;

use App\Filament\Resources\Disciplines\DisciplineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisciplines extends ListRecords
{
    protected static string $resource = DisciplineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Trainings\Pages;

use App\Filament\Resources\Trainings\TrainingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListTrainings extends ListRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewArchive')
                ->label('Archív na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url('/treningy', shouldOpenInNewTab: true),
            CreateAction::make(),
        ];
    }
}

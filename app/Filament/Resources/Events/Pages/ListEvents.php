<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewArchive')
                ->label('Archív na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url('/vystupenia', shouldOpenInNewTab: true),
            CreateAction::make(),
        ];
    }
}

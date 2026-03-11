<?php

namespace App\Filament\Resources\Competitions\Pages;

use App\Filament\Resources\Competitions\CompetitionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListCompetitions extends ListRecords
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewArchive')
                ->label('Archív na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url('/sutaze', shouldOpenInNewTab: true),
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnSite')
                ->label('Zobraziť na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url(fn () => $this->record->getLinkUrl(), shouldOpenInNewTab: true),
            EditAction::make(),
        ];
    }
}

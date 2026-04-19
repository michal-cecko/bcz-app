<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnSite')
                ->label('Zobraziť na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url(fn () => $this->record->getLinkUrl(), shouldOpenInNewTab: true),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare),
        ];
    }
}

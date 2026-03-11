<?php

namespace App\Filament\Resources\Trainings\Pages;

use App\Filament\Resources\Trainings\TrainingResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTraining extends ViewRecord
{
    protected static string $resource = TrainingResource::class;

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

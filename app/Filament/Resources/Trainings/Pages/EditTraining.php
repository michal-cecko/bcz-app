<?php

namespace App\Filament\Resources\Trainings\Pages;

use App\Filament\Resources\Trainings\TrainingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class EditTraining extends EditRecord
{
    protected static string $resource = TrainingResource::class;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnSite')
                ->label('Zobraziť na webe')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->url(fn () => $this->record->getLinkUrl(), shouldOpenInNewTab: true),
            $this->getSaveFormAction()
                ->formId('form')
                ->icon(Heroicon::OutlinedPencilSquare),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->icon(Heroicon::OutlinedPencilSquare);
    }
}

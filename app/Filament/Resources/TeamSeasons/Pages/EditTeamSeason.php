<?php

namespace App\Filament\Resources\TeamSeasons\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\TeamSeasons\TeamSeasonResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class EditTeamSeason extends EditRecord
{
    protected static string $resource = TeamSeasonResource::class;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToTeam')
                ->label('Späť na tím')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->color('gray')
                ->url(fn () => TeamResource::getUrl('edit', ['record' => $this->record->team_id])),
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

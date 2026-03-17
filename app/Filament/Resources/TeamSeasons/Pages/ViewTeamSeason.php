<?php

namespace App\Filament\Resources\TeamSeasons\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\TeamSeasons\TeamSeasonResource;
use App\Notifications\MembershipPaymentDue;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeamSeason extends ViewRecord
{
    protected static string $resource = TeamSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToTeam')
                ->label('Späť na tím')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->color('gray')
                ->url(fn () => TeamResource::getUrl('edit', ['record' => $this->record->team_id])),
            Action::make('notifyUnpaid')
                ->label('Upozorniť nezaplatených')
                ->icon(Heroicon::OutlinedBell)
                ->requiresConfirmation()
                ->action(function (): void {
                    $unpaidMemberships = $this->record->memberships()
                        ->where('status', 'pending')
                        ->where('is_free', false)
                        ->with('user')
                        ->get();

                    $count = 0;
                    foreach ($unpaidMemberships as $membership) {
                        if ($membership->user) {
                            $membership->user->notify(new MembershipPaymentDue($membership));
                            $count++;
                        }
                    }

                    Notification::make()
                        ->title("Notifikácia odoslaná {$count} členom.")
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}

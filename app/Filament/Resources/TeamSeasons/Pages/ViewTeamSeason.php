<?php

namespace App\Filament\Resources\TeamSeasons\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\TeamSeasons\TeamSeasonResource;
use App\Models\TeamSeason;
use App\Notifications\MembershipPaymentDue;
use App\Rules\NoOverlappingSeason;
use App\Services\TrainingSeasonService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ViewTeamSeason extends ViewRecord
{
    protected static string $resource = TeamSeasonResource::class;

    protected function getHeaderActions(): array
    {
        $teamId = Filament::getTenant()?->id ?? $this->record->team_id;

        return [
            Action::make('backToTeam')
                ->label('Späť na tím')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->color('gray')
                ->url(fn () => TeamResource::getUrl('edit', ['record' => $this->record->team_id])),
            Action::make('createNextSeason')
                ->label('Vytvoriť novú sezónu')
                ->icon(Heroicon::OutlinedPlusCircle)
                ->color('success')
                ->modalHeading('Vytvoriť novú sezónu')
                ->modalWidth('2xl')
                ->schema([
                    Section::make('Nová sezóna')->schema([
                        TextInput::make('name')
                            ->label('Názov')
                            ->required(),
                        DatePicker::make('starts_at')
                            ->label('Začiatok')
                            ->required()
                            ->default($this->record->ends_at->addDay())
                            ->rules([new NoOverlappingSeason($teamId)]),
                        DatePicker::make('ends_at')
                            ->label('Koniec')
                            ->required()
                            ->after('starts_at')
                            ->rules([new NoOverlappingSeason($teamId)]),
                        TextInput::make('fee_amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->default($this->record->fee_amount),
                        Select::make('fee_currency')
                            ->label('Mena')
                            ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                            ->default($this->record->fee_currency)
                            ->required(),
                        TextInput::make('payment_deadline_days')
                            ->label('Splatnosť (dní)')
                            ->numeric()
                            ->default($this->record->payment_deadline_days)
                            ->required(),
                    ]),
                    Section::make('Kopírovať tréningy z aktuálnej sezóny')
                        ->description('Tréningy označené ako "opakovať v ďalšej sezóne" sú predvolene zaškrtnuté.')
                        ->schema([
                            CheckboxList::make('training_ids')
                                ->label('')
                                ->options(function () {
                                    return $this->record->trainings()
                                        ->where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(fn ($t) => [
                                            $t->id => $t->getTranslation('title', app()->getLocale()) ?: $t->getTranslation('title', 'sk'),
                                        ]);
                                })
                                ->default(function () {
                                    return TrainingSeasonService::getRecurringTrainings($this->record)
                                        ->pluck('id')
                                        ->toArray();
                                })
                                ->columns(1)
                                ->bulkToggleable(),
                        ])
                        ->visible(fn () => $this->record->trainings()->where('is_active', true)->exists()),
                ])
                ->action(function (array $data): void {
                    $newSeason = TeamSeason::create([
                        'team_id' => $this->record->team_id,
                        'name' => $data['name'],
                        'starts_at' => $data['starts_at'],
                        'ends_at' => $data['ends_at'],
                        'fee_amount' => $data['fee_amount'],
                        'fee_currency' => $data['fee_currency'],
                        'payment_deadline_days' => $data['payment_deadline_days'],
                    ]);

                    $copiedCount = 0;
                    $trainingIds = collect($data['training_ids'] ?? []);

                    if ($trainingIds->isNotEmpty()) {
                        $copied = TrainingSeasonService::copyTrainingsToSeason($trainingIds, $newSeason);
                        $copiedCount = $copied->count();
                    }

                    Notification::make()
                        ->success()
                        ->title('Sezóna vytvorená')
                        ->body("Nová sezóna \"{$data['name']}\" bola vytvorená".($copiedCount > 0 ? " s {$copiedCount} tréningami." : '.'))
                        ->send();

                    $this->redirect(TeamSeasonResource::getUrl('view', ['record' => $newSeason]));
                }),
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
                        if ($membership->user && $membership->user->isMembershipPayer()) {
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

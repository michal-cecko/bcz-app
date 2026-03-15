<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Enums\PaymentMethodEnum;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Membership;
use App\Models\User;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recordPayment')
                ->label('Zaznamenať platbu')
                ->schema([
                    Select::make('user_id')
                        ->label('Používateľ')
                        ->options(fn (): array => Filament::getTenant()
                            ->members()
                            ->pluck('name', 'users.id')
                            ->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('payable_type')
                        ->label('Typ platby')
                        ->options([
                            'membership' => 'Členstvo',
                            'training_registration' => 'Registrácia na tréning',
                            'competition_registration' => 'Registrácia na súťaž',
                        ])
                        ->required()
                        ->live(),
                    Select::make('payable_id')
                        ->label('Záznam')
                        ->options(function (Get $get): array {
                            $type = $get('payable_type');
                            $userId = $get('user_id');

                            if (! $type || ! $userId) {
                                return [];
                            }

                            return match ($type) {
                                'membership' => Membership::where('user_id', $userId)
                                    ->where('team_id', Filament::getTenant()->id)
                                    ->get()
                                    ->mapWithKeys(fn ($m) => [$m->id => $m->period->getLabel().' ('.$m->starts_at->format('d.m.Y').' - '.$m->ends_at->format('d.m.Y').')'])
                                    ->toArray(),
                                'training_registration' => \App\Models\TrainingRegistration::where('user_id', $userId)
                                    ->whereHas('training', fn ($q) => $q->where('team_id', Filament::getTenant()->id))
                                    ->with('training')
                                    ->get()
                                    ->mapWithKeys(fn ($r) => [$r->id => $r->training->getTranslation('title', 'sk').' ('.$r->registered_at?->format('d.m.Y').')'])
                                    ->toArray(),
                                'competition_registration', 'event_registration' => \App\Models\EventRegistration::where('user_id', $userId)
                                    ->whereHas('event', fn ($q) => $q->where('team_id', Filament::getTenant()->id))
                                    ->with('event')
                                    ->get()
                                    ->mapWithKeys(fn ($r) => [$r->id => $r->event->getTranslation('title', 'sk').' ('.$r->registered_at?->format('d.m.Y').')'])
                                    ->toArray(),
                                default => [],
                            };
                        })
                        ->required()
                        ->visible(fn (Get $get): bool => filled($get('payable_type')) && filled($get('user_id'))),
                    TextInput::make('amount')
                        ->label('Suma')
                        ->numeric()
                        ->required()
                        ->minValue(0.01),
                    Select::make('currency')
                        ->label('Mena')
                        ->options([
                            'EUR' => 'EUR',
                            'CZK' => 'CZK',
                            'USD' => 'USD',
                        ])
                        ->default('EUR')
                        ->required(),
                    Select::make('payment_method')
                        ->label('Spôsob platby')
                        ->options(PaymentMethodEnum::translations())
                        ->default(PaymentMethodEnum::CASH->value)
                        ->required(),
                    Textarea::make('notes')
                        ->label('Poznámky')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $user = User::findOrFail($data['user_id']);
                    $team = Filament::getTenant();

                    $payableClass = match ($data['payable_type']) {
                        'membership' => Membership::class,
                        'training_registration' => \App\Models\TrainingRegistration::class,
                        'competition_registration' => \App\Models\EventRegistration::class,
                    };

                    $payable = $payableClass::findOrFail($data['payable_id']);

                    $paymentService = app(PaymentService::class);
                    $paymentService->recordManualPayment(
                        $user,
                        $team,
                        $payable,
                        (float) $data['amount'],
                        $data['currency'],
                        PaymentMethodEnum::from($data['payment_method']),
                        $data['notes'] ?? null,
                    );

                    Notification::make()
                        ->title('Platba bola zaznamenaná.')
                        ->success()
                        ->send();
                }),
        ];
    }
}

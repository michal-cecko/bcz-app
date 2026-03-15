<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use App\Models\Training;
use App\Models\TrainingRegistration;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Platby';

    protected static ?string $modelLabel = 'platba';

    protected static ?string $pluralModelLabel = 'Platby';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        /** @var Training $ownerRecord */
        return $ownerRecord->pricing_type === TrainingPricingTypeEnum::PAID;
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Žiadne platby')
            ->query(fn (): Builder => Payment::query()
                ->whereHasMorph('payable', TrainingRegistration::class, function (Builder $q) {
                    $q->where('training_id', $this->getOwnerRecord()->getKey());
                })
                ->latest('created_at'))
            ->columns([
                TextColumn::make('payable.user.name')
                    ->label('Zákazník')
                    ->placeholder('Hosť'),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->money(fn ($record) => $record->currency ?? 'EUR'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('payment_method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('paid_at')
                    ->label('Zaplatené')
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Vytvorená')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('create_payment')
                    ->label('Zaznamenať platbu')
                    ->modalHeading('Zaznamenať platbu za tréning')
                    ->schema([
                        Select::make('registration_id')
                            ->label('Registrácia')
                            ->options(function () {
                                $training = $this->getOwnerRecord();

                                return TrainingRegistration::where('training_id', $training->getKey())
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(fn (TrainingRegistration $r) => [
                                        $r->id => ($r->user?->name ?? 'Hosť').' — '.$r->registered_at?->format('d.m.Y'),
                                    ]);
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->default(fn () => $this->getOwnerRecord()->price_amount)
                            ->prefix('€'),
                        Select::make('payment_method')
                            ->label('Metóda platby')
                            ->options(PaymentMethodEnum::class)
                            ->required()
                            ->default(PaymentMethodEnum::CASH),
                        Select::make('status')
                            ->label('Stav')
                            ->options(PaymentStatusEnum::class)
                            ->required()
                            ->default(PaymentStatusEnum::COMPLETED),
                        DateTimePicker::make('paid_at')
                            ->label('Dátum platby')
                            ->default(now()),
                        Textarea::make('notes')
                            ->label('Poznámka')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $registration = TrainingRegistration::find($data['registration_id']);
                        $training = $this->getOwnerRecord();

                        $user = $registration->user;

                        Payment::create([
                            'team_id' => $training->team_id,
                            'user_id' => $registration->user_id,
                            'payer_name' => $user?->name,
                            'payer_email' => $user?->email,
                            'payable_type' => TrainingRegistration::class,
                            'payable_id' => $registration->id,
                            'amount' => $data['amount'],
                            'currency' => 'EUR',
                            'status' => $data['status'],
                            'payment_method' => $data['payment_method'],
                            'paid_at' => $data['paid_at'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Platba bola zaznamenaná.')
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Zobraziť')
                    ->url(fn (Payment $record): string => PaymentResource::getUrl('view', ['record' => $record]))
                    ->icon(Heroicon::Eye),
            ]);
    }
}

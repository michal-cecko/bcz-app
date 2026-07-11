<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Filament\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentConfirmed;
use App\Services\EmailService;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Registrácie';

    protected static ?string $modelLabel = 'registrácia';

    protected static ?string $pluralModelLabel = 'Registrácie';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->event_type, [EventTypeEnum::Organized, EventTypeEnum::Competition]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(3)
                    ->schema([
                        Select::make('user_id')
                            ->label('Používateľ')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Nepovinné — ak používateľ nemá účet, nechajte prázdne.')
                            ->placeholder('Bez priradenia k účtu'),
                        Select::make('status')
                            ->label('Stav')
                            ->options(RegistrationStatusEnum::class)
                            ->default(RegistrationStatusEnum::Approved)
                            ->required(),
                        Toggle::make('send_notification')
                            ->label('Odoslať notifikáciu')
                            ->inline(false)
                            ->default(true)
                            ->dehydrated(false),
                    ]),
                Select::make('athlete_category_id')
                    ->label('Kategória')
                    ->relationship('athleteCategory')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->preload()
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextInput::make('weight_in')
                    ->label('Váha (kg)')
                    ->numeric()
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                DateTimePicker::make('registered_at')
                    ->label('Dátum registrácie')
                    ->default(now())
                    ->timezone(fn (): string => $this->getOwnerRecord()->getTimezone()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('fieldValues'))
            ->columns([
                TextColumn::make('athlete_name')
                    ->label('Meno')
                    ->state(fn ($record): ?string => $record->athleteName())
                    ->searchable(query: fn ($query, string $search) => $query->where(
                        fn ($q) => $q
                            ->whereHas('user', fn ($u) => $u->where('name', 'ilike', "%{$search}%"))
                            ->orWhereHas('fieldValues', fn ($fv) => $fv
                                ->whereIn('field_type', ['first_name', 'last_name', 'full_name'])
                                ->where('value', 'ilike', "%{$search}%"))
                    ))
                    ->placeholder('Hosť'),
                TextColumn::make('athlete_email')
                    ->label('E-mail')
                    ->state(fn ($record): ?string => $record->athleteEmail())
                    ->placeholder('-'),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw("(select name->>'sk' from athlete_categories where athlete_categories.id = event_registrations.athlete_category_id) {$direction}"))
                    ->placeholder('-')
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                TextColumn::make('weight_in')
                    ->label('Váha')
                    ->suffix(' kg')
                    ->placeholder('-')
                    ->sortable()
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(RegistrationStatusEnum::class),
                SelectFilter::make('athlete_category_id')
                    ->label('Kategória')
                    ->options(fn () => $this->getOwnerRecord()->registrations()
                        ->with('athleteCategory')->get()
                        ->pluck('athleteCategory')->filter()->unique('id')
                        ->mapWithKeys(fn ($category) => [$category->id => $category->getTranslation('name', 'sk')])->all())
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                Filter::make('weight_in')
                    ->schema([
                        TextInput::make('weight_from')
                            ->label('Váha od (kg)')
                            ->numeric(),
                        TextInput::make('weight_to')
                            ->label('Váha do (kg)')
                            ->numeric(),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['weight_from'] ?? null, fn ($q, $value) => $q->where('weight_in', '>=', $value))
                        ->when($data['weight_to'] ?? null, fn ($q, $value) => $q->where('weight_in', '<=', $value)))
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
            ])
            ->headerActions([
                $this->makeEventSendEmailAction(),
                CreateAction::make()
                    ->label('Zaregistrovať')
                    ->modalHeading('Pridať zákazníka do podujatia')
                    ->after(function (array $data, EventRegistration $record) {
                        $sendNotification = $data['send_notification'] ?? false;
                        if (! $sendNotification || empty($data['user_id'])) {
                            return;
                        }

                        $user = User::find($data['user_id']);
                        if (! $user) {
                            return;
                        }

                        /** @var Event $event */
                        $event = $this->getOwnerRecord();
                        $org = $event->organization;

                        $payment = null;
                        if ($org?->pricing_type === EventPricingTypeEnum::Paid && $org->price_amount) {
                            $paymentService = app(PaymentService::class);
                            $payment = $paymentService->createPendingPayment(
                                user: $user,
                                team: $event->team,
                                payable: $record,
                                amount: (float) $org->price_amount,
                                currency: $org->price_currency ?? 'EUR',
                            );
                        }

                        $regLocale = $record->locale ?: 'sk';
                        RegistrationService::sendConfirmation(
                            userOrEmail: $user,
                            registrationKind: 'event',
                            registrationTitle: $event->getTranslation('title', $regLocale),
                            team: $event->team,
                            customEmailContent: $org?->confirmation_email_content,
                            locale: $regLocale,
                            attachments: $event->getMedia('email_attachments'),
                            payment: $payment,
                        );

                        Notification::make()
                            ->success()
                            ->title('Notifikácia odoslaná')
                            ->body("E-mail bol odoslaný na {$user->email}")
                            ->send();
                    }),
            ])
            ->recordActions([
                SendEmailAction::make('send_email')
                    ->contextVariables(['nazov_eventu', 'datum_eventu'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveEventRegistrationRecipient($record);
                    }),
                Action::make('record_payment')
                    ->label('Platba')
                    ->icon(Heroicon::CurrencyEuro)
                    ->color('success')
                    ->visible(function () {
                        /** @var Event $event */
                        $event = $this->getOwnerRecord();
                        $org = $event->organization;

                        return $org?->pricing_type === EventPricingTypeEnum::Paid && $org->price_amount > 0;
                    })
                    ->modalHeading('Zaznamenať platbu')
                    ->schema(function () {
                        /** @var Event $event */
                        $event = $this->getOwnerRecord();
                        $org = $event->organization;

                        return [
                            TextInput::make('amount')
                                ->label('Suma')
                                ->numeric()
                                ->required()
                                ->default($org?->price_amount)
                                ->prefix($org?->price_currency ?? '€'),
                            Select::make('payment_method')
                                ->label('Metóda platby')
                                ->options([
                                    PaymentMethodEnum::BANK_TRANSFER->value => PaymentMethodEnum::BANK_TRANSFER->getLabel(),
                                    PaymentMethodEnum::CASH->value => PaymentMethodEnum::CASH->getLabel(),
                                ])
                                ->required()
                                ->default(PaymentMethodEnum::CASH),
                            Select::make('payment_status')
                                ->label('Stav')
                                ->options(PaymentStatusEnum::class)
                                ->required()
                                ->default(PaymentStatusEnum::COMPLETED),
                            Textarea::make('notes')
                                ->label('Poznámka')
                                ->rows(2),
                            Toggle::make('notify_customer')
                                ->label('Odoslať potvrdenie zákazníkovi')
                                ->helperText('Pošle e-mail s potvrdením platby.')
                                ->default(true),
                        ];
                    })
                    ->action(function (array $data, EventRegistration $record): void {
                        /** @var Event $event */
                        $event = $this->getOwnerRecord();
                        $org = $event->organization;
                        $user = $record->user;
                        $paymentStatus = $data['payment_status'] instanceof PaymentStatusEnum
                            ? $data['payment_status']
                            : PaymentStatusEnum::from($data['payment_status']);

                        $payment = Payment::create([
                            'team_id' => $event->team_id,
                            'user_id' => $record->user_id,
                            'payer_name' => $user?->name,
                            'payer_email' => $user?->email,
                            'payable_type' => $record->getMorphClass(),
                            'payable_id' => $record->id,
                            'amount' => $data['amount'],
                            'currency' => $org?->price_currency ?? 'EUR',
                            'status' => $paymentStatus,
                            'payment_method' => $data['payment_method'],
                            'paid_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        if ($paymentStatus === PaymentStatusEnum::COMPLETED) {
                            $record->update(['status' => RegistrationStatusEnum::Approved]);
                        }

                        if (! empty($data['notify_customer']) && $paymentStatus === PaymentStatusEnum::COMPLETED && $user) {
                            $user->notify(new PaymentConfirmed($payment));
                        }

                        Notification::make()
                            ->success()
                            ->title('Platba bola zaznamenaná.')
                            ->send();
                    }),
                ViewAction::make()
                    ->url(fn ($record): string => EventRegistrationResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->modalHeading('Upraviť registráciu'),
                DeleteAction::make()
                    ->modalHeading('Odstrániť registráciu'),
            ])
            ->toolbarActions([
                SendEmailBulkAction::make('send_email_bulk')
                    ->contextVariables(['nazov_eventu', 'datum_eventu'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveEventRegistrationRecipient($record);
                    }),
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return list<array{email: string, variables: array<string, string>}>
     */
    protected function resolveEventRegistrationRecipient($record): array
    {
        $email = $record->athleteEmail();
        if (! $email) {
            return [];
        }

        /** @var Event $event */
        $event = $this->getOwnerRecord();
        $teamName = $event->team->getTranslation('name', 'sk');

        return [
            [
                'email' => $email,
                'variables' => [
                    'meno' => $record->athleteName() ?? '',
                    'email' => $email,
                    'nazov_timu' => $teamName,
                    'nazov_eventu' => $event->getTranslation('title', 'sk'),
                    'datum_eventu' => $event->date?->format('d.m.Y') ?? '',
                ],
            ],
        ];
    }

    protected function makeEventSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildEventRecipientsPlaceholder()],
                (new SendEmailAction('temp'))
                    ->contextVariables(['nazov_eventu', 'datum_eventu'])
                    ->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription('E-mail bude odoslaný všetkým registrovaným.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $allRecipients = [];

                foreach ($this->getOwnerRecord()->registrations()->with('user')->get() as $registration) {
                    $allRecipients = array_merge($allRecipients, $this->resolveEventRegistrationRecipient($registration));
                }

                if (empty($allRecipients)) {
                    Notification::make()->warning()->title('Žiadni príjemcovia')->send();

                    return;
                }

                $count = EmailService::send(
                    subject: $data['subject'],
                    brickContent: $data['content'],
                    recipients: $allRecipients,
                    team: filament()->getTenant(),
                );

                Notification::make()
                    ->success()
                    ->title('E-mail odoslaný')
                    ->body("E-mail bol odoslaný {$count} príjemcom.")
                    ->send();
            });
    }

    protected function buildEventRecipientsPlaceholder(): Placeholder
    {
        $emails = collect();
        foreach ($this->getOwnerRecord()->registrations()->with('user')->get() as $reg) {
            foreach ($this->resolveEventRegistrationRecipient($reg) as $r) {
                $emails->push($r['email']);
            }
        }
        $unique = $emails->unique()->values();
        $list = $unique->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$unique->count().')')
            ->content(new HtmlString($unique->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }
}

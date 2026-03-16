<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\TrainingRegistrationCancelled;
use App\Services\RegistrationService;
use App\Services\TrainingCapacityService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Registrácie';

    protected static ?string $modelLabel = 'registrácia';

    protected static ?string $pluralModelLabel = 'Registrácie';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('status')
                            ->label('Stav')
                            ->options([
                                RegistrationStatusEnum::Pending->value => RegistrationStatusEnum::Pending->getLabel(),
                                RegistrationStatusEnum::Approved->value => RegistrationStatusEnum::Approved->getLabel(),
                            ])
                            ->required()
                            ->default(RegistrationStatusEnum::Approved->value),
                        Toggle::make('send_notification')
                            ->label('Odoslať notifikáciu')
                            ->inline(false)
                            ->default(true)
                            ->dehydrated(false),
                    ]),
                Section::make('Registračný formulár')
                    ->schema($this->buildDynamicFormFields())
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn (): bool => ! empty($this->getTrainingFormSchema())),
            ]);
    }

    public function table(Table $table): Table
    {
        /** @var Training $training */
        $training = $this->getOwnerRecord();
        $pricingType = $training->pricing_type;
        $formSchema = $this->getTrainingFormSchema();
        $emailField = collect($formSchema)->firstWhere('type', RegistrationFieldTypeEnum::EMAIL->value);

        $columns = [
            TextColumn::make('user.name')
                ->label('Používateľ')
                ->placeholder('Hosť'),
        ];

        if ($emailField) {
            $columns[] = TextColumn::make('form_data.'.$emailField['name'])
                ->label('E-mail')
                ->searchable(query: function ($query, string $search) use ($emailField): void {
                    $query->where('form_data->'.$emailField['name'], 'ilike', "%{$search}%");
                });
        }

        $columns[] = TextColumn::make('status')
            ->label('Stav')
            ->badge();

        if ($pricingType === TrainingPricingTypeEnum::PAID) {
            $columns[] = TextColumn::make('payment_status')
                ->label('Platba')
                ->badge()
                ->state(function ($record): string {
                    $payment = $record->payments()->latest()->first();

                    return $payment?->status?->value ?? 'unpaid';
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'unpaid' => 'Nezaplatené',
                    default => PaymentStatusEnum::tryFrom($state)?->getLabel() ?? $state,
                })
                ->color(fn (string $state): string => match ($state) {
                    'unpaid' => 'danger',
                    default => PaymentStatusEnum::tryFrom($state)?->getColor() ?? 'gray',
                });
        }

        if ($pricingType === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED) {
            $columns[] = TextColumn::make('membership_status')
                ->label('Členstvo')
                ->badge()
                ->state(function ($record) use ($training): string {
                    if (! $record->user_id) {
                        return 'none';
                    }

                    $membership = Membership::where('team_id', $training->team_id)
                        ->where('user_id', $record->user_id)
                        ->where('status', MembershipStatusEnum::ACTIVE)
                        ->where('ends_at', '>=', now())
                        ->first();

                    return $membership ? 'active' : 'inactive';
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'active' => 'Aktívne',
                    'inactive' => 'Neaktívne',
                    default => 'Bez členstva',
                })
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                    default => 'gray',
                });
        }

        $columns = [
            ...$columns,
            TextColumn::make('registered_at')
                ->label('Registrovaný')
                ->dateTime()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Vytvorený')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];

        return $table
            ->emptyStateHeading('Žiadne registrácie')
            ->emptyStateDescription('Zatiaľ nie sú žiadne registrácie na tento tréning.')
            ->columns($columns)
            ->headerActions([
                $this->makeTrainingSendEmailAction(),
                CreateAction::make()
                    ->label('Zaregistrovať')
                    ->modalHeading('Pridať zákazníka do tréningu')
                    ->modalSubmitActionLabel('Zaregistrovať')
                    ->createAnotherAction(fn ($action) => $action->label('Pridať a vyplniť znova'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['registered_at'] = now();
                        $data['form_data'] = $this->extractFormData($data);

                        // Auto-resolve user from email if user_id not set
                        if (empty($data['user_id'])) {
                            $schema = $this->getTrainingFormSchema();
                            $email = RegistrationService::extractEmailFromFormData($data['form_data'], $schema);

                            if ($email) {
                                $name = RegistrationService::extractNameFromFormData($data['form_data'], $schema);
                                $result = RegistrationService::resolveOrCreateUser($email, $name);
                                $data['user_id'] = $result['user']->id;
                                $data['_is_new_user'] = $result['created'];
                            }
                        }

                        return $data;
                    })
                    ->after(function (array $data) {
                        $sendNotification = $data['send_notification'] ?? false;
                        if (! $sendNotification || empty($data['user_id'])) {
                            return;
                        }

                        $user = User::find($data['user_id']);
                        if (! $user) {
                            return;
                        }

                        /** @var Training $training */
                        $training = $this->getOwnerRecord();

                        RegistrationService::sendConfirmation(
                            user: $user,
                            registrationType: 'tréning',
                            registrationTitle: $training->getTranslation('title', 'sk'),
                            isNewUser: $data['_is_new_user'] ?? false,
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
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveRegistrationRecipient($record);
                    }),
                Action::make('record_payment')
                    ->label('Platba')
                    ->icon(Heroicon::CurrencyEuro)
                    ->color('success')
                    ->visible($pricingType === TrainingPricingTypeEnum::PAID)
                    ->modalHeading('Zaznamenať platbu')
                    ->schema([
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
                        Textarea::make('notes')
                            ->label('Poznámka')
                            ->rows(2),
                    ])
                    ->action(function (array $data, $record): void {
                        $training = $this->getOwnerRecord();

                        $user = $record->user;

                        Payment::create([
                            'team_id' => $training->team_id,
                            'user_id' => $record->user_id,
                            'payer_name' => $user?->name,
                            'payer_email' => $user?->email,
                            'payable_type' => \App\Models\TrainingRegistration::class,
                            'payable_id' => $record->id,
                            'amount' => $data['amount'],
                            'currency' => 'EUR',
                            'status' => $data['status'],
                            'payment_method' => $data['payment_method'],
                            'paid_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Platba bola zaznamenaná.')
                            ->send();
                    }),
                ViewAction::make()
                    ->modalHeading('Zobraziť registráciu'),
                EditAction::make()
                    ->modalHeading('Upraviť registráciu')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['form_data'] = $this->extractFormData($data);

                        return $data;
                    })
                    ->after(function (TrainingRegistration $record, array $data): void {
                        if ($record->status !== RegistrationStatusEnum::Rejected) {
                            return;
                        }

                        $this->sendCancellationNotification($record);
                        TrainingCapacityService::handleSpotFreed($this->getOwnerRecord());
                    }),
                Action::make('delete')
                    ->label('Odstrániť')
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->modalHeading('Odstrániť registráciu')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('cancellation_reason')
                            ->label('Dôvod zrušenia')
                            ->placeholder('Voliteľné — bude odoslané používateľovi')
                            ->rows(2),
                    ])
                    ->action(function (array $data, TrainingRegistration $record): void {
                        $reason = $data['cancellation_reason'] ?? null;

                        if ($reason) {
                            $record->update(['cancellation_reason' => $reason]);
                        }

                        $this->sendCancellationNotification($record, $reason);

                        $training = $this->getOwnerRecord();
                        $record->delete();

                        TrainingCapacityService::handleSpotFreed($training);

                        Notification::make()
                            ->success()
                            ->title('Registrácia bola odstránená.')
                            ->send();
                    }),
            ])
            ->toolbarActions([
                SendEmailBulkAction::make('send_email_bulk')
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveRegistrationRecipient($record);
                    }),
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function getTrainingFormSchema(): array
    {
        /** @var Training $training */
        $training = $this->getOwnerRecord();

        return $training->registration_form_schema ?? [];
    }

    /**
     * @return list<\Filament\Forms\Components\Component>
     */
    protected function buildDynamicFormFields(): array
    {
        $fields = [];

        foreach ($this->getTrainingFormSchema() as $fieldDef) {
            $name = 'form_data.'.$fieldDef['name'];
            $label = is_array($fieldDef['label'] ?? null)
                ? ($fieldDef['label']['sk'] ?? reset($fieldDef['label']))
                : ($fieldDef['label'] ?? $fieldDef['name']);
            $isRequired = $fieldDef['required'] ?? false;
            $isHalf = ($fieldDef['width'] ?? 'full') === 'half';
            $type = RegistrationFieldTypeEnum::tryFrom($fieldDef['type'] ?? '');

            $field = match ($type) {
                RegistrationFieldTypeEnum::TEXTAREA => Textarea::make($name)
                    ->label($label)
                    ->rows(3),
                RegistrationFieldTypeEnum::SELECT => Select::make($name)
                    ->label($label)
                    ->options($this->parseOptions($fieldDef['options'] ?? '')),
                RegistrationFieldTypeEnum::MULTI_SELECT => Select::make($name)
                    ->label($label)
                    ->multiple()
                    ->options($this->parseOptions($fieldDef['options'] ?? '')),
                RegistrationFieldTypeEnum::DATE_PICKER => DatePicker::make($name)
                    ->label($label),
                RegistrationFieldTypeEnum::YEAR_PICKER => Select::make($name)
                    ->label($label)
                    ->options(array_combine(
                        range(date('Y'), date('Y') - 80),
                        range(date('Y'), date('Y') - 80),
                    )),
                RegistrationFieldTypeEnum::NUMBER_INPUT => TextInput::make($name)
                    ->label($label)
                    ->numeric(),
                RegistrationFieldTypeEnum::TIME_PICKER => TimePicker::make($name)
                    ->label($label),
                RegistrationFieldTypeEnum::PHONE => TextInput::make($name)
                    ->label($label)
                    ->tel(),
                RegistrationFieldTypeEnum::EMAIL => TextInput::make($name)
                    ->label($label)
                    ->email(),
                RegistrationFieldTypeEnum::FILE_INPUT => FileUpload::make($name)
                    ->label($label)
                    ->disk('public')
                    ->directory('registrations')
                    ->visibility('public'),
                default => TextInput::make($name)
                    ->label($label),
            };

            if ($isRequired) {
                $field->required();
            }

            if (! $isHalf) {
                $field->columnSpanFull();
            }

            if ($fieldDef['has_condition'] ?? false) {
                $conditionField = $fieldDef['condition_field'] ?? null;
                $conditionValue = $fieldDef['condition_value'] ?? null;
                if ($conditionField && $conditionValue) {
                    $field->visible(fn (Get $get): bool => $get('form_data.'.$conditionField) == $conditionValue);
                }
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * @return array<string, string>
     */
    protected function parseOptions(string|array $options): array
    {
        if (is_array($options)) {
            return array_combine($options, $options);
        }

        $items = array_map('trim', explode(',', $options));

        return array_combine($items, $items);
    }

    protected function sendCancellationNotification(TrainingRegistration $record, ?string $reason = null): void
    {
        if (! $record->user_id) {
            return;
        }

        $user = User::find($record->user_id);
        if (! $user) {
            return;
        }

        /** @var Training $training */
        $training = $this->getOwnerRecord();

        $user->notify(new TrainingRegistrationCancelled($training, $reason));
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractFormData(array $data): array
    {
        $formData = [];
        foreach ($this->getTrainingFormSchema() as $fieldDef) {
            $key = $fieldDef['name'];
            if (isset($data['form_data'][$key])) {
                $formData[$key] = $data['form_data'][$key];
            }
        }

        return $formData;
    }

    /**
     * @return list<array{email: string, variables: array<string, string>}>
     */
    protected function resolveRegistrationRecipient(TrainingRegistration $record): array
    {
        $email = $record->user?->email;
        if (! $email) {
            $schema = $this->getTrainingFormSchema();
            $email = RegistrationService::extractEmailFromFormData($record->form_data ?? [], $schema);
        }

        if (! $email) {
            return [];
        }

        /** @var Training $training */
        $training = $this->getOwnerRecord();
        $teamName = $training->team->getTranslation('name', 'sk');

        return [
            [
                'email' => $email,
                'variables' => [
                    'meno' => $record->user?->name ?? '',
                    'email' => $email,
                    'nazov_timu' => $teamName,
                    'nazov_treningu' => $training->getTranslation('title', 'sk'),
                    'miesto' => $training->getTranslation('place_name', 'sk') ?? '',
                    'cas' => $training->start_time ?? '',
                    'kapacita' => (string) ($training->max_capacity ?? ''),
                ],
            ],
        ];
    }

    protected function makeTrainingSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildAllRecipientsPlaceholder()],
                (new \App\Filament\Actions\SendEmailAction('temp'))
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription(fn () => 'E-mail bude odoslaný všetkým registrovaným.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $allRecipients = [];

                foreach ($this->getOwnerRecord()->registrations()->with('user')->get() as $registration) {
                    $allRecipients = array_merge($allRecipients, $this->resolveRegistrationRecipient($registration));
                }

                if (empty($allRecipients)) {
                    Notification::make()->warning()->title('Žiadni príjemcovia')->send();

                    return;
                }

                $count = \App\Services\EmailService::send(
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

    protected function buildAllRecipientsPlaceholder(): \Filament\Forms\Components\Placeholder
    {
        $emails = collect();
        foreach ($this->getOwnerRecord()->registrations()->with('user')->get() as $reg) {
            foreach ($this->resolveRegistrationRecipient($reg) as $r) {
                $emails->push($r['email']);
            }
        }

        $unique = $emails->unique()->values();
        $list = $unique->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return \Filament\Forms\Components\Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$unique->count().')')
            ->content(new \Illuminate\Support\HtmlString($unique->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }
}

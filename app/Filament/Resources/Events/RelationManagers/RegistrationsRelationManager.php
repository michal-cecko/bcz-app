<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Models\Event;
use App\Models\User;
use App\Services\EmailService;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
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
                            ->options([
                                'pending' => 'Čakajúci',
                                'confirmed' => 'Potvrdený',
                                'cancelled' => 'Zrušený',
                            ])
                            ->default('confirmed')
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
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Meno')
                    ->searchable()
                    ->placeholder('Hosť'),
                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->placeholder('-'),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->placeholder('-')
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('weight_in')
                    ->label('Váha')
                    ->suffix(' kg')
                    ->placeholder('-')
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                $this->makeEventSendEmailAction(),
                CreateAction::make()
                    ->label('Zaregistrovať')
                    ->modalHeading('Pridať zákazníka do podujatia')
                    ->after(function (array $data) {
                        $sendNotification = $data['send_notification'] ?? false;
                        if (! $sendNotification || empty($data['user_id'])) {
                            return;
                        }

                        $user = User::find($data['user_id']);
                        if (! $user) {
                            return;
                        }

                        $event = $this->getOwnerRecord();

                        RegistrationService::sendConfirmation(
                            user: $user,
                            registrationType: 'podujatie',
                            registrationTitle: $event->getTranslation('title', 'sk'),
                            team: $event->team,
                            customEmailContent: $event->organization?->confirmation_email_content,
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
                ViewAction::make()
                    ->modalHeading('Zobraziť registráciu'),
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
        $email = $record->user?->email;
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
                    'meno' => $record->user?->name ?? '',
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

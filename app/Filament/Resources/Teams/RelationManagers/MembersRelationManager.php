<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\InvitationStatusEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Filament\Resources\Users\UserResource;
use App\Mail\TeamInvitationMail;
use App\Models\TeamInvitation;
use App\Models\TeamSeason;
use App\Models\User;
use App\Services\EmailService;
use App\Services\SeasonService;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Členovia';

    protected static ?string $modelLabel = 'člen';

    protected static ?string $pluralModelLabel = 'Členovia';

    /**
     * See {@see \App\Filament\Resources\Teams\RelationManagers\SeasonsRelationManager::isReadOnly()}
     * — without this, `DetachAction`/`DetachBulkAction` are hidden on `ViewTeam`
     * even though they work on `EditTeam` for the same team and user. Note that
     * Filament's Detach actions only consult `isReadOnly()` and never fall back
     * to a policy check, so this must stay based on `TeamPolicy::update` rather
     * than a blanket `false`.
     */
    public function isReadOnly(): bool
    {
        return ! auth()->user()?->can('update', $this->getOwnerRecord());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable()
                    ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record])),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roly')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RoleEnum::tryFrom($state)?->getLabel() ?? $state),
                TextColumn::make('pivot.joined_at')
                    ->label('Pripojený')
                    ->date()
                    ->placeholder('-'),
                TextColumn::make('membership_status')
                    ->label('Členstvo')
                    ->badge()
                    ->state(function (User $record, RelationManager $livewire): ?MembershipStatusEnum {
                        $team = $livewire->getOwnerRecord();
                        $membership = $record->memberships()
                            ->where('team_id', $team->id)
                            ->latest()
                            ->first();

                        return $membership?->status;
                    }),
                TextColumn::make('membership_expires_at')
                    ->label('Členstvo do')
                    ->state(function (User $record, RelationManager $livewire): ?string {
                        $team = $livewire->getOwnerRecord();
                        $membership = $record->memberships()
                            ->where('team_id', $team->id)
                            ->where('status', MembershipStatusEnum::ACTIVE)
                            ->latest()
                            ->first();

                        return $membership?->ends_at?->format('d.m.Y');
                    })
                    ->placeholder('-'),
            ])
            ->headerActions([
                $this->makeMembersSendEmailAction(),
                Action::make('invite')
                    ->label('Pozvať člena')
                    ->modalHeading('Pozvať člena')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        $team = $livewire->getOwnerRecord();
                        $email = $data['email'];

                        if ($team->members()->where('email', $email)->exists()) {
                            Notification::make()
                                ->title('Tento používateľ je už členom tímu.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $existingInvitation = TeamInvitation::where('team_id', $team->id)
                            ->where('email', $email)
                            ->where('status', InvitationStatusEnum::Pending)
                            ->exists();

                        if ($existingInvitation) {
                            Notification::make()
                                ->title('Pozvánka pre tento e-mail už existuje.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $invitation = TeamInvitation::create([
                            'team_id' => $team->id,
                            'email' => $email,
                            'code' => strtoupper(Str::random(8)),
                            'status' => InvitationStatusEnum::Pending,
                            'invited_by' => Auth::id(),
                            'expires_at' => now()->addDays(7),
                        ]);

                        Mail::to($email)->send(new TeamInvitationMail($invitation));

                        Notification::make()
                            ->title('Pozvánka bola odoslaná.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                SendEmailAction::make('send_email')
                    ->resolveRecipients(function (User $record) {
                        $team = $this->getOwnerRecord();

                        return [
                            [
                                'email' => $record->email,
                                'variables' => [
                                    'meno' => $record->name,
                                    'email' => $record->email,
                                    'nazov_timu' => $team->getTranslation('name', 'sk'),
                                ],
                            ],
                        ];
                    }),
                Action::make('addMembership')
                    ->label('Pridať členstvo')
                    ->modalHeading('Pridať členstvo')
                    ->icon('heroicon-o-identification')
                    ->visible(fn (User $record): bool => $record->hasRole(RoleEnum::CUSTOMER->value))
                    ->schema([
                        Select::make('team_season_id')
                            ->label('Sezóna')
                            ->options(function (RelationManager $livewire): array {
                                $team = $livewire->getOwnerRecord();

                                return TeamSeason::where('team_id', $team->id)
                                    ->where('ends_at', '>=', now())
                                    ->orderBy('starts_at', 'desc')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $season = TeamSeason::find($state);

                                if ($season) {
                                    $set('fee_amount', (string) $season->proratedFee());
                                    $set('fee_currency', $season->fee_currency);
                                }
                            }),
                        Toggle::make('is_free')
                            ->label('Zadarmo')
                            ->live()
                            ->afterStateUpdated(function (bool $state, Set $set, Get $get): void {
                                if ($state) {
                                    $set('fee_amount', '0');
                                } elseif ($get('team_season_id')) {
                                    $season = TeamSeason::find($get('team_season_id'));
                                    if ($season) {
                                        $set('fee_amount', (string) $season->proratedFee());
                                    }
                                }
                            }),
                        TextInput::make('fee_amount')
                            ->label('Suma')
                            ->numeric()
                            ->required(),
                        Select::make('fee_currency')
                            ->label('Mena')
                            ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                            ->default(fn (RelationManager $livewire): string => $livewire->getOwnerRecord()->membership_fee_currency ?? 'EUR')
                            ->required(),
                    ])
                    ->action(function (array $data, User $record, RelationManager $livewire): void {
                        $season = TeamSeason::find($data['team_season_id']);

                        if (! $season) {
                            Notification::make()
                                ->title('Sezóna nebola nájdená.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $seasonService = app(SeasonService::class);

                        if ($data['is_free'] ?? false) {
                            $membership = $seasonService->addMidSeasonMember($season, $record);
                            $seasonService->markMembershipFree($membership);
                        } else {
                            $seasonService->addMidSeasonMember($season, $record);
                        }

                        Notification::make()
                            ->title('Členstvo bolo vytvorené.')
                            ->success()
                            ->send();
                    }),
                DetachAction::make(),
            ])
            ->toolbarActions([
                SendEmailBulkAction::make('send_email_bulk')
                    ->resolveRecipients(function (User $record) {
                        $team = $this->getOwnerRecord();

                        return [
                            [
                                'email' => $record->email,
                                'variables' => [
                                    'meno' => $record->name,
                                    'email' => $record->email,
                                    'nazov_timu' => $team->getTranslation('name', 'sk'),
                                ],
                            ],
                        ];
                    }),
                DetachBulkAction::make(),
            ]);
    }

    protected function makeMembersSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildMembersRecipientsPlaceholder()],
                (new SendEmailAction('temp'))->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription('E-mail bude odoslaný všetkým členom tímu.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $team = $this->getOwnerRecord();
                $teamName = $team->getTranslation('name', 'sk');
                $allRecipients = [];

                foreach ($team->members as $member) {
                    $allRecipients[] = [
                        'email' => $member->email,
                        'variables' => [
                            'meno' => $member->name,
                            'email' => $member->email,
                            'nazov_timu' => $teamName,
                        ],
                    ];
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

    protected function buildMembersRecipientsPlaceholder(): Placeholder
    {
        $emails = $this->getOwnerRecord()->members->pluck('email')->unique()->values();
        $list = $emails->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$emails->count().')')
            ->content(new HtmlString($emails->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }
}

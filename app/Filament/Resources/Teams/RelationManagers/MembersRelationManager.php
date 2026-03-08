<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\InvitationStatusEnum;
use App\Enums\MembershipPeriodEnum;
use App\Enums\MembershipStatusEnum;
use App\Mail\TeamInvitationMail;
use App\Models\Membership;
use App\Models\TeamInvitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Členovia';

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
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roly')
                    ->badge(),
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
                Action::make('invite')
                    ->label('Pozvať člena')
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
                Action::make('addMembership')
                    ->label('Pridať členstvo')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Select::make('period')
                            ->label('Obdobie')
                            ->options(MembershipPeriodEnum::translations())
                            ->required(),
                        TextInput::make('fee_amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->default(fn (RelationManager $livewire): ?string => (string) $livewire->getOwnerRecord()->membership_fee_amount),
                        Select::make('fee_currency')
                            ->label('Mena')
                            ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                            ->default(fn (RelationManager $livewire): string => $livewire->getOwnerRecord()->membership_fee_currency ?? 'EUR')
                            ->required(),
                        DatePicker::make('starts_at')
                            ->label('Začiatok')
                            ->default(now())
                            ->required(),
                        DatePicker::make('ends_at')
                            ->label('Koniec')
                            ->default(now()->addYear())
                            ->required(),
                    ])
                    ->action(function (array $data, User $record, RelationManager $livewire): void {
                        $team = $livewire->getOwnerRecord();

                        Membership::create([
                            'team_id' => $team->id,
                            'user_id' => $record->id,
                            'status' => MembershipStatusEnum::PENDING,
                            'period' => $data['period'],
                            'fee_amount' => $data['fee_amount'],
                            'fee_currency' => $data['fee_currency'],
                            'starts_at' => $data['starts_at'],
                            'ends_at' => $data['ends_at'],
                        ]);

                        Notification::make()
                            ->title('Členstvo bolo vytvorené.')
                            ->success()
                            ->send();
                    }),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}

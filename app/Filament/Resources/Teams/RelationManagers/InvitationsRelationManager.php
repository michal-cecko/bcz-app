<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\InvitationStatusEnum;
use App\Mail\TeamInvitationMail;
use App\Models\TeamInvitation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationsRelationManager extends RelationManager
{
    protected static string $relationship = 'invitations';

    protected static ?string $title = 'Pozvánky';

    protected static ?string $modelLabel = 'pozvánka';

    protected static ?string $pluralModelLabel = 'Pozvánky';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Kód')
                    ->copyable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('invitedByUser.name')
                    ->label('Pozval'),
                TextColumn::make('expires_at')
                    ->label('Platnosť do')
                    ->dateTime('d.m.Y H:i'),
                TextColumn::make('accepted_at')
                    ->label('Prijatá')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('create')
                    ->label('Vytvoriť pozvánku')
                    ->modalHeading('Vytvoriť pozvánku')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $team = $this->getOwnerRecord();
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

                        $code = strtoupper(Str::random(8));

                        $invitation = TeamInvitation::create([
                            'team_id' => $team->id,
                            'email' => $email,
                            'code' => $code,
                            'status' => InvitationStatusEnum::Pending,
                            'invited_by' => Auth::id(),
                            'expires_at' => now()->addDays(7),
                        ]);

                        Mail::to($email)->send(new TeamInvitationMail($invitation));

                        Notification::make()
                            ->title('Pozvánka bola vytvorená a odoslaná.')
                            ->body("Kód: {$code}")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label('Znovu odoslať')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === InvitationStatusEnum::Pending)
                    ->action(function ($record): void {
                        $record->update([
                            'expires_at' => now()->addDays(7),
                        ]);

                        Mail::to($record->email)->send(new TeamInvitationMail($record));

                        Notification::make()
                            ->title('Pozvánka bola znovu odoslaná.')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Zrušiť')
                    ->visible(fn ($record) => $record->status === InvitationStatusEnum::Pending),
            ]);
    }
}

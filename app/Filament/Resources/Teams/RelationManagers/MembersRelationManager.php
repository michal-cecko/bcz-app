<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\InvitationStatusEnum;
use App\Mail\TeamInvitationMail;
use App\Models\TeamInvitation;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
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
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}

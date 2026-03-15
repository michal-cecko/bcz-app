<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\JoinRequestStatusEnum;
use App\Models\TeamJoinRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JoinRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'joinRequests';

    protected static ?string $title = 'Žiadosti o pripojenie';

    protected static ?string $modelLabel = 'žiadosť';

    protected static ?string $pluralModelLabel = 'Žiadosti';

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
                TextColumn::make('user.name')
                    ->label('Registrovaný používateľ')
                    ->placeholder('Hosť'),
                TextColumn::make('message')
                    ->label('Správa')
                    ->limit(50)
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Odoslaná')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->label('Spracovaná')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(JoinRequestStatusEnum::class),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Schváliť')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (TeamJoinRequest $record): bool => $record->isPending())
                    ->action(function (TeamJoinRequest $record): void {
                        $team = $this->getOwnerRecord();

                        if ($record->user_id && $team->members()->where('users.id', $record->user_id)->exists()) {
                            Notification::make()
                                ->title('Tento používateľ je už členom tímu.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'status' => JoinRequestStatusEnum::Approved,
                            'processed_at' => now(),
                        ]);

                        if ($record->user_id) {
                            $team->members()->attach($record->user_id, [
                                'is_active' => true,
                                'joined_at' => now(),
                            ]);
                        }

                        Notification::make()
                            ->title('Žiadosť bola schválená.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Zamietnuť')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (TeamJoinRequest $record): bool => $record->isPending())
                    ->action(function (TeamJoinRequest $record): void {
                        $record->update([
                            'status' => JoinRequestStatusEnum::Rejected,
                            'processed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Žiadosť bola zamietnutá.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}

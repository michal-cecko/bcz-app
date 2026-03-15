<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Models\User;
use App\Services\RegistrationService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                        );

                        Notification::make()
                            ->success()
                            ->title('Notifikácia odoslaná')
                            ->body("E-mail bol odoslaný na {$user->email}")
                            ->send();
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Zobraziť registráciu'),
                EditAction::make()
                    ->modalHeading('Upraviť registráciu'),
                DeleteAction::make()
                    ->modalHeading('Odstrániť registráciu'),
            ]);
    }
}

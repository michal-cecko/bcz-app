<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Enums\CoachRoleEnum;
use App\Enums\RoleEnum;
use App\Models\Training;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoachesRelationManager extends RelationManager
{
    protected static string $relationship = 'coaches';

    protected static ?string $title = 'Tréneri';

    protected static ?string $modelLabel = 'tréner';

    protected static ?string $pluralModelLabel = 'Tréneri';

    protected function getMainCoachCount(): int
    {
        return DB::table('coach_training')
            ->where('training_id', $this->getOwnerRecord()->getKey())
            ->where('role', CoachRoleEnum::MAIN->value)
            ->count();
    }

    /**
     * By default Filament treats this relation manager as read-only whenever it
     * renders on a Resource's `ViewRecord` page — which hides Attach/Detach
     * entirely, since Filament's DetachAction/DetachBulkAction only consult
     * `isReadOnly()` and never fall back to a policy check (see
     * `RelationManager::getDefaultActionAuthorizationResponse()`).
     *
     * `TrainingsTable::recordUrl()` always routes the training list to
     * `ViewTraining`, so every user — including admins — lands there by
     * default. That made "Priradiť trénera" / detach coach invisible unless
     * someone separately clicked through to the Edit page, which read as a
     * missing detach action.
     *
     * Base read-only-ness on whether the acting user can actually manage this
     * training (`TrainingPolicy::update`) instead of on the page type, so
     * attach/detach show up wherever the training is genuinely editable for
     * them (View or Edit), while still remaining hidden for users who cannot
     * manage this specific training's coaches at all.
     */
    public function isReadOnly(): bool
    {
        return ! auth()->user()?->can('update', $this->getOwnerRecord());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->label('Rola')
                    ->options(CoachRoleEnum::class)
                    ->required()
                    ->default(CoachRoleEnum::MAIN),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail'),
                TextColumn::make('pivot.role')
                    ->label('Rola')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CoachRoleEnum::from($state)->getLabel())
                    ->color(fn (string $state): string => CoachRoleEnum::tryFrom($state)?->getColor() ?? 'gray'),
            ])
            ->headerActions([
                Action::make('attach')
                    ->label('Priradiť trénera')
                    ->modalHeading('Priradiť trénera k tréningu')
                    ->schema([
                        Select::make('user_id')
                            ->label('Tréner')
                            ->options(function (): array {
                                /** @var Training $training */
                                $training = $this->getOwnerRecord();

                                return User::query()
                                    ->whereHas('teams', fn ($q) => $q
                                        ->where('teams.id', $training->team_id)
                                        ->whereIn('team_user.role', [RoleEnum::TEAM_ADMIN->value, RoleEnum::COACH->value])
                                    )
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->required(),
                        Select::make('role')
                            ->label('Rola')
                            ->options(CoachRoleEnum::class)
                            ->required()
                            ->default(CoachRoleEnum::MAIN),
                    ])
                    ->action(function (array $data): void {
                        $this->getOwnerRecord()->coaches()->attach($data['user_id'], [
                            'role' => $data['role'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Tréner bol priradený.')
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Upraviť trénera')
                    ->before(function (Model $record, array $data, EditAction $action): void {
                        if (($data['role'] ?? null) !== CoachRoleEnum::MAIN->value
                            && $record->pivot->role === CoachRoleEnum::MAIN->value
                            && $this->getMainCoachCount() <= 1
                        ) {
                            Notification::make()
                                ->danger()
                                ->title('Tréning musí mať aspoň jedného hlavného trénera.')
                                ->send();

                            $action->halt();
                        }
                    }),
                DetachAction::make()
                    ->successNotificationTitle('Tréner bol odobraný z tréningu.')
                    ->before(function (Model $record, DetachAction $action): void {
                        if ($record->pivot->role === CoachRoleEnum::MAIN->value
                            && $this->getMainCoachCount() <= 1
                        ) {
                            Notification::make()
                                ->danger()
                                ->title('Nie je možné odpojiť posledného hlavného trénera.')
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}

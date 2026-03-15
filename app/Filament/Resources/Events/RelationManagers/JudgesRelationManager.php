<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Models\Discipline;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class JudgesRelationManager extends RelationManager
{
    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Rozhodcovia';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table->columns([]);
        }

        return $table
            ->relationship(fn () => $detail->judges())
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('pivot.discipline_id')
                    ->label('Disciplína')
                    ->state(function ($record): string {
                        $discipline = Discipline::find($record->pivot->discipline_id);

                        return $discipline?->getTranslation('name', 'sk') ?? '-';
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Rozhodca')
                            ->searchable()
                            ->preload(),
                        Select::make('discipline_id')
                            ->label('Disciplína')
                            ->options(Discipline::all()->mapWithKeys(fn (Discipline $d) => [$d->id => $d->getTranslation('name', 'sk')]))
                            ->required()
                            ->searchable(),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Models\Discipline;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
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

    protected static ?string $modelLabel = 'rozhodca';

    protected static ?string $pluralModelLabel = 'Rozhodcovia';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('discipline_id')
                ->label('Disciplína')
                ->options(Discipline::all()->mapWithKeys(fn (Discipline $d) => [$d->id => $d->getTranslation('name', 'sk')]))
                ->required()
                ->searchable(),
        ]);
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
                    ->modalHeading('Priradiť rozhodcu')
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
                EditAction::make()
                    ->modalHeading('Upraviť rozhodcu')
                    ->fillForm(fn (Model $record): array => [
                        'discipline_id' => $record->pivot->discipline_id,
                    ])
                    ->using(function (Model $record, array $data) use ($detail): Model {
                        $detail->judges()->updateExistingPivot($record->id, [
                            'discipline_id' => $data['discipline_id'],
                        ]);

                        return $record;
                    }),
                DetachAction::make()
                    ->modalHeading('Odstrániť rozhodcu'),
            ]);
    }
}

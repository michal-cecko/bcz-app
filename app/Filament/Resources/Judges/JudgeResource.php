<?php

namespace App\Filament\Resources\Judges;

use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Resources\Judges\Pages\CreateJudge;
use App\Filament\Resources\Judges\Pages\EditJudge;
use App\Filament\Resources\Judges\Pages\ListJudges;
use App\Filament\Resources\Judges\Schemas\JudgeForm;
use App\Filament\Resources\Judges\Tables\JudgesTable;
use App\Models\Judge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JudgeResource extends Resource
{
    protected static ?string $model = Judge::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $modelLabel = 'rozhodcu';

    protected static ?string $pluralModelLabel = 'Rozhodcovia';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = ContentCluster::class;

    protected static ?int $navigationSort = 20;

    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function canGloballySearch(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return JudgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JudgesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJudges::route('/'),
            'create' => CreateJudge::route('/create'),
            'edit' => EditJudge::route('/{record}/edit'),
        ];
    }
}

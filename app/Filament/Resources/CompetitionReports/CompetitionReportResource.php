<?php

namespace App\Filament\Resources\CompetitionReports;

use App\Filament\Clusters\Competitions\CompetitionsCluster;
use App\Filament\Resources\CompetitionReports\Pages\CreateCompetitionReport;
use App\Filament\Resources\CompetitionReports\Pages\EditCompetitionReport;
use App\Filament\Resources\CompetitionReports\Pages\ListCompetitionReports;
use App\Filament\Resources\CompetitionReports\Schemas\CompetitionReportForm;
use App\Filament\Resources\CompetitionReports\Tables\CompetitionReportsTable;
use App\Models\CompetitionReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompetitionReportResource extends Resource
{
    protected static ?string $model = CompetitionReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'report zo súťaže';

    protected static ?string $pluralModelLabel = 'Reporty zo súťaží';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = CompetitionsCluster::class;

    protected static ?int $navigationSort = 4;

    protected static bool $isScopedToTenant = false;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('title', 'sk');
    }

    public static function form(Schema $schema): Schema
    {
        return CompetitionReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitionReports::route('/'),
            'create' => CreateCompetitionReport::route('/create'),
            'edit' => EditCompetitionReport::route('/{record}/edit'),
        ];
    }
}

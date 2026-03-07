<?php

namespace App\Filament\Resources\CompetitionReports;

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

class CompetitionReportResource extends Resource
{
    protected static ?string $model = CompetitionReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Report zo súťaže';

    protected static ?string $pluralModelLabel = 'Reporty zo súťaží';

    protected static string|\UnitEnum|null $navigationGroup = 'Súťaže';

    protected static ?int $navigationSort = 4;

    protected static bool $isScopedToTenant = false;

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

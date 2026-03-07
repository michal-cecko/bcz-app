<?php

namespace App\Filament\Resources\CompetitionReports\Pages;

use App\Filament\Resources\CompetitionReports\CompetitionReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionReports extends ListRecords
{
    protected static string $resource = CompetitionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

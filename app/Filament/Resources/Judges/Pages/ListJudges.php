<?php

namespace App\Filament\Resources\Judges\Pages;

use App\Filament\Resources\Judges\JudgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJudges extends ListRecords
{
    protected static string $resource = JudgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

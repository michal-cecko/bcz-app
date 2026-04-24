<?php

namespace App\Filament\Resources\Judges\Pages;

use App\Filament\Resources\Judges\JudgeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJudge extends EditRecord
{
    protected static string $resource = JudgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Competitions\Pages;

use App\Filament\Resources\Competitions\CompetitionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCompetition extends CreateRecord
{
    protected static string $resource = CompetitionResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

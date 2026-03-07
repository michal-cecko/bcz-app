<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

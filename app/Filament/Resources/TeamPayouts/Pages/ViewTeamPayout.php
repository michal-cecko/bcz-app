<?php

namespace App\Filament\Resources\TeamPayouts\Pages;

use App\Filament\Resources\TeamPayouts\TeamPayoutResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewTeamPayout extends ViewRecord
{
    protected static string $resource = TeamPayoutResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

<?php

namespace App\Filament\Resources\Athletes\Pages;

use App\Filament\Resources\Athletes\AthleteResource;
use Filament\Resources\Pages\ListRecords;

class ListAthletes extends ListRecords
{
    protected static string $resource = AthleteResource::class;
}

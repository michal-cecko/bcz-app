<?php

namespace App\Filament\Resources\Disciplines\Pages;

use App\Filament\Resources\Disciplines\DisciplineResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateDiscipline extends CreateRecord
{
    protected static string $resource = DisciplineResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

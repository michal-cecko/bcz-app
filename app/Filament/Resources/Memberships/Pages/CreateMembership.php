<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}

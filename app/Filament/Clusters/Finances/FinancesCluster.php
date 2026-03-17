<?php

namespace App\Filament\Clusters\Finances;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class FinancesCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Financie';

    protected static ?string $clusterBreadcrumb = 'Financie';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}

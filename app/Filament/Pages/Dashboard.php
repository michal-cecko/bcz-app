<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AvailableTrainingsWidget;
use App\Filament\Widgets\MembershipStatusWidget;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Filament\Widgets\UpcomingTrainingsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $user = auth()->user();

        if ($user?->isMemberLevel()) {
            return [
                MembershipStatusWidget::class,
                UpcomingTrainingsWidget::class,
                AvailableTrainingsWidget::class,
                RecentPaymentsWidget::class,
            ];
        }

        return [
            AccountWidget::class,
        ];
    }
}

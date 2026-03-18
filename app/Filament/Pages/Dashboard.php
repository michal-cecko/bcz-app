<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CompleteProfileWidget;
use App\Filament\Widgets\MembershipStatusWidget;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Filament\Widgets\UpcomingTrainingsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Prehľad';

    public function getTitle(): string
    {
        $user = auth()->user();

        if ($user?->isMemberLevel()) {
            $firstName = $user->first_name ?? explode(' ', $user->name ?? '')[0] ?? '';

            return "Ahoj, {$firstName}!";
        }

        return 'Prehľad';
    }

    public function getWidgets(): array
    {
        $user = auth()->user();

        if ($user?->isMemberLevel()) {
            return [
                CompleteProfileWidget::class,
                MembershipStatusWidget::class,
                RecentPaymentsWidget::class,
                UpcomingTrainingsWidget::class,
            ];
        }

        return [
            CompleteProfileWidget::class,
            AccountWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}

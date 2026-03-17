<?php

namespace App\Filament\Widgets;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class MembershipStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.membership-status-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $team = Filament::getTenant();
        $user = auth()->user();

        $membership = Membership::query()
            ->where('team_id', $team?->id)
            ->where('user_id', $user?->id)
            ->orderByDesc('created_at')
            ->first();

        return [
            'membership' => $membership,
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Widgets\Widget;

class CompleteProfileWidget extends Widget
{
    protected string $view = 'filament.widgets.complete-profile-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        if (! $user->hasRole([RoleEnum::CUSTOMER->value]) && ! $user->hasRole([RoleEnum::ATHLETE->value])) {
            return false;
        }

        return $user->isProfileIncomplete();
    }
}

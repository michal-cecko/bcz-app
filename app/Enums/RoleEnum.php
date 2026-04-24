<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum RoleEnum: string implements HasLabel
{
    use EnumHelper;

    case SUPER_ADMIN = 'SUPERADMIN';
    case ADMIN = 'ADMIN';
    case TEAM_ADMIN = 'TEAMADMIN';
    case COACH = 'COACH';
    case ATHLETE = 'ATHLETE';
    case EDITOR = 'EDITOR';
    case CUSTOMER = 'CUSTOMER';

    public function isTeamScoped(): bool
    {
        return in_array($this, [
            self::TEAM_ADMIN,
            self::COACH,
            self::ATHLETE,
        ]);
    }

    public function isGlobal(): bool
    {
        return ! $this->isTeamScoped();
    }

    /** @return list<self> */
    public static function teamScopedCases(): array
    {
        return [self::TEAM_ADMIN, self::COACH, self::ATHLETE];
    }

    /** @return list<self> */
    public static function globalCases(): array
    {
        return array_values(array_filter(self::cases(), fn (self $r) => $r->isGlobal()));
    }
}

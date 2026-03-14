<?php

namespace App\Enums;

use App\Models\Competition;
use App\Models\Event;
use App\Models\MediaLibraryItem;
use App\Models\Page;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;

enum LinkTypeEnum: string
{
    case Page = 'page';
    case Training = 'training';
    case Competition = 'competition';
    case Event = 'event';
    case Team = 'team';
    case Coach = 'coach';
    case Athlete = 'athlete';
    case Judge = 'judge';
    case Media = 'media';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Page => 'Stránka',
            self::Training => 'Tréning',
            self::Competition => 'Súťaž',
            self::Event => 'Vystúpenie',
            self::Team => 'Tím',
            self::Coach => 'Tréner',
            self::Athlete => 'Atlét',
            self::Judge => 'Rozhodca',
            self::Media => 'Súbor z knižnice',
            self::Custom => 'Vlastná URL',
        };
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model&\App\Contracts\Linkable>|null
     */
    public function getModelClass(): ?string
    {
        return match ($this) {
            self::Page => Page::class,
            self::Training => Training::class,
            self::Competition => Competition::class,
            self::Event => Event::class,
            self::Team => Team::class,
            self::Coach, self::Athlete, self::Judge => User::class,
            self::Media => MediaLibraryItem::class,
            self::Custom => null,
        };
    }

    /**
     * Returns the RoleEnum value to filter users by for role-based link types.
     */
    public function getRoleFilter(): ?string
    {
        return match ($this) {
            self::Coach => RoleEnum::COACH->value,
            self::Athlete => RoleEnum::ATHLETE->value,
            self::Judge => RoleEnum::JUDGE->value,
            default => null,
        };
    }

    /**
     * Returns the route prefix for role-based link types.
     */
    public function getRoutePrefix(): ?string
    {
        return match ($this) {
            self::Coach => '/treneri/',
            self::Athlete => '/atleti/',
            self::Judge => '/rozhodcovia/',
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->getLabel(), self::cases()),
        );
    }
}

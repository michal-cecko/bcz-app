<?php

namespace App\Enums;

use App\Models\Competition;
use App\Models\Event;
use App\Models\Page;
use App\Models\Team;
use App\Models\Training;

enum LinkTypeEnum: string
{
    case Page = 'page';
    case Training = 'training';
    case Competition = 'competition';
    case Event = 'event';
    case Team = 'team';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Page => 'Stránka',
            self::Training => 'Tréning',
            self::Competition => 'Súťaž',
            self::Event => 'Vystúpenie',
            self::Team => 'Tím',
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
            self::Custom => null,
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

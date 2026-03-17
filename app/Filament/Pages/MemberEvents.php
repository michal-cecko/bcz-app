<?php

namespace App\Filament\Pages;

use App\Models\Event;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class MemberEvents extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Podujatia';

    protected static ?string $title = 'Podujatia';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.member-events';

    #[Url]
    public string $tab = 'upcoming';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isMemberLevel() ?? false;
    }

    protected function getViewData(): array
    {
        $team = Filament::getTenant();
        $user = auth()->user();

        $query = Event::query()
            ->where('team_id', $team?->id)
            ->where('is_published', true)
            ->with(['eventCategory', 'registrations' => fn ($q) => $q->where('user_id', $user?->id)])
            ->orderBy('date');

        if ($this->tab === 'upcoming') {
            $events = $query->where('date', '>=', now()->startOfDay())->get();
        } else {
            $events = $query->where('date', '<', now()->startOfDay())->orderByDesc('date')->get();
        }

        return [
            'events' => $events,
        ];
    }
}

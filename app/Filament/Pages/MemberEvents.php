<?php

namespace App\Filament\Pages;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\Event;
use App\Models\EventRegistration;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Url;

class MemberEvents extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

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

    public function cancelRegistrationAction(): Action
    {
        return Action::make('cancelRegistration')
            ->label('Zrušiť registráciu')
            ->icon(Heroicon::XMark)
            ->color('danger')
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading('Zrušiť registráciu?')
            ->modalDescription('Po zrušení sa môžete znova zaregistrovať, ak je registrácia stále otvorená.')
            ->modalSubmitActionLabel('Áno, zrušiť')
            ->modalCancelActionLabel('Späť')
            ->action(function (array $arguments): void {
                $registration = EventRegistration::query()
                    ->where('id', $arguments['registration'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $registration || $registration->status === RegistrationStatusEnum::Cancelled) {
                    return;
                }

                $registration->update([
                    'status' => RegistrationStatusEnum::Cancelled->value,
                ]);

                $registration->payments()
                    ->where('status', PaymentStatusEnum::PENDING)
                    ->update(['status' => PaymentStatusEnum::CANCELLED->value]);

                Notification::make()
                    ->title('Registrácia bola zrušená')
                    ->success()
                    ->send();
            });
    }

    public function viewRegistrationAction(): Action
    {
        return Action::make('viewRegistration')
            ->label('Detail registrácie')
            ->icon(Heroicon::Eye)
            ->color('gray')
            ->size('sm')
            ->modalHeading('Detail registrácie')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Zavrieť')
            ->modalContent(function (array $arguments): ViewContract {
                $registration = EventRegistration::query()
                    ->where('id', $arguments['registration'])
                    ->where('user_id', auth()->id())
                    ->with(['event', 'payments'])
                    ->firstOrFail();

                return View::make(
                    'filament.components.registration-details-modal',
                    ['registration' => $registration],
                );
            });
    }

    protected function getViewData(): array
    {
        $team = Filament::getTenant();
        $user = auth()->user();

        $query = Event::query()
            ->where('team_id', $team?->id)
            ->where('is_published', true)
            ->with([
                'eventCategory',
                'registrations' => fn ($q) => $q->where('user_id', $user?->id)
                    ->where('status', '!=', RegistrationStatusEnum::Cancelled->value),
            ])
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

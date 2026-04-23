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

    public static function getNavigationLabel(): string
    {
        return __('member.events.title');
    }

    public function getTitle(): string
    {
        return __('member.events.title');
    }

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
            ->label(__('member.events.cancel_registration'))
            ->icon(Heroicon::XMark)
            ->color('danger')
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading(__('member.events.cancel_modal_heading'))
            ->modalDescription(__('member.events.cancel_modal_description'))
            ->modalSubmitActionLabel(__('member.events.cancel_confirm'))
            ->modalCancelActionLabel(__('member.events.cancel_back'))
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
                    ->title(__('member.events.cancel_success'))
                    ->success()
                    ->send();
            });
    }

    public function viewRegistrationAction(): Action
    {
        return Action::make('viewRegistration')
            ->label(__('member.events.view_registration'))
            ->icon(Heroicon::Eye)
            ->color('gray')
            ->size('sm')
            ->modalHeading(__('member.events.modal_heading'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('member.events.modal_close'))
            ->modalContent(function (array $arguments): ViewContract {
                $registration = EventRegistration::query()
                    ->where('id', $arguments['registration'])
                    ->where('user_id', auth()->id())
                    ->with(['event', 'payments', 'fieldValues'])
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

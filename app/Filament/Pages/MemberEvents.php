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
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
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
                    ->update(['status' => PaymentStatusEnum::FAILED->value]);

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
            ->fillForm(function (array $arguments): array {
                $registration = EventRegistration::query()
                    ->where('id', $arguments['registration'])
                    ->where('user_id', auth()->id())
                    ->with(['event', 'payments'])
                    ->firstOrFail();

                $latestPayment = $registration->payments->sortByDesc('created_at')->first();

                return [
                    'event_title' => $registration->event?->getTranslation('title', app()->getLocale())
                        ?: $registration->event?->getTranslation('title', 'sk'),
                    'event_date' => $registration->event?->date?->format('d.m.Y'),
                    'event_city' => $registration->event?->city,
                    'status' => $registration->status,
                    'registered_at' => $registration->registered_at?->format('d.m.Y H:i'),
                    'payment_status' => $latestPayment?->status?->value,
                    'payment_amount' => $latestPayment
                        ? number_format((float) $latestPayment->amount, 2).' '.$latestPayment->currency
                        : null,
                    'variable_symbol' => $latestPayment?->formattedVariableSymbol(),
                ];
            })
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('event_title')->label('Podujatie'),
                        TextEntry::make('event_date')->label('Dátum')->placeholder('-'),
                        TextEntry::make('event_city')->label('Miesto')->placeholder('-'),
                        TextEntry::make('status')->label('Stav registrácie')->badge(),
                        TextEntry::make('registered_at')->label('Zaregistrované')->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Platba')
                    ->schema([
                        TextEntry::make('payment_status')->label('Stav platby')->badge()->placeholder('-'),
                        TextEntry::make('payment_amount')->label('Suma')->placeholder('-'),
                        TextEntry::make('variable_symbol')->label('Variabilný symbol')->placeholder('-'),
                    ])
                    ->columns(3)
                    ->visible(fn (array $state): bool => ! empty($state['payment_status'])),
            ]);
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

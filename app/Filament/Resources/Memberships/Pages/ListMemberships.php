<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Actions\SendEmailAction;
use App\Filament\Resources\Memberships\MembershipResource;
use App\Models\Membership;
use App\Services\EmailService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ListMemberships extends ListRecords
{
    protected static string $resource = MembershipResource::class;

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if (auth()->user()?->isMemberLevel()) {
            $query?->where('user_id', auth()->id());
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->makeMembershipsSendEmailAction()
                ->visible(fn (): bool => ! auth()->user()?->isMemberLevel()),
            CreateAction::make()
                ->visible(fn (): bool => ! auth()->user()?->isMemberLevel()),
        ];
    }

    protected function makeMembershipsSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildMembershipsRecipientsPlaceholder()],
                (new SendEmailAction('temp'))->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription('E-mail bude odoslaný všetkým členom s členstvom.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $team = Filament::getTenant();
                $teamName = $team->getTranslation('name', 'sk');
                $allRecipients = [];

                $memberships = Membership::where('team_id', $team->id)
                    ->with('user')
                    ->get();

                foreach ($memberships as $membership) {
                    if (! $membership->user?->email) {
                        continue;
                    }

                    $allRecipients[] = [
                        'email' => $membership->user->email,
                        'variables' => [
                            'meno' => $membership->user->name,
                            'email' => $membership->user->email,
                            'nazov_timu' => $teamName,
                        ],
                    ];
                }

                if (empty($allRecipients)) {
                    Notification::make()->warning()->title('Žiadni príjemcovia')->send();

                    return;
                }

                $count = EmailService::send(
                    subject: $data['subject'],
                    brickContent: $data['content'],
                    recipients: $allRecipients,
                    team: $team,
                );

                Notification::make()
                    ->success()
                    ->title('E-mail odoslaný')
                    ->body("E-mail bol odoslaný {$count} príjemcom.")
                    ->send();
            });
    }

    protected function buildMembershipsRecipientsPlaceholder(): Placeholder
    {
        $team = Filament::getTenant();
        $emails = Membership::where('team_id', $team->id)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->filter()
            ->unique()
            ->values();

        $list = $emails->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$emails->count().')')
            ->content(new HtmlString($emails->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }
}

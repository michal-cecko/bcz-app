<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Enums\InquiryStatusEnum;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Inquiries\Tables\InquiriesTable;
use App\Models\Inquiry;
use App\Services\EmailService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeInquiriesSendEmailAction(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Všetky')
                ->badge(fn () => $this->getModel()::query()->count()),
            'new' => Tab::make('Nové')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::NEW))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::NEW)->count())
                ->badgeColor('danger'),
            'in_progress' => Tab::make('Prebieha')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::IN_PROGRESS))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::IN_PROGRESS)->count())
                ->badgeColor('warning'),
            'resolved' => Tab::make('Vyriešené')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::RESOLVED))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::RESOLVED)->count())
                ->badgeColor('success'),
        ];
    }

    protected function makeInquiriesSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildInquiriesRecipientsPlaceholder()],
                (new \App\Filament\Actions\SendEmailAction('temp'))->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription('E-mail bude odoslaný všetkým dopytujúcim.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $allRecipients = [];

                foreach (Inquiry::where('team_id', filament()->getTenant()->id)->get() as $inquiry) {
                    $allRecipients = array_merge($allRecipients, InquiriesTable::resolveInquiryRecipient($inquiry));
                }

                if (empty($allRecipients)) {
                    Notification::make()->warning()->title('Žiadni príjemcovia')->send();

                    return;
                }

                $count = EmailService::send(
                    subject: $data['subject'],
                    brickContent: $data['content'],
                    recipients: $allRecipients,
                    team: filament()->getTenant(),
                );

                Notification::make()
                    ->success()
                    ->title('E-mail odoslaný')
                    ->body("E-mail bol odoslaný {$count} príjemcom.")
                    ->send();
            });
    }

    protected function buildInquiriesRecipientsPlaceholder(): Placeholder
    {
        $emails = Inquiry::where('team_id', filament()->getTenant()->id)
            ->whereNotNull('email')
            ->pluck('email')
            ->unique()
            ->values();

        $list = $emails->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$emails->count().')')
            ->content(new HtmlString($emails->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }
}

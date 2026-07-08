<?php

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\HasSendEmailForm;
use App\Services\EmailService;
use Closure;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class SendEmailBulkAction extends BulkAction
{
    use HasSendEmailForm;

    protected ?Closure $resolveRecipients = null;

    /**
     * @var list<string>
     */
    protected array $contextVariables = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Odoslať e-mail');
        $this->icon(Heroicon::OutlinedEnvelope);
        $this->color('primary');
        $this->slideOver();

        $this->schema(fn (): array => array_merge(
            [$this->buildBulkRecipientPlaceholder()],
            $this->getEmailFormSchema(),
        ));

        $this->modalSubmitActionLabel('Odoslať e-mail');
        $this->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
            ->modalHeading('Potvrdiť odoslanie')
            ->modalDescription(fn () => $this->buildBulkConfirmationDescription())
            ->modalSubmitActionLabel('Áno, odoslať'));

        $this->action(function (array $data, Collection $records): void {
            $allRecipients = [];

            foreach ($records as $record) {
                $recipients = $this->resolveRecipients
                    ? call_user_func($this->resolveRecipients, $record)
                    : [];

                $allRecipients = array_merge($allRecipients, $recipients);
            }

            if (empty($allRecipients)) {
                Notification::make()
                    ->warning()
                    ->title('Žiadni príjemcovia')
                    ->body('Nenašli sa žiadni príjemcovia pre tento e-mail.')
                    ->send();

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

    public function resolveRecipients(Closure $callback): static
    {
        $this->resolveRecipients = $callback;

        return $this;
    }

    /**
     * @param  list<string>  $variables
     */
    public function contextVariables(array $variables): static
    {
        $this->contextVariables = $variables;

        return $this;
    }

    protected function getAvailableVariables(): array
    {
        return array_merge(
            ['meno', 'email', 'nazov_timu'],
            $this->contextVariables,
        );
    }

    protected function buildBulkRecipientPlaceholder(): Placeholder
    {
        $records = $this->getSelectedRecords();
        $allEmails = collect();

        foreach ($records as $record) {
            $recipients = $this->resolveRecipients
                ? call_user_func($this->resolveRecipients, $record)
                : [];

            foreach ($recipients as $r) {
                $allEmails->push($r['email']);
            }
        }

        $unique = $allEmails->unique()->values();

        if ($unique->isEmpty()) {
            return Placeholder::make('recipients_info')
                ->label('Príjemcovia')
                ->content(new HtmlString('<span style="color:#9ca3af;">Žiadni príjemcovia</span>'));
        }

        $list = $unique->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$unique->count().')')
            ->content(new HtmlString($list));
    }

    protected function buildBulkConfirmationDescription(): string
    {
        $records = $this->getSelectedRecords();
        $allEmails = collect();

        foreach ($records as $record) {
            $recipients = $this->resolveRecipients
                ? call_user_func($this->resolveRecipients, $record)
                : [];

            foreach ($recipients as $r) {
                $allEmails->push($r['email']);
            }
        }

        $unique = $allEmails->unique();

        return 'E-mail bude odoslaný '.$unique->count().' príjemcom: '.$unique->implode(', ');
    }
}

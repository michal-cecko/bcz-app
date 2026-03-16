<?php

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\HasSendEmailForm;
use App\Services\EmailService;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class SendEmailAction extends Action
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

        $this->schema(function (): array {
            $recipientInfo = $this->buildRecipientPlaceholder();

            return array_merge(
                $recipientInfo ? [$recipientInfo] : [],
                $this->getEmailFormSchema(),
            );
        });

        $this->modalSubmitActionLabel('Odoslať e-mail');
        $this->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
            ->modalHeading('Potvrdiť odoslanie')
            ->modalDescription(fn () => $this->buildConfirmationDescription())
            ->modalSubmitActionLabel('Áno, odoslať'));

        $this->action(function (array $data): void {
            $recipients = $this->resolveRecipients
                ? call_user_func($this->resolveRecipients, $this->getRecord())
                : [];

            if (empty($recipients)) {
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
                recipients: $recipients,
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

    protected function buildRecipientPlaceholder(): ?Placeholder
    {
        $recipients = $this->resolveRecipients
            ? call_user_func($this->resolveRecipients, $this->getRecord())
            : [];

        if (empty($recipients)) {
            return null;
        }

        $emails = collect($recipients)->pluck('email')->unique()->values();
        $list = $emails->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia')
            ->content(new HtmlString($list));
    }

    protected function buildConfirmationDescription(): string
    {
        $recipients = $this->resolveRecipients
            ? call_user_func($this->resolveRecipients, $this->getRecord())
            : [];

        $emails = collect($recipients)->pluck('email')->unique();

        return 'E-mail bude odoslaný na: '.$emails->implode(', ');
    }
}

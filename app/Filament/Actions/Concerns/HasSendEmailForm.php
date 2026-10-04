<?php

namespace App\Filament\Actions\Concerns;

use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use App\Models\EmailTemplate;
use App\Services\PaymentService;
use Awcodes\Mason\Mason;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

trait HasSendEmailForm
{
    /**
     * Built-in template that sends the system payment-due email instead of a
     * composed one, so each payer gets their own amount, due date and payment link.
     */
    public const PAYMENT_REQUEST_TEMPLATE = 'payment_request';

    protected bool $offersPaymentRequest = false;

    /**
     * Offer the built-in payment request template; for actions whose records
     * are payables (memberships, training and event registrations).
     */
    public function paymentRequestTemplate(bool $condition = true): static
    {
        $this->offersPaymentRequest = $condition;

        return $this;
    }

    protected function isPaymentRequest(?string $templateId): bool
    {
        return $this->offersPaymentRequest && $templateId === self::PAYMENT_REQUEST_TEMPLATE;
    }

    /**
     * @param  iterable<Model>  $records
     */
    protected function sendPaymentRequests(iterable $records): void
    {
        $paymentService = app(PaymentService::class);
        $sent = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $paymentService->resendPaymentRequest($record) ? $sent++ : $skipped++;
        }

        Notification::make()
            ->success()
            ->title("Výzva na platbu odoslaná: {$sent}")
            ->body($skipped > 0 ? "Preskočené (zaplatené, zrušené, zadarmo alebo bez platiteľa): {$skipped}" : null)
            ->send();
    }

    public function getEmailFormSchema(): array
    {
        return [
            Select::make('template_id')
                ->label('Šablóna')
                ->placeholder('Vyberte šablónu...')
                ->options(function (): array {
                    $builtIn = $this->offersPaymentRequest
                        ? [self::PAYMENT_REQUEST_TEMPLATE => 'Výzva na platbu (aktuálna suma, splatnosť a odkaz na platbu)']
                        : [];

                    $tenantId = filament()->getTenant()?->id;
                    if (! $tenantId) {
                        return $builtIn;
                    }

                    return $builtIn + EmailTemplate::where('team_id', $tenantId)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set): void {
                    if (! $state || $state === self::PAYMENT_REQUEST_TEMPLATE) {
                        return;
                    }

                    $template = EmailTemplate::find($state);
                    if (! $template) {
                        return;
                    }

                    $set('subject', $template->subject);
                    $set('content', $template->content);
                })
                ->dehydrated(fn (?string $state): bool => $this->isPaymentRequest($state)),
            Placeholder::make('payment_request_info')
                ->hiddenLabel()
                ->content('Každý nezaplatený dostane systémový e-mail s tým, čo mu ešte zostáva uhradiť, so splatnosťou a s odkazom na platbu (QR kód, bankový prevod). Zaplatení, zrušení a zadarmo sa preskočia.')
                ->visible(fn (Get $get): bool => $this->isPaymentRequest($get('template_id'))),
            TextInput::make('subject')
                ->label('Predmet')
                ->required()
                ->helperText($this->getVariableHints())
                ->hidden(fn (Get $get): bool => $this->isPaymentRequest($get('template_id'))),
            Section::make('Obsah e-mailu')
                ->hidden(fn (Get $get): bool => $this->isPaymentRequest($get('template_id')))
                ->schema([
                    Mason::make('content')
                        ->label('')
                        ->bricks(static::getEmailBricks())
                        ->previewLayout('mason.email-preview-layout')
                        ->required(),
                    Actions::make([
                        Action::make('preview')
                            ->label('Náhľad e-mailu')
                            ->icon(Heroicon::OutlinedEye)
                            ->color('gray')
                            ->action(function (Get $get, Component $livewire) {
                                $team = filament()->getTenant();
                                $key = Str::random(32);

                                Cache::put("email-preview:{$key}", [
                                    'subject' => $get('subject') ?? '',
                                    'content' => $get('content') ?? [],
                                    'team_name' => $team?->getTranslation('name', 'sk'),
                                    'team_logo_url' => $team?->getFirstMediaUrl('logo') ?: null,
                                    'team_url' => $team ? url("/timy/{$team->slug}") : url('/'),
                                    'team_email' => $team?->contact_email,
                                    'team_phone' => $team?->contact_phone,
                                    'team_website' => $team?->contact_website,
                                ], now()->addMinutes(30));

                                $url = route('admin.email-preview', $key);
                                $livewire->js("window.open('{$url}', '_blank')");
                            }),
                    ]),
                ]),
        ];
    }

    protected function getVariableHints(): string
    {
        $vars = $this->getAvailableVariables();

        return 'Dostupné premenné: '.implode(', ', array_map(fn (string $v) => '{{'.$v.'}}', $vars));
    }

    /**
     * @return list<string>
     */
    protected function getAvailableVariables(): array
    {
        return ['meno', 'email', 'nazov_timu'];
    }

    /**
     * @return list<class-string>
     */
    protected static function getEmailBricks(): array
    {
        return [
            EmailRichTextBrick::class,
            EmailButtonBrick::class,
            EmailHeadingBrick::class,
            EmailImageBrick::class,
            EmailCalloutBrick::class,
            EmailDividerBrick::class,
            EmailSpacerBrick::class,
        ];
    }
}

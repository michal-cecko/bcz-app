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
use Awcodes\Mason\Mason;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

trait HasSendEmailForm
{
    public function getEmailFormSchema(): array
    {
        return [
            Select::make('template_id')
                ->label('Šablóna')
                ->placeholder('Vyberte šablónu...')
                ->options(function (): array {
                    $tenantId = filament()->getTenant()?->id;
                    if (! $tenantId) {
                        return [];
                    }

                    return EmailTemplate::where('team_id', $tenantId)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set): void {
                    if (! $state) {
                        return;
                    }

                    $template = EmailTemplate::find($state);
                    if (! $template) {
                        return;
                    }

                    $set('subject', $template->subject);
                    $set('content', $template->content);
                })
                ->dehydrated(false),
            TextInput::make('subject')
                ->label('Predmet')
                ->required()
                ->helperText($this->getVariableHints()),
            Section::make('Obsah e-mailu')
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

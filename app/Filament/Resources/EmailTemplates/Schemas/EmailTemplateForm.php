<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use Awcodes\Mason\Mason;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Základné údaje')
                    ->schema([
                        TextInput::make('name')
                            ->label('Názov šablóny')
                            ->helperText('Interný názov pre jednoduchú identifikáciu')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subject')
                            ->label('Predmet e-mailu')
                            ->helperText('Dostupné premenné: {{meno}}, {{email}}, {{nazov_timu}}, {{nazov_treningu}}, {{miesto}}, {{cas}}, {{kapacita}}, {{nazov_eventu}}, {{datum_eventu}}')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Obsah e-mailu')
                    ->schema([
                        Mason::make('content')
                            ->label('')
                            ->bricks(self::bricks())
                            ->previewLayout('mason.email-preview-layout')
                            ->columnSpanFull(),
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
            ]);
    }

    /**
     * @return list<class-string>
     */
    public static function bricks(): array
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

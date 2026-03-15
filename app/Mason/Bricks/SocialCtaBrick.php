<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Setting;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SocialCtaBrick extends Brick
{
    public static function getId(): string
    {
        return 'social-cta';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.social-cta');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedShare;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $config['resolved_instagram'] = empty($config['override_instagram'])
            ? Setting::get('social_instagram_url', '')
            : ($config['instagram_url'] ?? '');

        $config['resolved_facebook'] = empty($config['override_facebook'])
            ? Setting::get('social_facebook_url', '')
            : ($config['facebook_url'] ?? '');

        $config['resolved_youtube'] = empty($config['override_youtube'])
            ? Setting::get('social_youtube_url', '')
            : ($config['youtube_url'] ?? '');

        return view('mason.bricks.social-cta.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                FileUpload::make('background_image')
                    ->image()
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public')
                    ->label(__('bricks.social_cta.background_image')),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title'))
                        ->required(),
                    BrickRichEditor::make("description.{$locale}")
                        ->label(__('bricks.fields.description')),
                ]),
                Fieldset::make(__('bricks.social_cta.instagram_url'))
                    ->schema([
                        Toggle::make('override_instagram')
                            ->label(__('bricks.social_cta.override'))
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('instagram_url')
                            ->label(__('bricks.social_cta.instagram_url'))
                            ->visible(fn (Get $get): bool => (bool) $get('override_instagram'))
                            ->columnSpanFull(),
                    ]),
                Fieldset::make(__('bricks.social_cta.facebook_url'))
                    ->schema([
                        Toggle::make('override_facebook')
                            ->label(__('bricks.social_cta.override'))
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('facebook_url')
                            ->label(__('bricks.social_cta.facebook_url'))
                            ->visible(fn (Get $get): bool => (bool) $get('override_facebook'))
                            ->columnSpanFull(),
                    ]),
                Fieldset::make(__('bricks.social_cta.youtube_url'))
                    ->schema([
                        Toggle::make('override_youtube')
                            ->label(__('bricks.social_cta.override'))
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('youtube_url')
                            ->label(__('bricks.social_cta.youtube_url'))
                            ->visible(fn (Get $get): bool => (bool) $get('override_youtube'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

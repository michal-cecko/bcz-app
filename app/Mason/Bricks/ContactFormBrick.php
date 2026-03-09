<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ContactFormBrick extends Brick
{
    public static function getId(): string
    {
        return 'contact-form';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.contact-form');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedEnvelope;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.contact-form.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("heading.{$locale}")
                        ->label(__('bricks.contact_form.heading')),
                ]),
                Toggle::make('show_reason')
                    ->label(__('bricks.contact_form.show_reason'))
                    ->default(true),
                Toggle::make('show_phone')
                    ->label(__('bricks.contact_form.show_phone'))
                    ->default(true),
                Fieldset::make(__('bricks.contact_form.sidebar'))
                    ->schema([
                        TextInput::make('contact_email')
                            ->label(__('bricks.contact_form.contact_email')),
                        TextInput::make('contact_phone')
                            ->label(__('bricks.contact_form.contact_phone')),
                        TextInput::make('contact_location')
                            ->label(__('bricks.contact_form.contact_location')),
                        TextInput::make('response_text')
                            ->label(__('bricks.contact_form.response_text')),
                    ]),
            ]);
    }
}

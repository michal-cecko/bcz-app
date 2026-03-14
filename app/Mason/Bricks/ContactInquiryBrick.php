<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ContactInquiryBrick extends Brick
{
    public static function getId(): string
    {
        return 'contact-inquiry';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.contact-inquiry');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChatBubbleLeftRight;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.contact-inquiry.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.contact_inquiry.label')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.contact_inquiry.title')),
                    TextInput::make("description.{$locale}")
                        ->label(__('bricks.contact_inquiry.description')),
                ]),
                TextInput::make('contact_email')
                    ->label(__('bricks.contact_inquiry.contact_email')),
                TextInput::make('contact_phone')
                    ->label(__('bricks.contact_inquiry.contact_phone')),
            ]);
    }
}

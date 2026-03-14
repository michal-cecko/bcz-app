<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class VideoSectionBrick extends Brick
{
    public static function getId(): string
    {
        return 'video-section';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.video-section');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPlayCircle;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.video-section.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Titulok')
                        ->required(),
                    TextInput::make("subtitle.{$locale}")
                        ->label('Podtitulok'),
                ]),
                Select::make('video_source')
                    ->label('Zdroj videa')
                    ->options([
                        'url' => 'YouTube / externá URL',
                        'media' => 'Súbor z knižnice',
                    ])
                    ->default('url')
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('video_url', null);
                        $set('video_media', null);
                    }),
                TextInput::make('video_url')
                    ->label('URL videa')
                    ->visible(fn (Get $get): bool => ($get('video_source') ?? 'url') === 'url'),
                MediaPicker::make('video_media')
                    ->label('Video súbor')
                    ->visible(fn (Get $get): bool => $get('video_source') === 'media'),
                Repeater::make('checkpoints')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("text.{$locale}")
                                ->label('Text')
                                ->required(),
                        ]),
                    ])
                    ->addActionLabel('Pridať bod')
                    ->defaultItems(0)
                    ->reorderable()
                    ->collapsible(),
            ]);
    }
}

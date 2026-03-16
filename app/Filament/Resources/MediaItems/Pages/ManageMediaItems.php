<?php

namespace App\Filament\Resources\MediaItems\Pages;

use App\Filament\Resources\MediaItems\MediaItemResource;
use App\Models\MediaItem;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ManageMediaItems extends ManageRecords
{
    protected static string $resource = MediaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_upload')
                ->label('Nahrať')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalHeading('Nahrať médiá')
                ->modalWidth('2xl')
                ->schema([
                    Tabs::make('upload_tabs')
                        ->tabs([
                            Tab::make('Rýchle nahranie')
                                ->icon(Heroicon::OutlinedBolt)
                                ->schema([
                                    FileUpload::make('quick_files')
                                        ->label('Súbory')
                                        ->multiple()
                                        ->helperText('Názov a popis sa vytvoria automaticky z názvu súboru.')
                                        ->storeFiles(false)
                                        ->required(false),
                                ]),
                            Tab::make('S popisom')
                                ->icon(Heroicon::OutlinedPencilSquare)
                                ->schema([
                                    Repeater::make('detailed_files')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Názov')
                                                ->required(),
                                            Textarea::make('description')
                                                ->label('Popis')
                                                ->rows(2),
                                            FileUpload::make('file')
                                                ->label('Súbor')
                                                ->storeFiles(false)
                                                ->required(),
                                        ])
                                        ->columns(1)
                                        ->addActionLabel('Pridať médium')
                                        ->defaultItems(1),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $teamId = filament()->getTenant()->id;

                    // Quick upload
                    if (! empty($data['quick_files'])) {
                        foreach ($data['quick_files'] as $file) {
                            $originalName = $file->getClientOriginalName();
                            $name = Str::beforeLast($originalName, '.');

                            $item = MediaItem::create([
                                'team_id' => $teamId,
                                'name' => $name,
                            ]);

                            $item->addMedia($file)->toMediaCollection('file');
                        }
                    }

                    // Detailed upload
                    if (! empty($data['detailed_files'])) {
                        foreach ($data['detailed_files'] as $entry) {
                            if (empty($entry['file'])) {
                                continue;
                            }

                            $item = MediaItem::create([
                                'team_id' => $teamId,
                                'name' => $entry['name'],
                                'description' => $entry['description'] ?? null,
                            ]);

                            $file = $entry['file'];
                            if (is_array($file)) {
                                $file = reset($file);
                            }
                            $item->addMedia($file)->toMediaCollection('file');
                        }
                    }
                }),
            CreateAction::make()
                ->label('Nahrať jednotlivo')
                ->icon(Heroicon::OutlinedPlus)
                ->color('gray')
                ->modalHeading('Nahrať médium'),
        ];
    }
}

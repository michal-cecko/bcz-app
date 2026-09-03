<?php

namespace App\Filament\Resources\MediaItems;

use App\Filament\Resources\MediaItems\Pages\ManageMediaItems;
use App\Models\MediaItem;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Number;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaItemResource extends Resource
{
    protected static ?string $model = MediaItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'médium';

    protected static ?string $pluralModelLabel = 'Média';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\UnitEnum|null $navigationGroup = 'Ostatné';

    protected static ?int $navigationSort = 11;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Názov')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Popis')
                    ->rows(2),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('Súbor')
                    ->collection('file')
                    ->visibility('public')
                    ->maxSize(10240)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('file')
                    ->label('')
                    ->collection('file')
                    ->filterMediaUsing(fn (Collection $media) => $media->filter(
                        fn (Media $item): bool => str_starts_with((string) $item->mime_type, 'image/'),
                    ))
                    ->width(48)
                    ->height(48)
                    ->rounded(),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Popis')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('media.file_name')
                    ->label('Súbor')
                    ->state(fn (MediaItem $record): ?string => $record->getFirstMedia('file')?->file_name)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('media.mime_type')
                    ->label('Typ')
                    ->state(fn (MediaItem $record): ?string => $record->getFirstMedia('file')?->mime_type)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('media.size')
                    ->label('Veľkosť')
                    ->state(fn (MediaItem $record): ?string => $record->getFirstMedia('file') ? Number::fileSize($record->getFirstMedia('file')->size) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('media.url')
                    ->label('URL')
                    ->state(fn (MediaItem $record): ?string => $record->getFirstMediaUrl('file') ?: null)
                    ->copyable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->emptyStateHeading('Nahrajte prvé médium')
            ->emptyStateDescription('Kliknite na tlačidlo "Nahrať" pre nahranie prvého média.')
            ->emptyStateIcon(Heroicon::OutlinedPhoto)
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('copy_url')
                    ->label('Kopírovať URL')
                    ->icon(Heroicon::OutlinedClipboard)
                    ->color('gray')
                    ->alpineClickHandler(function (MediaItem $record): string {
                        $url = Js::from($record->getFirstMediaUrl('file'));
                        $message = Js::from('URL skopírovaná');

                        return <<<JS
                            window.navigator.clipboard.writeText({$url})
                            \$tooltip({$message}, {
                                theme: \$store.theme,
                                timeout: 2000,
                            })
                            JS;
                    })
                    ->visible(fn (MediaItem $record): bool => (bool) $record->getFirstMedia('file')),
                Action::make('download')
                    ->label('Stiahnuť')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->url(fn (MediaItem $record): ?string => $record->getFirstMediaUrl('file') ?: null)
                    ->openUrlInNewTab()
                    ->visible(fn (MediaItem $record): bool => (bool) $record->getFirstMedia('file')),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMediaItems::route('/'),
        ];
    }
}

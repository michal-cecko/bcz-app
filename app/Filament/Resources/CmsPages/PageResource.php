<?php

namespace App\Filament\Resources\CmsPages;

use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Resources\CmsPages\Pages\CreatePage;
use App\Filament\Resources\CmsPages\Pages\EditPage;
use App\Filament\Resources\CmsPages\Pages\ListPages;
use App\Filament\Resources\CmsPages\Schemas\PageForm;
use App\Filament\Resources\CmsPages\Tables\PagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'stránku';

    protected static ?string $pluralModelLabel = 'Stránky';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = ContentCluster::class;

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function canGloballySearch(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('title', 'sk');
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}

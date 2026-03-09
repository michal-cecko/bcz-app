<?php

namespace App\Filament\Resources\Menus;

use App\Enums\MenuLocationEnum;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use App\Filament\Resources\Menus\Tables\MenusTable;
use App\Models\Menu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $modelLabel = 'menu';

    protected static ?string $pluralModelLabel = 'Menu';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = ContentCluster::class;

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function canCreate(): bool
    {
        return Menu::query()->count() < count(MenuLocationEnum::cases());
    }

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}

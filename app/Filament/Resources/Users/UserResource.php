<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use RalphJSmit\Filament\MediaLibrary\Filament\Infolists\Components\MediaEntry;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Používateľ';

    protected static ?string $pluralModelLabel = 'Používatelia';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Organizácia';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Profil')
                            ->schema([
                                MediaEntry::make('profile_image')
                                    ->label('Profilový obrázok')
                                    ->circular()
                                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),
                                TextEntry::make('name')
                                    ->label('Meno'),
                                TextEntry::make('email')
                                    ->label('E-mail')
                                    ->copyable(),
                                TextEntry::make('roles.name')
                                    ->label('Roly')
                                    ->badge(),
                            ])
                            ->columnSpan(2),

                        Section::make('Informácie')
                            ->schema([
                                TextEntry::make('email_verified_at')
                                    ->label('E-mail overený')
                                    ->dateTime()
                                    ->placeholder('Neoverený'),
                                TextEntry::make('created_at')
                                    ->label('Vytvorený')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Aktualizovaný')
                                    ->dateTime(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

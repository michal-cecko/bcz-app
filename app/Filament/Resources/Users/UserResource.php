<?php

namespace App\Filament\Resources\Users;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'používateľa';

    protected static ?string $pluralModelLabel = 'Používatelia';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Organizácia';

    protected static ?int $navigationSort = 90;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Profil')
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('profile_image')
                                    ->collection('profile_image')
                                    ->label('Profilový obrázok')
                                    ->circular()
                                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),
                                TextEntry::make('name')
                                    ->label('Meno'),
                                TextEntry::make('email')
                                    ->label('E-mail')
                                    ->copyable(),
                                TextEntry::make('all_roles')
                                    ->label('Roly')
                                    ->badge()
                                    ->state(function (User $record): array {
                                        $globalRoles = $record->getRoleNames()
                                            ->reject(fn ($r) => $r === 'panel_user')
                                            ->values();

                                        $tenant = filament()->getTenant();
                                        $teamRoles = $tenant
                                            ? $record->teams()
                                                ->where('teams.id', $tenant->id)
                                                ->pluck('team_user.role')
                                                ->map(fn ($r) => $r instanceof RoleEnum ? $r->value : $r)
                                            : collect();

                                        return $globalRoles->merge($teamRoles)->unique()->values()->toArray();
                                    })
                                    ->formatStateUsing(fn (string $state): string => RoleEnum::tryFrom($state)?->getLabel() ?? $state),
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
            RelationManagers\MembershipsRelationManager::class,
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

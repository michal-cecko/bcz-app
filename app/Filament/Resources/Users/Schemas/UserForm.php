<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Meno')
                    ->required(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label('Heslo')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('roles')
                    ->label('Roly')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),
                MediaPicker::make('profile_image')
                    ->label('Profilový obrázok'),
                Section::make('Verejný profil')
                    ->description('Nastavenia verejného profilu atléta')
                    ->collapsed()
                    ->components([
                        Checkbox::make('has_public_profile')
                            ->label('Má verejný profil'),
                        Placeholder::make('public_profile_approved_at')
                            ->label('Schválený dňa')
                            ->content(fn ($record) => $record?->public_profile_approved_at?->format('d.m.Y H:i') ?? 'Neschválený'),
                    ]),
            ]);
    }
}

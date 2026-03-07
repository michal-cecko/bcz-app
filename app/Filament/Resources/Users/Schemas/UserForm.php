<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Meno')
                            ->required(),
                        TextInput::make('last_name')
                            ->label('Priezvisko')
                            ->required(),
                    ]),
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
                    ->getOptionLabelFromRecordUsing(fn ($record): string => RoleEnum::tryFrom($record->name)?->getLabel() ?? $record->name)
                    ->multiple()
                    ->preload()
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Dátum narodenia')
                    ->native(false)
                    ->maxDate(now()),
                Select::make('gender')
                    ->label('Pohlavie')
                    ->options(GenderEnum::translations()),
                SpatieMediaLibraryFileUpload::make('profile_image')
                    ->collection('profile_image')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Profilový obrázok'),
                Section::make('Verejne profily')
                    ->description('Stav verejnych profilov podla roly')
                    ->collapsed()
                    ->components([
                        Placeholder::make('coach_profile_status')
                            ->label('Profil trenera')
                            ->content(function ($record) {
                                if (! $record) {
                                    return '-';
                                }
                                $draft = $record->coachProfile?->draft_status?->getLabel();
                                $approved = $record->coach_profile_approved_at?->format('d.m.Y H:i');

                                return $draft ? "Draft: {$draft}" : ($approved ? "Schvaleny: {$approved}" : 'Neaktivny');
                            }),
                        Placeholder::make('athlete_profile_status')
                            ->label('Profil sportovca')
                            ->content(function ($record) {
                                if (! $record) {
                                    return '-';
                                }
                                $draft = $record->athleteProfile?->draft_status?->getLabel();
                                $approved = $record->athlete_profile_approved_at?->format('d.m.Y H:i');

                                return $draft ? "Draft: {$draft}" : ($approved ? "Schvaleny: {$approved}" : 'Neaktivny');
                            }),
                        Placeholder::make('judge_profile_status')
                            ->label('Profil porotcu')
                            ->content(function ($record) {
                                if (! $record) {
                                    return '-';
                                }
                                $draft = $record->judgeProfile?->draft_status?->getLabel();
                                $approved = $record->judge_profile_approved_at?->format('d.m.Y H:i');

                                return $draft ? "Draft: {$draft}" : ($approved ? "Schvaleny: {$approved}" : 'Neaktivny');
                            }),
                    ]),
            ]);
    }
}

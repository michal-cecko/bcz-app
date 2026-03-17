<?php

namespace App\Filament\Pages\Auth;

use App\Enums\GenderEnum;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('profile_image')
                    ->collection('profile_image')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Profilový obrázok')
                    ->avatar()
                    ->circleCropper()
                    ->columnSpanFull(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                Grid::make(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Telefón')
                            ->tel(),
                        DatePicker::make('birth_date')
                            ->label('Dátum narodenia')
                            ->native(false)
                            ->maxDate(now()),
                        Select::make('gender')
                            ->label('Pohlavie')
                            ->options(GenderEnum::translations()),
                        Select::make('locale')
                            ->label('Predvolený jazyk')
                            ->options([
                                'sk' => 'Slovenčina',
                                'en' => 'Angličtina',
                                'cs' => 'Čeština',
                            ])
                            ->default('sk')
                            ->required(),
                    ]),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}

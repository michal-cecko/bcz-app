<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use App\Enums\InquiryStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail dopytu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Meno')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Telefón')
                            ->disabled(),
                        TextInput::make('reason')
                            ->label('Dôvod')
                            ->disabled(),
                        Select::make('status')
                            ->label('Stav')
                            ->options(InquiryStatusEnum::class)
                            ->required(),
                        Textarea::make('message')
                            ->label('Správa')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

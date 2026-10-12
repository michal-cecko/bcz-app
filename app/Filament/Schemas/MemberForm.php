<?php

namespace App\Filament\Schemas;

use App\Enums\GenderEnum;
use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class MemberForm
{
    public static function fields(): array
    {
        return [
            TextInput::make('first_name')->label(__('family.first_name'))->required()->maxLength(255),
            TextInput::make('last_name')->label(__('family.last_name'))->maxLength(255),
            DatePicker::make('birth_date')->label(__('family.birth_date'))->maxDate(today())->required(fn ($record) => ! $record || ($record instanceof Member && ! $record->legacy_user_id)),
            Select::make('gender')->label(__('archive.gender'))->options(GenderEnum::class),
        ];
    }
}

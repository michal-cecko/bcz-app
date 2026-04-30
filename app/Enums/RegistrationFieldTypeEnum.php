<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum RegistrationFieldTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case TEXT_INPUT = 'text_input';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case MULTI_SELECT = 'multi_select';
    case DATE_PICKER = 'date_picker';
    case YEAR_PICKER = 'year_picker';
    case NUMBER_INPUT = 'number_input';
    case TIME_PICKER = 'time_picker';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case FILE_INPUT = 'file_input';
    case FIRST_NAME = 'first_name';
    case LAST_NAME = 'last_name';
    case FULL_NAME = 'full_name';
    case BIRTH_DATE = 'birth_date';
    case GENDER = 'gender';
    case CATEGORY = 'category';
}

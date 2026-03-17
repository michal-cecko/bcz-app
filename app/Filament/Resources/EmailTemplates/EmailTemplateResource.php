<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplates\Schemas\EmailTemplateForm;
use App\Filament\Resources\EmailTemplates\Tables\EmailTemplatesTable;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $modelLabel = 'e-mailová šablóna';

    protected static ?string $pluralModelLabel = 'E-mailové šablóny';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\UnitEnum|null $navigationGroup = 'Ostatné';

    protected static ?int $navigationSort = 10;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function form(Schema $schema): Schema
    {
        return EmailTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}

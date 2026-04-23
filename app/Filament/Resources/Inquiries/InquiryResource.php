<?php

namespace App\Filament\Resources\Inquiries;

use App\Enums\InquiryStatusEnum;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Inquiries\Pages\ViewInquiry;
use App\Filament\Resources\Inquiries\Schemas\InquiryForm;
use App\Filament\Resources\Inquiries\Tables\InquiriesTable;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $modelLabel = 'dopyt';

    protected static ?string $pluralModelLabel = 'Dopyty';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\UnitEnum|null $navigationGroup = 'Ostatné';

    protected static ?int $navigationSort = 12;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function form(Schema $schema): Schema
    {
        return InquiryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Detail dopytu')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Meno'),
                                TextEntry::make('email')
                                    ->label('E-mail')
                                    ->copyable(),
                                TextEntry::make('phone')
                                    ->label('Telefón')
                                    ->placeholder('-')
                                    ->copyable(),
                                TextEntry::make('message')
                                    ->label('Správa')
                                    ->columnSpanFull(),
                                TextEntry::make('internal_note')
                                    ->label('Interná poznámka')
                                    ->placeholder('Žiadna poznámka')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->columnSpan(3),

                        Section::make('Informácie')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Stav')
                                    ->badge(),
                                TextEntry::make('reason')
                                    ->label('Dôvod'),
                                TextEntry::make('created_at')
                                    ->label('Vytvorený')
                                    ->since(),
                                TextEntry::make('updated_at')
                                    ->label('Aktualizovaný')
                                    ->since(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return InquiriesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('team_id', Filament::getTenant()?->id)
            ->where('status', InquiryStatusEnum::NEW)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view' => ViewInquiry::route('/{record}'),
        ];
    }
}

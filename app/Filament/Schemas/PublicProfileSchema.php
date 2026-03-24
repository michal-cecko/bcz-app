<?php

namespace App\Filament\Schemas;

use App\Enums\GoalStatusEnum;
use App\Models\Exercise;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared profile field schemas reused by UserForm and UserSetupWizard.
 */
class PublicProfileSchema
{
    /**
     * Profile-specific fields (biography, dates, images).
     * These are scoped to the profile model (CoachProfile, AthleteProfile, JudgeProfile).
     *
     * @return list<Component>
     */
    public static function profileFields(string $role, ?Model $mediaModel = null): array
    {
        return match ($role) {
            'coach' => self::coachProfileFields($mediaModel),
            'athlete' => self::athleteProfileFields($mediaModel),
            'judge' => self::judgeProfileFields($mediaModel),
        };
    }

    /**
     * @return list<Component>
     */
    private static function coachProfileFields(?Model $mediaModel): array
    {
        return [
            DatePicker::make('date_started_coaching')
                ->label('Začiatok trénerskej kariéry')
                ->native(false)
                ->maxDate(now()),
            Tabs::make('coach_bio_tabs')
                ->tabs([
                    Tab::make('SK')->schema([
                        RichEditor::make('biography.sk')
                            ->label('Biografia (SK)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('EN')->schema([
                        RichEditor::make('biography.en')
                            ->label('Biography (EN)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('CS')->schema([
                        RichEditor::make('biography.cs')
                            ->label('Biografie (CS)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                ])
                ->columnSpanFull(),
            Grid::make(2)->schema([
                SpatieMediaLibraryFileUpload::make('main_background_image')
                    ->collection('main_background_image')
                    ->label('Hlavný obrázok (pozadie)')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->when($mediaModel, fn ($c) => $c->model($mediaModel)),
                SpatieMediaLibraryFileUpload::make('biography_image')
                    ->collection('biography_image')
                    ->label('Obrázok k biografii')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->when($mediaModel, fn ($c) => $c->model($mediaModel)),
            ]),
        ];
    }

    /**
     * @return list<Component>
     */
    private static function athleteProfileFields(?Model $mediaModel): array
    {
        return [
            DatePicker::make('date_started_working_out')
                ->label('Začiatok cvičenia')
                ->native(false)
                ->maxDate(now()),
            Tabs::make('athlete_journey_tabs')
                ->tabs([
                    Tab::make('SK')->schema([
                        RichEditor::make('journey_text.sk')
                            ->label('Môj príbeh (SK)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('EN')->schema([
                        RichEditor::make('journey_text.en')
                            ->label('My story (EN)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('CS')->schema([
                        RichEditor::make('journey_text.cs')
                            ->label('Môj príbeh (CS)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                ])
                ->columnSpanFull(),
            Grid::make(2)->schema([
                SpatieMediaLibraryFileUpload::make('journey_image')
                    ->collection('journey_image')
                    ->label('Obrázok k príbehu')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->when($mediaModel, fn ($c) => $c->model($mediaModel)),
                SpatieMediaLibraryFileUpload::make('main_image')
                    ->collection('main_image')
                    ->label('Hlavný obrázok')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->when($mediaModel, fn ($c) => $c->model($mediaModel)),
            ]),
        ];
    }

    /**
     * @return list<Component>
     */
    private static function judgeProfileFields(?Model $mediaModel): array
    {
        return [
            DatePicker::make('date_started_judging')
                ->label('Začiatok porotcovania')
                ->native(false)
                ->maxDate(now()),
            TagsInput::make('disciplines')
                ->label('Disciplíny')
                ->placeholder('Pridajte disciplínu')
                ->suggestions(['freestyle', 'speed', 'endurance', 'strength', 'parkour']),
            Tabs::make('judge_bio_tabs')
                ->tabs([
                    Tab::make('SK')->schema([
                        RichEditor::make('biography.sk')
                            ->label('Biografia (SK)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('EN')->schema([
                        RichEditor::make('biography.en')
                            ->label('Biography (EN)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('CS')->schema([
                        RichEditor::make('biography.cs')
                            ->label('Biografie (CS)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                ])
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('hero_image')
                ->collection('hero_image')
                ->label('Hlavný obrázok')
                ->disk('public')
                ->visibility('public')
                ->image()
                ->when($mediaModel, fn ($c) => $c->model($mediaModel)),
        ];
    }

    public static function certificationsRepeater(): Repeater
    {
        return Repeater::make('certifications')
            ->label('')
            ->itemLabel(fn (array $state): ?string => $state['name']['sk'] ?? null)
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name.sk')
                        ->label('Názov (SK)')
                        ->required()
                        ->live(onBlur: true),
                    TextInput::make('name.en')
                        ->label('Name (EN)'),
                ]),
                Textarea::make('description.sk')
                    ->label('Popis (SK)')
                    ->rows(2),
                TextInput::make('year_of_issue')
                    ->label('Rok vydania')
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue(date('Y')),
            ])
            ->orderColumn('sort_order')
            ->addActionLabel('Pridať certifikát')
            ->reorderable()
            ->reorderableWithButtons()
            ->cloneable()
            ->collapsible()
            ->deleteAction(fn ($action) => $action->requiresConfirmation())
            ->defaultItems(0);
    }

    public static function exercisesRepeater(): Repeater
    {
        return Repeater::make('athleteExercises')
            ->label('')
            ->hiddenLabel()
            ->itemLabel(function (array $state): ?string {
                if (! empty($state['custom_name'])) {
                    return $state['custom_name'];
                }
                if (! empty($state['exercise_id'])) {
                    $name = Exercise::find($state['exercise_id'])?->getTranslation('name', 'sk');

                    return $name ?: null;
                }

                return null;
            })
            ->schema([
                Grid::make(2)->schema([
                    Toggle::make('is_custom')
                        ->label('Vlastný názov cviku')
                        ->live()
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Toggle $component, $state, Get $get): void {
                            $component->state(! empty($get('custom_name')));
                        })
                        ->columnSpanFull(),
                    Select::make('exercise_id')
                        ->label('Cvik')
                        ->options(
                            Exercise::query()
                                ->pluck('name', 'id')
                                ->map(fn ($name) => is_array($name) ? ($name['sk'] ?? '') : $name)
                        )
                        ->searchable()
                        ->visible(fn (Get $get): bool => ! $get('is_custom')),
                    TextInput::make('custom_name')
                        ->label('Vlastný názov')
                        ->visible(fn (Get $get): bool => (bool) $get('is_custom')),
                    TextInput::make('duration')
                        ->label('Čas učenia'),
                ]),
                Textarea::make('description.sk')
                    ->label('Popis (SK)')
                    ->rows(3),
                SpatieMediaLibraryFileUpload::make('exercise_media')
                    ->collection('exercise_media')
                    ->label('Médiá (obrázky / videá)')
                    ->multiple()
                    ->reorderable()
                    ->disk('public')
                    ->visibility('public')
                    ->panelLayout('grid')
                    ->extraAttributes(['accept' => 'image/*,video/*'])
                    ->helperText('Prvý obrázok bude použitý ako náhľad na karte.'),
            ])
            ->orderColumn('sort_order')
            ->addActionLabel('Pridať prvok')
            ->reorderable()
            ->reorderableWithButtons()
            ->cloneable()
            ->collapsible()
            ->deleteAction(fn ($action) => $action->requiresConfirmation())
            ->defaultItems(0);
    }

    public static function goalsRepeater(): Repeater
    {
        return Repeater::make('athleteGoals')
            ->label('')
            ->hiddenLabel()
            ->itemLabel(fn (array $state): ?string => $state['heading']['sk'] ?? null)
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('heading.sk')
                        ->label('Názov cieľa (SK)')
                        ->required()
                        ->live(onBlur: true),
                    Select::make('status')
                        ->label('Status')
                        ->options(GoalStatusEnum::translations())
                        ->default('planned'),
                ]),
                Textarea::make('description.sk')
                    ->label('Popis (SK)')
                    ->rows(2),
                IconPicker::make('icon')
                    ->label('Ikona'),
                SpatieMediaLibraryFileUpload::make('goal_media')
                    ->collection('goal_media')
                    ->label('Médiá')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->helperText('Prvý obrázok bude použitý ako náhľad.'),
            ])
            ->orderColumn('sort_order')
            ->addActionLabel('Pridať cieľ')
            ->reorderable()
            ->reorderableWithButtons()
            ->cloneable()
            ->collapsible()
            ->deleteAction(fn ($action) => $action->requiresConfirmation())
            ->defaultItems(0);
    }

    public static function galleryRepeater(string $role): Repeater
    {
        $relation = match ($role) {
            'coach' => 'coachGalleryItems',
            'athlete' => 'athleteGalleryItems',
            'judge' => 'judgeGalleryItems',
        };

        return Repeater::make($relation)
            ->label('')
            ->itemLabel(fn (array $state): ?string => $state['description']['sk'] ?? null)
            ->schema([
                Textarea::make('description.sk')
                    ->label('Popis (SK)')
                    ->rows(2)
                    ->live(onBlur: true),
                TagsInput::make('tags')
                    ->label('Tagy'),
            ])
            ->orderColumn('sort_order')
            ->addActionLabel('Pridať obrázok')
            ->reorderable()
            ->reorderableWithButtons()
            ->cloneable()
            ->collapsible()
            ->deleteAction(fn ($action) => $action->requiresConfirmation())
            ->defaultItems(0);
    }

    /**
     * Build the complete sub-tabs for a role (profile, certifications, exercises, goals, gallery).
     *
     * @return list<Tab>
     */
    public static function roleSubTabs(string $role, ?Model $mediaModel = null): array
    {
        $profileLabel = match ($role) {
            'athlete' => 'Môj príbeh',
            default => 'Profil',
        };

        $tabs = [
            Tab::make($profileLabel)
                ->icon('heroicon-o-user')
                ->schema(self::profileFields($role, $mediaModel)),
        ];

        if (in_array($role, ['coach', 'judge'])) {
            $tabs[] = Tab::make('Certifikáty')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    self::certificationsRepeater(),
                ]);
        }

        if ($role === 'athlete') {
            $tabs[] = Tab::make('Cesta k prvkom')
                ->icon('heroicon-o-bolt')
                ->schema([
                    self::exercisesRepeater(),
                ]);
            $tabs[] = Tab::make('Moje ciele')
                ->icon('heroicon-o-flag')
                ->schema([
                    self::goalsRepeater(),
                ]);
        }

        $tabs[] = Tab::make('Galéria')
            ->icon('heroicon-o-photo')
            ->schema([
                Section::make()
                    ->description('Nové obrázky budú schválené spolu s profilom')
                    ->schema([self::galleryRepeater($role)]),
            ]);

        return $tabs;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, list<string>> */
    private array $tables = [
        'teams' => ['name', 'story', 'achievements', 'socials'],
        'users' => ['socials'],
        'coach_profiles' => ['biography'],
        'athlete_profiles' => ['journey_text'],
        'sport_categories' => ['name', 'description', 'page_content'],
        'exercise_categories' => ['name', 'description'],
        'exercises' => ['name', 'description'],
        'athlete_exercises' => ['description'],
        'athlete_goals' => ['heading', 'description'],
        'certifications' => ['name', 'description'],
        'trainings' => ['title', 'description', 'schedule_days', 'place_name', 'gathering_place', 'registration_form_schema', 'gallery_images'],
        'training_registrations' => ['form_data'],
        'event_categories' => ['title', 'card_subtitle', 'card_description', 'detail_title', 'about_title', 'about_description', 'types_section_title', 'types_section_subtitle', 'types_cards', 'stats', 'cta_title', 'cta_description'],
        'events' => ['title', 'card_description', 'content'],
        'event_organizations' => ['registration_form_schema'],
        'disciplines' => ['name', 'description'],
        'athlete_categories' => ['name', 'description'],
        'timetable_entries' => ['title'],
        'round_parts' => ['name'],
        'battles' => ['competitor_a_id', 'competitor_b_id', 'winner_id'],
        'settings' => ['value', 'label', 'description'],
        'faq_categories' => ['title'],
        'faqs' => ['question', 'answer'],
        'tags' => ['name', 'slug'],
        'pages' => ['title', 'content', 'meta_title', 'meta_description'],
        'menus' => ['label', 'items'],
        'currencies' => ['name'],
        'subscription_plans' => ['name', 'description', 'features', 'limits'],
        'media' => ['manipulations', 'custom_properties', 'generated_conversions', 'responsive_images'],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE jsonb USING \"{$column}\"::jsonb");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE json USING \"{$column}\"::json");
            }
        }
    }
};

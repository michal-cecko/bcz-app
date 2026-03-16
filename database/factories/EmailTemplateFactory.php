<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->words(3, true),
            'subject' => fake()->sentence(),
            'content' => [
                [
                    'type' => 'email-rich-text',
                    'data' => ['content' => '<p>'.fake()->paragraph().'</p>'],
                ],
            ],
        ];
    }
}

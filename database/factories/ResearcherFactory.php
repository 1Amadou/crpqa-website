<?php

namespace Database\Factories;

use App\Models\Researcher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResearcherFactory extends Factory
{
    protected $model = Researcher::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'slug' => Str::slug($firstName . '-' . $lastName),
            'first_name_fr' => $firstName,
            'first_name_en' => $firstName,
            'last_name_fr' => $lastName,
            'last_name_en' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'title_position_fr' => fake()->randomElement(['Dr.', 'Prof.', '']),
            'title_position_en' => fake()->randomElement(['Dr.', 'Prof.', '']),
            'biography_fr' => '<p>' . implode('</p><p>', fake()->paragraphs(3)) . '</p>',
            'biography_en' => '<p>' . implode('</p><p>', fake()->paragraphs(3)) . '</p>',
            'research_interests_fr' => implode(', ', fake()->words(rand(3, 6))),
            'research_interests_en' => implode(', ', fake()->words(rand(3, 6))),
            'linkedin_url' => 'https://linkedin.com/in/' . Str::slug($firstName . '-' . $lastName),
            'google_scholar_url' => 'https://scholar.google.com/citations?user=' . Str::random(10),
            'is_active' => true,
            'display_order' => fake()->numberBetween(1, 10),
        ];
    }
}

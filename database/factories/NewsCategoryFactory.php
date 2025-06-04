<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(rand(1, 3), true); // Génère 1 à 3 mots pour le nom

        return [
            'name' => Str::title($name), // Met la première lettre de chaque mot en majuscule
            'slug' => Str::slug($name),
            'is_active' => fake()->boolean(90), // 90% de chance d'être active
            'color' => fake()->optional(0.7)->hexColor(), // 70% de chance d'avoir une couleur
            'text_color' => fake()->optional(0.7)->hexColor(), // 70% de chance d'avoir une couleur de texte
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (NewsCategory $category) {
            // Assurer l'unicité du slug après la création au cas où Str::slug produirait une collision
            // (rare avec fake()->unique()->words() pour le nom, mais une bonne pratique)
            $slug = $category->slug;
            $originalSlug = $slug;
            $count = 1;
            while (NewsCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            if ($slug !== $category->slug) {
                $category->slug = $slug;
                $category->saveQuietly(); // Sauvegarde sans déclencher d'événements de modèle
            }
        });
    }
}
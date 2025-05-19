<?php

namespace Database\Factories;

use App\Models\ResearchAxis;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResearchAxisFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResearchAxis::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence(rand(3, 6)); // Génère un nom unique de 3 à 6 mots
        // Supprime le point final si fake()->sentence() en ajoute un.
        $name = rtrim($name, '.');

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => '<p>' . implode('</p><p>', fake()->paragraphs(fake()->numberBetween(2, 4))) . '</p>',
            // Pour l'image de couverture, nous pouvons laisser null ou mettre un placeholder si vous en avez
            // ou utiliser un service d'images placeholder comme placehold.co
            // 'cover_image_path' => 'https://placehold.co/800x400/EBF4FF/7F9CF5?text=' . urlencode($name),
            'cover_image_path' => null, // Plus simple pour commencer, vous pourrez les ajouter manuellement via l'admin
            'is_active' => fake()->boolean(90), // 90% de chance d'être actif
            'display_order' => fake()->numberBetween(0, 5),
        ];
    }
}
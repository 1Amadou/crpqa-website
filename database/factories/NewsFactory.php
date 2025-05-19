<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->realText(70); // Titre plus réaliste
        $contentParagraphs = fake()->paragraphs(fake()->numberBetween(5, 15));
        $content = "";
        foreach ($contentParagraphs as $para) {
            $content .= "<p>{$para}</p>";
        }

        $summary = Str::limit(strip_tags($content), 150);
        $published = fake()->boolean(80); // 80% de chance d'être publié

        $authorId = null;
    $author = User::whereHas('roles', function ($query) {
        $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur']);
    })->inRandomOrder()->first();

    if ($author) {
        $authorId = $author->id;
    } else {
        // Si aucun admin/éditeur n'est trouvé, créer un utilisateur simple avec un rôle par défaut ou prendre le premier utilisateur
        $user = User::first(); // Prend le premier utilisateur (souvent le Super Admin)
        if (!$user) { // Si absolument aucun utilisateur n'existe (ne devrait pas arriver après UserSeeder)
            $user = User::factory()->create(); // Crée un nouvel utilisateur
            // Optionnel: assigner un rôle par défaut si nécessaire pour la logique de l'application
            // $user->assignRole('Éditeur'); // Assurez-vous que ce rôle existe
        }
        $authorId = $user->id;
    }

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => '<p>' . fake()->realText(150) . '</p>', // Résumé HTML simple
            'content' => $content,
            // 'cover_image_path' => 'https://placehold.co/800x450/EBF4FF/7F9CF5?text=' . urlencode(Str::words($title, 4, '')),
            'cover_image_path' => null, // Laisser null, ajout via l'admin
            'published_at' => $published ? fake()->dateTimeThisYear() : null,
            'is_featured' => fake()->boolean(25), // 25% de chance d'être en vedette
            'meta_title' => Str::limit($title, 60),
            'meta_description' => Str::limit(strip_tags($summary), 155),
            // Essayer de trouver un utilisateur admin ou éditeur pour created_by_user_id
            'created_by_user_id' => User::whereHas('roles', function ($query) {
                                        $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur']);
                                    })->inRandomOrder()->first()?->id ?? User::factory(), // Crée un user si aucun admin/éditeur trouvé
        ];
    }

    /**
     * Indicate that the news item is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => fake()->dateTimeThisYear(),
            ];
        });
    }

    /**
     * Indicate that the news item is featured.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function featured(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }
}
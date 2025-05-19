<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Partner;
use App\Models\User; // <<< AJOUTEZ CETTE LIGNE POUR IMPORTER LE MODÈLE USER
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon; // Assurez-vous que Carbon est importé si vous l'utilisez directement dans la factory

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(4, 8));
        $title = rtrim($title, '.');
        
        // Utiliser Carbon pour les dates si vous l'avez importé
        $startDateTime = Carbon::instance(fake()->dateTimeBetween('+1 week', '+3 months'));
        $endDateTime = null;
        if (fake()->boolean(70)) { // 70% de chance d'avoir une date de fin
            $endDateTime = Carbon::instance(fake()->dateTimeBetween(
                (clone $startDateTime)->addHours(1), // Au moins 1 heure après le début
                (clone $startDateTime)->addHours(fake()->numberBetween(2, 48))
            ));
        }

        $contentParagraphs = fake()->paragraphs(fake()->numberBetween(3, 7));
        $description = "";
        foreach ($contentParagraphs as $para) {
            $description .= "<p>{$para}</p>";
        }

        $authorId = null;
        // La ligne 42 est ici :
        $author = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur']);
        })->inRandomOrder()->first();

        if ($author) {
            $authorId = $author->id;
        } else {
            // Si aucun admin/éditeur n'est trouvé, prendre le premier utilisateur
            // ou en créer un si la table User est vide (ne devrait pas arriver après UserSeeder)
            $user = User::first() ?? User::factory()->create(); // User::factory() nécessite App\Models\User
            $authorId = $user->id;
        }

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $description,
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime,
            'location' => fake()->optional(0.8, null)
                        ->passthrough(fake()->randomElement(['Salle de conférence Alpha', 'Amphithéâtre Bêta', 'En ligne (Zoom)', 'CRPQA - Salle Polyvalente', fake()->address()])),
            'cover_image_path' => null,
            'is_featured' => fake()->boolean(20),
            'registration_url' => fake()->optional(0.3, null)->url(),
            'meta_title' => Str::limit($title, 60),
            'meta_description' => Str::limit(strip_tags($description), 155),
            'target_audience' => fake()->optional(0.6, null)
                                ->passthrough(implode(', ', fake()->words(rand(2, 5)))),
            'created_by_user_id' => $authorId, // Assurez-vous que cette colonne existe dans votre table 'events'
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Event $event) {
            if (Partner::count() > 0) {
                $partners = Partner::where('is_active', true)
                                   ->inRandomOrder()
                                   ->limit(fake()->numberBetween(0, min(2, Partner::where('is_active', true)->count())))
                                   ->pluck('id');
                if ($partners->isNotEmpty()) {
                    $event->partners()->syncWithoutDetaching($partners->all());
                }
            }
        });
    }
}
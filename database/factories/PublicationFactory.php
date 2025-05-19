<?php

namespace Database\Factories;

use App\Models\Publication;
use App\Models\User;
use App\Models\Researcher; // Pour lier des chercheurs
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PublicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Publication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->bs() . ' : ' . fake()->catchPhrase(); // Titre plus académique
        $publicationType = fake()->randomElement(array_keys(Publication::getPublicationTypes()));

        $details = [
            'journal_name' => null,
            'conference_name' => null,
            'volume' => null,
            'issue' => null,
            'pages' => null,
        ];

        if ($publicationType === 'journal_article') {
            $details['journal_name'] = fake()->company() . ' Journal of Quantum Studies';
            $details['volume'] = fake()->numberBetween(1, 50);
            $details['issue'] = fake()->numberBetween(1, 12);
            $details['pages'] = fake()->numberBetween(1, 20) . '-' . fake()->numberBetween(21, 40);
        } elseif ($publicationType === 'conference_paper') {
            $details['conference_name'] = 'International Conference on ' . fake()->bs();
            $details['pages'] = fake()->numberBetween(100, 500);
        } elseif ($publicationType === 'book_chapter') {
            $details['journal_name'] = 'Advanced Topics in ' . fake()->bs(); // Utiliser journal_name pour le titre du livre
            $details['pages'] = fake()->numberBetween(50, 80);
        } elseif ($publicationType === 'book') {
            // Pas de champs spécifiques obligatoires autres que titre, etc.
        }

        $authorId = null;
        $author = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur', 'Chercheur']);
        })->inRandomOrder()->first();

        if ($author) {
            $authorId = $author->id;
        } else {
            $user = User::first();
            if (!$user) {
                $user = User::factory()->create();
                // Optionnel: $user->assignRole('Chercheur');
            }
            $authorId = $user->id;
        }


        return array_merge([
            'title' => $title,
            'slug' => Str::slug($title),
            'abstract' => '<p>' . implode('</p><p>', fake()->paragraphs(fake()->numberBetween(2, 5))) . '</p>',
            'publication_date' => fake()->dateTimeThisDecade(),
            'type' => $publicationType,
            'doi_url' => 'https://doi.org/10.' . fake()->randomNumber(4) . '/' . Str::random(10),
            'external_url' => fake()->optional()->url(),
            // 'pdf_path' => null, // Laisser null, ajout via l'admin
            'is_featured' => fake()->boolean(15),
            'created_by_user_id' => $authorId,
            'authors_external' => fake()->optional(0.3, null)->passthrough(fake()->name() . "\n" . fake()->name()), // 30% de chance d'avoir des auteurs externes
        ], $details);
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
{
    return $this->afterCreating(function (Publication $publication) {
        // Attacher des chercheurs seulement si aucun n'a été explicitement lié par le seeder
        // et s'il y a des chercheurs disponibles.
        if ($publication->researchers()->count() === 0 && Researcher::count() > 0) {
            $numberOfResearchersToAttach = fake()->numberBetween(1, min(2, Researcher::count()));
            $researchers = Researcher::inRandomOrder()
                               ->limit($numberOfResearchersToAttach)
                               ->pluck('id');
            if ($researchers->isNotEmpty()) {
                // Utiliser syncWithoutDetaching pour éviter les erreurs de duplication.
                $publication->researchers()->syncWithoutDetaching($researchers->all());
            }
        }
    });
    }
}
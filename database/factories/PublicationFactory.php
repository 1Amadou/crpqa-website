<?php

namespace Database\Factories;

use App\Models\Publication;
use App\Models\User;
use App\Models\Researcher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        // La méthode getPublicationTypes() est dans PublicationController,
        // Pour l'utiliser ici, il faudrait la rendre accessible (ex: via un helper ou sur le modèle Publication)
        // Pour l'instant, définissons une liste simple ici ou copiez la logique si elle est complexe.
        $publicationTypesArray = [
            'journal_article' => 'Article de Journal',
            'conference_paper' => 'Article de Conférence',
            'book_chapter' => 'Chapitre de Livre',
            'book' => 'Livre',
            'report' => 'Rapport',
            'thesis' => 'Thèse',
            'preprint' => 'Prépublication',
            'other' => 'Autre',
        ];
        $publicationType = fake()->randomElement(array_keys($publicationTypesArray));

        // Génération des champs traduits
        $localizedTitle = [];
        $localizedAbstract = [];
        $baseTitleForSlug = '';

        foreach ($availableLocales as $locale) {
            $currentTitle = fake()->unique()->bs() . ' (' . strtoupper($locale) . ') : ' . fake()->catchPhrase();
            $localizedTitle['title_' . $locale] = $currentTitle;
            $localizedAbstract['abstract_' . $locale] = '<p>' . implode('</p><p>', fake()->paragraphs(fake()->numberBetween(2, 4))) . ' (' . strtoupper($locale) . ')</p>';
            
            if ($locale === config('app.locale', 'fr')) { // Utiliser la locale par défaut pour le slug
                $baseTitleForSlug = $currentTitle;
            }
        }
        // S'assurer qu'on a un titre de base pour le slug même si la locale par défaut n'est pas dans availableLocales
        if (empty($baseTitleForSlug) && !empty($localizedTitle)) {
            $baseTitleForSlug = reset($localizedTitle); // Prend le premier titre disponible
        }


        $details = [
            'journal_name' => null,
            'conference_name' => null,
            'volume' => null,
            'issue' => null,
            'pages' => null,
        ];

        if ($publicationType === 'journal_article') {
            $details['journal_name'] = fake()->company() . ' Journal';
            $details['volume'] = fake()->numberBetween(1, 30);
            $details['issue'] = fake()->numberBetween(1, 12);
            $details['pages'] = fake()->numberBetween(1, 20) . '-' . fake()->numberBetween(21, 40);
        } elseif ($publicationType === 'conference_paper') {
            $details['conference_name'] = 'Proc. of Intl. Conf. on ' . fake()->bs() . ' ' . fake()->year();
            $details['pages'] = fake()->numberBetween(100, 120);
        } elseif ($publicationType === 'book_chapter') {
            $details['journal_name'] = 'Chapter in: Advanced Topics in ' . fake()->bs();
            $details['pages'] = fake()->numberBetween(15, 40);
        } elseif ($publicationType === 'book') {
            // Rien de spécifique
        }

        $authorId = null;
        // Essayer de trouver un utilisateur avec un rôle pertinent
        $author = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur', 'Chercheur']);
        })->inRandomOrder()->first();

        if ($author) {
            $authorId = $author->id;
        } else {
            // Fallback: prendre le premier utilisateur ou en créer un si la table est vide
            $user = User::first();
            if (!$user) {
                // Si vous avez une UserFactory configurée pour les rôles, c'est mieux
                $user = User::factory()->create();
                // Optionnel: Attribuer un rôle par défaut ici si nécessaire
                // $user->assignRole('Éditeur'); 
            }
            $authorId = $user->id;
        }

        return array_merge(
            $localizedTitle,
            $localizedAbstract,
            [
                'slug' => Str::slug($baseTitleForSlug), // Slug basé sur le titre de la langue par défaut
                'publication_date' => fake()->dateTimeThisDecade(),
                'type' => $publicationType,
                'doi_url' => fake()->optional(0.7)->regexify('10\.\d{4,9}/[-._;()/:A-Z0-9]+'), // DOI plus réaliste
                'external_url' => fake()->optional()->url(),
                'is_featured' => fake()->boolean(15), // 15% de chance d'être en vedette
                'created_by_user_id' => $authorId,
                'authors_external' => fake()->optional(0.3)->passthrough(fake()->name() . ' et al.'),
                // Les champs non suffixés 'title' et 'abstract' sont retirés
            ],
            $details
        );
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Publication $publication) {
            if ($publication->researchers()->count() === 0 && Researcher::count() > 0) {
                $numberOfResearchersToAttach = fake()->numberBetween(1, min(3, Researcher::count())); // Max 3 chercheurs
                $researchers = Researcher::inRandomOrder()
                                       ->limit($numberOfResearchersToAttach)
                                       ->pluck('id');
                if ($researchers->isNotEmpty()) {
                    $publication->researchers()->syncWithoutDetaching($researchers->all());
                }
            }

            // Logique pour s'assurer que le slug est unique après la création si nécessaire
            // (bien que la factory essaie déjà avec unique() sur le titre, des collisions de slug sont possibles)
            $slug = $publication->slug;
            $originalSlug = $slug;
            $count = 1;
            while (Publication::where('slug', $slug)->where('id', '!=', $publication->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            if ($slug !== $publication->slug) {
                $publication->slug = $slug;
                $publication->saveQuietly(); // Sauvegarde sans déclencher d'événements
            }
        });
    }
}
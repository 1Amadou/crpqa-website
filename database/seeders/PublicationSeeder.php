<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Researcher;
use App\Models\User;
use Illuminate\Database\Seeder;
// Pas besoin de Str ici si la factory s'en charge, mais gardons-le au cas où.
// use Illuminate\Support\Str; 

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Publications...');
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        if (Researcher::count() == 0) {
            $this->command->warn('No researchers found. Please seed researchers before publications. Skipping publication seeding.');
            return;
        }
        if (User::count() == 0) {
            $this->command->warn('No users found to act as creators. Please seed users. Skipping publication seeding.');
            return;
        }

        $adminOrEditor = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Super Administrateur', 'Éditeur']))
                             ->inRandomOrder()->first() ?? User::firstOrFail();

        // Données pour les publications spécifiques
        $publicationsData = [
            [
                // Champs traduits
                'title_fr' => 'Avancées Récentes dans la Cryptographie Quantique Post-Moderne (FR)',
                'title_en' => 'Recent Advances in Post-Modern Quantum Cryptography (EN)',
                'abstract_fr' => '<p>Cet article explore les dernières avancées significatives dans le domaine de la cryptographie quantique, spécifiquement celles conçues pour résister aux menaces posées par les ordinateurs quantiques futurs. (FR)</p>',
                'abstract_en' => '<p>This paper delves into the latest significant breakthroughs in the field of quantum cryptography, specifically those designed to withstand threats posed by future quantum computers. (EN)</p>',
                // Autres champs
                'type' => 'journal_article',
                'publication_date' => '2024-03-15',
                'journal_name' => 'Journal of Quantum Information Security',
                'volume' => '12',
                'issue' => '3',
                'pages' => '115-130',
                'is_featured' => true,
                'created_by_user_id' => $adminOrEditor->id,
                'authors_external' => "Dr. John Doe (Quantum Institute)\nProf. Alice Smith (Tech University)",
                'researcher_ids' => Researcher::inRandomOrder()->limit(fake()->numberBetween(1,2))->pluck('id')->all(), // Associer 1 ou 2 chercheurs
            ],
            [
                'title_fr' => 'Algorithmes d\'Optimisation Basés sur le Recuit Quantique pour la Logistique (FR)',
                'title_en' => 'Quantum Annealing-Based Optimization Algorithms for Logistics (EN)',
                'abstract_fr' => '<p>Présentation d\'une nouvelle classe d\'algorithmes utilisant le recuit quantique pour résoudre des problèmes complexes d\'optimisation logistique, démontrant des améliorations potentielles par rapport aux méthodes classiques. (FR)</p>',
                'abstract_en' => '<p>Introducing a novel class of algorithms employing quantum annealing to address complex logistical optimization problems, demonstrating potential improvements over classical methods. (EN)</p>',
                'type' => 'conference_paper',
                'publication_date' => '2023-09-22',
                'conference_name' => 'International Conference on Quantum Computing Applications (ICQCA 2023)',
                'pages' => '250-258',
                'created_by_user_id' => $adminOrEditor->id,
                'researcher_ids' => Researcher::inRandomOrder()->limit(fake()->numberBetween(1,3))->pluck('id')->all(),
            ],
            [
                'title_fr' => 'Introduction à la Mécanique Quantique pour les Ingénieurs (FR)',
                'title_en' => 'Introduction to Quantum Mechanics for Engineers (EN)',
                'abstract_fr' => '<p>Un manuel complet fournissant une introduction accessible aux principes fondamentaux de la mécanique quantique, adaptée aux étudiants et professionnels en ingénierie. (FR)</p>',
                'abstract_en' => '<p>A comprehensive textbook providing an accessible introduction to the foundational principles of quantum mechanics, tailored for engineering students and professionals. (EN)</p>',
                'type' => 'book',
                'publication_date' => '2024-01-10',
                'is_featured' => true,
                'created_by_user_id' => $adminOrEditor->id,
                'researcher_ids' => Researcher::whereHas('user', fn($q) => $q->where('email', 'LIKE', '%marie.curie%'))->pluck('id')->all() ?: Researcher::inRandomOrder()->limit(1)->pluck('id')->all(), // Exemple pour lier un chercheur spécifique si trouvé
            ]
        ];

        foreach ($publicationsData as $pubData) {
            $researcherIdsToAttach = $pubData['researcher_ids'] ?? []; // Récupérer les IDs des chercheurs
            unset($pubData['researcher_ids']); // Retirer du tableau principal pour éviter conflit avec create()

            // La factory génère un slug à partir du titre de la langue par défaut
            // Si un slug est explicitement fourni dans $pubData, il sera utilisé, sinon la factory le génère
            // $pubData['slug'] = $pubData['slug'] ?? Str::slug($pubData['title_' . $defaultLocale]);
            
            $publication = Publication::factory()->create($pubData);
            
            if (!empty($researcherIdsToAttach)) {
                $publication->researchers()->sync($researcherIdsToAttach); // sync pour un contrôle exact des chercheurs pour ces entrées spécifiques
            }
            $this->command->line("Created publication: " . $publication->getTranslation('title', $defaultLocale, false));
        }

        $this->command->info('Creating 10 additional random publications using factory...');
        // La factory s'occupe maintenant de générer les champs traduits et d'associer les chercheurs.
        Publication::factory()->count(10)->create();
        $this->command->line("Created 10 additional random publications.");

        $this->command->info('Publications seeding completed.');
    }
}
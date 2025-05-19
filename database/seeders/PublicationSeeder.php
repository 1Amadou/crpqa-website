<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Researcher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Publications...');

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

        // Publication 1: Article de Journal
        $pub1Data = [
            'title' => 'Avancées Récentes dans la Cryptographie Quantique Post-Moderne',
            'type' => 'journal_article',
            'publication_date' => '2024-03-15',
            'journal_name' => 'Journal of Quantum Information Security',
            'volume' => '12',
            'issue' => '3',
            'pages' => '115-130',
            'is_featured' => true,
            'created_by_user_id' => $adminOrEditor->id,
            'authors_external' => "Dr. John Doe (Quantum Institute)\nProf. Alice Smith (Tech University)",
        ];
        $pub1 = Publication::factory()->create($pub1Data);
        $researcher1 = Researcher::inRandomOrder()->first(); // Pourrait être déjà lié par la factory
        if ($researcher1) {
            // Utiliser syncWithoutDetaching pour éviter l'erreur de duplication
            $pub1->researchers()->syncWithoutDetaching([$researcher1->id]); 
        }
        $this->command->line("Created publication: {$pub1->title}");

        // Publication 2: Papier de Conférence
        $pub2Data = [
            'title' => 'Algorithmes d\'Optimisation Basés sur le Recuit Quantique pour la Logistique',
            'type' => 'conference_paper',
            'publication_date' => '2023-09-22',
            'conference_name' => 'International Conference on Quantum Computing Applications (ICQCA 2023)',
            'pages' => '250-258',
            'created_by_user_id' => $adminOrEditor->id,
        ];
        $pub2 = Publication::factory()->create($pub2Data);
        $researchers2 = Researcher::inRandomOrder()->limit(2)->pluck('id');
        if ($researchers2->isNotEmpty()) {
            // Utiliser syncWithoutDetaching ici aussi
            $pub2->researchers()->syncWithoutDetaching($researchers2->all()); 
        }
        $this->command->line("Created publication: {$pub2->title}");

        // Publication 3: Livre
        $pub3Data = [
            'title' => 'Introduction à la Mécanique Quantique pour les Ingénieurs',
            'type' => 'book',
            'publication_date' => '2024-01-10',
            'is_featured' => true,
            'created_by_user_id' => $adminOrEditor->id,
        ];
        $pub3 = Publication::factory()->create($pub3Data);
        $marieCurie = Researcher::where('email', 'marie.curie@example.com')->first();
        if ($marieCurie) {
            // Si vous voulez que Marie Curie soit la SEULE auteur, utilisez sync.
            // Si vous voulez l'AJOUTER aux auteurs potentiels de la factory, utilisez syncWithoutDetaching.
            // Supposons que pour cet exemple, nous voulons seulement Marie Curie.
            $pub3->researchers()->sync([$marieCurie->id]); 
        }
        $this->command->line("Created publication: {$pub3->title}");


        $this->command->info('Creating 10 additional random publications...');
        // La factory s'occupe des liaisons pour ces publications génériques
        // grâce à la condition `if ($publication->researchers()->count() === 0 ...)`
        // et `syncWithoutDetaching` dans la factory.
        Publication::factory()->count(10)->create();
        $this->command->line("Created 10 additional random publications.");

        $this->command->info('Publications seeding completed.');
    }
}
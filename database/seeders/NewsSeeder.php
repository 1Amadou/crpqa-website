<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding News...');

        // Récupérer un utilisateur admin/éditeur pour created_by_user_id
        $author = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Administrateur', 'Administrateur', 'Éditeur']);
        })->inRandomOrder()->first();

        if (!$author) {
            $author = User::first();
            if (!$author) {
                $this->command->error('No users found to assign as author for news. Please seed users first.');
                return;
            }
        }

        $newsItems = [
            [
                'title_fr' => 'Découverte Révolutionnaire en Informatique Quantique au CRPQA',
                'summary_fr' => '<p>Une équipe de chercheurs du CRPQA a annoncé une avancée majeure qui pourrait redéfinir les capacités de calcul quantique.</p>',
                'content_fr' => '<p>Après des années de recherche intensive, le Dr. Aïssata Traoré et son équipe ont publié leurs résultats dans la prestigieuse revue "Quantum Innovations". Leur nouvelle méthode permet de stabiliser les qubits sur des périodes significativement plus longues, ouvrant la voie à des ordinateurs quantiques plus fiables et plus puissants.</p><p>Le centre prévoit une conférence de presse la semaine prochaine pour détailler cette découverte et ses implications futures.</p>',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'created_by_user_id' => $author->id,
            ],
            [
                'title_fr' => 'Le CRPQA Lance un Nouveau Programme de Bourses Doctorales en Physique des Particules',
                'summary_fr' => '<p>Afin de soutenir la recherche de pointe et de former la prochaine génération de scientifiques, le CRPQA ouvre son programme de bourses 2025-2028.</p>',
                'content_fr' => '<p>Le programme "Jeunes Talents Quantiques" offrira un financement complet et un mentorat à cinq doctorants exceptionnels. Les candidatures sont ouvertes jusqu\'au 31 juillet. Les domaines de recherche prioritaires incluent la théorie des cordes, la matière noire et l\'étude des neutrinos.</p><p>Plus d\'informations et le formulaire de candidature sont disponibles sur notre site web.</p>',
                'published_at' => now()->subDays(10),
                'is_featured' => false,
                'created_by_user_id' => $author->id,
            ],
            [
                'title_fr' => 'Atelier International sur les Matériaux Topologiques organisé par le CRPQA',
                'summary_fr' => '<p>Des experts mondiaux se réuniront au CRPQA du 15 au 17 novembre pour discuter des dernières avancées dans le domaine des isolants et supraconducteurs topologiques.</p>',
                'content_fr' => '<p>Cet atelier vise à favoriser les collaborations internationales et à explorer les applications potentielles des matériaux topologiques dans l\'électronique de demain et l\'informatique quantique. L\'inscription est gratuite mais obligatoire.</p>',
                'published_at' => null, // Brouillon
                'is_featured' => false,
                'created_by_user_id' => $author->id,
            ],
        ];

        foreach ($newsItems as $itemData) {
            // Générer le slug à partir du titre français
            $itemData['slug'] = Str::slug($itemData['title_fr']);

            // Générer les meta-tags français si non fournis
            $itemData['meta_title_fr'] = $itemData['meta_title_fr'] ?? Str::limit($itemData['title_fr'], 60);
            $itemData['meta_description_fr'] = $itemData['meta_description_fr'] ?? Str::limit(strip_tags($itemData['summary_fr']), 155);

            News::create($itemData);
            $this->command->line("Created news: {$itemData['title_fr']}");
        }

        // Créer des actualités aléatoires avec la factory
        News::factory()->count(7)->published()->create();
        $this->command->line("Created 7 additional random published news items.");

        News::factory()->count(3)->create(['published_at' => null]); // Brouillons
        $this->command->line("Created 3 additional random draft news items.");

        $this->command->info('News seeding completed.');
    }
}

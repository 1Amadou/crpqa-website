<?php

namespace Database\Seeders;

use App\Models\News; // Utiliser le modèle News consolidé
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // Peut être utile pour les slugs manuels

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding News Articles...');
        
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        // Vérification de l'existence des dépendances
        if (User::count() == 0) {
            $this->command->warn('No users found. Please seed users first. Attempting to create a default user for news.');
            User::factory()->create(['name' => 'Default Admin', 'email' => 'admin@example.com']); // Crée un admin par défaut si aucun utilisateur
        }
        if (NewsCategory::count() == 0) {
            $this->command->warn('No news categories found. Consider seeding news categories first for better data.');
            // Optionnel: créer une catégorie par défaut ici si nécessaire
            // NewsCategory::factory()->create(['name_fr' => 'Général', 'name_en' => 'General', 'slug' => 'general']);
        }

        $adminOrEditor = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Super Administrateur', 'Éditeur']))
                             ->inRandomOrder()->first() ?? User::firstOrFail();
        
        $categories = NewsCategory::all();

        // Création de quelques actualités spécifiques avec traductions
        $specificNewsData = [
            [
                'title_fr' => 'Grande Découverte au CRPQA : Une Nouvelle Particule Élémentaire',
                'title_en' => 'Major Discovery at CRPQA: A New Elementary Particle',
                'summary_fr' => 'Des chercheurs du CRPQA annoncent la découverte d\'une particule subatomique qui pourrait redéfinir notre compréhension de la matière noire.',
                'summary_en' => 'CRPQA researchers announce the discovery of a subatomic particle that could redefine our understanding of dark matter.',
                'content_fr' => '<p>L\'équipe dirigée par le Dr. Traoré a publié ses résultats dans la prestigieuse revue "Quantum Frontiers". Cette découverte est l\'aboutissement de 5 ans de recherches intensives...</p>',
                'content_en' => '<p>The team led by Dr. Traoré published their findings in the prestigious journal "Quantum Frontiers". This discovery is the culmination of 5 years of intensive research...</p>',
                'meta_title_fr' => 'Nouvelle Particule Découverte au CRPQA',
                'meta_title_en' => 'New Particle Discovered at CRPQA',
                'meta_description_fr' => 'Le CRPQA annonce une découverte majeure : une nouvelle particule élémentaire avec des implications pour la physique fondamentale.',
                'meta_description_en' => 'CRPQA announces a major discovery: a new elementary particle with implications for fundamental physics.',
                'cover_image_alt_fr' => 'Illustration de la nouvelle particule élémentaire',
                'cover_image_alt_en' => 'Illustration of the new elementary particle',
                'news_category_id' => $categories->isNotEmpty() ? $categories->random()->id : null,
                'created_by_user_id' => $adminOrEditor->id,
                'published_at' => now()->subDays(5),
                'is_published' => true,
                'is_featured' => true,
                'slug' => Str::slug('Grande Decouverte CRPQA Nouvelle Particule Elementaire'), // Slug manuel ou laisser la factory générer
            ],
            [
                'title_fr' => 'Conférence Annuelle du CRPQA sur l\'Informatique Quantique : Les Inscriptions sont Ouvertes',
                'title_en' => 'CRPQA Annual Quantum Computing Conference: Registrations Open',
                'summary_fr' => 'Ne manquez pas la conférence phare du CRPQA sur l\'informatique quantique. Les meilleurs experts mondiaux seront présents.',
                'summary_en' => 'Don\'t miss CRPQA\'s flagship conference on quantum computing. Top world experts will be attending.',
                'content_fr' => '<p>Inscrivez-vous dès maintenant pour notre conférence annuelle qui se tiendra du 10 au 12 Décembre. Au programme : ateliers, keynotes et networking...</p>',
                'content_en' => '<p>Register now for our annual conference to be held from December 10th to 12th. Program includes workshops, keynotes, and networking opportunities...</p>',
                'news_category_id' => $categories->isNotEmpty() ? $categories->random()->id : null,
                'created_by_user_id' => $adminOrEditor->id,
                'published_at' => now()->subDays(10),
                'is_published' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($specificNewsData as $data) {
            // Si le slug n'est pas fourni, la factory s'en chargera à partir du titre_fr (ou locale par défaut)
            if (!isset($data['slug'])) {
                 $titleForSlug = $data['title_' . $primaryLocale] ?? ($data['title_en'] ?? 'news-article');
                 $data['slug'] = Str::slug($titleForSlug);
            }
            // Assurer l'unicité du slug pour les données spécifiques
            $originalSlug = $data['slug'];
            $count = 1;
            while (News::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }

            News::create($data); // Utiliser create() directement car nous avons tous les champs
            $this->command->line("Created specific news: " . ($data['title_' . $primaryLocale] ?? $data['slug']));
        }

        // Créer des actualités supplémentaires avec la factory
        $numberOfRandomNews = 15;
        $this->command->info("Creating {$numberOfRandomNews} additional random news articles using factory...");
        News::factory()->count($numberOfRandomNews)->create();
        $this->command->line("Created {$numberOfRandomNews} additional random news articles.");

        $this->command->info('News seeding completed.');
    }
}
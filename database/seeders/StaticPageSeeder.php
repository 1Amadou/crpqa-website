<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaticPage; // Importer le modèle StaticPage
use App\Models\User;       // Importer le modèle User
use Illuminate\Support\Facades\Log; // Pour un logging plus discret si besoin

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Seeding Static Pages...');

        // Récupérer l'utilisateur admin (créé par UserSeeder ou RolesAndPermissionsSeeder)
        // L'email dans votre seeder original était admin@crpqa.test, 
        // mais dans RolesAndPermissionsSeeder, il était superadmin@crpqa.ml
        // Utilisons superadmin@crpqa.ml pour la cohérence, ou adaptez selon votre UserSeeder.
        $adminUser = User::where('email', 'superadmin@crpqa.ml')->first();
        $defaultLocale = config('app.locale', 'fr'); // Locale par défaut
        $availableLocales = config('app.available_locales', ['fr', 'en']);


        if (!$adminUser) {
            $this->command->error('Default admin user (superadmin@crpqa.ml) not found. Static pages will not be seeded or will lack a user_id.');
            // Optionnel: Créer un utilisateur admin par défaut ici si crucial et qu'il peut manquer
            // $adminUser = User::factory()->create(['name' => 'Default Admin User', 'email' => 'admin_static@crpqa.ml']);
            // $adminUser->assignRole('Super Administrateur'); // Si vous avez un système de rôles
            // $this->command->info('Created a fallback admin user for static pages.');
            return; // Ou arrêter le seeder si l'utilisateur est indispensable
        }

        $pagesData = [
            [
                'slug' => 'a-propos',
                'title_key' => 'À Propos du CRPQA', // Titre de base
                'content_key' => '<h2>Notre Histoire</h2><p>Le Centre de Recherche en Physique Quantique et ses Applications (CRPQA) a été fondé avec l\'ambition de devenir un pôle d\'excellence en Afrique de l\'Ouest...</p><h2>Notre Mission</h2><p>Notre mission est de faire progresser la recherche fondamentale et appliquée en physique quantique, de former la prochaine génération de scientifiques maliens et africains dans ce domaine, et de développer des applications innovantes...</p><h2>Notre Vision</h2><p>Nous aspirons à être un centre de renommée internationale, contribuant significativement aux avancées scientifiques et technologiques issues de la physique quantique...</p><p>(Ce contenu est un placeholder et peut être modifié via le tableau de bord.)</p>',
                'is_published' => true,
                'user_id' => $adminUser->id,
                'meta_title_key' => 'À Propos | CRPQA',
                'meta_description_key' => 'Découvrez le Centre de Recherche en Physique Quantique et ses Applications (CRPQA), sa mission, sa vision et son histoire.',
                // Vous pouvez ajouter ici des traductions spécifiques pour d'autres langues si vous le souhaitez
                // 'title_en' => 'About CRPQA',
                // 'content_en' => '<h2>Our History</h2><p>...</p>',
            ],
            [
                'slug' => 'contactez-nous',
                'title_key' => 'Contactez-Nous',
                'content_key' => '<p>Pour toute question, demande de collaboration ou information, n\'hésitez pas à nous contacter :</p><ul><li><strong>Email :</strong> contact@crpqa.ml (email officiel à confirmer)</li><li><strong>Téléphone :</strong> +223 XX XX XX XX (à remplacer)</li><li><strong>Adresse Postale :</strong> CRPQA, Université des Sciences, des Techniques et des Technologies de Bamako (USTTB), Bamako, Mali (à préciser)</li></ul><p>(Un formulaire de contact sera ajouté ultérieurement via le tableau de bord.)</p>',
                'is_published' => true,
                'user_id' => $adminUser->id,
                'meta_title_key' => 'Contactez-Nous | CRPQA',
                'meta_description_key' => 'Informations de contact pour le Centre de Recherche en Physique Quantique et ses Applications.',
            ],
            [
                'slug' => 'axes-de-recherche-introduction', // Ce slug existe aussi dans ResearchAxisSeeder
                'title_key' => 'Introduction aux Axes de Recherche du CRPQA', // Titre plus spécifique pour éviter confusion
                'content_key' => '<p>Le CRPQA concentre ses efforts de recherche sur plusieurs axes thématiques clés, à la pointe de la physique quantique et de ses applications. Ces axes sont choisis pour leur potentiel scientifique et leur pertinence pour le développement technologique.</p><p>Les détails de chaque axe sont disponibles dans les sections dédiées du site.</p><p>(Ce contenu est un placeholder.)</p>',
                'is_published' => true,
                'user_id' => $adminUser->id,
                'meta_title_key' => 'Axes de Recherche - Introduction | CRPQA',
                'meta_description_key' => 'Aperçu des principaux axes de recherche développés au sein du CRPQA.',
            ],
            // Ajoutez d'autres pages statiques ici si nécessaire
        ];

        foreach ($pagesData as $pageData) {
            $localizedFields = [];
            foreach ($availableLocales as $locale) {
                // Pour cet exemple, nous utilisons le même contenu pour toutes les langues,
                // en suffixant avec (LANGUE) pour la démonstration.
                // Dans un cas réel, vous auriez des traductions distinctes.
                $suffix = count($availableLocales) > 1 ? ' (' . strtoupper($locale) . ')' : '';
                
                $localizedFields['title_' . $locale] = ($pageData['title_key'] ?? 'Titre par défaut') . ($locale !== $defaultLocale ? $suffix : '');
                $localizedFields['content_' . $locale] = ($pageData['content_key'] ?? 'Contenu par défaut') . ($locale !== $defaultLocale ? $suffix : '');
                $localizedFields['meta_title_' . $locale] = ($pageData['meta_title_key'] ?? $localizedFields['title_' . $locale]);
                $localizedFields['meta_description_' . $locale] = ($pageData['meta_description_key'] ?? Str::limit(strip_tags($localizedFields['content_' . $locale]), 160));
                // Pour cover_image_alt_text, si vous l'avez ajouté au modèle StaticPage
                // $localizedFields['cover_image_alt_text_' . $locale] = "Image pour " . $localizedFields['title_' . $locale];
            }

            StaticPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                array_merge(
                    [
                        'is_published' => $pageData['is_published'],
                        'user_id' => $pageData['user_id'],
                    ],
                    $localizedFields // Fusionne les champs traduits
                )
            );
            $this->command->line("Static page '{$pageData['slug']}' seeded/updated.");
        }
        $this->command->info('Static Pages seeding completed.');
    }
}
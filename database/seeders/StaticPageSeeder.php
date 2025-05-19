<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaticPage; // Importer le modèle StaticPage
use App\Models\User;       // Importer le modèle User

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Récupérer l'utilisateur admin (créé par UserSeeder)
        $adminUser = User::where('email', 'admin@crpqa.test')->first();

        if ($adminUser) {
            StaticPage::firstOrCreate(
                ['slug' => 'a-propos'], // Identifiant unique pour la page
                [
                    'title' => 'À Propos du CRPQA',
                    'content' => '<h2>Notre Histoire</h2><p>Le Centre de Recherche en Physique Quantique et ses Applications (CRPQA) a été fondé avec l\'ambition de devenir un pôle d\'excellence en Afrique de l\'Ouest...</p>
                                  <h2>Notre Mission</h2><p>Notre mission est de faire progresser la recherche fondamentale et appliquée en physique quantique, de former la prochaine génération de scientifiques maliens et africains dans ce domaine, et de développer des applications innovantes...</p>
                                  <h2>Notre Vision</h2><p>Nous aspirons à être un centre de renommée internationale, contribuant significativement aux avancées scientifiques et technologiques issues de la physique quantique...</p>
                                  <p>(Ce contenu est un placeholder et peut être modifié via le tableau de bord.)</p>',
                    'is_published' => true,
                    'user_id' => $adminUser->id,
                    'meta_title' => 'À Propos | CRPQA',
                    'meta_description' => 'Découvrez le Centre de Recherche en Physique Quantique et ses Applications (CRPQA), sa mission, sa vision et son histoire.'
                ]
            );

            StaticPage::firstOrCreate(
                ['slug' => 'contactez-nous'],
                [
                    'title' => 'Contactez-Nous',
                    'content' => '<p>Pour toute question, demande de collaboration ou information, n\'hésitez pas à nous contacter :</p>
                                  <ul>
                                    <li><strong>Email :</strong> contact@crpqa.test (à remplacer par l\'email officiel)</li>
                                    <li><strong>Téléphone :</strong> +223 XX XX XX XX (à remplacer)</li>
                                    <li><strong>Adresse Postale :</strong> CRPQA, Université des Sciences, des Techniques et des Technologies de Bamako (USTTB), Bamako, Mali (à préciser)</li>
                                  </ul>
                                  <p>(Un formulaire de contact sera ajouté ultérieurement via le tableau de bord.)</p>',
                    'is_published' => true,
                    'user_id' => $adminUser->id,
                    'meta_title' => 'Contactez-Nous | CRPQA',
                    'meta_description' => 'Informations de contact pour le Centre de Recherche en Physique Quantique et ses Applications.'
                ]
            );

            StaticPage::firstOrCreate(
                ['slug' => 'axes-de-recherche-introduction'],
                [
                    'title' => 'Introduction aux Axes de Recherche',
                    'content' => '<p>Le CRPQA concentre ses efforts de recherche sur plusieurs axes thématiques clés, à la pointe de la physique quantique et de ses applications. Ces axes sont choisis pour leur potentiel scientifique et leur pertinence pour le développement technologique.</p>
                                  <p>Les détails de chaque axe seront disponibles dans les sections dédiées du site.</p>
                                  <p>(Ce contenu est un placeholder.)</p>',
                    'is_published' => true,
                    'user_id' => $adminUser->id,
                    'meta_title' => 'Axes de Recherche | Introduction | CRPQA',
                    'meta_description' => 'Aperçu des principaux axes de recherche développés au sein du CRPQA.'
                ]
            );

        } else {
            // Si l'utilisateur admin n'est pas trouvé, on affiche un message dans la console
            if ($this->command) {
                $this->command->error('Utilisateur admin (admin@crpqa.test) non trouvé. Les pages statiques ne seront pas associées à un user_id.');
            }
        }
    }
}
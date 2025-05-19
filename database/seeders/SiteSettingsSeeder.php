<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting; // Importer le modèle SiteSetting

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Crée ou met à jour la ligne de paramètres (en supposant qu'elle a l'ID 1)
        SiteSetting::updateOrCreate(
            ['id' => 1], // Critère pour trouver la ligne de paramètres
            [
                'site_name' => 'CRPQA - Centre de Recherche en Physique Quantique',
                'contact_email' => 'contact@crpqa.test', // Modifiez avec le vrai email plus tard
                'contact_phone' => '+223 XX XX XX XX', // Modifiez avec le vrai numéro
                'address' => 'Université de Bamako, Colline de Badalabougou, Bamako, Mali', // Exemple, à affiner
                'footer_text' => '© ' . date('Y') . ' CRPQA. Tous droits réservés.',
                // Laissez les chemins de logo/favicon à null pour l'instant,
                // ou mettez des placeholders si vous en avez.
                'logo_path' => null,
                'favicon_path' => null,
                'facebook_url' => 'https://facebook.com/crpqa', // Exemple
                'twitter_url' => 'https://twitter.com/crpqa',   // Exemple
                'linkedin_url' => 'https://linkedin.com/company/crpqa', // Exemple
                'youtube_url' => null,
            ]
        );
    }
}
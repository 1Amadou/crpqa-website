<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Partners...');

        $partners = [
            [
                'name' => 'Université des Sciences et Techniques Avancées (USTA)',
                'website_url' => 'https://www.usta-mali.ml',
                'description' => '<p>Partenaire académique clé pour la recherche fondamentale et la formation de la prochaine génération de physiciens quantiques.</p>',
                'type' => 'Université',
                'is_active' => true,
                'display_order' => 0,
            ],
            [
                'name' => 'QuantumLeap Technologies Inc.',
                'website_url' => 'https://www.quantumleap.tech',
                'description' => '<p>Entreprise leader dans le développement de matériel informatique quantique, collaborant sur des projets d\'application industrielle.</p>',
                'type' => 'Entreprise',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Fondation pour l\'Innovation Scientifique (FIS)',
                'website_url' => 'https://www.fis-foundation.org',
                'description' => '<p>Soutient financièrement nos projets de recherche et nos bourses doctorales.</p>',
                'type' => 'Fondation',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Ministère de l\'Enseignement Supérieur et de la Recherche Scientifique',
                'website_url' => null,
                'description' => '<p>Partenaire institutionnel pour l\'alignement avec les stratégies nationales de recherche.</p>',
                'type' => 'Institution Gouvernementale',
                'is_active' => true,
                'display_order' => 3,
            ],
             [
                'name' => 'TechInnov Mali',
                'website_url' => 'https://techinnovmali.com',
                'description' => '<p>Start-up malienne spécialisée dans les solutions logicielles, partenaire pour des projets d\'IA quantique.</p>',
                'type' => 'Entreprise',
                'is_active' => false, // Exemple de partenaire non actif
                'display_order' => 4,
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::factory()->create($partnerData);
            $this->command->line("Created partner: {$partnerData['name']}");
        }

        // Créer quelques partenaires supplémentaires avec la factory pour plus de volume
        Partner::factory()->count(5)->create();
        $this->command->line("Created 5 additional random partners.");


        $this->command->info('Partners seeding completed.');
    }
}
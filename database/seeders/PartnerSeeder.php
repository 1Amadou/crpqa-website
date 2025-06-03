<?php

namespace Database\Seeders;

use App\Models\Partner;
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
                'name_fr'       => 'Université des Sciences et Techniques Avancées (USTA)',
                'name_en'       => 'Advanced Science and Technology University (USTA)',
                'website_url'   => 'https://www.usta-mali.ml',
                'description_fr'=> '<p>Partenaire académique clé pour la recherche fondamentale et la formation de la prochaine génération de physiciens quantiques.</p>',
                'description_en'=> '<p>Key academic partner for fundamental research and training the next generation of quantum physicists.</p>',
                'type'          => 'Académique',
                'is_active'     => true,
                'display_order' => 0,
            ],
            [
                'name_fr'       => 'QuantumLeap Technologies Inc.',
                'name_en'       => 'QuantumLeap Technologies Inc.',
                'website_url'   => 'https://www.quantumleap.tech',
                'description_fr'=> '<p>Entreprise leader dans le développement de matériel informatique quantique, collaborant sur des projets d\'application industrielle.</p>',
                'description_en'=> '<p>Leading company in quantum computing hardware development, collaborating on industrial application projects.</p>',
                'type'          => 'Entreprise',
                'is_active'     => true,
                'display_order' => 1,
            ],
            [
                'name_fr'       => 'Fondation pour l\'Innovation Scientifique (FIS)',
                'name_en'       => 'Foundation for Scientific Innovation (FSI)',
                'website_url'   => 'https://www.fis-foundation.org',
                'description_fr'=> '<p>Soutient financièrement nos projets de recherche et nos bourses doctorales.</p>',
                'description_en'=> '<p>Provides financial support for our research projects and doctoral scholarships.</p>',
                'type'          => 'Fondation',
                'is_active'     => true,
                'display_order' => 2,
            ],
            [
                'name_fr'       => 'Ministère de l\'Enseignement Supérieur et de la Recherche Scientifique',
                'name_en'       => 'Ministry of Higher Education and Scientific Research',
                'website_url'   => null,
                'description_fr'=> '<p>Partenaire institutionnel pour l\'alignement avec les stratégies nationales de recherche.</p>',
                'description_en'=> '<p>Institutional partner for alignment with national research strategies.</p>',
                'type'          => 'Institutionnel',
                'is_active'     => true,
                'display_order' => 3,
            ],
            [
                'name_fr'       => 'TechInnov Mali',
                'name_en'       => 'TechInnov Mali',
                'website_url'   => 'https://techinnovmali.com',
                'description_fr'=> '<p>Start-up malienne spécialisée dans les solutions logicielles, partenaire pour des projets d\'IA quantique.</p>',
                'description_en'=> '<p>Malian startup specializing in software solutions, partner for quantum AI projects.</p>',
                'type'          => 'Entreprise',
                'is_active'     => false,
                'display_order' => 4,
            ],
        ];

        foreach ($partners as $data) {
            Partner::factory()->create($data);
            $this->command->line("Created partner: {$data['name_fr']}");
        }

        Partner::factory()->count(5)->create();
        $this->command->line("Created 5 additional random partners.");

        $this->command->info('Partners seeding completed.');
    }
}

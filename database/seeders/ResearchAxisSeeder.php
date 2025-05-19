<?php

namespace Database\Seeders;

use App\Models\ResearchAxis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearchAxisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Research Axes...');

        $axes = [
            [
                'name' => 'Physique Théorique et Computationnelle',
                'description' => '<p>Exploration des fondements théoriques de la physique quantique et développement de méthodes computationnelles pour simuler des systèmes complexes.</p><p>Nous nous concentrons sur la théorie des champs quantiques, la matière condensée et l\'informatique quantique.</p>',
                'is_active' => true,
                'display_order' => 0,
            ],
            [
                'name' => 'Optique et Photonique Quantiques',
                'description' => '<p>Étude de l\'interaction lumière-matière à l\'échelle quantique, incluant les lasers, les capteurs quantiques et la communication quantique.</p>',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Matériaux Quantiques et Nanostructures',
                'description' => '<p>Conception, synthèse et caractérisation de nouveaux matériaux aux propriétés quantiques exotiques, tels que les supraconducteurs, les matériaux topologiques et les points quantiques.</p>',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Informatique et Algorithmes Quantiques',
                'description' => '<p>Développement d\'algorithmes quantiques pour résoudre des problèmes actuellement insolubles par les ordinateurs classiques, et recherche sur les architectures d\'ordinateurs quantiques.</p>',
                'is_active' => false, // Exemple d'un axe non actif
                'display_order' => 3,
            ],
            [
                'name' => 'Cosmologie et Gravitation Quantiques',
                'description' => '<p>Recherche sur l\'unification de la mécanique quantique et de la relativité générale pour comprendre les premiers instants de l\'univers et la nature des trous noirs.</p>',
                'is_active' => true,
                'display_order' => 4,
            ]
        ];

        foreach ($axes as $axisData) {
            ResearchAxis::factory()->create($axisData);
            $this->command->line("Created research axis: {$axisData['name']}");
        }

        // Vous pouvez également ajouter quelques axes générés aléatoirement si besoin
        // ResearchAxis::factory()->count(2)->create();
        // $this->command->line("Created 2 additional random research axes.");


        $this->command->info('Research Axes seeding completed.');
    }
}
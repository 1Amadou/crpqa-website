<?php

namespace Database\Seeders;

use App\Models\ResearchAxis;
use Illuminate\Database\Seeder;

class ResearchAxisSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Research Axes...');

        // Données spécifiques si nécessaire, sinon la factory s'en charge
        $axesData = [
            [
                // Pour chaque langue dans config('app.available_locales')
                'name_fr' => 'Physique Théorique et Computationnelle (FR)',
                'name_en' => 'Theoretical and Computational Physics (EN)',
                'subtitle_fr' => 'Exploration des fondements de l\'univers quantique. (FR)',
                'subtitle_en' => 'Exploring the foundations of the quantum universe. (EN)',
                'description_fr' => '<p>Cet axe se concentre sur...</p>',
                'description_en' => '<p>This axis focuses on...</p>',
                'icon_svg_fr' => '<svg></svg>',
                'icon_svg_en' => '<svg></svg>',
                'color_hex' => '#2C3E50',
                'display_order' => 1,
                'is_active' => true,
                // meta_title, meta_description, cover_image_alt_text pour chaque langue
            ],
            // ... autres axes spécifiques
        ];

        foreach ($axesData as $data) {
            // La factory génère le slug, mais si vous le spécifiez ici, il sera utilisé
            // et la factory s'assurera de son unicité via la méthode configure()
             ResearchAxis::factory()->create($data); 
             $this->command->line("Created specific research axis: " . ($data['name_fr'] ?? 'Axe'));
        }

        // Créer des axes supplémentaires avec la factory si besoin, en plus de ceux spécifiques
        $numberOfRandomAxes = 3; // Si vous en voulez moins après les spécifiques
        if (ResearchAxis::count() < (count($axesData) + $numberOfRandomAxes)) {
             $this->command->info("Creating {$numberOfRandomAxes} additional random research axes...");
             ResearchAxis::factory()->count($numberOfRandomAxes - (ResearchAxis::count() - count($axesData)))->create();
             $this->command->line("Created {$numberOfRandomAxes} additional random research axes.");
        }

        $this->command->info('Research Axes seeding completed.');
    }
}
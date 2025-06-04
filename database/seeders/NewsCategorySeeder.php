<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding News Categories...');

        $categories = [
            ['name' => 'Actualités du Centre', 'color' => '#2563EB', 'text_color' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Découvertes Scientifiques', 'color' => '#10B981', 'text_color' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Événements à Venir', 'color' => '#F59E0B', 'text_color' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Collaborations', 'color' => '#8B5CF6', 'text_color' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Publications Récentes', 'color' => '#EC4899', 'text_color' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Annonces', 'color' => '#64748B', 'text_color' => '#FFFFFF', 'is_active' => false], // Exemple de catégorie inactive
        ];

        foreach ($categories as $categoryData) {
            $slug = Str::slug($categoryData['name']);
            NewsCategory::updateOrCreate(
                ['slug' => $slug], // Clé pour trouver/créer
                [                 // Valeurs à insérer/mettre à jour
                    'name' => $categoryData['name'],
                    'color' => $categoryData['color'] ?? null,
                    'text_color' => $categoryData['text_color'] ?? null,
                    'is_active' => $categoryData['is_active'] ?? true,
                ]
            );
            $this->command->line("Seeded news category: {$categoryData['name']}");
        }

        // Optionnel: Créer quelques catégories supplémentaires avec la factory
        $numberOfRandomCategories = 3;
        if (NewsCategory::count() < (count($categories) + $numberOfRandomCategories)) {
            $this->command->info("Creating {$numberOfRandomCategories} additional random news categories using factory...");
            NewsCategory::factory()->count($numberOfRandomCategories)->create();
        }
        
        $this->command->info('News Categories seeding completed.');
    }
}
<?php

namespace Database\Factories;

use App\Models\ResearchAxis;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResearchAxisFactory extends Factory
{
    protected $model = ResearchAxis::class;

    public function definition(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $localizedData = [];
        $baseNameForSlug = '';

        foreach ($availableLocales as $locale) {
            $currentName = fake()->words(rand(2, 5), true) . ' (' . strtoupper($locale) . ')';
            if ($locale === $primaryLocale) {
                $baseNameForSlug = $currentName;
            }
            $localizedData['name_' . $locale] = Str::title($currentName); // Met la première lettre de chaque mot en majuscule
            $localizedData['subtitle_' . $locale] = fake()->optional(0.7)->sentence(rand(5, 10));
            $localizedData['description_' . $locale] = '<p>' . implode('</p><p>', fake()->paragraphs(rand(2, 4))) . '</p>';
            $localizedData['meta_title_' . $locale] = Str::limit($localizedData['name_' . $locale], 60);
            $localizedData['meta_description_' . $locale] = Str::limit(strip_tags($localizedData['description_' . $locale]), 155);
            $localizedData['icon_svg_' . $locale] = fake()->optional(0.5)->passthrough('<svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path></svg>'); // Exemple de SVG
            $localizedData['cover_image_alt_text_' . $locale] = __('Image de couverture pour l\'axe de recherche') . ' ' . $localizedData['name_' . $locale];
        }
        
        if (empty($baseNameForSlug) && !empty($localizedData['name_' . $primaryLocale])) {
            $baseNameForSlug = $localizedData['name_' . $primaryLocale];
        } elseif (empty($baseNameForSlug) && !empty($localizedData)) {
            $firstNameKey = key(array_filter($localizedData, fn($key) => strpos($key, 'name_') === 0, ARRAY_FILTER_USE_KEY));
            $baseNameForSlug = $localizedData[$firstNameKey] ?? 'axe-recherche-generique';
        }

        return array_merge(
            $localizedData,
            [
                'slug' => Str::slug($baseNameForSlug), // Le slug est géré par le modèle, mais on peut en fournir un de base
                'icon_class' => fake()->optional(0.5)->randomElement(['fas fa-flask', 'fas fa-atom', 'fas fa-brain', 'fas fa-microchip', 'fas fa-satellite-dish']),
                'color_hex' => fake()->optional(0.8)->hexColor(),
                'is_active' => fake()->boolean(90), // 90% de chance d'être actif
                'display_order' => fake()->numberBetween(0, 10),
                // cover_image_path est supprimé, géré par Spatie Media Library
            ]
        );
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ResearchAxis $researchAxis) {
            // Assurer l'unicité du slug après la création
            $slug = $researchAxis->slug;
            $originalSlug = $slug;
            $count = 1;
            while (ResearchAxis::where('slug', $slug)->where('id', '!=', $researchAxis->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            if ($slug !== $researchAxis->slug) {
                $researchAxis->slug = $slug;
                $researchAxis->saveQuietly();
            }

            // Optionnel : Ajouter une image de couverture par défaut via Spatie Media Library
            /*
            if (app()->environment() !== 'testing' && !$researchAxis->hasMedia('research_axis_cover_image')) {
                $placeholderDir = storage_path('app/public/seeders/research_axis_covers');
                if (!is_dir($placeholderDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($placeholderDir, 0755, true, true);
                }
                $placeholderImages = \Illuminate\Support\Facades\File::glob($placeholderDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
                if (!empty($placeholderImages)) {
                    $randomImage = $placeholderImages[array_rand($placeholderImages)];
                    try {
                        $researchAxis->addMedia($randomImage)
                                     ->preservingOriginal()
                                     ->toMediaCollection('research_axis_cover_image');
                    } catch (\Exception $e) {
                        // Log::error("Failed to add media to ResearchAxis ID {$researchAxis->id}: " . $e->getMessage());
                    }
                }
            }
            */
        });
    }
}
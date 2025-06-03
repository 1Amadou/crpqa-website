<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $localizedData = [];
        $baseNameForSlug = ''; // Si vous décidez d'ajouter un slug aux partenaires

        // Champs à traduire
        $partnerTypes = ['Institutionnel', 'Entreprise', 'Académique', 'ONG', 'Autre'];

        foreach ($availableLocales as $locale) {
            $currentName = fake()->company() . ' (' . strtoupper($locale) . ')';
            if ($locale === $primaryLocale) {
                $baseNameForSlug = $currentName; // Pour un éventuel slug
            }
            $localizedData['name_' . $locale] = $currentName;
            $localizedData['description_' . $locale] = fake()->optional(0.7)->paragraph(rand(2, 5)); // Description optionnelle
            $localizedData['logo_alt_text_' . $locale] = __('Logo de notre partenaire') . ' ' . $currentName;
        }
        
        // Fallback pour baseNameForSlug si la locale primaire n'a pas généré de nom
        if (empty($baseNameForSlug) && !empty($localizedData['name_' . $primaryLocale])) {
            $baseNameForSlug = $localizedData['name_' . $primaryLocale];
        } elseif (empty($baseNameForSlug) && !empty($localizedData)) {
            $firstNameKey = key(array_filter($localizedData, fn($key) => strpos($key, 'name_') === 0, ARRAY_FILTER_USE_KEY));
            $baseNameForSlug = $localizedData[$firstNameKey] ?? 'partenaire-generique';
        }

        return array_merge(
            $localizedData,
            [
                // 'slug' => Str::slug($baseNameForSlug), // À décommenter si vous ajoutez un slug
                'website_url' => fake()->optional(0.8)->url(),
                'type' => fake()->optional(0.9)->randomElement($partnerTypes),
                'display_order' => fake()->numberBetween(0, 20),
                'is_active' => fake()->boolean(90), // 90% de chance d'être actif
                // logo_path est supprimé, géré par Spatie Media Library
            ]
        );
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Partner $partner) {
            // Si vous ajoutez un slug et que vous voulez assurer son unicité ici :
            /*
            if (property_exists($partner, 'slug')) {
                $slug = $partner->slug;
                $originalSlug = $slug;
                $count = 1;
                while (Partner::where('slug', $slug)->where('id', '!=', $partner->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                if ($slug !== $partner->slug) {
                    $partner->slug = $slug;
                    $partner->saveQuietly();
                }
            }
            */

            // Optionnel : Ajouter un logo par défaut via Spatie Media Library
            /*
            if (app()->environment() !== 'testing' && !$partner->hasMedia('partner_logo')) {
                $placeholderDir = storage_path('app/public/seeders/partner_logos');
                if (!is_dir($placeholderDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($placeholderDir, 0755, true, true);
                }
                $placeholderImages = \Illuminate\Support\Facades\File::glob($placeholderDir . '/*.{jpg,jpeg,png,svg,webp}', GLOB_BRACE);
                if (!empty($placeholderImages)) {
                    $randomImage = $placeholderImages[array_rand($placeholderImages)];
                    try {
                        $partner->addMedia($randomImage)
                                ->preservingOriginal()
                                ->toMediaCollection('partner_logo');
                    } catch (\Exception $e) {
                        // Log::error("Failed to add media to Partner ID {$partner->id}: " . $e->getMessage());
                    }
                }
            }
            */
        });
    }
}
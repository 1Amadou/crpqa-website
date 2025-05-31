<?php

namespace Database\Factories;

use App\Models\News; // Utiliser le modèle News consolidé
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsFactory extends Factory
{
    protected $model = News::class; // Utiliser le modèle News consolidé

    public function definition(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $localizedData = [];
        $baseTitleForSlug = '';

        foreach ($availableLocales as $locale) {
            $title = fake()->unique()->sentence(mt_rand(5, 10)) . ' (' . strtoupper($locale) . ')';
            if ($locale === $primaryLocale) {
                $baseTitleForSlug = $title;
            }
            $localizedData['title_' . $locale] = $title;
            $localizedData['summary_' . $locale] = fake()->paragraph(mt_rand(2, 4));
            $localizedData['content_' . $locale] = '<p>' . implode('</p><p>', fake()->paragraphs(mt_rand(5, 10))) . '</p>';
            $localizedData['meta_title_' . $locale] = Str::limit($title, 60);
            $localizedData['meta_description_' . $locale] = Str::limit(strip_tags($localizedData['summary_' . $locale]), 160);
            $localizedData['cover_image_alt_' . $locale] = __('Image de couverture pour') . ' ' . $title;
        }

        if (empty($baseTitleForSlug) && !empty($localizedData['title_' . $primaryLocale])) {
            $baseTitleForSlug = $localizedData['title_' . $primaryLocale];
        } elseif (empty($baseTitleForSlug) && !empty($localizedData)) {
            // Fallback si la locale primaire n'a pas généré de titre (improbable mais sécurisant)
            $firstTitleKey = key(array_filter($localizedData, fn($key) => strpos($key, 'title_') === 0, ARRAY_FILTER_USE_KEY));
            $baseTitleForSlug = $localizedData[$firstTitleKey] ?? 'actualite-generique';
        }
        
        $userIds = User::pluck('id');
        $categoryIds = NewsCategory::pluck('id');

        $publishedAt = fake()->optional(0.85)->dateTimeThisYear(); // 85% de chance d'être publié cette année

        return array_merge(
            $localizedData,
            [
                'slug' => Str::slug($baseTitleForSlug),
                'news_category_id' => $categoryIds->isNotEmpty() ? fake()->optional(0.9)->randomElement($categoryIds) : null,
                'created_by_user_id' => $userIds->isNotEmpty() ? $userIds->random() : User::factory()->create()->id, // Crée un utilisateur si aucun n'existe
                'published_at' => $publishedAt,
                'is_published' => !is_null($publishedAt) && $publishedAt <= now(), // Publié si la date est passée ou aujourd'hui
                'is_featured' => fake()->boolean(20), // 20% de chance d'être en vedette
            ]
        );
    }

    /**
     * Configurer la factory après la création d'une instance.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (News $newsItem) {
            // Assurer l'unicité du slug après la création au cas où
            $slug = $newsItem->slug;
            $originalSlug = $slug;
            $count = 1;
            while (News::where('slug', $slug)->where('id', '!=', $newsItem->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            if ($slug !== $newsItem->slug) {
                $newsItem->slug = $slug;
                $newsItem->saveQuietly();
            }

            // Optionnel : Ajouter une image de couverture par défaut via Spatie Media Library
            // Cela nécessite d'avoir une image placeholder dans votre storage/app/public/seeders/news_covers
            // et d'avoir fait `php artisan storage:link`
            /*
            if (app()->environment() !== 'testing' && !$newsItem->hasMedia('news_cover_image')) {
                $placeholderDir = storage_path('app/public/seeders/news_covers');
                if (!is_dir($placeholderDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($placeholderDir, 0755, true);
                }
                // Créez quelques images placeholder (ex: placeholder1.jpg, placeholder2.jpg) dans ce dossier
                $placeholderImages = \Illuminate\Support\Facades\File::glob($placeholderDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
                if (!empty($placeholderImages)) {
                    $randomImage = $placeholderImages[array_rand($placeholderImages)];
                    try {
                        $newsItem->addMedia($randomImage)
                                 ->preservingOriginal()
                                 ->toMediaCollection('news_cover_image');
                    } catch (\Exception $e) {
                        // Gérer l'erreur si l'image ne peut être ajoutée
                        // Log::error("Failed to add media to News item ID {$newsItem->id}: " . $e->getMessage());
                    }
                }
            }
            */
        });
    }
}
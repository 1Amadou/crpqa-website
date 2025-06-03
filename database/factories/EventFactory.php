<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Partner; // Pour lier des partenaires
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $localizedData = [];
        $baseTitleForSlug = '';

        // Champs à traduire
        $fieldsToLocalize = [
            'title', 'description', 'location', 
            'meta_title', 'meta_description', 
            'target_audience', 'cover_image_alt'
        ];

        foreach ($availableLocales as $locale) {
            $currentTitle = fake()->unique()->sentence(mt_rand(4, 8)) . ' (' . strtoupper($locale) . ')';
            if ($locale === $primaryLocale) {
                $baseTitleForSlug = $currentTitle;
            }
            $localizedData['title_' . $locale] = $currentTitle;
            $localizedData['description_' . $locale] = '<p>' . implode('</p><p>', fake()->paragraphs(mt_rand(3, 7))) . '</p>';
            $localizedData['location_' . $locale] = fake()->optional(0.7)->city() . ', ' . fake()->country() . ' (' . strtoupper($locale) . ')';
            $localizedData['meta_title_' . $locale] = Str::limit($currentTitle, 55);
            $localizedData['meta_description_' . $locale] = Str::limit(strip_tags($localizedData['description_' . $locale]), 155);
            $localizedData['target_audience_' . $locale] = fake()->optional(0.8)->sentence(mt_rand(5, 15));
            $localizedData['cover_image_alt_' . $locale] = __('Image de couverture pour l\'événement') . ': ' . $currentTitle;
        }
        
        if (empty($baseTitleForSlug) && !empty($localizedData['title_' . $primaryLocale])) {
            $baseTitleForSlug = $localizedData['title_' . $primaryLocale];
        } elseif (empty($baseTitleForSlug) && !empty($localizedData)) {
            $firstTitleKey = key(array_filter($localizedData, fn($key) => strpos($key, 'title_') === 0, ARRAY_FILTER_USE_KEY));
            $baseTitleForSlug = $localizedData[$firstTitleKey] ?? 'evenement-generique';
        }

        $userIds = User::pluck('id');
        $startDateTime = fake()->dateTimeBetween('+1 week', '+3 months');
        $endDateTime = fake()->optional(0.7)->dateTimeBetween($startDateTime, (clone $startDateTime)->modify('+5 days'));

        return array_merge(
            $localizedData,
            [
                'slug' => Str::slug($baseTitleForSlug),
                'start_datetime' => $startDateTime,
                'end_datetime' => $endDateTime,
                'registration_url' => fake()->optional(0.6)->url(),
                'is_featured' => fake()->boolean(15),
                'created_by_user_id' => $userIds->isNotEmpty() ? $userIds->random() : User::factory()->create()->id,
            ]
        );
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Event $event) {
            // Assurer l'unicité du slug
            $slug = $event->slug;
            $originalSlug = $slug;
            $count = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            if ($slug !== $event->slug) {
                $event->slug = $slug;
                $event->saveQuietly();
            }

            // Attacher des partenaires si disponibles
            if (Partner::count() > 0 && fake()->boolean(70)) { // 70% de chance d'avoir des partenaires
                $numberOfPartners = fake()->numberBetween(1, min(3, Partner::count()));
                $partners = Partner::inRandomOrder()->limit($numberOfPartners)->pluck('id');
                if ($partners->isNotEmpty()) {
                    $event->partners()->sync($partners->all());
                }
            }

            // Optionnel : Ajouter une image de couverture par défaut via Spatie Media Library
            /*
            if (app()->environment() !== 'testing' && !$event->hasMedia('event_cover_image')) {
                $placeholderDir = storage_path('app/public/seeders/event_covers');
                if (!is_dir($placeholderDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($placeholderDir, 0755, true, true);
                }
                // Créez des images placeholder (ex: placeholder1.jpg) dans ce dossier
                $placeholderImages = \Illuminate\Support\Facades\File::glob($placeholderDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
                if (!empty($placeholderImages)) {
                    $randomImage = $placeholderImages[array_rand($placeholderImages)];
                    try {
                        $event->addMedia($randomImage)
                              ->preservingOriginal()
                              ->toMediaCollection('event_cover_image');
                    } catch (\Exception $e) {
                        // Log::error("Failed to add media to Event ID {$event->id}: " . $e->getMessage());
                    }
                }
            }
            */
        });
    }
}
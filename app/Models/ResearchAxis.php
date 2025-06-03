<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasLocalizedFields;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ResearchAxis extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'icon_class', // Conservé
        'color_hex',  // NOUVEAU champ non traduisible
        'is_active',
        'display_order',
        // 'name', // Remplacé
        // 'subtitle', // Remplacé
        // 'description', // Remplacé
        // 'cover_image_path', // Géré par Spatie Media Library
        // 'meta_title', // Remplacé
        // 'meta_description', // Remplacé

        // Champs traduits (à ajouter après la migration pour correspondre aux colonnes DB)
        'name_fr', 'name_en',
        'subtitle_fr', 'subtitle_en',
        'description_fr', 'description_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'icon_svg_fr', 'icon_svg_en', // icon_svg comme champ texte traduisible
        'cover_image_alt_text_fr', 'cover_image_alt_text_en',
    ];

    public array $localizedFields = [
        'name',
        'subtitle',
        'description',
        'meta_title',
        'meta_description',
        'icon_svg',             // icon_svg comme champ texte traduisible
        'cover_image_alt_text'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($researchAxis) {
            $defaultLocale = config('app.locale', 'fr');
            $nameForSlug = $researchAxis->getTranslation('name', $defaultLocale, false);

            if (empty($researchAxis->slug) ||
                ($researchAxis->isDirty('name_'.$defaultLocale) && !$researchAxis->isDirty('slug'))) {

                if (!empty($nameForSlug)) {
                    $slug = Str::slug($nameForSlug);
                    $originalSlug = $slug;
                    $count = 1;
                    while (static::where('slug', $slug)->where('id', '!=', $researchAxis->id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $researchAxis->slug = $slug;
                } elseif (empty($researchAxis->slug)) {
                    $researchAxis->slug = Str::slug('axe-de-recherche-' . time() . Str::random(3));
                }
            } elseif (!empty($researchAxis->slug) && $researchAxis->isDirty('slug')) {
                 $researchAxis->slug = Str::slug($researchAxis->slug);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('research_axis_cover_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/research_axis_default.jpg'))
            ->useFallbackPath(public_path('assets/images/placeholders/research_axis_default.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if ($media && $media->collection_name === 'research_axis_cover_image') {
            $this->addMediaConversion('thumbnail')
                  ->width(400)
                  ->height(250)
                  ->sharpen(10);

            $this->addMediaConversion('banner')
                  ->width(1200)
                  ->height(400);
        }
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('research_axis_cover_image');
    }

    public function getCoverImageThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('research_axis_cover_image', 'thumbnail');
    }
}
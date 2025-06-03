<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasLocalizedFields;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class Researcher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasLocalizedFields;

    protected $fillable = [
        'slug',
        'email',
        'phone',
        'website_url',
        'linkedin_url',
        'researchgate_url',
        'google_scholar_url',
        'orcid_id',
        'is_active',
        'user_id',
        'display_order',

        // Champs traduits (basés sur create_researchers_table et la nouvelle migration pour photo_alt_text)
        'first_name_fr', 'first_name_en',
        'last_name_fr', 'last_name_en',
        'title_position_fr', 'title_position_en',
        'biography_fr', 'biography_en',
        'research_interests_fr', 'research_interests_en',
        'photo_alt_text_fr', 'photo_alt_text_en', // AJOUTÉ
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public array $localizedFields = [
        'first_name',
        'last_name',
        'title_position',
        'biography',
        'research_interests',
        'photo_alt_text', // AJOUTÉ
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($researcher) {
            $defaultLocale = config('app.locale', 'fr');
            $firstNameForSlug = $researcher->getTranslation('first_name', $defaultLocale, false);
            $lastNameForSlug = $researcher->getTranslation('last_name', $defaultLocale, false);
            $nameForSlug = trim($firstNameForSlug . ' ' . $lastNameForSlug);

            if (empty($researcher->slug) ||
                (($researcher->isDirty('first_name_'.$defaultLocale) || $researcher->isDirty('last_name_'.$defaultLocale)) && !$researcher->isDirty('slug'))) {

                if (!empty($nameForSlug)) {
                    $slug = Str::slug($nameForSlug);
                    $originalSlug = $slug;
                    $count = 1;
                    while (static::where('slug', $slug)->where('id', '!=', $researcher->id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $researcher->slug = $slug;
                } elseif (empty($researcher->slug)) {
                    $researcher->slug = Str::slug('chercheur-' . time() . Str::random(3));
                }
            } else if (!empty($researcher->slug) && $researcher->isDirty('slug')) {
                $researcher->slug = Str::slug($researcher->slug);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('researcher_photo')
             ->singleFile()
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
             ->useFallbackUrl(asset('assets/images/placeholders/researcher_default.png')) // Adaptez
             ->useFallbackPath(public_path('assets/images/placeholders/researcher_default.png')); // Adaptez
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
              ->width(200)
              ->height(200)
              ->crop('crop-center', 200, 200)
              ->sharpen(10);

        $this->addMediaConversion('profile')
              ->width(400)
              ->height(400)
              ->crop('crop-center', 400, 400);
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_researcher', 'researcher_id', 'publication_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('researcher_photo');
    }

    public function getPhotoThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('researcher_photo', 'thumbnail');
    }

     public function getPhotoProfileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('researcher_photo', 'profile');
    }
}
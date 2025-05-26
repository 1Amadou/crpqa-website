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

class Researcher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasLocalizedFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'title_position', // Changed from 'title' and 'position'
        'email',
        'phone', // Changed from 'phone_number'
        'biography',
        'research_interests', // Changed from 'research_areas'
        'website_url',
        'linkedin_url',
        'researchgate_url',
        'google_scholar_url',
        'orcid_id',
        'is_active',
        'user_id',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that are localized.
     *
     * @var array<string>
     */
    protected $localizedFields = [
        'first_name',
        'last_name',
        'title_position',
        'biography',
        'research_interests',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
             ->singleFile()
             ->acceptsImage('image/jpeg', 'image/png', 'image/webp');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(368)
              ->height(232)
              ->sharpen(10);

        $this->addMediaConversion('card')
              ->width(600)
              ->height(400);
    }

    /**
     * The publications that belong to the researcher.
     */
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_researcher');
    }

    /**
     * Get the user account associated with the researcher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the researcher's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
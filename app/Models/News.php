<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon;

class News extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'news_category_id',
        // 'user_id', // ANCIENNE LIGNE À CHANGER/SUPPRIMER
        'created_by_user_id', // <--- CORRECTION ICI
        'published_at',
        'is_published',
        'is_featured',

        'title_fr', 'title_en',
        'summary_fr', 'summary_en',
        'content_fr', 'content_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'cover_image_alt_fr', 'cover_image_alt_en',
    ];

    public array $localizedFields = [
        'title',
        'summary',
        'content',
        'meta_title',
        'meta_description',
        'cover_image_alt',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function user(): BelongsTo // Représente l'auteur (created_by)
    {
        // Spécifier la clé étrangère correcte si elle n'est pas 'user_id'
        return $this->belongsTo(User::class, 'created_by_user_id'); // <--- CORRECTION ICI
    }

    // Configuration pour Spatie Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('news_cover_image') // Nom de la collection
            ->singleFile() // Une seule image de couverture par actualité
            ->useFallbackUrl(asset('images/default_news_placeholder.jpg'))
            ->useFallbackPath(public_path('images/default_news_placeholder.jpg'));
    }

    // Optionnel : Définir des conversions d'images (thumbnails, etc.)
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
              ->width(400)
              ->height(250) // Ajustez les dimensions selon vos besoins
              ->sharpen(10)
              ->nonQueued(); // ou ->queued() si vous utilisez une file d'attente pour les conversions
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Retiré : getCoverImageUrlAttribute (géré par getFirstMediaUrl de Spatie)
    // Retiré : getLocalizedField (géré par le trait HasLocalizedFields)
}
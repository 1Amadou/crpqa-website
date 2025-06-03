<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLocalizedFields;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Pour le type hint

class StaticPage extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'is_published',
        'user_id', // Sera 'created_by_user_id' si on standardise comme les autres modèles

        // Champs traduits (les colonnes réelles de la base de données)
        'title_fr', 'title_en',
        'content_fr', 'content_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'cover_image_alt_text_fr', 'cover_image_alt_text_en', // Pour le texte alternatif de l'image
    ];

    public array $localizedFields = [
        'title',
        'content',
        'meta_title',
        'meta_description',
        'cover_image_alt_text' // Texte alternatif pour l'image de couverture
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Récupère l'utilisateur qui a créé/modifié la page statique.
     * Renommer en createdBy pour la cohérence si user_id représente le créateur.
     */
    public function user(): BelongsTo // Ou createdBy()
    {
        // Si user_id est bien la FK pour l'utilisateur qui a créé/modifié
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('static_page_cover')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/page_cover_default.jpg')) // Adaptez le chemin
            ->useFallbackPath(public_path('assets/images/placeholders/page_cover_default.jpg')); // Adaptez le chemin
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if ($media && $media->collection_name === 'static_page_cover') {
            $this->addMediaConversion('thumbnail')
                  ->width(400)
                  ->height(200) // ou un autre ratio pour les bannières de page
                  ->sharpen(10);

            $this->addMediaConversion('banner')
                  ->width(1200)
                  ->height(400); // Adaptez selon vos besoins
        }
    }

    // Accesseurs pour l'image de couverture
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('static_page_cover');
    }

    public function getCoverImageThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('static_page_cover', 'thumbnail');
    }
    
    // Permettre le binding par slug dans les routes
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
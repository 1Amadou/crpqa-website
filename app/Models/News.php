<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; 


class News extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'news';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'news_category_id',
        'created_by_user_id',
        'published_at',
        'is_published',
        'is_featured', // Assurez-vous que cette colonne existe via une migration

        // Champs traduits (le trait s'attend aux noms de base dans $localizedFields)
        // Les champs réels dans $fillable doivent être les colonnes de la DB
        'title_fr', 'title_en',
        'summary_fr', 'summary_en',         // Renommé depuis 'excerpt' pour cohérence avec votre modèle News.php initial
        'content_fr', 'content_en',
        'meta_title_fr', 'meta_title_en', // Champs SEO
        'meta_description_fr', 'meta_description_en', // Champs SEO
        'cover_image_alt_fr', 'cover_image_alt_en', // Texte alternatif pour l'image de couverture
    ];

    /**
     * Les champs qui doivent être traduits.
     * Le trait HasLocalizedFields utilisera ces noms de base.
     *
     * @var array<int, string>
     */
    public array $localizedFields = [
        'title',
        'summary',          // Renommé depuis 'excerpt'
        'content',
        'meta_title',
        'meta_description',
        'cover_image_alt',  
        'name',
    ];

    /**
     * Les attributs qui doivent être castés vers des types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean', 
        'news_category_id' => 'integer',
        'is_active' => 'boolean',
    ];

   /**
     * Get the category that owns the news item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    
    public function createdBy(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
    
    // OPTIONNEL: Si vous voulez toujours pouvoir utiliser $news->user (moins sémantique)
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'created_by_user_id');
    // }

    /**
     * Enregistre les collections de médias pour ce modèle.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('news_cover_image')
            ->singleFile() // Une seule image de couverture par actualité
            ->useFallbackUrl(asset('assets/images/placeholders/news_default.jpg')) // Chemin vers votre placeholder
            ->useFallbackPath(public_path('assets/images/placeholders/news_default.jpg')); // Chemin physique vers votre placeholder
    }

    /**
     * Enregistre les conversions de médias.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
              ->width(400)
              ->height(250) // Vous pouvez ajuster ou ajouter crop, fit, etc.
              ->sharpen(10)
              ->nonQueued(); // Exécute la conversion immédiatement

        $this->addMediaConversion('card')
              ->width(600)
              ->height(400)
              ->sharpen(10)
              ->nonQueued();
    }

    /**
     * Récupère la clé de route pour le modèle.
     * Permet d'utiliser le slug dans les URLs au lieu de l'ID.
     */
    public function newsItems(): HasMany // Ou news()
    {
        return $this->hasMany(News::class, 'news_category_id', 'id'); // Utiliser le modèle News consolidé
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Accesseur pour obtenir l'URL de l'image de couverture.
     * Permet d'utiliser $news->cover_image_url dans les vues.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        // Le deuxième argument est le nom de la conversion (ex: 'thumbnail') si vous en voulez une spécifique
        return $this->getFirstMediaUrl('news_cover_image'); 
    }

    /**
     * Accesseur pour obtenir l'URL de la miniature de l'image de couverture.
     */
    public function getCoverImageThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('news_cover_image', 'thumbnail');
    }
    
    /**
     * Accesseur pour obtenir l'objet Media de l'image de couverture.
     */
    public function getCoverImageMediaAttribute() // : ?Media
    {
        return $this->getFirstMedia('news_cover_image');
    }

    /**
     * Accesseur pour obtenir le nom de la catégorie (traduit).
     */
    public function getCategoryNameAttribute(): ?string
    {
        return $this->category ? $this->category->name : null; // Le trait HasLocalizedFields sur NewsCategory gérera la traduction de name
    }
}
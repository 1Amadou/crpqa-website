<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations; // <-- AJOUTE CETTE LIGNE
use Carbon\Carbon; // Pour les mutateurs de date si besoin
use Illuminate\Support\Facades\Storage; // Pour le stockage des fichiers

class News extends Model
{
    use HasFactory, HasTranslations; // <-- AJOUTE HasTranslations ICI

    protected $fillable = [
        'title',
        'slug',
        'short_content', // Ajoute ce champ pour la traduction
        'content',       // Ajoute ce champ pour la traduction
        'meta_title',    // Ajoute ce champ pour la traduction
        'meta_description', // Ajoute ce champ pour la traduction
        'summary',       // Souvent utilisé pour la méta-description ou un aperçu, donc traduisible
        'cover_image_url', // Si tu stockes l'URL complète, sinon juste le path
        'cover_image_alt',
        'news_category_id',
        'user_id',
        'published_at',
        'is_published',
        'is_featured',
        'gallery_images_json', // Si tu as une galerie JSON traduisible (captions)
    ];

    // Déclare ici tous les attributs que tu veux rendre traduisibles
    public array $translatable = [
        'title',
        'short_content',
        'content',
        'meta_title',
        'meta_description',
        'summary',
        // 'gallery_images_json', // Si tu veux traduire les captions ou alts DANS le JSON.
                               // Si le JSON contient déjà des champs localisés comme {'fr': 'caption_fr', 'en': 'caption_en'}, alors pas besoin ici.
                               // Si c'est un tableau simple d'objets, la logique de getLocalizedField() dans la vue est suffisante.
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'gallery_images_json' => 'array', // Pour que Laravel caste ce champ en tableau automatiquement
    ];

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accesseur pour l'URL de l'image de couverture
    public function getCoverImageUrlAttribute(?string $value): ?string
    {
        // Si tu utilises Spatie Media Library, tu devrais probablement avoir une méthode du genre
        // return $this->getFirstMediaUrl('news_covers');

        // Si tu gères les chemins manuellement avec Storage::disk('public')
        if ($this->cover_image_path) {
            return Storage::url($this->cover_image_path);
        }
        return $value; // Retourne la valeur existante si elle est déjà une URL complète
    }

    // Méthode helper pour récupérer les champs traduits
    public function getLocalizedField(string $fieldName)
    {
        // Vérifie si l'attribut est traduisible avant d'appeler getTranslation()
        if (in_array($fieldName, $this->translatable)) {
            return $this->getTranslation($fieldName, app()->getLocale());
        }
        // Sinon, retourne simplement l'attribut comme d'habitude
        return $this->$fieldName;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Assure-toi que la méthode 'isFuture()' est disponible si tu l'utilises,
    // elle est normalement déjà sur les objets Carbon.
}

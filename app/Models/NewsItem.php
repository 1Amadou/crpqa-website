<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasLocalizedFields; // IMPORTATION DU TRAIT

class NewsItem extends Model
{
    use HasFactory, HasLocalizedFields; // UTILISATION DES TRAITS

    protected $table = 'news'; // Correct

    // Champs assignables en masse
    protected $fillable = [
        'title_fr', 'title_en', 'slug', 'content_fr', 'content_en',
        'excerpt_fr', 'excerpt_en', 'cover_image_path', 
        'cover_image_alt_text_fr', 'cover_image_alt_text_en',
        'published_at', 'is_published', 
        'seo_title_fr', 'seo_title_en',
        'seo_description_fr', 'seo_description_en', 
        'created_by_user_id',
        'news_category_id', // Important pour la relation de catégorie
    ];

    // Conversion de types pour les attributs Eloquent
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'news_category_id' => 'integer',
    ];

    // Noms de base des champs qui sont localisés (le trait cherchera _fr, _en, etc.)
    protected $localizedFields = [
        'title', 
        'content', 
        'excerpt', 
        'cover_image_alt_text', 
        'seo_title', 
        'seo_description'
    ];

    /**
     * La catégorie à laquelle cette actualité appartient.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->category ? $this->category->getTranslation('name') : null;
    }
    /**
     * L'utilisateur qui a créé cette actualité.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Accesseur pour obtenir l'URL complète de l'image de couverture.
     * Permet d'utiliser $newsItem->cover_image_url dans les vues.
     */
    public function getCoverImageUrlAttribute(): ?string 
    {
        if ($this->cover_image_path) {
            // Assurez-vous que votre disque 'public' est bien configuré dans filesystems.php
            // pour que Storage::url() génère le bon chemin.
            return Storage::disk('public')->url($this->cover_image_path); 
        }
        return null; // Ou un placeholder URL si vous préférez
    }
}
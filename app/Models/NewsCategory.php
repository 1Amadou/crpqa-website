<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasLocalizedFields;


class NewsCategory extends Model
{
    use HasFactory, HasLocalizedFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     * Permet de s'assurer que certains champs sont convertis dans le bon type.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the news items for the category.
     * Définit la relation "a plusieurs" avec le modèle News.
     */
    public function newsItems(): HasMany // Le nom 'newsItems' est bien pour la clarté
    {
        // 'news_category_id' est la clé étrangère dans la table 'news' (NewsItem)
        // 'id' est la clé primaire dans la table 'news_categories' (NewsCategory)
        return $this->hasMany(NewsItem::class, 'news_category_id', 'id');
    }
}

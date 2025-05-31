<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // 'name' est directement fillable
        'slug',
        'is_active',
    ];

    // Définir les champs de base qui sont traduits
    public array $localizedFields = ['name']; // <--- AJOUT IMPORTANT

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function newsItems(): HasMany
    {
        return $this->hasMany(News::class, 'news_category_id', 'id'); // Utiliser News::class
    }

    // getRouteKeyName pour utiliser le slug dans les routes est une bonne pratique
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
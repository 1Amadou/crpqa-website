<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Ne pas importer App\Traits\HasLocalizedFields si 'name' n'est pas le seul champ à traduire et qu'il ne l'est pas
// Si d'autres champs étaient traduits (ex: description de la catégorie), alors on le garderait.

class NewsCategory extends Model
{
    use HasFactory; // Ne pas utiliser HasLocalizedFields si 'name' n'est pas traduit

    protected $fillable = [
        'name', // Champ 'name' simple, non traduit
        'slug',
        'is_active',
        'color',      // Si vous avez ce champ
        'text_color', // Si vous avez ce champ
    ];

    // Supprimer la propriété $localizedFields si aucun champ n'est traduit
    // public array $localizedFields = []; 

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function newsItems(): HasMany
    {
        // Utiliser le nom de modèle News.php consolidé
        return $this->hasMany(News::class, 'news_category_id', 'id');
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
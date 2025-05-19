<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class StaticPage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'user_id', // Clé étrangère pour lier au dernier éditeur (modèle User)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean', // S'assure que 'is_published' est bien un booléen
    ];

    /**
     * Get the user (last editor) that owns the static page.
     * Définit la relation "appartient à" avec le modèle User.
     */
    public function user(): BelongsTo
    {
        // Chaque page statique a un dernier éditeur.
        return $this->belongsTo(User::class);
    }
}
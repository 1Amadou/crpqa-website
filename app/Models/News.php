<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class News extends Model
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
        'meta_title',       
        'meta_description', 
        'summary',
        'content',
        'cover_image_path',
        'published_at',
        'user_id',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     * Permet de s'assurer que certains champs sont convertis dans le bon type.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime', 
        'is_featured' => 'boolean',   
    ];

    /**
     * Get the user (author) that owns the news item.
     * Définit la relation "appartient à" avec le modèle User.
     */
    public function user(): BelongsTo
    {
        
        return $this->belongsTo(User::class);
    }
}
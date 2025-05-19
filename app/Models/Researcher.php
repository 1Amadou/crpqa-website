<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Ajoutez cette ligne

class Researcher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'title',
        'position',
        'email',
        'phone_number',
        'biography',
        'photo_path',
        'research_areas',
        'linkedin_url',
        'google_scholar_url',
        'is_active',
        'display_order',
        'user_id', // Ajout de user_id ici
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        // 'research_areas' => 'array', // Décommentez si vous stockez research_areas en JSON
    ];

    /**
     * The publications that belong to the researcher.
     */
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_researcher');
    }

    /**
     * Get the user account associated with the researcher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the researcher's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
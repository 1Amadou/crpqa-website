<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Ajoutez cette ligne

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'authors_internal_notes',
        'authors_external',
        'abstract',
        'publication_date',
        'type',
        'journal_name',
        'conference_name',
        'volume',
        'issue',
        'pages',
        'doi_url',
        'pdf_path',
        'external_url',
        'is_featured',
        'created_by_user_id', // << NOUVEAU CHAMP AJOUTÉ ICI
    ];

    // Casts pour les dates ou booléens si nécessaire
    protected $casts = [
        'publication_date' => 'date',
        'is_featured' => 'boolean',
    ];

    public function researchers(): BelongsToMany
    {
        return $this->belongsToMany(Researcher::class, 'publication_researcher');
    }

    /**
     * Get the user who created the publication entry in the system.
     */
    public function creator(): BelongsTo // Ou createdBy(), user(), etc.
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        // 'authors_internal_notes', // Commentez ou supprimez si non utilisé
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
        'created_by_user_id',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'is_featured' => 'boolean',
    ];

    // ----- AJOUTEZ CETTE MÉTHODE STATIQUE -----
    public static function getPublicationTypes(): array
    {
        return [
            'journal_article' => __('Article de Journal'),
            'conference_paper' => __('Papier de Conférence'),
            'book' => __('Livre'),
            'book_chapter' => __('Chapitre de Livre'),
            'thesis' => __('Thèse'),
            'report' => __('Rapport'),
            'preprint' => __('Prépublication (ex: arXiv)'),
            'patent' => __('Brevet'),
            'other' => __('Autre'),
        ];
    }
    // ------------------------------------------

    public function researchers(): BelongsToMany
    {
        return $this->belongsToMany(Researcher::class, 'publication_researcher');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
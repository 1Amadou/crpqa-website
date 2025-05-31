<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Publication extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasLocalizedFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'authors_internal_notes', // Provenant de create_publications_table
        'authors_external',       // Provenant de create_publications_table
        'publication_date',
        'type',                   // Provenant de create_publications_table
        'journal_name',           // Provenant de create_publications_table
        'conference_name',        // Provenant de create_publications_table
        'volume',                 // Provenant de create_publications_table
        'issue',                  // Provenant de create_publications_table
        'pages',                  // Provenant de create_publications_table
        'doi_url',                // Provenant de create_publications_table
        'external_url',           // Provenant de create_publications_table
        // 'pdf_path' n'est plus nécessaire ici car géré par Spatie Media Library
        'is_featured',            // Provenant de create_publications_table
        // 'is_published', // À vérifier : cette colonne existe-t-elle dans votre table 'publications' ?
                            // Non présente dans create_publications_table.php. Si ajoutée ailleurs, c'est ok.
        'created_by_user_id',     // Ajouté par une migration ultérieure

        // Champs traduits (les anciennes colonnes 'title' et 'abstract' ne doivent plus être ici)
        'title_fr',
        'title_en',
        'abstract_fr',
        'abstract_en',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'is_featured' => 'boolean',
        // 'is_published' => 'boolean', // Idem, à caster seulement si la colonne existe.
        // 'authors_external' => 'array', // Seulement si vous stockez du JSON valide dans ce champ `text`.
                                        // Sinon, pas besoin de caster.
    ];

    /**
     * Define the localized fields.
     * These are the base names of the fields that have translations.
     * The trait will expect columns like 'title_fr', 'title_en', etc.
     *
     * @var array<int, string>
     */
    protected array $localizedFields = ['title', 'abstract']; // PARFAIT

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('publication_pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        // Optionnel: Si vous voulez une image de couverture pour les publications
        // $this->addMediaCollection('publication_cover_image')
        //     ->singleFile(); // ou ->useDisk('public')->acceptsMimeTypes(['image/jpeg', 'image/png']) etc.
    }

    /**
     * Get the user who created the publication.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * The researchers that are authors of this publication.
     */
    public function researchers(): BelongsToMany
    {
        return $this->belongsToMany(Researcher::class, 'publication_researcher', 'publication_id', 'researcher_id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ACCESSEURS POUR LES MÉDIAS (OPTIONNEL MAIS PRATIQUE)

    /**
     * Get the URL of the publication PDF.
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('publication_pdf');
    }

    /**
     * Get the PDF media object.
     */
    public function getPdfMediaAttribute() // : ?Media // PHP 7.4+ pour le type hint
    {
        return $this->getFirstMedia('publication_pdf');
    }

    // Si vous ajoutez une image de couverture:
    // public function getCoverImageUrlAttribute(): ?string
    // {
    //     // Si vous voulez une URL avec une conversion (ex: une miniature)
    //     // return $this->getFirstMediaUrl('publication_cover_image', 'thumb');
    //     return $this->getFirstMediaUrl('publication_cover_image');
    // }
}
<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
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
        'authors_internal_notes',
        'authors_external',
        'publication_date',
        'type',
        'journal_name',
        'conference_name',
        'volume',
        'issue',
        'pages',
        'doi_url',
        'external_url',
        'is_featured',
        'created_by_user_id',
        'title_fr',
        'title_en',
        'abstract_fr',
        'abstract_en',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'is_featured'      => 'boolean',
    ];

    /**
     * Define the localized fields.
     *
     * These are the base names of the fields that have translations.
     *
     * @var array<int, string>
     */
    protected array $localizedFields = [
        'title',
        'abstract',
    ];

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('publication_pdf')
             ->singleFile()
             ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * Get the user who created the publication.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the researchers that are authors of this publication.
     */
    public function researchers(): BelongsToMany
    {
        return $this->belongsToMany(
            Researcher::class,
            'publication_researcher',
            'publication_id',
            'researcher_id'
        );
    }

    /**
     * Determine which field is used for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the URL of the publication PDF.
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('publication_pdf');
    }

    /**
     * Get the publication PDF media object.
     */
    public function getPdfMediaAttribute()
    {
        return $this->getFirstMedia('publication_pdf');
    }

    /**
     * Return a list of publication types and their display labels.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'research_article'   => 'Research Article',
            'conference_paper'   => 'Conference Paper',
            'book_chapter'       => 'Book Chapter',
            'thesis'             => 'Thesis',
            'report'             => 'Report',
        ];
    }

    /**
     * Accessor to get a human-readable label for "type".
     */
    public function getTypeDisplayAttribute(): string
    {
        $types = self::types();
        return $types[$this->type] ?? Str::title(str_replace('_', ' ', $this->type));
    }

    /**
     * Automatically generate slug from title_en if not provided.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Publication $publication) {
            if (empty($publication->slug) && !empty($publication->title_en)) {
                $publication->slug = Str::slug($publication->title_en);
            }
        });
    }
}

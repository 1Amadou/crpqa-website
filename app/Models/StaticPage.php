<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLocalizedFields; // Vous utilisez ce trait
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class StaticPage extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'is_published',
        'user_id',
        // Champs localisés avec suffixe de langue si remplis directement
        'title_fr', 'title_en', // Exemple
        'content_fr', 'content_en', // Exemple
        'meta_title_fr', 'meta_title_en', // Exemple
        'meta_description_fr', 'meta_description_en', // Exemple
        // Ou vous pouvez omettre les versions linguistiques ici si elles sont définies via des méthodes dédiées
    ];

    /**
     * Déclare les champs de base qui sont localisés.
     * Le trait HasLocalizedFields utilisera cette liste.
     * DOIT s'appeler $localizedFields pour correspondre au Trait.
     * @var array<int, string>
     */
    public array $localizedFields = [ // <<< RENOMMÉ DE $translatable
        'title',
        'content',
        'meta_title',
        'meta_description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get the user (last editor) that owns the static page.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('static_page_cover') // Un nom de collection plus spécifique
            ->singleFile()
            ->useFallbackUrl(asset('images/default_page_cover.jpg')) 
        ->useFallbackPath(public_path('images/default_page_cover.jpg')); 
    }
}
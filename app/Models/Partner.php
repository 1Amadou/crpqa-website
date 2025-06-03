<?php

namespace App\Models;

use App\Traits\HasLocalizedFields; // Ajout du trait pour la localisation
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;         // Ajout pour Spatie Media Library
use Spatie\MediaLibrary\InteractsWithMedia; // Ajout pour Spatie Media Library
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Pour le type hint

class Partner extends Model implements HasMedia // Implémenter HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia; // Utilisation des traits

    protected $fillable = [
        'website_url',
        'type',
        'display_order',
        'is_active',
        // Champs traduits (après migration)
        'name_fr', 'name_en',
        'description_fr', 'description_en',
        'logo_alt_text_fr', 'logo_alt_text_en',
        // Ajoutez d'autres locales si nécessaire
    ];

    /**
     * Les champs qui doivent être traduits.
     * Le trait HasLocalizedFields utilisera ces noms de base.
     *
     * @var array<int, string>
     */
    public array $localizedFields = [
        'name',
        'description',
        'logo_alt_text' // Texte alternatif pour le logo
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Les événements associés à ce partenaire.
     * Renommé depuis associatedEvents pour suivre la convention.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_partner', 'partner_id', 'event_id')
                    ->withTimestamps();
    }

    /**
     * Enregistre les collections de médias pour ce modèle.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('partner_logo')
            ->singleFile() // Un seul logo par partenaire
            // ->useFallbackUrl(asset('assets/images/placeholders/partner_default.png')) // Adaptez le chemin
            // ->useFallbackPath(public_path('assets/images/placeholders/partner_default.png')); // Adaptez le chemin
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']);
    }

    /**
     * Enregistre les conversions de médias.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
              ->width(200) // Ou la taille souhaitée pour les miniatures de logo
              ->height(100) // Maintenir le ratio ou utiliser fit/crop
              ->nonQueued();
    }
    
    /**
     * Accesseur pour obtenir l'URL du logo.
     * Permet d'utiliser $partner->logo_url dans les vues.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('partner_logo');
    }

    /**
     * Accesseur pour obtenir l'URL de la miniature du logo.
     */
    public function getLogoThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('partner_logo', 'thumbnail');
    }

    // Si vous générez les slugs automatiquement pour les partenaires :
    // public function getRouteKeyName()
    // {
    //     return 'slug'; // Assurez-vous d'avoir une colonne slug et la logique de génération
    // }

    // La logique de génération de slug devrait être dans un boot() ou un Observer si besoin,
    // basée sur name_fr ou name_en.
}
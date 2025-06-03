<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Pour le type hint
use Illuminate\Support\Str;

class Event extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'start_datetime',
        'end_datetime',
        'registration_url',
        'is_featured',
        'created_by_user_id',
        // Les champs suivants seront remplacés par leurs versions localisées
        // 'title', 'description', 'location', 'meta_title', 'meta_description', 'target_audience'
        // Et 'cover_image_path' sera géré par Spatie Media Library

        // Champs traduits (à ajouter dans $fillable une fois les colonnes créées)
        // Exemple : 'title_fr', 'title_en', 'description_fr', 'description_en', etc.
        // Pour l'instant, on les omet de $fillable car les colonnes n'existent pas encore.
        // Ils seront gérés par le trait HasLocalizedFields pour l'accès.
        // Le contrôleur devra assigner les valeurs traduites directement (ex: $event->title_fr = ...).
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_featured' => 'boolean',
    ];

    // Noms de base des champs qui seront traduits
    public array $localizedFields = [
        'title',
        'description',
        'location',
        'meta_title',
        'meta_description',
        'target_audience',
        'cover_image_alt' // Pour le texte alternatif de l'image de couverture
    ];

    // S'assurer que les champs suffixés sont bien dans $fillable une fois la migration passée
    // Pour cela, il est souvent plus simple de ne pas les lister explicitement
    // si vous gérez l'assignation manuellement dans le contrôleur ou via des méthodes sur le modèle.
    // Ou, après la migration, vous ajouterez 'title_fr', 'title_en', etc. ici.

    /**
     * Génère le slug automatiquement à partir du titre (langue par défaut) si non fourni.
     * Cette méthode est appelée par Eloquent lors de la définition d'un attribut.
     * Avec HasLocalizedFields, le slug sera généré lors du premier `save` si vide.
     * Il est préférable de gérer la génération du slug dans un Observer (ex: creating/saving)
     * ou dans le contrôleur pour plus de contrôle avec les champs localisés.
     *
     * Pour l'instant, nous retirons le mutateur setSlugAttribute pour éviter les conflits
     * et nous gérerons le slug dans le contrôleur ou un observer.
     */
    // public function setTitleAttribute($value)
    // {
    // // Le trait HasLocalizedFields gère maintenant cela.
    // // La logique de slug sera déplacée.
    // }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($event) {
            // Générer le slug à partir du titre de la langue par défaut si le slug est vide
            // ou si le titre de la langue par défaut a changé et que le slug n'a pas été explicitement modifié.
            $defaultLocale = config('app.locale', 'fr');
            $titleForSlug = $event->getTranslation('title', $defaultLocale, false);

            if (empty($event->slug) || ($event->isDirty('title_'.$defaultLocale) && !$event->isDirty('slug'))) {
                if (!empty($titleForSlug)) {
                    $slug = Str::slug($titleForSlug);
                    $originalSlug = $slug;
                    $count = 1;
                    // Assurer l'unicité du slug
                    while (static::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $event->slug = $slug;
                } elseif (empty($event->slug)) { // Fallback si le titre est vide et slug aussi
                    $event->slug = 'evenement-' . time(); // ou une autre logique de fallback
                }
            } else if (!empty($event->slug) && $event->isDirty('slug')) {
                // Si le slug est explicitement fourni et modifié, on le normalise
                $event->slug = Str::slug($event->slug);
            }
        });
    }


    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'event_partner', 'event_id', 'partner_id');
    }

    public function createdBy(): BelongsTo // Renommé depuis user() et creator() pour cohérence
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('event_cover_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/event_default.jpg')) // Adaptez le chemin
            ->useFallbackPath(public_path('assets/images/placeholders/event_default.jpg')); // Adaptez le chemin

        $this->addMediaCollection('event_gallery'); // Pour plusieurs images
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
              ->width(400)
              ->height(250) // Maintenir le ratio, ou utiliser fit/crop
              ->sharpen(10);

        $this->addMediaConversion('banner')
              ->width(1200)
              ->height(600); // Maintenir le ratio
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Accesseurs pour les médias
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('event_cover_image');
    }

    public function getCoverImageThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('event_cover_image', 'thumbnail');
    }

    public function getGalleryImagesAttribute() // : Collection
    {
        return $this->getMedia('event_gallery');
    }
}
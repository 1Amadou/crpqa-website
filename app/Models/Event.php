<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // AJOUTÉ
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'cover_image_path',
        'is_featured',
        'registration_url',
        'meta_title',
        'meta_description',
        'target_audience',
        'created_by_user_id', // AJOUTÉ ICI
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'event_partner');
    }

    // AJOUTER CETTE RELATION
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by_user_id');
}

// ----- AJOUTEZ CETTE MÉTHODE POUR LA RELATION -----
    /**
     * Get all of the registrations for the Event.
     * Un événement (Event) peut avoir plusieurs inscriptions (EventRegistration).
     */
    public function registrations(): HasMany // Le nom doit correspondre à celui utilisé dans le contrôleur
    {
        // La clé étrangère 'event_id' est sur la table 'event_registrations'
        return $this->hasMany(EventRegistration::class, 'event_id');
    }
    // ----------------------------------------------------
}
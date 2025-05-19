<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'registration_url', // Nous pourrions reconsidérer l'usage de ce champ plus tard
        'is_featured',
        'user_id', 
        'meta_title',
        'meta_description',
        'target_audience',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * L'utilisateur (admin) qui a créé l'événement.
     */
    public function user(): BelongsTo // Ou creator() selon votre préférence
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère toutes les inscriptions pour cet événement.
     */
    public function registrations(): HasMany 
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Les partenaires associés à cet événement.
     */
    public function associatedPartners(): BelongsToMany // << NOUVELLE RELATION
    {
        return $this->belongsToMany(Partner::class, 'event_partner', 'event_id', 'partner_id')
                    ->withTimestamps(); // Optionnel, si vous voulez gérer les timestamps de la table pivot
    }

    // Vous pourriez ajouter d'autres relations ici plus tard,
    // par exemple pour les partenaires de l'événement ou le public cible si ce sont des tables séparées.
}
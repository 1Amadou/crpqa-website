<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone_number',
        'organization',
        'motivation',
        'status',
        'notes',
        'registered_at',
        'user_id',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /**
     * Récupère l'événement auquel cette inscription est associée.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Récupère l'utilisateur (si applicable) associé à cette inscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
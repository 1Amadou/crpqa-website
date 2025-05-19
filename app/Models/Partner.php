<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'website_url',
        'description',
        'type',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function associatedEvents(): BelongsToMany // << NOUVELLE RELATION
    {
        return $this->belongsToMany(Event::class, 'event_partner', 'partner_id', 'event_id')
                    ->withTimestamps();
    }
}
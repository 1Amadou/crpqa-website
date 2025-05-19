<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    /**
     * Indique si les IDs sont auto-incrémentés.
     * La table site_settings n'a qu'une seule ligne, donc l'ID est généralement fixe (ex: 1).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string'; // Ou 'integer' si vous utilisez un ID numérique fixe.

    /**
     * Indique si le modèle doit être horodaté.
     *
     * @var bool
     */
    // public $timestamps = true; // Déjà géré par défaut, mais peut être explicite.

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'logo_path',
        'favicon_path',
        'contact_email',
        'contact_phone',
        'address',
        'maps_url', // Attention à la casse, dans la migration c'était Maps_url, ici aussi pour la cohérence
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'footer_text',

        // Nouveaux champs ajoutés
        'cookie_consent_enabled',
        'cookie_consent_message',
        'cookie_policy_url',
        'privacy_policy_url',
        'terms_of_service_url',
        'default_sender_email',
        'default_sender_name',
        'google_analytics_id',
        'maintenance_mode',
        'maintenance_message',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cookie_consent_enabled' => 'boolean',
        'maintenance_mode' => 'boolean',
    ];

    /**
     * Définir une valeur par défaut pour la clé primaire si elle n'est pas auto-incrémentée
     * et si vous voulez forcer une certaine valeur pour la seule ligne.
     * Ceci est optionnel et dépend de comment vous gérez la ligne unique.
     *
     * protected static function boot()
     * {
     * parent::boot();
     * static::creating(function ($model) {
     * if (empty($model->{$model->getKeyName()})) {
     * $model->{$model->getKeyName()} = 'global_settings'; // Ou 1 si c'est un entier
     * }
     * });
     * }
     */
}
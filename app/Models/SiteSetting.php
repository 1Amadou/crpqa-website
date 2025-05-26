<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    protected $fillable = [
        'site_name_fr', 'site_name_en',
        'seo_meta_title_fr', 'seo_meta_title_en',
        'seo_meta_description_fr', 'seo_meta_description_en',
        'hero_title_fr', 'hero_title_en',
        'hero_subtitle_fr', 'hero_subtitle_en',
        'address_fr', 'address_en',
        'footer_text_fr', 'footer_text_en',
        'cookie_consent_message_fr', 'cookie_consent_message_en',
        'maintenance_message_fr', 'maintenance_message_en',

        'contact_email',
        'contact_phone',
        'maps_url',

        // Colonnes individuelles pour les réseaux sociaux (selon votre migration initiale)
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        // 'instagram_url', // Ajoutez si vous avez une colonne pour Instagram

        'cookie_consent_enabled',
        'cookie_policy_url',
        'privacy_policy_url',
        'terms_of_service_url',
        'default_sender_email',
        'default_sender_name',
        'google_analytics_id',
        'maintenance_mode',
    ];

    public array $localizedFields = [
        'site_name',
        'seo_meta_title',
        'seo_meta_description',
        'hero_title',
        'hero_subtitle',
        'address',
        'footer_text',
        'cookie_consent_message',
        'maintenance_message',
    ];

    protected $casts = [
        // 'social_media_links' => 'array', // SUPPRIMER CETTE LIGNE
        'cookie_consent_enabled' => 'boolean',
        'maintenance_mode' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('favicon')
            ->singleFile()
            ->useFallbackUrl(asset('images/default_favicon.ico'))
            ->useFallbackPath(public_path('images/default_favicon.ico'));

        $this->addMediaCollection('logo')
            ->singleFile()
            ->useFallbackUrl(asset('images/default_logo.png'))
            ->useFallbackPath(public_path('images/default_logo.png'));

        $this->addMediaCollection('logo_dark')
            ->singleFile()
            ->useFallbackUrl(asset('images/default_logo_dark.png'))
            ->useFallbackPath(public_path('images/default_logo_dark.png'));

        $this->addMediaCollection('hero_background')
            ->singleFile()
            ->useFallbackUrl(asset('images/default_hero_bg.jpg'))
            ->useFallbackPath(public_path('images/default_hero_bg.jpg'));
    }

    // Accesseurs pour les URL des médias
    public function getLogoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo') ?: asset('images/default_logo.png');
    }

    public function getLogoDarkUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo_dark') ?: ($this->getFirstMediaUrl('logo') ?: asset('images/default_logo_dark.png'));
    }

    public function getFaviconUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('favicon') ?: asset('images/default_favicon.ico');
    }

    public function getHeroBackgroundUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('hero_background') ?: asset('images/default_hero_bg.jpg');
    }
}
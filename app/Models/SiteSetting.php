<?php

namespace App\Models;

use App\Traits\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use HasFactory, HasLocalizedFields, InteractsWithMedia;

    /**
     * La clé de cache utilisée pour stocker/récupérer les paramètres du site.
     */
    public const CACHE_KEY = 'site_settings_cache';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name_fr', 'site_name_en',
        'site_name_short_fr', 'site_name_short_en',
        'site_description_fr', 'site_description_en',

        'hero_main_title_fr', 'hero_main_title_en',
        'hero_highlight_word_fr', 'hero_highlight_word_en',
        'hero_subtitle_line2_fr', 'hero_subtitle_line2_en',
        'hero_description_fr', 'hero_description_en',

        'hero_button1_text_fr', 'hero_button1_text_en',
        'hero_button1_url',
        'hero_button1_icon',
        'hero_button2_text_fr', 'hero_button2_text_en',
        'hero_button2_url',
        'hero_button2_icon',

        'hero_banner_image_alt_fr', 'hero_banner_image_alt_en',

        'about_home_title_fr', 'about_home_title_en',
        'about_home_subtitle_fr', 'about_home_subtitle_en',
        'about_home_short_description_fr', 'about_home_short_description_en',
        'about_home_points', // JSON
        'about_home_page_slug', // Ajouté car présent dans la migration et le seeder
        'about_page_slug',

        'home_cta_title_fr', 'home_cta_title_en',
        'home_cta_text_fr', 'home_cta_text_en',
        'home_cta_button1_text_fr', 'home_cta_button1_text_en',
        'home_cta_button1_url',
        'home_cta_button1_icon',
        'home_cta_button2_text_fr', 'home_cta_button2_text_en',
        'home_cta_button2_url',
        'home_cta_button2_icon',
        'careers_page_slug',

        'contact_email',
        'contact_phone',
        'address_fr', 'address_en',
        'maps_url',
        'facebook_url', 'twitter_url', 'linkedin_url', 'youtube_url',
        // 'instagram_url', // Décommenter si ajouté dans les migrations et utilisé

        'footer_text_fr', 'footer_text_en',
        'copyright_text_fr', 'copyright_text_en',

        'cookie_consent_message_fr', 'cookie_consent_message_en',
        'cookie_consent_enabled',
        'cookie_policy_page_slug',
        'privacy_policy_page_slug',
        'terms_of_service_page_slug',

        'default_sender_email',
        'default_sender_name',
        'google_analytics_id',
        'maintenance_mode',
        'maintenance_message_fr', 'maintenance_message_en',

        // Nouveaux champs traduits pour "À Propos"
        'about_page_hero_title_fr', 'about_page_hero_title_en',
        'about_page_hero_subtitle_fr', 'about_page_hero_subtitle_en',
        'about_introduction_title_fr', 'about_introduction_title_en',
        'about_introduction_content_fr', 'about_introduction_content_en',
        'about_history_title_fr', 'about_history_title_en',
        'about_mission_title_fr', 'about_mission_title_en',
        'about_mission_content_fr', 'about_mission_content_en',
        'about_vision_title_fr', 'about_vision_title_en',
        'about_vision_content_fr', 'about_vision_content_en',
        'about_values_title_fr', 'about_values_title_en',
        'about_director_message_title_fr', 'about_director_message_title_en',
        'about_director_name_fr', 'about_director_name_en',
        'about_director_position_fr', 'about_director_position_en',
        'about_director_message_content_fr', 'about_director_message_content_en',
        'about_decree_title_fr', 'about_decree_title_en',
        'about_decree_intro_text_fr', 'about_decree_intro_text_en',
        'about_fst_title_fr', 'about_fst_title_en',
        'about_fst_content_fr', 'about_fst_content_en',

        // Nouveaux champs non traduits pour "À Propos"
        'about_mission_icon_class',
        'about_vision_icon_class',
        'about_values_icon_class',
        'about_history_timeline_json', // Sera casté en array
        'about_values_list_json',      // Sera casté en array
        'about_fst_statistics_json',   // Sera casté en array
    
    ];

    /**
     * Les champs qui doivent être localisés.
     * La logique pour cela est gérée par le Trait HasLocalizedFields.
     *
     * @var array<string>
     */
    public array $localizedFields = [
        'site_name', 'site_name_short', 'site_description',
        'hero_main_title', 'hero_highlight_word', 'hero_subtitle_line2', 'hero_description',
        'hero_button1_text', 'hero_button2_text',
        'hero_banner_image_alt',
        'about_home_title', 'about_home_subtitle', 'about_home_short_description',
        'home_cta_title', 'home_cta_text', 'home_cta_button1_text', 'home_cta_button2_text',
        'address', 'footer_text', 'copyright_text',
        'cookie_consent_message', 'maintenance_message',
        // Nouveaux champs localisés pour la page "À Propos"
        'about_page_hero_title',
        'about_page_hero_subtitle',
        'about_introduction_title',
        'about_introduction_content',
        'about_history_title',
        'about_mission_title',
        'about_mission_content',
        'about_vision_title',
        'about_vision_content',
        'about_values_title',
        'about_director_message_title',
        'about_director_name',
        'about_director_position',
        'about_director_message_content',
        'about_decree_title',
        'about_decree_intro_text',
        'about_fst_title',
        'about_fst_content',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cookie_consent_enabled' => 'boolean',
        'maintenance_mode' => 'boolean',
        'about_home_points' => 'array',
        'about_history_timeline_json' => 'array',
        'about_values_list_json' => 'array',
        'about_fst_statistics_json' => 'array',
    ];

    /**
     * Enregistre les collections de médias pour le modèle.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('favicon')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_favicon.ico'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_favicon.ico'));

        $this->addMediaCollection('logo_header')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_logo_header.png'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_logo_header.png'));

        $this->addMediaCollection('logo_footer_dark')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_logo_footer_dark.png'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_logo_footer_dark.png'));

        $this->addMediaCollection('logo_footer_light')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_logo_footer_light.png'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_logo_footer_light.png'));

        $this->addMediaCollection('hero_background_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_hero_bg.jpg'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_hero_bg.jpg'));

        $this->addMediaCollection('hero_banner_images'); // Peut contenir plusieurs images

        $this->addMediaCollection('about_home_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_about_home.jpg'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_about_home.jpg'));

        $this->addMediaCollection('home_cta_bg_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_cta_bg.jpg'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_cta_bg.jpg'));

        $this->addMediaCollection('default_og_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/default_og_image.jpg'))
            ->useFallbackPath(public_path('assets/images/placeholders/default_og_image.jpg'));

        // NOUVELLES collections de médias pour la page "À Propos"
        $this->addMediaCollection('about_director_photo')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/director_avatar.png')) // Adaptez
            ->useFallbackPath(public_path('assets/images/placeholders/director_avatar.png')); // Adaptez

        $this->addMediaCollection('about_decree_pdf')
            ->singleFile(); // Pas de fallback image pour un PDF généralement

        $this->addMediaCollection('about_fst_logo')
            ->singleFile()
            ->useFallbackUrl(asset('assets/images/placeholders/fst_logo_default.png')) // Adaptez
            ->useFallbackPath(public_path('assets/images/placeholders/fst_logo_default.png')); // Adaptez
    
    }

    // ACCESSEURS POUR LES URL DES MÉDIAS

    public function getLogoHeaderUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo_header') ?: asset('assets/images/placeholders/default_logo_header.png');
    }

    public function getLogoFooterDarkUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo_footer_dark') ?: $this->logo_header_url; // Utilise logo_header_url comme fallback
    }

    public function getLogoFooterLightUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo_footer_light') ?: $this->logo_header_url; // Utilise logo_header_url comme fallback
    }

    public function getFaviconUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('favicon') ?: asset('assets/images/placeholders/default_favicon.ico');
    }

    public function getHeroBackgroundImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('hero_background_image') ?: asset('assets/images/placeholders/default_hero_bg.jpg');
    }

    public function getAboutHomeImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('about_home_image') ?: asset('assets/images/placeholders/default_about_home.jpg');
    }

    public function getHomeCtaBgImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('home_cta_bg_image') ?: asset('assets/images/placeholders/default_cta_bg.jpg');
    }

    public function getDefaultOgImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('default_og_image') ?: asset('assets/images/placeholders/default_og_image.jpg');
    }

    // Nouveaux accesseurs pour les médias de la page "À Propos"
    public function getAboutDirectorPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('about_director_photo');
    }
    public function getAboutDecreePdfUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('about_decree_pdf');
    }
    public function getAboutFstLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('about_fst_logo');
    }

    /**
     * Récupère toutes les images de la collection 'hero_banner_images'.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\MediaCollections\Models\Media[]
     */
    public function getHeroBannerImagesAttribute()
    {
        return $this->getMedia('hero_banner_images');
    }

    /**
     * Définir les conversions pour les médias
     *
     * @param Media|null $media
     * @return void
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('banner')
            ->width(1200)
            ->height(600)
            ->crop('center', 1200, 600);
    }
}
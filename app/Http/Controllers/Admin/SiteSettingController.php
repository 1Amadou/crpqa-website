<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage; // Peut encore être utile pour supprimer d'anciens fichiers non-Spatie
use Illuminate\Validation\Rule;
use App\Models\StaticPage; // Utilisé dans edit()
use Illuminate\Support\Facades\Schema;


class SiteSettingController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        $this->middleware(['permission:manage site settings']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']); // Assurez-vous que cette config existe
    }

    private function getSettings(): SiteSetting
    {
        return SiteSetting::firstOrCreate(['id' => 1]);
    }

    public function edit()
{
    $settings = $this->getSettings();
    $availableLocales = $this->availableLocales; // Utilise la propriété de classe

    $staticPagesForSelect = [];
    // Vérifie si la table 'static_pages' existe et si le modèle StaticPage est correctement configuré
    // pour la traduction avant de tenter de trier ou de traduire.
    if (Schema::hasTable('static_pages') && method_exists(StaticPage::class, 'getTranslation') && property_exists(new StaticPage(), 'localizedFields')) {
        $firstLocale = $this->availableLocales[0] ?? config('app.fallback_locale', 'en');
        $orderByColumn = 'title_' . $firstLocale; // Construit le nom de colonne comme title_fr

        // Vérifie si la colonne de tri existe réellement pour éviter les erreurs SQL.
        // Cette vérification est une sécurité supplémentaire ; idéalement, les migrations garantissent cela.
        if (Schema::hasColumn('static_pages', $orderByColumn)) {
            $staticPages = StaticPage::where('is_published', true)
                ->orderBy($orderByColumn) // Tri par la colonne traduite, ex: title_fr
                ->get();
        } else {
            // Fallback si la colonne de tri localisée n'existe pas (ne devrait pas arriver si les migrations sont correctes)
            $staticPages = StaticPage::where('is_published', true)
                ->orderBy('title') // Tri par la colonne de base 'title'
                ->get();
        }

        foreach ($staticPages as $page) {
            $displayTitle = $page->getTranslation('title', $firstLocale);
            // Fallback si getTranslation retourne vide mais que le champ de base existe (pourrait arriver si la traduction est vide)
            if (empty($displayTitle) && !empty($page->getAttributes()['title_' . $firstLocale])) {
                 $displayTitle = $page->getAttributes()['title_' . $firstLocale];
            } elseif (empty($displayTitle) && isset($page->title)) {
                $displayTitle = $page->title; // Fallback sur le champ 'title' de base
            }
            $staticPagesForSelect[$page->slug] = $displayTitle . ' (/' . $page->slug . ')';
        }
    }

    return view('admin.settings.edit', compact('settings', 'staticPagesForSelect', 'availableLocales'));
}

    public function update(Request $request)
{
    // 1. Récupérer l'objet $settings EN PREMIER
    $settings = $this->getSettings();

    // 2. Définir les règles de validation
    $rules = [
        // Champs traduits
        'site_name_fr' => 'required|string|max:255',
        'site_name_en' => 'nullable|string|max:255',
        'seo_meta_title_fr' => 'nullable|string|max:255',
        'seo_meta_title_en' => 'nullable|string|max:255',
        'seo_meta_description_fr' => 'nullable|string|max:500',
        'seo_meta_description_en' => 'nullable|string|max:500',
        'hero_title_fr' => 'nullable|string|max:255',
        'hero_title_en' => 'nullable|string|max:255',
        'hero_subtitle_fr' => 'nullable|string|max:1000',
        'hero_subtitle_en' => 'nullable|string|max:1000',
        'address_fr' => 'nullable|string|max:1000',
        'address_en' => 'nullable|string|max:1000',
        'footer_text_fr' => 'nullable|string|max:1000',
        'footer_text_en' => 'nullable|string|max:1000',
        'cookie_consent_message_fr' => 'nullable|string|max:2000',
        'cookie_consent_message_en' => 'nullable|string|max:2000',
        'maintenance_message_fr' => 'nullable|string|max:2000',
        'maintenance_message_en' => 'nullable|string|max:2000',

        // Champs non traduits
        'contact_email' => 'nullable|email|max:255',
        'contact_phone' => 'nullable|string|max:50',
        'maps_url' => 'nullable|url|max:500',

        'social_media_links.facebook' => 'nullable|url|max:255',
        'social_media_links.twitter' => 'nullable|url|max:255',
        'social_media_links.linkedin' => 'nullable|url|max:255',
        'social_media_links.youtube' => 'nullable|url|max:255',
        'social_media_links.instagram' => 'nullable|url|max:255',

        'cookie_consent_enabled' => 'nullable|boolean',
        // Valider soit le slug de la page, soit l'URL externe
        'cookie_policy_url' => ['nullable', 'string', 'max:255',
            Rule::exists('static_pages', 'slug')->where(function ($query) {
                $query->where('is_published', true);
            })
        ],
        'cookie_policy_url_external' => ['nullable', 'url', 'max:255'],

        'privacy_policy_url' => ['nullable', 'string', 'max:255',
            Rule::exists('static_pages', 'slug')->where(function ($query) {
                $query->where('is_published', true);
            })
        ],
        'privacy_policy_url_external' => ['nullable', 'url', 'max:255'],

        'terms_of_service_url' => ['nullable', 'string', 'max:255',
            Rule::exists('static_pages', 'slug')->where(function ($query) {
                $query->where('is_published', true);
            })
        ],
        'terms_of_service_url_external' => ['nullable', 'url', 'max:255'],


        'default_sender_email' => 'nullable|email|max:255',
        'default_sender_name' => 'nullable|string|max:255',
        'google_analytics_id' => 'nullable|string|max:50',
        'maintenance_mode' => 'nullable|boolean',

        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
        'hero_background' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
    ];

    // 3. Valider la requête
    $validatedData = $request->validate($rules);

    // 4. Mise à jour des champs traduits
    foreach ($settings->localizedFields as $field) {
        foreach ($this->availableLocales as $locale) {
            $columnName = $field . '_' . $locale;
            if ($request->has($columnName)) {
                $settings->{$columnName} = $request->input($columnName);
            }
        }
    }

    // 5. Mise à jour des champs non traduits directs (ceux qui ne sont pas gérés spécifiquement ci-dessous)
    $directFields = [
        'contact_email', 'contact_phone', 'maps_url',
        'default_sender_email', 'default_sender_name', 'google_analytics_id',
    ];
    foreach ($directFields as $field) {
        if ($request->has($field)) {
            $settings->{$field} = $request->input($field); // ou $validatedData[$field] si vous préférez
        }
    }

    // 6. Gestion des booléens
    $settings->cookie_consent_enabled = $request->boolean('cookie_consent_enabled');
    $settings->maintenance_mode = $request->boolean('maintenance_mode');

    // 7. Gestion des réseaux sociaux
    $socialPlatformsToSave = [
        'facebook' => 'facebook_url',
        'twitter' => 'twitter_url',
        'linkedin' => 'linkedin_url',
        'youtube' => 'youtube_url',
        'instagram' => 'instagram_url', // Assurez-vous que cette colonne existe ou supprimez cette ligne
    ];

    if ($request->has('social_media_links')) {
        $submittedSocialLinks = $request->input('social_media_links');
        foreach ($socialPlatformsToSave as $formKey => $dbColumn) {
            // Vérifie si la colonne existe dans $fillable du modèle pour éviter MassAssignmentException
            // et aussi pour s'assurer qu'on ne tente pas d'assigner à une colonne inexistante.
            if (in_array($dbColumn, $settings->getFillable())) {
                $settings->{$dbColumn} = $submittedSocialLinks[$formKey] ?? null;
            }
        }
    } else {
        // Si 'social_media_links' n'est pas du tout dans la requête (par ex. si tous les champs sont vides et ne sont pas envoyés)
        // Mettre à null les champs correspondants pour les effacer
        foreach ($socialPlatformsToSave as $dbColumn) {
            if (in_array($dbColumn, $settings->getFillable())) {
                $settings->{$dbColumn} = null;
            }
        }
    }

    // 8. Gestion des URL de politiques (priorité à l'URL externe si fournie)
    if ($request->filled('cookie_policy_url_external')) {
        $settings->cookie_policy_url = $request->input('cookie_policy_url_external');
    } elseif ($request->filled('cookie_policy_url')) { // Slug de la page statique
        $settings->cookie_policy_url = $request->input('cookie_policy_url');
    } else {
        $settings->cookie_policy_url = null; // Les deux sont vides, on met à null
    }

    if ($request->filled('privacy_policy_url_external')) {
        $settings->privacy_policy_url = $request->input('privacy_policy_url_external');
    } elseif ($request->filled('privacy_policy_url')) {
        $settings->privacy_policy_url = $request->input('privacy_policy_url');
    } else {
        $settings->privacy_policy_url = null;
    }

    if ($request->filled('terms_of_service_url_external')) {
        $settings->terms_of_service_url = $request->input('terms_of_service_url_external');
    } elseif ($request->filled('terms_of_service_url')) {
        $settings->terms_of_service_url = $request->input('terms_of_service_url');
    } else {
        $settings->terms_of_service_url = null;
    }

    // 9. Gestion des médias avec Spatie Media Library
    $mediaFields = ['logo', 'logo_dark', 'favicon', 'hero_background'];
    foreach ($mediaFields as $mediaField) {
        if ($request->hasFile($mediaField)) {
            $settings->clearMediaCollection($mediaField);
            $settings->addMediaFromRequest($mediaField)->toMediaCollection($mediaField);
        } elseif ($request->boolean('remove_' . $mediaField)) {
            $settings->clearMediaCollection($mediaField);
        }
    }

    // 10. Sauvegarder l'objet $settings
    $settings->save();

    Cache::forget('site_settings_processed'); // Ou le nom de cache que vous utilisez

    return redirect()->route('admin.settings.edit')
        ->with('success', 'Paramètres du site mis à jour avec succès.');
}
}
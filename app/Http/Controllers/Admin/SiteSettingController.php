<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Http\Requests\Admin\SiteSettingUpdateRequest; // <-- UTILISER CE FORM REQUEST
use Illuminate\Support\Facades\Cache;
use App\Models\StaticPage; 
use Illuminate\Support\Facades\Schema; 
use Illuminate\Http\Request; // Retirer si plus utilisé directement
use Illuminate\Support\Str;


class SiteSettingController extends Controller
{
    protected array $availableLocales;
    public const CACHE_KEY = 'site_settings_processed'; // Déplacer vers le modèle SiteSetting serait mieux

    public function __construct()
    {
        $this->middleware(['permission:manage site settings']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    private function getSettings(): SiteSetting
    {
        return SiteSetting::firstOrCreate(['id' => 1]);
    }
    
    public function edit()
    {
        $settings = $this->getSettings();
        $settings->load('media'); 
        $availableLocales = $this->availableLocales;
        $primaryLocale = config('app.locale', 'fr'); // Pour le fallback de titre de page

        $staticPagesForSelect = [];
        if (Schema::hasTable('static_pages') && class_exists(StaticPage::class) && property_exists(new StaticPage(), 'localizedFields')) {
            $titleColumn = 'title_' . $primaryLocale;
            $query = StaticPage::where('is_published', true);
            if (Schema::hasColumn('static_pages', $titleColumn)) {
                $query->orderBy($titleColumn);
            } elseif (Schema::hasColumn('static_pages', 'title')) {
                $query->orderBy('title');
            } else { 
                $query->orderBy('id');
            }
            $staticPages = $query->get();
            foreach ($staticPages as $page) {
                $displayTitle = $page->getTranslation('title', $primaryLocale, false) ?: $page->slug;
                if (empty($displayTitle) && $page->hasAttribute('title_'.$primaryLocale)) {
                     $displayTitle = $page->{'title_'.$primaryLocale};
                }
                if (empty($displayTitle)) $displayTitle = $page->slug;
                $staticPagesForSelect[$page->slug] = $displayTitle . ' (/page/' . $page->slug . ')';
            }
        }
        return view('admin.settings.edit', compact('settings', 'staticPagesForSelect', 'availableLocales', 'primaryLocale'));
    }

    public function update(SiteSettingUpdateRequest $request) 
    {
        $settings = $this->getSettings();
        $validatedData = $request->validated(); 

        $dataToUpdate = [];
        $localizedFieldsInModel = $settings->localizedFields; 
        $fillableFields = $settings->getFillable();

        // Assignation des champs traduits
        foreach ($this->availableLocales as $locale) {
            foreach ($localizedFieldsInModel as $baseFieldName) {
                $suffixedFieldName = $baseFieldName . '_' . $locale;
                if (array_key_exists($suffixedFieldName, $validatedData) && in_array($suffixedFieldName, $fillableFields)) {
                    $dataToUpdate[$suffixedFieldName] = $validatedData[$suffixedFieldName];
                }
            }
        }
        
        // Champs non traduits
        $nonLocalizedFillable = array_diff($fillableFields, $this->getAllSuffixedLocalizedFields($localizedFieldsInModel, $this->availableLocales));
        
        foreach ($nonLocalizedFillable as $field) {
            if (array_key_exists($field, $validatedData)) {
                // Gérer les champs JSON
                if (in_array($field, ['about_home_points', 'about_history_timeline_json', 'about_values_list_json', 'about_fst_statistics_json']) && is_string($validatedData[$field])) {
                    $decoded = json_decode($validatedData[$field], true);
                    // Sauvegarder null si le JSON est invalide, ou la valeur décodée
                    $dataToUpdate[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : null; 
                } else {
                    $dataToUpdate[$field] = $validatedData[$field];
                }
            }
        }
        
        // Booléens déjà castés par prepareForValidation
        if (isset($validatedData['cookie_consent_enabled'])) $dataToUpdate['cookie_consent_enabled'] = $validatedData['cookie_consent_enabled'];
        if (isset($validatedData['maintenance_mode'])) $dataToUpdate['maintenance_mode'] = $validatedData['maintenance_mode'];

        $settings->update($dataToUpdate);

        // Gestion des médias Spatie (single files)
        $singleMediaFields = [
            'favicon', 'logo_header', 'logo_footer_dark', 'logo_footer_light',
            'hero_background_image', 'about_home_image', 'home_cta_bg_image', 'default_og_image',
            'about_director_photo', 'about_decree_pdf', 'about_fst_logo' // Nouveaux médias
        ];
        foreach ($singleMediaFields as $mediaField) {
            if ($request->hasFile($mediaField)) {
                $settings->clearMediaCollection($mediaField); // Toujours supprimer l'ancien si un nouveau est uploadé
                $settings->addMediaFromRequest($mediaField)->toMediaCollection($mediaField);
            } elseif ($request->boolean('remove_' . $mediaField)) {
                $settings->clearMediaCollection($mediaField);
            }
        }
        
        // Gestion de la collection hero_banner_images (multiple files)
        if ($request->boolean('remove_hero_banner_images_all')) {
            $settings->clearMediaCollection('hero_banner_images');
        } else {
            if ($request->has('remove_specific_hero_banner_images')) {
                foreach ($request->input('remove_specific_hero_banner_images', []) as $mediaIdToRemove) {
                    $mediaItem = $settings->getMedia('hero_banner_images')->find($mediaIdToRemove);
                    if ($mediaItem) $mediaItem->delete();
                }
            }
            if ($request->hasFile('hero_banner_images')) {
                $altTextsArray = $request->input('hero_banner_alt_text', []); // Doit être hero_banner_images_alt_text ??
                foreach ($request->file('hero_banner_images') as $key => $file) {
                    if ($file->isValid()) {
                        $media = $settings->addMedia($file)->toMediaCollection('hero_banner_images');
                        foreach ($this->availableLocales as $locale) {
                            // Le nom du champ d'alt text doit correspondre à ce qui est envoyé par le formulaire
                            // S'il est généré dynamiquement par JS comme hero_banner_alt_text[INDEX][LOCALE]
                            if (isset($altTextsArray[$key][$locale]) && !empty($altTextsArray[$key][$locale])) {
                                $media->setCustomProperty('alt_text_' . $locale, $altTextsArray[$key][$locale]);
                            }
                        }
                        $media->save(); 
                    }
                }
            }
        }

        Cache::forget(SiteSetting::CACHE_KEY ?? 'site_settings_processed');

        return redirect()->route('admin.settings.edit')
                         ->with('success', __('Paramètres du site mis à jour avec succès.'));
    }

    private function getAllSuffixedLocalizedFields(array $baseLocalizedFields, array $locales): array
    {
        $suffixedFields = [];
        foreach ($baseLocalizedFields as $baseField) {
            foreach ($locales as $locale) {
                $suffixedFields[] = $baseField . '_' . $locale;
            }
        }
        return $suffixedFields;
    }
}
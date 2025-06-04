<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\SiteSetting; // Importez le modèle pour accéder à $localizedFields

class SiteSettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assurez-vous que la permission 'manage site settings' est définie
        // et assignée aux rôles appropriés.
        return Auth::user()->can('manage site settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');
        
        $rules = [
            // Champs Non Traduits
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'maps_url' => 'nullable|url|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            // 'instagram_url' => 'nullable|url|max:255', // Décommentez si vous ajoutez cette colonne

            'hero_button1_url' => 'nullable|string|max:255', // Peut être une URL ou un slug
            'hero_button1_icon' => 'nullable|string|max:100',
            'hero_button2_url' => 'nullable|string|max:255',
            'hero_button2_icon' => 'nullable|string|max:100',

            'cookie_consent_enabled' => 'boolean',
            'cookie_policy_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            'privacy_policy_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            'terms_of_service_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            'about_home_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            'about_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            'careers_page_slug' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where('is_published', true)],
            
            'about_home_points' => 'nullable|json', // Valide que la chaîne est un JSON
            // Si vous avez des champs pour les témoignages et chiffres clés en JSON :
            // 'home_testimonials' => 'nullable|json',
            // 'home_key_figures' => 'nullable|json',

            'home_cta_button1_url' => 'nullable|string|max:255',
            'home_cta_button1_icon' => 'nullable|string|max:100',
            'home_cta_button2_url' => 'nullable|string|max:255',
            'home_cta_button2_icon' => 'nullable|string|max:100',

            'default_sender_email' => 'nullable|email|max:255',
            'default_sender_name' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:50',
            'maintenance_mode' => 'boolean',
            // --- NOUVEAUX Champs Non Traduits pour "À Propos" ---
            'about_mission_icon_class' => 'nullable|string|max:100',
            'about_vision_icon_class' => 'nullable|string|max:100',
            'about_values_icon_class' => 'nullable|string|max:100',
            'about_history_timeline_json' => 'nullable|json',
            'about_values_list_json' => 'nullable|json',
            'about_fst_statistics_json' => 'nullable|json',

            // Médias (les noms correspondent aux collections définies dans le modèle SiteSetting)
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
            'logo_header' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'logo_footer_dark' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'logo_footer_light' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'hero_background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'about_home_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'home_cta_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'default_og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            
            'hero_banner_images'   => 'nullable|array',
            'hero_banner_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            
            'hero_banner_alt_text' => 'nullable|array',
            'hero_banner_alt_text.*' => 'nullable|array', // Chaque image a un tableau de traductions d'alt
            'hero_banner_alt_text.*.*' => 'nullable|string|max:255', // Chaque traduction d'alt text
            // Nouveaux médias pour "À Propos"
            'about_director_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'about_decree_pdf' => 'nullable|file|mimes:pdf|max:10240', // PDF max 10MB
            'about_fst_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',

            // Cases à cocher pour la suppression des médias
            'remove_favicon' => 'nullable|boolean',
            'remove_logo_header' => 'nullable|boolean',
            'remove_logo_footer_dark' => 'nullable|boolean',
            'remove_logo_footer_light' => 'nullable|boolean',
            'remove_hero_background_image' => 'nullable|boolean',
            'remove_about_home_image' => 'nullable|boolean',
            'remove_home_cta_bg_image' => 'nullable|boolean',
            'remove_default_og_image' => 'nullable|boolean',
            'remove_hero_banner_images_all' => 'nullable|boolean',
            'remove_specific_hero_banner_images' => 'nullable|array',
            'remove_specific_hero_banner_images.*' => 'integer|exists:media,id',
            'remove_about_director_photo' => 'nullable|boolean',
            'remove_about_decree_pdf' => 'nullable|boolean',
            'remove_about_fst_logo' => 'nullable|boolean',
        ];

        $siteSettingModel = new SiteSetting();
        // Assurez-vous que $localizedFields est public dans le modèle SiteSetting
        $localizedFieldsInModel = $siteSettingModel->localizedFields; 

        foreach ($localizedFieldsInModel as $field) {
            foreach ($availableLocales as $locale) {
                $isRequired = ($locale === $primaryLocale && in_array($field, ['site_name'])); 
                $rule = ($isRequired ? 'required' : 'nullable') . '|string';
                
                // Logique de max length (à affiner si besoin)
                if (in_array($field, ['site_description', 'hero_description', 'cookie_consent_message', 'maintenance_message', 'about_home_short_description', 'home_cta_text', 'about_introduction_content', 'about_mission_content', 'about_vision_content', 'about_values_content', 'about_director_message_content', 'about_decree_intro_text', 'about_fst_content'])) {
                    $rule .= '|max:10000'; // Limite plus grande pour les descriptions/contenus longs
                } elseif (in_array($field, ['hero_banner_image_alt', 'site_name_short', 'hero_highlight_word', 'hero_button1_text', 'hero_button2_text', 'copyright_text', 'about_home_subtitle', 'home_cta_button1_text', 'home_cta_button2_text', 'about_page_hero_subtitle', 'about_introduction_title', 'about_history_title', 'about_mission_title', 'about_vision_title', 'about_values_title', 'about_director_message_title', 'about_director_name', 'about_director_position', 'about_decree_title', 'about_fst_title'])) {
                     $rule .= '|max:255';
                } else { 
                    $rule .= '|max:255';
                }
                $rules[$field . '_' . $locale] = $rule;
            }
        }
        
        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cookie_consent_enabled' => $this->boolean('cookie_consent_enabled'),
            'maintenance_mode' => $this->boolean('maintenance_mode'),
            'is_active' => $this->boolean('is_active'), // Pour les modèles qui ont ce champ
            'remove_favicon' => $this->boolean('remove_favicon'),
            'remove_logo_header' => $this->boolean('remove_logo_header'),
            'remove_logo_footer_dark' => $this->boolean('remove_logo_footer_dark'),
            'remove_logo_footer_light' => $this->boolean('remove_logo_footer_light'),
            'remove_hero_background_image' => $this->boolean('remove_hero_background_image'),
            'remove_about_home_image' => $this->boolean('remove_about_home_image'),
            'remove_home_cta_bg_image' => $this->boolean('remove_home_cta_bg_image'),
            'remove_default_og_image' => $this->boolean('remove_default_og_image'),
            'remove_hero_banner_images_all' => $this->boolean('remove_hero_banner_images_all'),
            'remove_about_director_photo' => $this->boolean('remove_about_director_photo'),
            'remove_about_decree_pdf' => $this->boolean('remove_about_decree_pdf'),
            'remove_about_fst_logo' => $this->boolean('remove_about_fst_logo'),
        ]);
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');
        // Pour la personnalisation des messages, vous pouvez itérer sur les champs que vous attendez
        // et construire des messages spécifiques.

        $messages['site_name_' . $primaryLocale . '.required'] = __('Le nom du site (:locale) est obligatoire.', ['locale' => strtoupper($primaryLocale)]);
        
        $messages['*.image'] = __('Le fichier doit être une image.');
        $messages['*.mimes'] = __('Le format du fichier n\'est pas supporté.');
        $messages['*.max'] = __('Le fichier est trop volumineux (max :size ko).', ['size' => ':max']); // :max sera remplacé par la valeur de la règle
        $messages['*.url'] = __('Le champ :attribute doit être une URL valide.');
        $messages['*.exists'] = __('La valeur sélectionnée pour :attribute n\'est pas valide.');
        $messages['color_hex.regex'] = __('Le code couleur doit être un format hexadécimal valide (ex: #FF0000).');
        $messages['about_home_points.json'] = __('Les points clés pour la section "À Propos" doivent être au format JSON valide.');
        $messages = [
            'color_hex.regex' => __('Le code couleur doit être un format hexadécimal valide (ex: #FF0000).'),
            '*.json' => __('Le champ :attribute doit être au format JSON valide.'),
       ];
        return $messages;
    }
}
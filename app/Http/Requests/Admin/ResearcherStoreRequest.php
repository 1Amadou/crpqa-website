<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ResearcherStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assurez-vous que la permission 'manage researchers' est définie
        return Auth::user()->can('manage researchers');
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
            'slug' => 'nullable|string|max:255|alpha_dash:ascii|unique:researchers,slug',
            'email' => 'nullable|string|email|max:255|unique:researchers,email',
            'phone' => 'nullable|string|max:50',
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'researchgate_url' => 'nullable|url|max:255',
            'google_scholar_url' => 'nullable|url|max:255',
            'orcid_id' => 'nullable|string|max:100', // ex: 0000-0002-1825-0097
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id|unique:researchers,user_id', // Un utilisateur ne peut être lié qu'à un seul profil chercheur
            'display_order' => 'nullable|integer|min:0',
            'researcher_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Pour Spatie Media Library
        ];

        foreach ($availableLocales as $locale) {
            $rules['first_name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['last_name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['title_position_' . $locale] = 'nullable|string|max:255';
            $rules['biography_' . $locale] = 'nullable|string';
            $rules['research_interests_' . $locale] = 'nullable|string';
            $rules['photo_alt_text_' . $locale] = 'nullable|string|max:255';
        }
        
        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

        // La génération du slug est gérée par le modèle Researcher via son boot() method
        // Si un slug est fourni, il sera normalisé par le modèle.
        // if (!empty($this->slug)) {
        //     $this->merge(['slug' => \Illuminate\Support\Str::slug($this->slug)]);
        // }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        foreach ($availableLocales as $locale) {
            if ($locale === $primaryLocale) {
                $messages['first_name_' . $locale . '.required'] = __('Le prénom (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
                $messages['last_name_' . $locale . '.required'] = __('Le nom de famille (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
            }
            // Ajoutez d'autres messages si nécessaire
        }
        $messages['email.unique'] = __('Cette adresse email est déjà utilisée par un autre chercheur.');
        $messages['user_id.unique'] = __('Ce compte utilisateur est déjà lié à un autre profil chercheur.');
        $messages['user_id.exists'] = __('Le compte utilisateur sélectionné n\'est pas valide.');
        $messages['researcher_photo.image'] = __('Le fichier de la photo doit être une image.');
        $messages['researcher_photo.mimes'] = __('Le format de la photo doit être jpeg, png, jpg, ou webp.');
        $messages['researcher_photo.max'] = __('La photo ne doit pas dépasser 2Mo.');
        $messages['slug.unique'] = __('Ce slug est déjà utilisé. Veuillez en choisir un autre ou le laisser vide pour une génération automatique.');
        $messages['slug.alpha_dash'] = __('Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.');
        
        return $messages;
    }
}
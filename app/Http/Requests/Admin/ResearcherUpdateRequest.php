<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResearcherUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage researchers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $researcherId = $this->route('researcher')->id; // 'researcher' est le nom du paramètre de route
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                Rule::unique('researchers', 'slug')->ignore($researcherId),
            ],
            'email' => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique('researchers', 'email')->ignore($researcherId),
            ],
            'phone' => 'nullable|string|max:50',
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'researchgate_url' => 'nullable|url|max:255',
            'google_scholar_url' => 'nullable|url|max:255',
            'orcid_id' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'user_id' => [
                'nullable', 
                'exists:users,id',
                Rule::unique('researchers', 'user_id')->ignore($researcherId),
            ],
            'display_order' => 'nullable|integer|min:0',
            'researcher_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_researcher_photo' => 'nullable|boolean',
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
            'remove_researcher_photo' => $this->boolean('remove_researcher_photo'),
        ]);

        // La génération/mise à jour du slug est gérée par le modèle Researcher via son boot() method
        // Si un slug est fourni et modifié, il sera normalisé par le modèle.
        // if (!empty($this->slug)) {
        //     $this->merge(['slug' => \Illuminate\Support\Str::slug($this->slug)]);
        // }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        // Vous pouvez réutiliser les messages de ResearcherStoreRequest ou les définir spécifiquement ici.
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        foreach ($availableLocales as $locale) {
            if ($locale === $primaryLocale) {
                $messages['first_name_' . $locale . '.required'] = __('Le prénom (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
                $messages['last_name_' . $locale . '.required'] = __('Le nom de famille (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
            }
        }
        $messages['email.unique'] = __('Cette adresse email est déjà utilisée par un autre chercheur.');
        $messages['user_id.unique'] = __('Ce compte utilisateur est déjà lié à un autre profil chercheur.');
        // ... etc.
        return $messages;
    }
}
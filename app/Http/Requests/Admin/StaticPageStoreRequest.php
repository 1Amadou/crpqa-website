<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StaticPageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assurez-vous que la permission 'manage static_pages' est définie
        return Auth::user()->can('manage static_pages');
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
            'slug' => 'nullable|string|max:255|alpha_dash:ascii|unique:static_pages,slug',
            'is_published' => 'boolean',
            // user_id sera géré dans le contrôleur (Auth::id() ou depuis le formulaire si vous ajoutez un select)
            // Si user_id est un champ du formulaire, ajoutez sa validation ici :
            // 'user_id' => 'required|exists:users,id', 
            'static_page_cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Pour Spatie Media Library
        ];

        foreach ($availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['content_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['cover_image_alt_text_' . $locale] = 'nullable|string|max:255';
        }
        
        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);

        // La génération du slug est gérée par le StaticPageController ou le modèle StaticPage
        // Si un slug est fourni et que vous voulez le normaliser avant validation d'unicité:
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
                $messages['title_' . $locale . '.required'] = __('Le titre (:locale) de la page est obligatoire.', ['locale' => strtoupper($locale)]);
                $messages['content_' . $locale . '.required'] = __('Le contenu (:locale) de la page est obligatoire.', ['locale' => strtoupper($locale)]);
            }
            // Ajoutez d'autres messages si nécessaire
        }
        $messages['static_page_cover.image'] = __('L\'image de couverture doit être une image.');
        $messages['static_page_cover.mimes'] = __('Le format de l\'image doit être jpeg, png, jpg, ou webp.');
        $messages['static_page_cover.max'] = __('L\'image de couverture ne doit pas dépasser 2Mo.');
        $messages['slug.unique'] = __('Ce slug est déjà utilisé. Veuillez en choisir un autre ou le laisser vide.');
        $messages['slug.alpha_dash'] = __('Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.');
        // $messages['user_id.required'] = __('Veuillez assigner un auteur à cette page.');
        // $messages['user_id.exists'] = __('L\'auteur sélectionné n\'est pas valide.');
        
        return $messages;
    }
}
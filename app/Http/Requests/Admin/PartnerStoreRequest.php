<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PartnerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Permettre la requête si l'utilisateur a la permission de gérer les partenaires
        // Assurez-vous que la permission 'manage partners' est bien définie.
        return Auth::user()->can('manage partners');
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
            // 'slug' => 'nullable|string|max:255|alpha_dash:ascii|unique:partners,slug', // Si vous ajoutez un slug
            'website_url' => 'nullable|url|max:255',
            'type' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'partner_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048', // Pour Spatie Media Library
        ];

        foreach ($availableLocales as $locale) {
            $rules['name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['description_' . $locale] = 'nullable|string';
            $rules['logo_alt_text_' . $locale] = 'nullable|string|max:255';
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

        // Si vous ajoutez un slug et que vous voulez le générer automatiquement si vide
        // $primaryLocale = config('app.locale', 'fr');
        // if (empty($this->slug) && $this->has('name_' . $primaryLocale) && !empty($this->input('name_' . $primaryLocale))) {
        //     $this->merge(['slug' => \Illuminate\Support\Str::slug($this->input('name_' . $primaryLocale))]);
        // } elseif (!empty($this->slug)) {
        //     $this->merge(['slug' => \Illuminate\Support\Str::slug($this->slug)]);
        // }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        foreach ($availableLocales as $locale) {
            if ($locale === $primaryLocale) {
                $messages['name_' . $locale . '.required'] = 'Le nom du partenaire (' . strtoupper($locale) . ') est obligatoire.';
            }
            $messages['name_' . $locale . '.string'] = 'Le nom du partenaire (' . strtoupper($locale) . ') doit être une chaîne de caractères.';
            $messages['name_' . $locale . '.max'] = 'Le nom du partenaire (' . strtoupper($locale) . ') ne doit pas dépasser 255 caractères.';
            // Ajoutez d'autres messages pour description et logo_alt_text si nécessaire
        }
        $messages['partner_logo.image'] = 'Le fichier du logo doit être une image.';
        $messages['partner_logo.mimes'] = 'Le format du logo doit être jpeg, png, jpg, svg ou webp.';
        $messages['partner_logo.max'] = 'Le logo ne doit pas dépasser 2Mo.';
        // $messages['slug.unique'] = 'Ce slug est déjà utilisé. Veuillez en choisir un autre ou le laisser vide.';
        // $messages['slug.alpha_dash'] = 'Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.';
        
        return $messages;
    }
}
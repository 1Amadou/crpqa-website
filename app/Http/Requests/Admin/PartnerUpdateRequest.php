<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PartnerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage partners');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $partnerId = $this->route('partner')->id; // 'partner' est le nom du paramètre de route pour Route::resource
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $rules = [
            // 'slug' => [
            //     'nullable', 'string', 'max:255', 'alpha_dash:ascii',
            //     Rule::unique('partners', 'slug')->ignore($partnerId),
            // ], // Si vous ajoutez un slug
            'website_url' => 'nullable|url|max:255',
            'type' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'partner_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'remove_partner_logo' => 'nullable|boolean',
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
            'remove_partner_logo' => $this->boolean('remove_partner_logo'),
        ]);

        // Si vous ajoutez un slug et que vous voulez le générer automatiquement si vide
        // $primaryLocale = config('app.locale', 'fr');
        // $partner = $this->route('partner');
        // if (empty($this->slug) && $this->has('name_' . $primaryLocale) && !empty($this->input('name_' . $primaryLocale))) {
        //     if ($partner && $partner->getTranslation('name', $primaryLocale, false) !== $this->input('name_' . $primaryLocale)) {
        //          $this->merge(['slug' => \Illuminate\Support\Str::slug($this->input('name_' . $primaryLocale))]);
        //     }
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
        // Vous pouvez réutiliser les messages de PartnerStoreRequest ou les définir spécifiquement ici.
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        foreach ($availableLocales as $locale) {
            if ($locale === $primaryLocale) {
                $messages['name_' . $locale . '.required'] = 'Le nom du partenaire (' . strtoupper($locale) . ') est obligatoire.';
            }
            // ... autres messages ...
        }
        // $messages['slug.unique'] = 'Ce slug est déjà utilisé par un autre partenaire.';
        // ... etc.
        return $messages;
    }
}
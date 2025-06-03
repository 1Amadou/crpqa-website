<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ResearchAxisStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assurez-vous que la permission 'manage research_axes' est définie
        return Auth::user()->can('manage research_axes');
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
            'slug' => 'nullable|string|max:255|alpha_dash:ascii|unique:research_axes,slug',
            'icon_class' => 'nullable|string|max:100',
            'color_hex' => ['nullable', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'], // Valide #RRGGBB ou #RGB
            'research_axis_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048', // Pour Spatie
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ];

        foreach ($availableLocales as $locale) {
            $rules['name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['subtitle_' . $locale] = 'nullable|string|max:255';
            $rules['description_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['icon_svg_' . $locale] = 'nullable|string'; // Pour le code SVG brut
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
            'is_active' => $this->boolean('is_active'),
        ]);

        // La génération du slug est gérée par le modèle ResearchAxis via sa méthode boot()
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
                $messages['name_' . $locale . '.required'] = __('Le nom de l\'axe de recherche (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
                $messages['description_' . $locale . '.required'] = __('La description (:locale) est obligatoire.', ['locale' => strtoupper($locale)]);
            }
            // Ajoutez d'autres messages si nécessaire
        }
        $messages['research_axis_cover_image.image'] = __('L\'image de couverture doit être une image.');
        $messages['research_axis_cover_image.mimes'] = __('Le format de l\'image doit être jpeg, png, jpg, svg ou webp.');
        $messages['research_axis_cover_image.max'] = __('L\'image de couverture ne doit pas dépasser 2Mo.');
        $messages['slug.unique'] = __('Ce slug est déjà utilisé. Veuillez en choisir un autre ou le laisser vide.');
        $messages['slug.alpha_dash'] = __('Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.');
        $messages['color_hex.regex'] = __('Le code couleur doit être un format hexadécimal valide (ex: #FF0000).');
        
        return $messages;
    }
}
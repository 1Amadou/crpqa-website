<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResearchAxisUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage research_axes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $researchAxisId = $this->route('research_axis')->id; // 'research_axis' est le nom du paramètre de route pour Route::resource
        
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                Rule::unique('research_axes', 'slug')->ignore($researchAxisId),
            ],
            'icon_class' => 'nullable|string|max:100',
            'color_hex' => ['nullable', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'research_axis_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'remove_research_axis_cover_image' => 'nullable|boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ];

        foreach ($availableLocales as $locale) {
            $rules['name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['subtitle_' . $locale] = 'nullable|string|max:255';
            $rules['description_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['icon_svg_' . $locale] = 'nullable|string';
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
            'remove_research_axis_cover_image' => $this->boolean('remove_research_axis_cover_image'),
        ]);

        // La génération/mise à jour du slug est gérée par le modèle ResearchAxis
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
        }
        $messages['slug.unique'] = __('Ce slug est déjà utilisé par un autre axe de recherche.');
        // ... autres messages ...
        return $messages;
    }
}
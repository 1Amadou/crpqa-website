<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StaticPageUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage static_pages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $staticPageId = $this->route('static_page')->id; // 'static_page' est le nom du paramètre de route
        
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                Rule::unique('static_pages', 'slug')->ignore($staticPageId),
            ],
            'is_published' => 'boolean',
            // 'user_id' => 'required|exists:users,id', // Si modifiable
            'static_page_cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_static_page_cover' => 'nullable|boolean',
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
            'remove_static_page_cover' => $this->boolean('remove_static_page_cover'),
        ]);

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
        }
        $messages['slug.unique'] = __('Ce slug est déjà utilisé par une autre page.');
        // ... etc.
        return $messages;
    }
}
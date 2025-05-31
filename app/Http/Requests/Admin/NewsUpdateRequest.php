<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Important pour Rule::unique

class NewsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage news');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $newsItemId = $this->route('news_item') ? $this->route('news_item')->id : ($this->route('news') ? $this->route('news')->id : null);
        // Le paramètre de route peut être 'news_item' ou 'news' selon votre définition dans web.php pour les routes admin.
        // Si vous utilisez Route::resource('news', NewsController::class), le paramètre sera 'news'.

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        $rules = [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('news', 'slug')->ignore($newsItemId),
            ],
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'remove_cover_image' => 'nullable|boolean',
        ];

        foreach ($availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['summary_' . $locale] = 'nullable|string|max:5000';
            $rules['content_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
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
            'is_featured' => $this->boolean('is_featured'),
            'remove_cover_image' => $this->boolean('remove_cover_image'),
        ]);

        // Optionnel : Si le slug est vide et que le titre principal a changé, on peut le pré-générer.
        // $primaryLocale = config('app.locale', 'fr');
        // $newsItem = $this->route('news_item') ?? $this->route('news');
        // if (empty($this->slug) && $this->has('title_' . $primaryLocale) && !empty($this->input('title_' . $primaryLocale))) {
        //     if ($newsItem && $newsItem->getTranslation('title', $primaryLocale, false) !== $this->input('title_' . $primaryLocale)) {
        //          $this->merge(['slug' => \Illuminate\Support\Str::slug($this->input('title_' . $primaryLocale))]);
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
        // Vous pouvez réutiliser les messages de NewsStoreRequest ou les définir spécifiquement ici.
        // Pour l'exemple, je vais les dupliquer, mais une meilleure approche serait de les centraliser si possible.
        $messages = [];
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        foreach ($availableLocales as $locale) {
            if ($locale === $primaryLocale) {
                $messages['title_' . $locale . '.required'] = 'Le titre (' . strtoupper($locale) . ') est obligatoire.';
                $messages['content_' . $locale . '.required'] = 'Le contenu (' . strtoupper($locale) . ') est obligatoire.';
            }
            // ... autres messages ...
        }
        $messages['slug.unique'] = 'Ce slug est déjà utilisé par une autre actualité.';
        $messages['slug.alpha_dash'] = 'Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.';
        $messages['news_category_id.exists'] = 'La catégorie sélectionnée n\'est pas valide.';
        $messages['cover_image.image'] = 'Le fichier de couverture doit être une image.';
        // ... etc.

        return $messages;
    }
}
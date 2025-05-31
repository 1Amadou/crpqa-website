<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Pour vérifier les permissions si besoin dans authorize

class NewsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Permettre la requête si l'utilisateur a la permission de gérer les actualités
        // Assurez-vous que la permission 'manage news' est bien définie
        // et assignée aux rôles appropriés (ex: Administrateur, Éditeur).
        return Auth::user()->can('manage news'); 
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
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                'alpha_dash:ascii', // Autorise les caractères alphanumériques, tirets et underscores
                'unique:news,slug'
            ],
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i', // Laravel combine automatiquement avec la date si présent
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048', // Max 2MB
            // created_by_user_id sera défini dans le contrôleur
        ];

        foreach ($availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['summary_' . $locale] = 'nullable|string|max:5000'; // Augmenté la limite pour summary
            $rules['content_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000'; // Augmenté la limite
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
        }
        
        return $rules;
    }

    /**
     * Prepare the data for validation.
     * Utile pour convertir les cases à cocher en booléens avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
        ]);

        // Optionnel : si le slug est vide, on peut le laisser vide pour que le contrôleur le génère.
        // Si vous voulez le générer ici à partir du titre principal pour la validation d'unicité :
        // $primaryLocale = config('app.locale', 'fr');
        // if (empty($this->slug) && $this->has('title_' . $primaryLocale) && !empty($this->input('title_' . $primaryLocale))) {
        //     $this->merge(['slug' => \Illuminate\Support\Str::slug($this->input('title_' . $primaryLocale))]);
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
                $messages['title_' . $locale . '.required'] = 'Le titre (' . strtoupper($locale) . ') est obligatoire.';
                $messages['content_' . $locale . '.required'] = 'Le contenu (' . strtoupper($locale) . ') est obligatoire.';
            }
            $messages['title_' . $locale . '.string'] = 'Le titre (' . strtoupper($locale) . ') doit être une chaîne de caractères.';
            $messages['title_' . $locale . '.max'] = 'Le titre (' . strtoupper($locale) . ') ne doit pas dépasser 255 caractères.';
            // Ajoutez d'autres messages personnalisés si nécessaire pour les autres champs et locales
        }
        $messages['news_category_id.exists'] = 'La catégorie sélectionnée n\'est pas valide.';
        $messages['cover_image.image'] = 'Le fichier de couverture doit être une image.';
        $messages['cover_image.mimes'] = 'Le format de l\'image de couverture doit être jpeg, png, jpg, webp ou gif.';
        $messages['cover_image.max'] = 'L\'image de couverture ne doit pas dépasser 2Mo.';
        $messages['slug.unique'] = 'Ce slug est déjà utilisé. Veuillez en choisir un autre ou le laisser vide pour une génération automatique.';
        $messages['slug.alpha_dash'] = 'Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.';

        return $messages;
    }
}
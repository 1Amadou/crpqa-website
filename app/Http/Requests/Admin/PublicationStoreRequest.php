<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App; // Pour obtenir les locales

class PublicationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ou mettez votre logique d'autorisation ici (ex: $this->user()->can('create_publications'))
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'slug' => 'required|string|max:255|unique:publications,slug',
            'publication_date' => 'required|date',
            'type' => 'nullable|string|max:255',
            'journal_conference_name' => 'nullable|string|max:255',
            'doi_url' => 'nullable|url|max:255',
            'external_url' => 'nullable|url|max:255',
            'authors_external' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'created_by_user_id' => 'required|exists:users,id', // Ou logique pour l'utilisateur connecté
            'publication_pdf' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB, PDF seulement
            // Ajoutez d'autres champs non traduits ici
        ];

        // Dynamically add rules for localized fields
        // Suppose $availableLocales = config('app.available_locales', ['fr', 'en']);
        $availableLocales = config('translatable.locales') ?: ['fr', 'en']; // Ou votre méthode pour obtenir les locales

        foreach ($availableLocales as $locale) {
            $rules['title_' . $locale] = 'required|string|max:255';
            $rules['abstract_' . $locale] = 'required|string';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        $messages = [];
        $availableLocales = config('translatable.locales') ?: ['fr', 'en'];

        foreach ($availableLocales as $locale) {
            $messages['title_' . $locale . '.required'] = 'Le titre (' . strtoupper($locale) . ') est requis.';
            $messages['title_' . $locale . '.string'] = 'Le titre (' . strtoupper($locale) . ') doit être une chaîne de caractères.';
            $messages['title_' . $locale . '.max'] = 'Le titre (' . strtoupper($locale) . ') ne doit pas dépasser 255 caractères.';
            $messages['abstract_' . $locale . '.required'] = 'Le résumé (' . strtoupper($locale) . ') est requis.';
            $messages['abstract_' . $locale . '.string'] = 'Le résumé (' . strtoupper($locale) . ') doit être une chaîne de caractères.';
        }
        $messages['publication_pdf.mimes'] = 'Le fichier doit être un PDF.';
        $messages['publication_pdf.max'] = 'Le fichier PDF ne doit pas dépasser 10MB.';
        // Ajoutez d'autres messages personnalisés
        return $messages;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
            // Assigner l'ID de l'utilisateur connecté si created_by_user_id n'est pas soumis
            // ou si vous avez une logique spécifique.
            // 'created_by_user_id' => $this->created_by_user_id ?? auth()->id(),
        ]);
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule; // Pour Rule::unique

class PublicationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ou $this->user()->can('edit_publications')
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $publicationId = $this->route('publication')->id; // Obtenir l'ID de la publication depuis la route

        $rules = [
            // Le slug doit être unique, mais ignorer la publication actuelle
            'slug' => ['required', 'string', 'max:255', Rule::unique('publications', 'slug')->ignore($publicationId)],
            'publication_date' => 'required|date',
            'type' => 'nullable|string|max:255',
            'journal_conference_name' => 'nullable|string|max:255',
            'doi_url' => 'nullable|url|max:255',
            'external_url' => 'nullable|url|max:255',
            'authors_external' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'created_by_user_id' => 'required|exists:users,id',
            'publication_pdf' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB, PDF seulement
            // Ajoutez d'autres champs non traduits ici
        ];

        $availableLocales = config('translatable.locales') ?: ['fr', 'en'];

        foreach ($availableLocales as $locale) {
            $rules['title_' . $locale] = 'required|string|max:255';
            $rules['abstract_' . $locale] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array // Identique à StoreRequest
    {
        $messages = [];
        $availableLocales = config('translatable.locales') ?: ['fr', 'en'];

        foreach ($availableLocales as $locale) {
            $messages['title_' . $locale . '.required'] = 'Le titre (' . strtoupper($locale) . ') est requis.';
            // ... autres messages de StoreRequest
            $messages['abstract_' . $locale . '.required'] = 'Le résumé (' . strtoupper($locale) . ') est requis.';
        }
        $messages['publication_pdf.mimes'] = 'Le fichier doit être un PDF.';
        // ...
        return $messages;
    }

    protected function prepareForValidation() // Identique à StoreRequest
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
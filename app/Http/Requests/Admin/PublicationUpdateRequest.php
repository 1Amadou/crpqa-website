<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // Important pour Rule::unique

class PublicationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $publicationId = $this->route('publication')->id; // Récupère l'ID de la publication en cours d'édition
        $defaultLocale = config('app.locale', 'fr');

        return [
            'title_' . $defaultLocale => 'required|string|max:255',
            // 'title_en' => 'nullable|string|max:255',
            
            'slug' => [
                'nullable',
                'string',
                'max:255',
                // Ignore l'enregistrement actuel lors de la vérification de l'unicité
                Rule::unique('publications', 'slug')->ignore($publicationId),
            ],
            'publication_date' => 'required|date',
            'type' => 'required|string|max:255',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:255',
            'issue' => 'nullable|string|max:255',
            'pages' => 'nullable|string|max:255',
            'doi_url' => 'nullable|url|max:255',
            'external_url' => 'nullable|url|max:255',
            'authors_internal_notes' => 'nullable|string',
            'authors_external' => 'nullable|string',
            'is_featured' => 'boolean',
            // 'is_published' => 'boolean',
            'created_by_user_id' => 'required|exists:users,id',
            'publication_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'remove_publication_pdf' => 'nullable|boolean', // Pour la case à cocher
            'researchers' => 'nullable|array',
            'researchers.*' => 'exists:researchers,id',

            'abstract_' . $defaultLocale => 'required|string',
            // 'abstract_en' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            // 'is_published' => $this->boolean('is_published'),
            'remove_publication_pdf' => $this->boolean('remove_publication_pdf'),
        ]);
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str; // Nécessaire si vous pré-traitez le slug ici

class PublicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ou votre logique d'autorisation
    }

    public function rules(): array
    {
        $defaultLocale = config('app.locale', 'fr');

        return [
            'title_' . $defaultLocale => 'required|string|max:255',
            // Rendre les autres titres optionnels ou requis selon votre logique
            // Exemple pour 'en' si c'est une autre locale :
            // 'title_en' => 'nullable|string|max:255',
            
            'slug' => [
                'nullable', // Rend le champ optionnel
                'string',
                'max:255',
                // L'unicité est vérifiée sur la table 'publications', colonne 'slug'
                'unique:publications,slug' 
            ],
            'publication_date' => 'required|date',
            'type' => 'required|string|max:255', // Ajustez si vous utilisez une liste prédéfinie
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
            // 'is_published' => 'boolean', // Si cette colonne existe
            'created_by_user_id' => 'required|exists:users,id',
            'publication_pdf' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'researchers' => 'nullable|array',
            'researchers.*' => 'exists:researchers,id',

            // Ajoutez les règles pour les autres champs traduits title_xx, abstract_xx
            // Exemple pour abstract (rendu requis pour la langue par défaut)
            'abstract_' . $defaultLocale => 'required|string',
            // 'abstract_en' => 'nullable|string', 
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $defaultLocale = config('app.locale', 'fr');
        // Si le slug est vide et qu'un titre pour la locale par défaut est présent,
        // on pourrait le pré-remplir ici pour la validation d'unicité si souhaité,
        // mais la logique actuelle du contrôleur le gère après validation.
        // Pour l'instant, nous laissons le contrôleur s'en charger.
        // Si vous voulez générer et valider l'unicité du slug même s'il est vide :
        /*
        if (empty($this->slug) && !empty($this->input('title_' . $defaultLocale))) {
            $slug = Str::slug($this->input('title_' . $defaultLocale));
            // La logique pour assurer l'unicité ici serait complexe car elle nécessiterait
            // de vérifier la base de données, ce qui n'est pas idéal dans prepareForValidation.
            // Il est préférable de gérer la génération finale et l'unicité dans le contrôleur.
            // $this->merge([
            //     'slug' => $slug,
            // ]);
        }
        */
        
        // S'assurer que les booléens sont bien présents dans la requête pour la validation
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            // 'is_published' => $this->boolean('is_published'), // Si la colonne existe
        ]);
    }
}
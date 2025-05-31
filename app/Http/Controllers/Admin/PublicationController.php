<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User; // Pour la liste des auteurs potentiels
use App\Http\Requests\Admin\PublicationStoreRequest; // Importer le FormRequest
use App\Http\Requests\Admin\PublicationUpdateRequest; // Importer le FormRequest
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour Str::slug si vous générez le slug automatiquement

class PublicationController extends Controller
{
    // Assurez-vous d'avoir une méthode pour obtenir les locales
    private function getAvailableLocales()
    {
        return config('translatable.locales') ?: ['fr', 'en'];
    }

    public function index()
    {
        $publications = Publication::with('createdBy')->latest()->paginate(15);
        return view('admin.publications.index', compact('publications'));
    }

    public function create()
    {
        $availableLocales = $this->getAvailableLocales();
        $users = User::pluck('name', 'id'); // Pour le champ 'created_by_user_id'
        return view('admin.publications.create', compact('availableLocales', 'users'));
    }

    public function store(PublicationStoreRequest $request) // Utiliser le FormRequest
    {
        $validatedData = $request->validated();

        // Si le slug n'est pas soumis, vous pouvez le générer à partir du titre (langue par défaut)
        if (empty($validatedData['slug'])) {
            $defaultLocale = $this->getAvailableLocales()[0];
            $validatedData['slug'] = Str::slug($validatedData['title_' . $defaultLocale]);
            // Assurez-vous que le slug généré est unique
            $originalSlug = $validatedData['slug'];
            $count = 1;
            while (Publication::where('slug', $validatedData['slug'])->exists()) {
                $validatedData['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Assurez-vous que created_by_user_id est défini (par exemple, utilisateur connecté ou depuis le formulaire)
        $validatedData['created_by_user_id'] = $validatedData['created_by_user_id'] ?? auth()->id();

        $publication = Publication::create($validatedData);

        if ($request->hasFile('publication_pdf')) {
            $publication->addMediaFromRequest('publication_pdf')->toMediaCollection('publication_pdf');
        }

        // Gestion des relations Many-to-Many (ex: chercheurs) si vous avez un champ pour cela
        // if ($request->has('researchers')) {
        //     $publication->researchers()->sync($request->input('researchers'));
        // }

        return redirect()->route('admin.publications.index')->with('success', 'Publication créée avec succès.');
    }

    public function show(Publication $publication)
    {
        $availableLocales = $this->getAvailableLocales();
        return view('admin.publications.show', compact('publication', 'availableLocales'));
    }

    public function edit(Publication $publication)
    {
        $availableLocales = $this->getAvailableLocales();
        $users = User::pluck('name', 'id');
        // Charger la relation média pour pouvoir y accéder dans la vue
        $publication->load('media');
        return view('admin.publications.edit', compact('publication', 'availableLocales', 'users'));
    }

    public function update(PublicationUpdateRequest $request, Publication $publication) // Utiliser le FormRequest
    {
        $validatedData = $request->validated();

        // Si le slug n'est pas soumis ou a changé à partir du titre (langue par défaut)
        if (empty($validatedData['slug']) || $validatedData['slug'] !== $publication->slug) {
            $defaultLocale = $this->getAvailableLocales()[0];
             // Vérifier si le titre par défaut a changé pour regénérer le slug
            if ($publication->getTranslation('title', $defaultLocale, false) !== $validatedData['title_' . $defaultLocale] || empty($validatedData['slug'])) {
                $validatedData['slug'] = Str::slug($validatedData['title_' . $defaultLocale]);
                $originalSlug = $validatedData['slug'];
                $count = 1;
                while (Publication::where('slug', $validatedData['slug'])->where('id', '!=', $publication->id)->exists()) {
                    $validatedData['slug'] = $originalSlug . '-' . $count++;
                }
            } else if (empty($validatedData['slug'])) {
                 // S'il n'y avait pas de slug soumis, mais que le titre n'a pas changé, on garde l'ancien
                 $validatedData['slug'] = $publication->slug;
            }
        }


        $publication->update($validatedData);

        if ($request->hasFile('publication_pdf')) {
            // Supprime l'ancien PDF s'il existe avant d'ajouter le nouveau
            $publication->clearMediaCollection('publication_pdf');
            $publication->addMediaFromRequest('publication_pdf')->toMediaCollection('publication_pdf');
        } elseif ($request->input('remove_publication_pdf') == 1) {
            // Si une case à cocher 'remove_publication_pdf' est envoyée et cochée
            $publication->clearMediaCollection('publication_pdf');
        }

        // if ($request->has('researchers')) {
        //     $publication->researchers()->sync($request->input('researchers'));
        // } else {
        //     $publication->researchers()->detach(); // Si aucun chercheur n'est sélectionné, détacher tous
        // }

        return redirect()->route('admin.publications.index')->with('success', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $publication)
    {
        // Spatie Media Library devrait gérer la suppression des médias associés
        // lors de la suppression du modèle si configuré pour cela (généralement par défaut).
        $publication->delete();
        return redirect()->route('admin.publications.index')->with('success', 'Publication supprimée avec succès.');
    }
}
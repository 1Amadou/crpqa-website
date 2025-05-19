<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Researcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // NÉCESSAIRE
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicationController extends Controller
{
    public function __construct()
    {
        // Appliquer automatiquement les autorisations de PublicationPolicy aux méthodes du contrôleur.
        // 'publication' est le nom du paramètre de route pour le modèle Publication (ex: /publications/{publication})
        $this->authorizeResource(Publication::class, 'publication');
    }

    private function getPublicationTypes(): array
    {
        return [
            'Journal Article' => 'Article de Revue',
            'Conference Paper' => 'Communication de Conférence',
            'Book' => 'Livre',
            'Book Chapter' => 'Chapitre de Livre',
            'Thesis' => 'Thèse',
            'Report' => 'Rapport',
            'Preprint' => 'Prépublication',
            'Other' => 'Autre',
        ];
    }

    private function validationRules(Publication $publication = null): array
    {
        $pdfRule = 'nullable|file|mimes:pdf|max:10240'; // 10MB Max

        return [
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('publications', 'slug')->ignore($publication ? $publication->id : null)],
            'researcher_ids' => 'nullable|array',
            'researcher_ids.*' => 'exists:researchers,id',
            'authors_external' => 'nullable|string|max:500',
            'abstract' => 'required|string',
            'publication_date' => 'required|date',
            'type' => ['required', 'string', Rule::in(array_keys($this->getPublicationTypes()))],
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:50',
            'issue' => 'nullable|string|max:50',
            'pages' => 'nullable|string|max:50',
            'doi_url' => 'nullable|url|max:255',
            'pdf' => $pdfRule,
            'external_url' => 'nullable|url|max:255',
            'is_featured' => 'nullable|boolean',
        ];
    }

    public function index()
    {
        // L'autorisation 'viewAny' est gérée par authorizeResource dans le constructeur
        $user = Auth::user();
        $publicationsQuery = Publication::with('researchers')->orderBy('publication_date', 'desc');

        // Si l'utilisateur est un Chercheur ET n'a PAS la permission de gérer TOUTES les publications
        if ($user->hasRole('Chercheur') && !$user->can('manage publications')) {
            if ($user->researcher) { // S'assurer que l'utilisateur est lié à un profil chercheur
                $researcherId = $user->researcher->id;
                // Filtrer les publications pour n'inclure que celles où le chercheur est un auteur
                $publicationsQuery->whereHas('researchers', function ($query) use ($researcherId) {
                    $query->where('researchers.id', $researcherId);
                });
            } else {
                // Si l'utilisateur a le rôle Chercheur mais pas de profil chercheur lié, il ne voit rien.
                $publicationsQuery->whereRaw('1 = 0'); // Condition toujours fausse
            }
        }
        // Les utilisateurs avec la permission 'manage publications' (Super Admin, Éditeur) verront toutes les publications.

        $publications = $publicationsQuery->paginate(15);
        return view('admin.publications.index', compact('publications'));
    }

    public function create()
    {
        $this->authorize('create', Publication::class); // Géré par authorizeResource si __construct est bien configuré
        $researchers = Researcher::orderBy('last_name')->orderBy('first_name')->get();
        $publicationTypes = $this->getPublicationTypes();
        
        $loggedInResearcherId = null;
        $user = Auth::user();
        if ($user->hasRole('Chercheur') && $user->researcher) {
            $loggedInResearcherId = $user->researcher->id;
        }

        return view('admin.publications.create', compact('researchers', 'publicationTypes', 'loggedInResearcherId'));
    }

    public function store(Request $request)
    {
        // L'autorisation 'create' est gérée par authorizeResource
        $validatedData = $request->validate($this->validationRules());
        $user = Auth::user();

        $publication = new Publication();
        $publication->title = $validatedData['title'];
        $publication->slug = $validatedData['slug'];
        $publication->authors_external = $validatedData['authors_external'] ?? null;
        $publication->abstract = $validatedData['abstract'];
        $publication->publication_date = $validatedData['publication_date'];
        $publication->type = $validatedData['type'];
        $publication->journal_name = $validatedData['journal_name'] ?? null;
        $publication->conference_name = $validatedData['conference_name'] ?? null;
        $publication->volume = $validatedData['volume'] ?? null;
        $publication->issue = $validatedData['issue'] ?? null;
        $publication->pages = $validatedData['pages'] ?? null;
        $publication->doi_url = $validatedData['doi_url'] ?? null;
        $publication->external_url = $validatedData['external_url'] ?? null;
        $publication->is_featured = $request->boolean('is_featured');
        $publication->created_by_user_id = $user->id; // Traçabilité de l'utilisateur système

        if ($request->hasFile('pdf')) {
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('pdf')->getClientOriginalExtension();
            $path = $request->file('pdf')->storeAs('publication_pdfs', $fileName, 'public');
            $publication->pdf_path = $path;
        }
        $publication->save();

        $researcherIdsToSync = $request->input('researcher_ids', []);

        // Si l'utilisateur créateur est un Chercheur et qu'il est lié à un profil chercheur,
        // s'assurer qu'il est ajouté comme auteur de cette publication.
        if ($user->hasRole('Chercheur') && $user->researcher) {
            if (!in_array($user->researcher->id, $researcherIdsToSync)) {
                $researcherIdsToSync[] = $user->researcher->id;
            }
        }

        if (!empty($researcherIdsToSync)) {
            $publication->researchers()->sync($researcherIdsToSync);
        } else {
            // Si aucun researcher_id n'est fourni, et que le créateur n'est pas un chercheur qui s'ajoute lui-même
            // alors on détache tous les chercheurs.
            $publication->researchers()->sync([]);
        }

        return redirect()->route('admin.publications.index')
                         ->with('success', 'Publication "' . $publication->title . '" créée avec succès.');
    }

    public function show(Publication $publication)
    {
        // L'autorisation 'view' est gérée par authorizeResource
        $publication->load('researchers', 'creator'); // Charger aussi le créateur
        $publicationTypes = $this->getPublicationTypes();
        $publicationTypeDisplay = $publicationTypes[$publication->type] ?? $publication->type;
        return view('admin.publications.show', compact('publication', 'publicationTypeDisplay'));
    }

    public function edit(Publication $publication)
    {
        // L'autorisation 'update' est gérée par authorizeResource
        $researchers = Researcher::orderBy('last_name')->orderBy('first_name')->get();
        $publicationTypes = $this->getPublicationTypes();
        $publication->load('researchers');
        return view('admin.publications.edit', compact('publication', 'researchers', 'publicationTypes'));
    }

    public function update(Request $request, Publication $publication)
    {
        // L'autorisation 'update' est gérée par authorizeResource
        $validatedData = $request->validate($this->validationRules($publication));
        $user = Auth::user(); // Utilisateur qui effectue la mise à jour

        $publication->title = $validatedData['title'];
        $publication->slug = $validatedData['slug'];
        $publication->authors_external = $validatedData['authors_external'] ?? null;
        $publication->abstract = $validatedData['abstract'];
        $publication->publication_date = $validatedData['publication_date'];
        $publication->type = $validatedData['type'];
        $publication->journal_name = $validatedData['journal_name'] ?? null;
        $publication->conference_name = $validatedData['conference_name'] ?? null;
        $publication->volume = $validatedData['volume'] ?? null;
        $publication->issue = $validatedData['issue'] ?? null;
        $publication->pages = $validatedData['pages'] ?? null;
        $publication->doi_url = $validatedData['doi_url'] ?? null;
        $publication->external_url = $validatedData['external_url'] ?? null;
        $publication->is_featured = $request->boolean('is_featured');
        // Optionnel : Mettre à jour un champ 'updated_by_user_id' si vous en avez un
        // $publication->updated_by_user_id = $user->id;


        if ($request->hasFile('pdf')) {
            if ($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path)) {
                Storage::disk('public')->delete($publication->pdf_path);
            }
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('pdf')->getClientOriginalExtension();
            $path = $request->file('pdf')->storeAs('publication_pdfs', $fileName, 'public');
            $publication->pdf_path = $path;
        } elseif ($request->boolean('remove_pdf')) {
            if ($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path)) {
                Storage::disk('public')->delete($publication->pdf_path);
            }
            $publication->pdf_path = null;
        }
        $publication->save();

        // Gérer la synchronisation des chercheurs
        // Si l'utilisateur est un chercheur qui modifie sa propre publication, la policy a déjà autorisé l'action.
        // Il peut modifier la liste des auteurs.
        // Si l'utilisateur n'est pas un chercheur (admin/éditeur), il peut aussi modifier la liste.
        if ($request->has('researcher_ids')) {
            $publication->researchers()->sync($request->input('researcher_ids'));
        } else {
            // Si le champ researcher_ids n'est pas envoyé (par exemple, toutes les cases décochées),
            // on détache tous les chercheurs.
            $publication->researchers()->sync([]);
        }

        return redirect()->route('admin.publications.index')
                         ->with('success', 'Publication "' . $publication->title . '" mise à jour avec succès.');
    }

    public function destroy(Publication $publication)
    {
        // L'autorisation 'delete' est gérée par authorizeResource
        $publicationTitle = $publication->title;

        if ($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path)) {
            Storage::disk('public')->delete($publication->pdf_path);
        }
        
        $publication->researchers()->detach(); // Détacher tous les chercheurs liés
        $publication->delete();

        return redirect()->route('admin.publications.index')
                         ->with('success', 'Publication "' . $publicationTitle . '" supprimée avec succès.');
    }
}
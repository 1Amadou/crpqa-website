<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = StaticPage::orderBy('title')->paginate(10); // Récupère toutes les pages, paginées
        return view('admin.static_pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Simplement afficher la vue du formulaire de création
        return view('admin.static_pages.create');
    }

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|alpha_dash|unique:static_pages,slug', // alpha_dash autorise lettres, chiffres, tirets, underscores
        'content' => 'required|string',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:600',
        'is_published' => 'nullable|boolean', // La case à cocher enverra '1' ou ne sera pas envoyée
    ]);

    $staticPage = new StaticPage();
    $staticPage->title = $validatedData['title'];
    $staticPage->slug = $validatedData['slug']; // Pour l'instant, nous utilisons le slug fourni par l'utilisateur.
                                              // On pourrait aussi le générer automatiquement à partir du titre.
    $staticPage->content = $validatedData['content'];
    $staticPage->meta_title = $validatedData['meta_title'] ?? $validatedData['title']; // Méta titre par défaut si vide
    $staticPage->meta_description = $validatedData['meta_description'];
    $staticPage->is_published = $request->boolean('is_published'); // Convertit la valeur de la case à cocher en booléen
    $staticPage->user_id = Auth::id(); // ID de l'utilisateur admin connecté

    $staticPage->save();

    // Redirection vers la liste des pages avec un message de succès
    return redirect()->route('admin.static-pages.index')
                     ->with('success', 'La page statique "' . $staticPage->title . '" a été créée avec succès !');
}

    /**
     * Display the specified resource.
     */
    public function show(StaticPage $staticPage)
    {
        return view('admin.static_pages.show', compact('staticPage')); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaticPage $staticPage) // Laravel injecte automatiquement l'instance de StaticPage basée sur l'ID dans l'URL
    {
        return view('admin.static_pages.edit', compact('staticPage')); // Passe la page à la vue
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaticPage $staticPage) // L'instance de la page est injectée
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            // Pour le slug, il doit être unique SAUF pour l'enregistrement actuel.
            'slug' => 'required|string|max:255|alpha_dash|unique:static_pages,slug,' . $staticPage->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:600',
            'is_published' => 'nullable|boolean',
        ]);

        $staticPage->title = $validatedData['title'];
        $staticPage->slug = $validatedData['slug'];
        $staticPage->content = $validatedData['content'];
        $staticPage->meta_title = $validatedData['meta_title'] ?? $validatedData['title'];
        $staticPage->meta_description = $validatedData['meta_description'];
        $staticPage->is_published = $request->boolean('is_published');
        $staticPage->user_id = Auth::id(); // Met à jour avec l'ID de l'utilisateur qui a fait la modification

        $staticPage->save();

        return redirect()->route('admin.static-pages.index')
                        ->with('success', 'La page statique "' . $staticPage->title . '" a été mise à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaticPage $staticPage) // Laravel injecte l'instance de la page à supprimer
    {
        $pageTitle = $staticPage->title; // Garder le titre pour le message de succès

        $staticPage->delete(); // Supprime la page de la base de données

        return redirect()->route('admin.static-pages.index')
                        ->with('success', 'La page statique "' . $pageTitle . '" a été supprimée avec succès !');
    }
}

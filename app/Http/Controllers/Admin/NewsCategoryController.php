<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Http\Requests\Admin\NewsCategoryStoreRequest;
use App\Http\Requests\Admin\NewsCategoryUpdateRequest;
use Illuminate\Support\Str; // Pour Str::slug au cas où la génération dans le FormRequest n'est pas suffisante

class NewsCategoryController extends Controller
{
    public function __construct()
    {
        // Assurez-vous que ces permissions sont bien définies dans votre RolesAndPermissionsSeeder
        $this->middleware(['permission:manage news_categories'])->except(['index', 'show']);
        $this->middleware(['permission:view news_categories'])->only(['index', 'show']);
    }

    public function index()
    {
        // Puisque 'name' n'est pas traduit, on trie simplement par 'name'
        $categories = NewsCategory::orderBy('name', 'asc')->paginate(15);
        return view('admin.news_categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new NewsCategory(['is_active' => true]); // Valeurs par défaut
        return view('admin.news_categories.create', compact('category'));
    }

    public function store(NewsCategoryStoreRequest $request)
    {
        $validatedData = $request->validated();

        // Le slug devrait être généré par prepareForValidation dans le FormRequest
        // S'il n'est pas généré ou que vous voulez assurer une dernière normalisation :
        if (empty($validatedData['slug']) && !empty($validatedData['name'])) {
            $validatedData['slug'] = Str::slug($validatedData['name']);
        } elseif (!empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['slug']);
        }
        
        // Assurer l'unicité du slug au cas où la validation unique du FormRequest ne suffirait pas (rare)
        if (NewsCategory::where('slug', $validatedData['slug'])->exists()) {
            // Gérer le cas d'un slug dupliqué si la validation unique ne l'a pas attrapé,
            // ou si la génération dans prepareForValidation n'a pas vérifié l'unicité.
            // Pour l'instant, on se fie à la validation 'unique' dans le FormRequest.
        }

        NewsCategory::create($validatedData);

        return redirect()->route('admin.news-categories.index')
                         ->with('success', __('Catégorie d\'actualité créée avec succès.'));
    }

    public function show(NewsCategory $newsCategory)
    {
        // La vue show pour une catégorie peut être simple ou ne pas exister
        // Si elle existe, elle afficherait le nom, slug, et les actualités liées.
        $newsCategory->load('newsItems'); // newsItems est la relation dans NewsCategory.php
        return view('admin.news_categories.show', compact('newsCategory'));
    }

    public function edit(NewsCategory $newsCategory)
    {
        return view('admin.news_categories.edit', compact('newsCategory'));
    }

    public function update(NewsCategoryUpdateRequest $request, NewsCategory $newsCategory)
    {
        $validatedData = $request->validated();

        // Le slug devrait être généré/validé par prepareForValidation et les règles du FormRequest
        if (empty($validatedData['slug']) && !empty($validatedData['name'])) {
            if ($newsCategory->name !== $validatedData['name'] || !$newsCategory->slug) {
                 $validatedData['slug'] = Str::slug($validatedData['name']);
            } else {
                // Si le nom n'a pas changé et que le slug soumis est vide, on garde l'ancien slug
                $validatedData['slug'] = $newsCategory->slug;
            }
        } elseif (!empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['slug']);
        }

        $newsCategory->update($validatedData);

        return redirect()->route('admin.news-categories.index')
                         ->with('success', __('Catégorie d\'actualité mise à jour avec succès.'));
    }

    public function destroy(NewsCategory $newsCategory)
    {
        // Avant de supprimer, vérifiez si des actualités sont liées à cette catégorie
        if ($newsCategory->newsItems()->count() > 0) {
            return redirect()->route('admin.news-categories.index')
                             ->with('error', __('Impossible de supprimer cette catégorie car elle est associée à des actualités. Veuillez d\'abord changer la catégorie de ces actualités.'));
        }

        $newsCategory->delete();

        return redirect()->route('admin.news-categories.index')
                         ->with('success', __('Catégorie d\'actualité supprimée avec succès.'));
    }
}
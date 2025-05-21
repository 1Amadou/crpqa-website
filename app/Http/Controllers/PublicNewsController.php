<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Inutile si MediaLibrary gère toutes les URLs

class PublicNewsController extends Controller
{
    /**
     * Affiche la liste paginée des actualités.
     */
    public function index(Request $request)
    {
        $siteSettings = app('siteSettings'); // Accès aux settings globaux si nécessaire

        $query = News::with(['category', 'user']) // Eager load
                       ->where('is_published', true)
                       ->whereNotNull('published_at')
                       ->where('published_at', '<=', now())
                       ->orderBy('published_at', 'desc');

        if ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        if ($request->filled('search_term')) {
            $searchTerm = '%' . $request->search_term . '%';
            // Pour la recherche sur champs traduits, il faut adapter selon comment Spatie Translatable stocke.
            // Si 'title' est un JSON : $query->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.app()->getLocale().'")) LIKE ?', [strtolower($searchTerm)]);
            // Ou plus simple si la recherche sur la langue par défaut suffit pour l'instant:
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm) // Adaptez pour la traduction
                  ->orWhere('short_content', 'LIKE', $searchTerm) // Adaptez pour la traduction
                  ->orWhere('content', 'LIKE', $searchTerm); // Adaptez pour la traduction
            });
        }

        $perPage = $siteSettings['news_items_per_page'] ?? 9;
        $newsItems = $query->paginate($perPage)->withQueryString();

        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->whereHas('news', function ($q) {
                $q->where('is_published', true)
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
            })
            ->withCount(['news' => function ($query) { // Compter uniquement les actualités publiées
                $query->where('is_published', true)
                      ->whereNotNull('published_at')
                      ->where('published_at', '<=', now());
            }])
            ->orderBy('name->'.app()->getLocale(), 'asc') // Tri par nom traduit
            ->get();

        return view('public.news.index', compact('newsItems', 'categories'));
    }

    /**
     * Affiche une actualité spécifique.
     */
    public function show(News $news) // Route Model Binding sur slug
    {
        // Vérifie si l'actualité est publiée ou si sa date de publication est dans le futur
        if (!$news->is_published || ($news->published_at && $news->published_at->isFuture())) {
            abort(404, 'Actualité non trouvée ou non encore publiée.');
        }

        // Charger les relations nécessaires pour la vue de détail
        $news->load(['category', 'user']);

        // --- Récupération des données pour la sidebar (Déplacé ici) ---
        $sidebarCategories = NewsCategory::where('is_active', true)
            ->whereHas('news', fn($q) => $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->withCount(['news' => fn($q) => $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())])
            ->orderBy('name->'.app()->getLocale())->get();

        $sidebarRecentNews = News::where('is_published', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->where('id', '!=', $news->id) // Exclut l'article actuel
            ->orderBy('published_at', 'desc')
            ->take(5)->get();
        // --- Fin récupération données sidebar ---


        // Logique pour les articles liés
        $relatedNewsQuery = News::with('category')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $news->id);

        if ($news->news_category_id) {
            $relatedNewsQuery->where('news_category_id', $news->news_category_id);
        }

        $relatedNews = $relatedNewsQuery->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Compléter avec des articles récents si pas assez d'articles liés dans la même catégorie
        if ($relatedNews->count() < 3) {
            $additionalNewsNeeded = 3 - $relatedNews->count();
            $recentNews = News::with('category')
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('id', '!=', $news->id)
                ->whereNotIn('id', $relatedNews->pluck('id')->toArray()) // Assure l'exclusion des déjà liés
                ->orderBy('published_at', 'desc')
                ->take($additionalNewsNeeded)
                ->get();
            $relatedNews = $relatedNews->merge($recentNews);
        }

        // Passe toutes les variables à la vue
        return view('public.news.show', compact('news', 'relatedNews', 'sidebarCategories', 'sidebarRecentNews'));
    }
}
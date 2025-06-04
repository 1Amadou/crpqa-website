<?php

namespace App\Http\Controllers;

use App\Models\News; // Assurez-vous que c'est le bon nom de modèle (News et non NewsItem)
use App\Models\NewsCategory; // Si vous voulez permettre de filtrer par catégorie
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicNewsController extends Controller
{
    /**
     * Display a listing of the published news items.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = News::where('is_published', true)
                     ->orderBy('published_at', 'desc');

        // Optionnel: Filtrage par catégorie si un slug de catégorie est passé en query string
        if ($request->has('category') && $request->category) {
            $category = NewsCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('news_category_id', $category->id);
                $categoryName = $category->name; // Sera traduit si NewsCategory utilise HasLocalizedFields
            }
        }
        
        // Optionnel: Recherche
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $currentLocale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $currentLocale) {
                $q->where('title_' . $currentLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('summary_' . $currentLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content_' . $currentLocale, 'LIKE', "%{$searchTerm}%");
            });
        }

        $newsItems = $query->with(['media', 'category', 'createdBy']) // Eager load relations
                           ->paginate(9); // Nombre d'actualités par page

        return view('public.news.index', [
            'newsItems' => $newsItems,
            'pageTitle' => isset($categoryName) ? __('Actualités de la catégorie : ') . $categoryName : __('Toutes les Actualités'),
            'currentCategory' => $category ?? null,
            'searchTerm' => $searchTerm ?? null,
        ]);
    }

    /**
     * Display the specified news item.
     *
     * @param \App\Models\News $news (Route Model Binding sur le slug)
     * @return \Illuminate\View\View
     */
    public function show(News $news) // Laravel va automatiquement résoudre par le slug si getRouteKeyName() est défini dans le modèle News
    {
        if (!$news->is_published && !(auth()->check() && auth()->user()->can('preview unpublished news'))) { // Permettre un aperçu si l'utilisateur a la permission
            abort(404);
        }

        $news->load(['media', 'category', 'createdBy']); // Charger les relations

        // Pour les méta-tags SEO
        // Le trait HasLocalizedFields s'occupe de la traduction automatique pour $news->title, $news->meta_title, etc.
        $metaTitle = $news->meta_title ?: $news->title;
        $metaDescription = $news->meta_description ?: Str::limit(strip_tags($news->summary ?: $news->content), 160);
        $ogImage = $news->cover_image_url ?: ($siteSettings->default_og_image_url ?? null); // $siteSettings est global

        // Optionnel: Récupérer des actualités similaires ou récentes
        $relatedNews = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->when($news->news_category_id, function ($query) use ($news) {
                return $query->where('news_category_id', $news->news_category_id);
            })
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('public.news.show', compact('news', 'metaTitle', 'metaDescription', 'ogImage', 'relatedNews'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Inutile si MediaLibrary gère toutes les URLs
use App\Models\NewsItem;
use Illuminate\Support\Facades\DB;

class PublicNewsController extends Controller
{
    /**
     * Affiche la liste paginée des actualités.
     */
    public function index(Request $request)
    {
        $pageTitle = "Actualités du CRPQA";

        $query = NewsItem::query()
            ->where('is_published', true)
            ->where('published_at', '<=', now());

        if ($searchTerm = $request->input('search')) {
            $query->where(function ($q) use ($searchTerm) {
                // Adaptez les champs de recherche si vous n'utilisez pas de localisation via trait
                $q->where('title_fr', 'like', "%{$searchTerm}%")
                  ->orWhere('content_fr', 'like', "%{$searchTerm}%");
                // Ajoutez _en si vous avez ces champs et voulez chercher dedans
            });
        }
        
        $categorySlug = $request->input('category');
        if ($categorySlug) {
            // On cherche la catégorie par son slug pour obtenir son ID
            $categoryModel = NewsCategory::where('slug', $categorySlug)->first();
            if ($categoryModel) {
                $query->where('news_category_id', $categoryModel->id); // Filtrer par news_category_id
            } else {
                // Optionnel : si la catégorie n'existe pas, ne retourner aucune actualité
                // ou ignorer le filtre de catégorie. Ici, on ne retourne rien pour cette cat.
                $query->whereRaw('1 = 0'); 
            }
        }

        $sortOrder = $request->input('sort', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        $query->orderBy('published_at', $sortOrder);

        $newsItems = $query->paginate(10)->withQueryString();

        // Pour la sidebar : Liste des catégories
        // La relation newsItems() dans NewsCategory est maintenant hasMany.
        $categories = NewsCategory::where('is_active', true) // Seulement les catégories actives
            ->withCount(['newsItems' => function ($query) {
                $query->where('is_published', true)->where('published_at', '<=', now());
            }])
            ->orderBy('name') // Si 'name' est votre champ principal non localisé, sinon 'name_fr'
            ->get();
        
        $categories = $categories->filter(function ($category) {
            return $category->news_items_count > 0;
        });

        $archives = NewsItem::select(
                DB::raw('YEAR(published_at) as year'),
                DB::raw('MONTH(published_at) as month_number'),
                DB::raw('COUNT(*) as count')
            )
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->groupBy('year', 'month_number')
            ->orderBy('year', 'desc')
            ->orderBy('month_number', 'desc')
            ->get()
            ->map(function ($item) {
                $date = \Carbon\Carbon::createFromDate($item->year, $item->month_number, 1);
                // Assurez-vous que la locale est bien configurée pour translatedFormat
                $item->month_name_fr = ucfirst($date->translatedFormat('F')); 
                return $item;
            });

        return view('public.news.index', compact(
            'pageTitle', 
            'newsItems', 
            'categories', 
            'archives',
            'searchTerm',
            'categorySlug', // Pour présélectionner dans le formulaire
            'sortOrder'
        ));
    }

    /**
     * Affiche le détail d'une actualité.
     *
     * @param  \App\Models\NewsItem  $newsItem (grâce au route model binding sur le slug)
     * @return \Illuminate\View\View
     */
    public function show(NewsItem $newsItem) // Laravel injecte l'instance de NewsItem trouvée par son slug
    {
        // dd($newsItem, $newsItem->slug); 
        // Vérifier si l'actualité est publiée et si sa date de publication n'est pas dans le futur
        // Sauf si un admin est connecté, auquel cas il peut voir les brouillons (logique à ajouter si besoin)
    //     if (!$newsItem->is_published || ($newsItem->published_at && $newsItem->published_at->isFuture())) {
    //     // dd('Article non publié ou date future', $newsItem->is_published, $newsItem->published_at); // Débogage conditionnel
    //     abort(404); 
    // }

        $pageTitle = $newsItem->getTranslation('title', app()->getLocale()); // Titre de la page basé sur l'actualité

        // Récupérer les actualités similaires/récentes
        // Par exemple, 3 actualités de la même catégorie (si la catégorie existe), excluant l'actuelle
        $similarNews = collect(); // Initialise une collection vide

        if ($newsItem->category) { // Si l'actualité a une catégorie
            $similarNews = NewsItem::where('news_category_id', $newsItem->news_category_id)
                ->where('id', '!=', $newsItem->id) // Exclure l'actualité actuelle
                ->where('is_published', true)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
        }
        
        // S'il n'y a pas assez d'actualités similaires dans la même catégorie (ou si pas de catégorie),
        // compléter avec les actualités les plus récentes toutes catégories confondues.
        if ($similarNews->count() < 3) {
            $needed = 3 - $similarNews->count();
            $recentNews = NewsItem::where('id', '!=', $newsItem->id) // Exclure l'actuelle
                ->whereNotIn('id', $similarNews->pluck('id')->all()) // Exclure celles déjà trouvées
                ->where('is_published', true)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->take($needed)
                ->get();
            $similarNews = $similarNews->merge($recentNews);
        }

        // Boutons de partage (URLs de base)
        if ($newsItem) { 
    // Assurez-vous que $pageTitle est défini avant d'être utilisé ici, 
    // si ce n'est pas déjà fait plus haut dans la méthode.
    // $pageTitle = $newsItem->getTranslation('title', app()->getLocale()); // Si pas déjà défini

    $pageTitle = $newsItem->getTranslation('title', app()->getLocale()); 

$shareLinks = [
    'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode(route('public.news.show', ['news' => $newsItem->slug])),
    'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode(route('public.news.show', ['news' => $newsItem->slug])) . '&text=' . urlencode($pageTitle ?? ''),
    'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(route('public.news.show', ['news' => $newsItem->slug])) . '&title=' . urlencode($pageTitle ?? ''),
];

} else {
    // Cette partie ne devrait normalement pas être atteinte si le route model binding
    // pour $newsItem fonctionne, car Laravel lèverait un 404 avant.
    // Cependant, si $newsItem pouvait être null d'une autre manière :
    return abort(404, 'Article introuvable');
}   


        return view('public.news.show', compact('pageTitle', 'newsItem', 'similarNews', 'shareLinks'));
    }
}
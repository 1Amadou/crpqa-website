<?php

namespace App\Http\Controllers;

use App\Models\News; // Assurez-vous que le modèle News est bien ici
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    /**
     * Affiche la liste des actualités publiées.
     */
    public function index()
    {
        $newsItems = News::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->orderBy('published_at', 'desc')
                            ->paginate(10); // Ou le nombre d'éléments que vous souhaitez par page

        return view('public.news.index', compact('newsItems')); // Nous créerons cette vue
    }

    /**
     * Affiche une actualité spécifique par son slug.
     */
    public function show($slug) // On pourrait utiliser le Route Model Binding : public function show(News $news)
    {
        $newsItem = News::where('slug', $slug)
                         ->whereNotNull('published_at')
                         ->where('published_at', '<=', now())
                         ->firstOrFail(); // firstOrFail() lèvera une erreur 404 si non trouvée ou non publiée

        // Passer aussi les méta-données SEO si elles existent
        $metaTitle = $newsItem->meta_title ?: $newsItem->title;
        $metaDescription = $newsItem->meta_description ?: Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 160);


        return view('public.news.show', compact('newsItem', 'metaTitle', 'metaDescription')); // Nous créerons cette vue
    }
}
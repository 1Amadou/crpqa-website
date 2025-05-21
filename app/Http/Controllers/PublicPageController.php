<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaticPage;
use App\Models\News; // <== Assure-toi que ce modèle est bien importé
use App\Models\Event;
use App\Models\Publication;
use App\Models\Partner;
use App\Models\Researcher;
use App\Models\ResearchAxis;
use Illuminate\Support\Facades\Log;

class PublicPageController extends Controller
{
    /**
     * Affiche la page d'accueil.
     */
    public function home()
    {
        $latestNews = News::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->orderBy('published_at', 'desc')
                            ->take(3)
                            ->get();

        $upcomingEvents = Event::whereNotNull('start_datetime')
                               ->where('start_datetime', '>=', now())
                               ->orderBy('start_datetime', 'asc')
                               ->take(3)
                               ->get();

        $featuredPublications = Publication::where('is_featured', true)
                                            ->orderBy('publication_date', 'desc')
                                            ->take(3)
                                            ->get();

        if ($featuredPublications->isEmpty() && Publication::count() > 0) {
            $featuredPublications = Publication::orderBy('publication_date', 'desc')
                                                    ->take(3)
                                                    ->get();
        }

        $keyResearchAxes = ResearchAxis::orderBy('display_order', 'asc')
                                        ->take(4)
                                        ->get();

        return view('public.home', compact(
            'latestNews',
            'upcomingEvents',
            'featuredPublications',
            'keyResearchAxes'
        ));
    }

    /**
     * Affiche la page "À Propos" dédiée.
     */
    public function about()
    {
        $aboutPage = StaticPage::where('slug', 'a-propos')
                               ->firstOrFail();

        return view('public.about', ['page' => $aboutPage]);
    }

    /**
     * Affiche une page statique via route-model binding.
     *
     * @param  StaticPage  $staticPage
     */
    public function showStaticPage(StaticPage $staticPage)
    {
        return view('public.static-page', ['page' => $staticPage]);
    }

    /**
     * Affiche les autres pages statiques génériques basées sur slug.
     *
     * @param  string  $slug
     */
    public function showOtherStaticPage($slug)
    {
        try {
            $staticPage = StaticPage::where('slug', $slug)->firstOrFail();
            return view('public.static-page', ['page' => $staticPage]);
        } catch (\Exception $e) {
            Log::error("Erreur StaticPage slug={$slug}: {$e->getMessage()}");
            abort(404);
        }
    }

    // === AJOUTE LA MÉTHODE SUIVANTE ICI ===
    /**
     * Affiche un article d'actualité spécifique.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showNewsDetail(string $slug)
    {
        $newsItem = News::where('slug', $slug)
                        ->where('is_published', true) // Assure-toi qu'il est publié
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now())
                        ->with('category', 'user') // Charge les relations pour afficher les infos liées
                        ->firstOrFail(); // Va lancer une 404 si non trouvé

        // Optionnel : Si tu as un champ 'views_count' ou similaire, tu peux l'incrémenter
        // $newsItem->increment('views_count');

        return view('public.news_detail', compact('newsItem'));
    }
}
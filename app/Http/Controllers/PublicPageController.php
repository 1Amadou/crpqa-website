<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaticPage;
use App\Models\News;
use App\Models\Event;
use App\Models\Publication;
use App\Models\Partner;
use App\Models\Researcher;
use Carbon\Carbon;

class PublicPageController extends Controller
{
    public function home()
    {
        // Nouvelles publiées et disponibles
        $latestNews = News::whereNotNull('published_at')
                          ->where('published_at', '<=', now())
                          ->orderBy('published_at', 'desc')
                          ->take(2)
                          ->get();

        // Événements à venir avec date valide
        $upcomingEvents = Event::whereNotNull('start_datetime')
                               ->where('start_datetime', '>=', now())
                               ->orderBy('start_datetime', 'asc')
                               ->take(2)
                               ->get();

        // Publications vedettes ou fallback
        $featuredPublications = Publication::where('is_featured', true)
                                           ->orderBy('publication_date', 'desc')
                                           ->take(3)
                                           ->get();

        if ($featuredPublications->isEmpty() && Publication::count() > 0) {
            $featuredPublications = Publication::orderBy('publication_date', 'desc')
                                               ->take(3)
                                               ->get();
        }

        // Partenaires actifs
        $activePartners = Partner::where('is_active', true)
                                 ->orderBy('display_order', 'asc')
                                 ->take(5)
                                 ->get();

        // Chercheurs en vedette (temporairement aléatoire)
        $featuredResearchers = Researcher::where('is_active', true)
                                         ->inRandomOrder()
                                         ->take(3)
                                         ->get();

        // Message d'accueil (si non administrable via siteSettings)
        $welcomeMessage = "Bienvenue au Centre de Recherche pour la Promotion de la Qualité en Afrique."; // À adapter

        return view('public.home', compact(
            'latestNews',
            'upcomingEvents',
            'featuredPublications',
            'activePartners',
            'featuredResearchers',
            'welcomeMessage'
        ));
    }

    public function showStaticPage($slug)
    {
        $page = StaticPage::where('slug', $slug)
                          ->where('is_published', true)
                          ->firstOrFail();

        // Page spécifique "À propos"
        if ($page->slug === 'a-propos-crpqa') {
            return view('public.about', compact('page'));
        }

        // Autres pages statiques
        return view('public.static-page', compact('page'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\StaticPage; // Importer le modèle
use Illuminate\Http\Request;
use App\Models\News; // << AJOUTEZ CETTE LIGNE SI VOTRE MODÈLE EST App\Models\News
use App\Models\Event;
use App\Models\Publication;
use App\Models\Partner; 
use App\Models\Researcher;
// use App\Models\Researcher;
use Carbon\Carbon;

class PublicPageController extends Controller
{
    public function home()
{
    $latestNews = News::whereNotNull('published_at')
                        ->where('published_at', '<=', now())
                        ->orderBy('published_at', 'desc')
                        ->take(2) // Réduit à 2 pour l'accueil
                        ->get();

    $upcomingEvents = Event::where('start_datetime', '>=', now())
                           ->orderBy('start_datetime', 'asc')
                           ->take(2) // Réduit à 2 pour l'accueil
                           ->get();

    $featuredPublications = Publication::where('is_featured', true)
                                        ->orderBy('publication_date', 'desc')
                                        ->take(3)
                                        ->get();
    if ($featuredPublications->isEmpty() && Publication::count() > 0) { // S'assurer qu'il y a des publications avant de prendre les dernières
        $featuredPublications = Publication::orderBy('publication_date', 'desc')
                                          ->take(3)
                                          ->get();
    }

    $activePartners = Partner::where('is_active', true)
                             ->orderBy('display_order', 'asc')
                             ->take(5) // Afficher 5 partenaires max
                             ->get();

    // TODO: Implémenter la logique pour $featuredResearchers (ex: un champ 'is_homepage_featured' dans la table researchers)
    // Pour l'instant, on peut passer un tableau vide ou quelques chercheurs manuellement pour le design.
    $featuredResearchers = Researcher::where('is_active', true)
                                     // ->where('is_homepage_featured', true) // Exemple de champ
                                     ->inRandomOrder() // Juste pour l'exemple, à remplacer par une vraie logique
                                     ->take(3)
                                     ->get();

    // Les textes éditoriaux de l'accueil peuvent être récupérés des $siteSettings
    // ou être mis en dur dans la vue pour l'instant si non administrables.
    // J'ai utilisé $siteSettings->homepage_hero_title etc. dans la vue.
    // Assurez-vous que votre middleware ShareSiteSettings passe bien $siteSettings.

    $welcomeMessage = "Bienvenue..."; // Adaptez comme avant

    return view('public.home', compact(
        'latestNews',
        'upcomingEvents',
        'featuredPublications',
        'activePartners',
        'featuredResearchers', // Passez cette variable
        'welcomeMessage' // Si vous l'utilisez toujours
        // $siteSettings est déjà disponible globalement via le middleware
    ));
}

    public function showStaticPage($slug)
{
    $page = StaticPage::where('slug', $slug)->where('is_published', true)->firstOrFail();

    // Pour la page "À Propos", nous utilisons une vue dédiée
    if ($page->slug === 'a-propos-crpqa') { // Ou le slug que vous avez choisi
        // Vous pourriez passer des données supplémentaires spécifiques à "À Propos" ici si nécessaire
        // $featuredResearchersForAbout = ...;
        return view('public.about', compact('page'/*, 'featuredResearchersForAbout'*/));
    }

    // Pour les autres pages statiques plus simples
    return view('public.static-page', compact('page'));
}
}
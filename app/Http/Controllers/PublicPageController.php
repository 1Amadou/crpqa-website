<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\News;
use App\Models\Publication;
use App\Models\ResearchAxis;
use App\Models\StaticPage;
use App\Models\Researcher; // AJOUTÉ : Pour récupérer les chercheurs
use App\Models\Partner;    // AJOUTÉ : Pour récupérer les partenaires
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Conservé pour getLocale()
use Illuminate\Support\Facades\View; // Si vous partagez des données globalement ici

class PublicPageController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $locale = App::getLocale();

        // Actualités Récentes
        $latestNews = News::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(3) // Nombre d'actualités à afficher sur l'accueil
            ->with('media', 'category') // Charger les médias et la catégorie
            ->get();

        // Événements à Venir
        $upcomingEvents = Event::where('start_datetime', '>=', now()) // Événements dont la date de début est future ou aujourd'hui
            // ->where('is_published', true) // Si vous ajoutez un statut de publication aux événements
            ->orderBy('start_datetime', 'asc')
            ->take(3) // Nombre d'événements à afficher
            ->with('media') // Charger l'image de couverture si nécessaire
            ->get();

        // Axes de Recherche Clés (en vedette ou selon un ordre)
        $featuredResearchAxes = ResearchAxis::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->take(4) // Nombre d'axes à afficher
            ->with('media') // Charger l'image de couverture si nécessaire
            ->get();

        // Publications Récentes/En Vedette
        $featuredPublications = Publication::where('is_featured', true)
            // ->where('is_published', true) // Si vous ajoutez un statut de publication aux publications
            ->orderBy('publication_date', 'desc')
            ->take(4) // Nombre de publications à afficher
            ->with(['media', 'researchers']) // Charger PDF et chercheurs
            ->get();

        // Aperçu de l'Équipe (Chercheurs en vedette ou aléatoires)
        $keyTeamMembers = Researcher::where('is_active', true)
            // ->where('is_home_featured', true) // Si vous ajoutez un champ pour les mettre en avant sur l'accueil
            ->orderBy('display_order', 'asc') // Ou inRandomOrder()
            ->take(4) // Nombre de chercheurs à afficher
            ->with('media') // Charger la photo
            ->get();
            
        // Partenaires Actifs (pour un carrousel/défilement)
        $activePartners = Partner::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('name_' . $locale, 'asc') // Tri par nom localisé
            ->take(10) // Ou le nombre que vous voulez pour le défilement
            ->with('media') // Charger le logo
            ->get();

        // Section "À propos" depuis une page statique
        // La variable $siteSettings est injectée globalement via ShareSiteSettings middleware.
        // Si vous avez un slug spécifique pour la section "À propos" de l'accueil dans $siteSettings:
        $aboutPageSlug = $siteSettings['about_home_page_slug'] ?? 'a-propos-accueil'; // Clé à définir dans les settings
        $aboutSectionPage = StaticPage::where('slug', $aboutPageSlug)
                                ->where('is_published', true)
                                ->first();
        // Ou si le contenu est directement dans les SiteSettings:
        // $aboutSectionContent = $siteSettings->about_home_short_description; (sera traduit par le trait)

        // Le contenu pour "Appel à collaboration" est déjà dans $siteSettings si vous l'avez ajouté là.

        // Les témoignages et chiffres clés sont aussi dans $siteSettings (format JSON)
        $testimonials = isset($siteSettings['home_testimonials']) && is_string($siteSettings['home_testimonials']) 
                        ? json_decode($siteSettings['home_testimonials'], true) 
                        : ($siteSettings['home_testimonials'] ?? []);
        if (!is_array($testimonials)) $testimonials = [];

        $keyFigures = isset($siteSettings['home_key_figures']) && is_string($siteSettings['home_key_figures'])
                        ? json_decode($siteSettings['home_key_figures'], true)
                        : ($siteSettings['home_key_figures'] ?? []);
        if (!is_array($keyFigures)) $keyFigures = [];


        return view('public.home', compact(
            'latestNews',
            'upcomingEvents',
            'featuredResearchAxes',
            'featuredPublications',
            'keyTeamMembers',         // Données pour l'équipe
            'activePartners',         // Données pour les partenaires
            'aboutSectionPage',       // Ou 'aboutSectionContent' si vous préférez passer le texte directement
            'testimonials',
            'keyFigures'
            // $siteSettings est déjà disponible globalement
        ));
    }

    /**
     * Display the about page (page "À Propos" dédiée).
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        // La variable $siteSettings est disponible globalement.
        $aboutPageSlug = $siteSettings['about_page_slug'] ?? 'a-propos'; // Clé à définir dans les settings

        $page = StaticPage::where('slug', $aboutPageSlug)
                          ->where('is_published', true)
                          ->firstOrFail();
        
        // Les traductions sont gérées par le trait HasLocalizedFields sur le modèle
        // et seront accessibles directement dans la vue via $page->title, $page->content, etc.
        // $metaTitle = $page->meta_title ?: $page->title;
        // $metaDescription = $page->meta_description ?: Str::limit(strip_tags($page->content), 160);

        return view('public.about', compact('page' /*, 'metaTitle', 'metaDescription'*/));
    }

    /**
     * Display a generic static page.
     *
     * @param \App\Models\StaticPage $staticPage // Route Model Binding sur le slug
     * @return \Illuminate\View\View
     */
    public function showStaticPage(StaticPage $staticPage) // Renommé pour clarté et utilisation du RBM
    {
        if (!$staticPage->is_published) {
            abort(404);
        }
        
        // Les traductions sont gérées par le trait HasLocalizedFields sur le modèle
        // $metaTitle = $staticPage->meta_title ?: $staticPage->title;
        // $metaDescription = $staticPage->meta_description ?: Str::limit(strip_tags($staticPage->content), 160);
        
        // Déterminer si une vue spécifique existe pour ce slug
        if (View::exists("public.pages.{$staticPage->slug}")) {
            return view("public.pages.{$staticPage->slug}", compact('staticPage'/*, 'metaTitle', 'metaDescription'*/));
        }

        // Sinon, utiliser une vue générique pour les pages statiques
        return view('public.static-page', compact('staticPage'/*, 'metaTitle', 'metaDescription'*/));
    }
    
    /**
     * Display the partners page.
     */
    public function partners() // Ajouté pour la page /partenaires
    {
        $primaryLocale = app()->getLocale();
        $partners = Partner::where('is_active', true)
                           ->orderBy('display_order', 'asc')
                           ->orderBy('name_' . $primaryLocale, 'asc')
                           ->paginate(12); // Ou get() si pas de pagination

        return view('public.partners.index', compact('partners'));
    }
}
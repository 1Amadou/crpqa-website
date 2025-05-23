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
use App\Models\SiteSetting;
use App\Models\Contact;
use App\Models\ResearcherPublication;

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
    $pageTitle = "À Propos du CRPQA";

    // Textes extraits et adaptés du document CRPQA
    $crpqaInfo = [
        'preambule_titre' => "Préambule : L'Essence de la Physique Quantique",
        'preambule_contenu' => "La physique quantique, souvent appelée Mécanique Quantique ou Physique Théorique, est la science des particules élémentaires. Elle a révolutionné notre compréhension de la matière au début du 20e siècle, surpassant la physique classique en expliquant des phénomènes jusqu'alors paradoxaux comme la stabilité de l'atome ou les chaleurs spécifiques des gaz. Erwin Schrödinger a même relié ses fondements aux philosophes atomistes grecs, marquant 2400 ans de quête pour comprendre la structure intime de la matière. Aujourd'hui, la théorie quantique est le cadre conceptuel le plus riche pour explorer les composants ultimes de l'univers et leurs interactions, transformant ainsi la physique, la chimie et interpellant la philosophie.",

        'historique_titre' => "Notre Histoire et la Genèse du CRPQA",
        'historique_contenu' => "L'enseignement de la Physique Quantique a débuté à l'École Normale Supérieure de Bamako en octobre 1970, un enseignement qui se perpétue et se développe au sein de l'Université malienne depuis plus de cinquante-cinq ans. Conscient de l'impératif de ne pas rester en marge de la révolution quantique mondiale – illustrée par des avancées majeures en communication sécurisée, calcul intensif et technologies de mesure de haute précision – et de la nécessité de développer les ressources humaines qualifiées, le gouvernement malien a soutenu la formation de jeunes chercheurs. C'est dans ce contexte, et pour doter l'Université du Mali d'un outil stratégique, que le Consortium de Recherche en Physique Quantique et de ses Applications (CRPQA) a été envisagé, avec des figures de proue comme le Dr. Kaniba Mady KEITA. Car, en vérité, l'avenir est quantique.",

        'mission_titre' => "Notre Mission",
        'mission_contenu' => "La mission du CRPQA est de promouvoir l'excellence dans la recherche fondamentale et appliquée en physique quantique et ses multiples applications. Nous nous engageons à former une nouvelle génération de scientifiques, chercheurs et ingénieurs maliens de haut niveau, capables de relever les défis scientifiques et technologiques contemporains. Le centre vise également à contribuer activement au développement socio-économique du Mali par l'innovation, la valorisation de la recherche et le transfert de technologies issues des sciences quantiques.",

        'vision_titre' => "Notre Vision",
        'vision_contenu' => "Le CRPQA aspire à devenir un pôle d'excellence en physique quantique, jouissant d'une reconnaissance nationale, régionale et internationale. Nous ambitionnons d'être un centre attractif pour les meilleurs talents, un lieu de collaborations scientifiques fructueuses et un moteur d'innovation, transformant les découvertes issues de nos laboratoires en solutions concrètes et bénéfiques pour la société malienne et africaine.",

        'valeurs_titre' => "Nos Valeurs Fondamentales",
        'valeurs_liste' => [
            "Rigueur scientifique et Intégrité éthique",
            "Innovation, Créativité et Pensée critique",
            "Collaboration multidisciplinaire et Partage ouvert des connaissances",
            "Formation d'excellence et Encadrement de la relève scientifique",
            "Pertinence nationale et Impact sociétal positif"
        ],

        // Les sections suivantes seront ajoutées au fur et à mesure
        'structure_titre' => "Structure et Organisation",
        'structure_contenu' => "Informations à venir sur l'organisation interne du CRPQA, ses départements et laboratoires de recherche.",

        'directeur_titre' => "Message du Directeur",
        'directeur_nom' => "Dr. Kaniba Mady KEITA (Exemple)", // À rendre dynamique ou à confirmer
        'directeur_photo_exemple' => "img/placeholders/director.jpeg", // Placeholder, à remplacer par une vraie image ou une donnée de l'admin
        'directeur_message' => "C'est avec un immense honneur et une grande conviction que nous portons le projet du CRPQA. Notre ambition est de bâtir un centre vibrant, au service de la science et du développement de notre nation. Nous invitons toutes les bonnes volontés à se joindre à cette aventure passionnante. [Message à compléter]",

        'gouvernance_titre' => "Gouvernance",
        'gouvernance_contenu' => "Le CRPQA est doté d'un Conseil Scientifique et d'un Conseil d'Administration dont la composition et les rôles seront détaillés prochainement.",

        'infrastructure_titre' => "Infrastructure et Moyens",
        'infrastructure_contenu' => "Le centre s'équipera progressivement d'infrastructures et de moyens techniques permettant de mener des recherches de pointe. [Détails à venir]",
        'usttb_fst_titre' => "Notre Ancrage Académique : USTTB et FST",
            'usttb_fst_contenu' => "Le Centre de Recherche en Physique Quantique et de ses Applications (CRPQA) est fier d'être abrité au sein de la prestigieuse Faculté des Sciences et Techniques (FST) de l'Université des Sciences, des Techniques et des Technologies de Bamako (USTTB). Cette affiliation stratégique ancre solidement le CRPQA dans le paysage académique malien et favorise une synergie essentielle entre recherche de pointe et enseignement supérieur. La majorité de nos chercheurs sont d'éminents professeurs et membres du corps enseignant de l'USTTB, garantissant ainsi une transmission directe des savoirs et une implication forte des étudiants dans les activités de recherche.",
            'usttb_logo_placeholder' => "img/placeholders/usttb_logo." // Placeholder pour le logo USTTB
    ];

    // Récupérer quelques chercheurs SANS les filtres 'is_active' et 'display_order' pour l'instant
    $featuredResearchers = Researcher::take(4)->get();

    // Récupérer quelques partenaires SANS les filtres 'is_visible' et 'display_order' pour l'instant
    // Mais on garde le filtre pour s'assurer qu'il y a un logo
    $featuredPartners = Partner::whereNotNull('logo_path')->take(8)->get();

    return view('public.about', compact('pageTitle', 'crpqaInfo', 'featuredResearchers', 'featuredPartners'));
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
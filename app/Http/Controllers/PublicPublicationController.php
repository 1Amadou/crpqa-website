<?php

namespace App\Http\Controllers;

use App\Models\Publication; // Assurez-vous que le modèle Publication est bien ici
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour Str::limit

class PublicPublicationController extends Controller
{
    /**
     * Affiche la liste des publications.
     */
    public function index()
    {
        $publications = Publication::orderBy('publication_date', 'desc')
                                   // ->where('is_published', true) // Si vous avez un tel champ
                                   ->paginate(10); // Ou le nombre souhaité

        // Vous pourriez vouloir passer aussi les types de publication pour des filtres, etc.
        return view('public.publications.index', compact('publications')); // Vue à créer
    }

    /**
     * Affiche une publication spécifique par son slug.
     */
    public function show($slug) // Ou public function show(Publication $publication) avec Route Model Binding
    {
        $publication = Publication::where('slug', $slug)
                                // ->where('is_published', true) // Si vous avez un tel champ
                                ->with('researchers') // Charger les auteurs chercheurs
                                ->firstOrFail();

        // Pour les méta-données SEO (simple exemple, vous pouvez l'affiner)
        // La table 'publications' n'a pas de champs meta_title/meta_description dédiés pour l'instant
        // d'après le schéma que j'ai. Nous pourrions les ajouter plus tard si besoin.
        $metaTitle = $publication->title;
        $metaDescription = Str::limit(strip_tags($publication->abstract), 160);

        // Pour afficher le type de publication de manière lisible
        // Vous aviez une méthode getPublicationTypes() dans Admin/PublicationController.
        // Il faudrait une manière d'y accéder ici aussi (helper, trait, service, ou dupliquer la logique).
        // Pour l'instant, on affiche la clé brute :
        $publicationTypeDisplay = $publication->type;
        // Idéalement : $publicationTypeDisplay = $this->getPublicationTypesArray()[$publication->type] ?? $publication->type;

        return view('public.publications.show', compact('publication', 'metaTitle', 'metaDescription', 'publicationTypeDisplay')); // Vue à créer
    }

    // Optionnel: Si vous aviez une méthode pour les types dans le contrôleur admin
    // private function getPublicationTypesArray(): array
    // {
    //     return [
    //         'Journal Article' => 'Article de Revue',
    //         'Conference Paper' => 'Communication de Conférence',
    //         // ... autres types ...
    //     ];
    // }
}
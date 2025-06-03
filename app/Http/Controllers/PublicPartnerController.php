<?php

namespace App\Http\Controllers;

use App\Models\Partner; // Assurez-vous que le modèle Partner est bien ici
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour Str::limit si utilisé

class PublicPartnerController extends Controller
{
    /**
     * Affiche la liste des partenaires actifs.
     */
    public function index()
    {
        $primaryLocale = app()->getLocale();
        $partners = Partner::where('is_active', true)
                           ->orderBy('display_order', 'asc')
                           ->orderBy('name_' . $primaryLocale, 'asc') // Tri par nom localisé
                           ->paginate(15); // Ou ->get() si vous ne voulez pas de pagination

        // Vous pourriez vouloir grouper par type ici si votre vue index le gère
        // Exemple: $groupedPartners = $partners->groupBy('type');

        return view('public.partners.index', compact('partners'));
    }

    /**
     * Affiche un partenaire spécifique par son slug (si vous avez une page de détail).
     * Assurez-vous que votre modèle Partner utilise getRouteKeyName() pour retourner 'slug'
     * et que la table 'partners' a une colonne 'slug' unique.
     */
    public function show(Partner $partner) // Utilisation du Route Model Binding (nécessite un champ slug)
    {
        if (!$partner->is_active) {
            abort(404); // Ne pas montrer les partenaires inactifs
        }

        // Charger les relations nécessaires si vous en avez (ex: événements associés)
        // $partner->load('events');

        // Pour les méta-données SEO
        // Le trait HasLocalizedFields devrait gérer l'affichage de $partner->name et $partner->description
        // dans la langue courante directement dans la vue.
        $metaTitle = $partner->getTranslation('name', app()->getLocale(), false);
        $metaDescription = Str::limit(strip_tags($partner->getTranslation('description', app()->getLocale(), false)), 160);

        return view('public.partners.show', compact('partner', 'metaTitle', 'metaDescription'));
    }
}
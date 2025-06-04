<?php

namespace App\Http\Controllers;

use App\Models\ResearchAxis;
// Importer d'autres modèles si vous voulez lister des chercheurs/projets liés sur la page de détail de l'axe
// use App\Models\Researcher;
// use App\Models\Project; // Si vous avez un modèle Project
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicResearchAxisController extends Controller
{
    /**
     * Display a listing of the active research axes.
     */
    public function index(Request $request)
    {
        $primaryLocale = app()->getLocale();
        $searchTerm = $request->input('search');

        $query = ResearchAxis::where('is_active', true)
                             ->orderBy('display_order', 'asc')
                             ->orderBy('name_' . $primaryLocale, 'asc')
                             ->with('media'); // Eager load la cover_image et l'icon_svg si géré par Spatie

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm, $primaryLocale) {
                $q->where('name_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('subtitle_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description_' . $primaryLocale, 'LIKE', "%{$searchTerm}%");
            });
        }

        $researchAxes = $query->paginate(9)->appends($request->query()); // 9 axes par page par exemple

        $pageTitle = __('Nos Axes de Recherche');
        if($searchTerm) {
            $pageTitle = __('Résultats de recherche pour :term dans les Axes de Recherche', ['term' => $searchTerm]);
        }

        return view('public.research_axes.index', compact('researchAxes', 'pageTitle', 'searchTerm'));
    }

    /**
     * Display the specified research axis.
     */
    public function show(ResearchAxis $researchAxis) // Route Model Binding par slug
    {
        if (!$researchAxis->is_active && !(auth()->check() && auth()->user()->can('preview inactive content'))) {
            abort(404);
        }

        $researchAxis->load('media');
        // Optionnel: Charger les chercheurs ou projets liés à cet axe
        // $researchAxis->load(['researchers' => function ($query) {
        //     $query->where('is_active', true)->orderBy('display_order');
        // }, 'projects' => function ($query) {
        //     $query->where('is_published', true)->orderBy('start_date', 'desc');
        // }]);
        
        $siteSettings = app('siteSettings');
        $metaTitle = $researchAxis->getTranslation('meta_title', app()->getLocale(), false) ?: $researchAxis->name;
        $metaDescription = $researchAxis->getTranslation('meta_description', app()->getLocale(), false) ?: Str::limit(strip_tags($researchAxis->description), 160);
        $ogImage = $researchAxis->cover_image_url ?: ($siteSettings->default_og_image_url ?? null);

        // Axes similaires (ex: les autres axes actifs)
        $relatedAxes = ResearchAxis::where('is_active', true)
            ->where('id', '!=', $researchAxis->id)
            ->orderBy('display_order', 'asc')
            ->take(3)
            ->get();

        return view('public.research_axes.show', compact('researchAxis', 'metaTitle', 'metaDescription', 'ogImage', 'relatedAxes'));
    }
}
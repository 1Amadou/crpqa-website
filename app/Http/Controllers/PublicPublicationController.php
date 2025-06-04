<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Researcher; // Pour filtrer par chercheur
use App\Models\ResearchAxis; // Pour filtrer par axe de recherche (si pertinent)
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicPublicationController extends Controller
{
    /**
     * Display a listing of the publications.
     */
    public function index(Request $request)
    {
        $query = Publication::query()
            // ->where('is_published', true) // Si vous ajoutez un statut de publication aux publications
            ->orderBy('publication_date', 'desc')
            ->with(['media', 'researchers']); // Eager load

        $searchTerm = $request->input('search');
        $typeFilter = $request->input('type');
        $yearFilter = $request->input('year');
        $researcherFilter = $request->input('researcher'); // ID du chercheur

        if ($searchTerm) {
            $currentLocale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $currentLocale) {
                $q->where('title_' . $currentLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('abstract_' . $currentLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('keywords_' . $currentLocale, 'LIKE', "%{$searchTerm}%") // Si keywords est localisé
                  ->orWhere('journal_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('conference_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        if ($yearFilter) {
            $query->whereYear('publication_date', $yearFilter);
        }
        
        if ($researcherFilter) {
            $query->whereHas('researchers', function ($q) use ($researcherFilter) {
                $q->where('researchers.id', $researcherFilter);
            });
        }

        $publications = $query->paginate(10)->appends($request->query());

        // Pour les filtres dans la vue
        $types = Publication::select('type')->distinct()->orderBy('type')->pluck('type')->mapWithKeys(function ($type) {
            return [$type => __(Str::title(str_replace('_', ' ', $type)))]; // Traduire les types si possible
        });
        $years = Publication::selectRaw('YEAR(publication_date) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');
        $researchersForFilter = Researcher::where('is_active', true)
                                    ->orderBy('last_name_'.app()->getLocale())->orderBy('first_name_'.app()->getLocale())
                                    ->get(['id', 'first_name_'.app()->getLocale().' as first_name', 'last_name_'.app()->getLocale().' as last_name'])
                                    ->mapWithKeys(function($researcher){
                                        return [$researcher->id => $researcher->first_name . ' ' . $researcher->last_name];
                                    });


        return view('public.publications.index', compact(
            'publications', 
            'types', 
            'years', 
            'researchersForFilter',
            'searchTerm',
            'typeFilter',
            'yearFilter',
            'researcherFilter'
        ));
    }

    /**
     * Display the specified publication.
     */
    public function show(Publication $publication) // Route Model Binding par slug
    {
        // if (!$publication->is_published && !(auth()->check() && auth()->user()->can('preview unpublished publications'))) {
        //     abort(404);
        // }
        $publication->load(['media', 'researchers' => function ($query) {
            $query->where('is_active', true)->orderBy('display_order');
        }]);
        
        $siteSettings = app('siteSettings'); // Assumant que $siteSettings est globalement disponible
        $metaTitle = $publication->getTranslation('meta_title', app()->getLocale(), false) ?: $publication->title;
        $metaDescription = $publication->getTranslation('meta_description', app()->getLocale(), false) ?: Str::limit(strip_tags($publication->abstract), 160);
        $ogImage = $publication->cover_image_url ?: ($siteSettings->default_og_image_url ?? null);


        // Publications similaires (ex: même type, ou par les mêmes auteurs)
        $relatedPublications = Publication::where('id', '!=', $publication->id)
            // ->where('is_published', true)
            ->where('type', $publication->type) // Exemple simple: même type
            ->orderBy('publication_date', 'desc')
            ->take(3)
            ->get();

        return view('public.publications.show', compact('publication', 'metaTitle', 'metaDescription', 'ogImage', 'relatedPublications'));
    }
}
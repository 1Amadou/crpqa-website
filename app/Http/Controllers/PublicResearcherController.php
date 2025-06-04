<?php

namespace App\Http\Controllers;

use App\Models\Researcher;
use App\Models\Publication; // Pour lister les publications d'un chercheur
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicResearcherController extends Controller
{
    /**
     * Display a listing of the active researchers.
     */
    public function index(Request $request)
    {
        $primaryLocale = app()->getLocale();
        $searchTerm = $request->input('search');

        $query = Researcher::where('is_active', true)
                           ->orderBy('display_order', 'asc') // Pour un ordre défini si vous en avez un
                           ->orderBy('last_name_' . $primaryLocale, 'asc')
                           ->orderBy('first_name_' . $primaryLocale, 'asc')
                           ->with('media'); // Eager load la photo

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm, $primaryLocale) {
                $q->where('first_name_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('title_position_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('research_interests_' . $primaryLocale, 'LIKE', "%{$searchTerm}%");
            });
        }

        $researchers = $query->paginate(12)->appends($request->query()); // 12 chercheurs par page par exemple

        $pageTitle = __('Notre Équipe de Chercheurs');
        if($searchTerm) {
            $pageTitle = __('Résultats de recherche pour :name dans l\'équipe', ['name' => $searchTerm]);
        }


        return view('public.researchers.index', compact('researchers', 'pageTitle', 'searchTerm'));
    }

    /**
     * Display the specified researcher.
     */
    public function show(Researcher $researcher) // Route Model Binding par slug
    {
        if (!$researcher->is_active && !(auth()->check() && auth()->user()->can('preview inactive content'))) {
            abort(404);
        }

        $researcher->load(['media', 'publications' => function ($query) {
            // $query->where('is_published', true)->orderBy('publication_date', 'desc'); // Si is_published existe pour publications
            $query->orderBy('publication_date', 'desc');
        }]);
        
        $siteSettings = app('siteSettings'); // Accès global
        $metaTitle = $researcher->full_name . ' - ' . $researcher->title_position;
        $metaDescription = Str::limit(strip_tags($researcher->biography), 160);
        $ogImage = $researcher->photo_profile_url ?: ($siteSettings->default_og_image_url ?? null);

        return view('public.researchers.show', compact('researcher', 'metaTitle', 'metaDescription', 'ogImage'));
    }
}
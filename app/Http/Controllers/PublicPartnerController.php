<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicPartnerController extends Controller
{
    /**
     * Display a listing of the active partners.
     */
    public function index(Request $request)
    {
        $primaryLocale = app()->getLocale();
        $searchTerm = $request->input('search');
        $typeFilter = $request->input('type'); // Pour un éventuel filtre par type

        $query = Partner::where('is_active', true)
                        ->orderBy('display_order', 'asc')
                        ->orderBy('name_' . $primaryLocale, 'asc') // Tri par nom localisé
                        ->with('media'); // Eager load le logo

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm, $primaryLocale) {
                $q->where('name_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description_' . $primaryLocale, 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%"); // Si type est un champ texte simple
            });
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }
        
        $partners = $query->paginate(12)->appends($request->query()); // 12 partenaires par page par exemple

        // Pour le filtre par type, si vous voulez une liste des types existants
        $partnerTypes = Partner::where('is_active', true)
                                ->select('type')
                                ->distinct()
                                ->orderBy('type')
                                ->pluck('type')
                                ->filter() // Enlever les valeurs nulles ou vides
                                ->mapWithKeys(function($type){
                                    return [$type => __(Str::title(str_replace('_', ' ', $type)))]; // Traduire les types
                                });


        $pageTitle = __('Nos Partenaires');
        if($searchTerm){
            $pageTitle = __('Résultats de recherche pour :term parmi nos partenaires', ['term' => $searchTerm]);
        } elseif($typeFilter){
             $pageTitle = __('Partenaires de type : ') . ($partnerTypes[$typeFilter] ?? Str::title($typeFilter));
        }


        return view('public.partners.index', compact(
            'partners', 
            'pageTitle', 
            'searchTerm', 
            'typeFilter',
            'partnerTypes'
        ));
    }

    /**
     * Display the specified partner.
     * (Optionnel, si vous avez une page de détail pour les partenaires)
     */
    // public function show(Partner $partner) // Route Model Binding par slug si vous ajoutez un slug
    // {
    //     if (!$partner->is_active && !(auth()->check() && auth()->user()->can('preview inactive content'))) {
    //         abort(404);
    //     }
    //     $partner->load('media'); // Charger le logo
    //     // $partner->load('events'); // Si vous voulez lister les événements en collaboration

    //     $siteSettings = app('siteSettings');
    //     $metaTitle = $partner->name . ' - ' . __('Partenaire');
    //     $metaDescription = Str::limit(strip_tags($partner->description), 160);
    //     $ogImage = $partner->logo_url ?: ($siteSettings->default_og_image_url ?? null);


    //     return view('public.partners.show', compact('partner', 'metaTitle', 'metaDescription', 'ogImage'));
    // }
}
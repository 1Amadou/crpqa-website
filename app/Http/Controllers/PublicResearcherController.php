<?php

namespace App\Http\Controllers;

use App\Models\Researcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicResearcherController extends Controller
{
    public function index()
    {
        $primaryLocale = app()->getLocale();
        $researchers = Researcher::where('is_active', true)
                               ->orderBy('display_order', 'asc')
                               ->orderBy('last_name_' . $primaryLocale, 'asc')
                               ->orderBy('first_name_' . $primaryLocale, 'asc')
                               ->paginate(12); // Ou ->get();

        return view('public.researchers.index', compact('researchers'));
    }

    public function show(Researcher $researcher) // Route Model Binding par slug
    {
        if (!$researcher->is_active) {
            abort(404);
        }
        $researcher->load(['publications' => function ($query) {
            // $query->where('is_published', true)->orderBy('publication_date', 'desc'); // Si publications ont un statut publié
            $query->orderBy('publication_date', 'desc');
        }, 'media']); // Charger publications et médias

        return view('public.researchers.show', compact('researcher'));
    }
}
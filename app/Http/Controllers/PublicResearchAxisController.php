<?php

namespace App\Http\Controllers;

use App\Models\ResearchAxis;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicResearchAxisController extends Controller
{
    public function index()
    {
        $primaryLocale = app()->getLocale();
        $researchAxes = ResearchAxis::where('is_active', true)
                               ->orderBy('display_order', 'asc')
                               ->orderBy('name_' . $primaryLocale, 'asc')
                               ->get(); // Ou ->paginate(9); si vous avez beaucoup d'axes

        return view('public.research_axes.index', compact('researchAxes'));
    }

    public function show(ResearchAxis $researchAxis) // Route Model Binding par slug
    {
        if (!$researchAxis->is_active) {
            abort(404);
        }
        // Charger les relations nécessaires si vous en avez (ex: chercheurs, projets liés)
        // $researchAxis->load(['researchers' => function($q){ $q->where('is_active', true)->orderBy('display_order'); }]);

        return view('public.research_axes.show', compact('researchAxis'));
    }
}
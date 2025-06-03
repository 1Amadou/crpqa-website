<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResearchAxis;
// Pour une meilleure pratique, déplacez les règles de validation dans ces Form Requests :
use App\Http\Requests\Admin\ResearchAxisStoreRequest;
use App\Http\Requests\Admin\ResearchAxisUpdateRequest;
use Illuminate\Http\Request; // À remplacer par les FormRequests spécifiques
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResearchAxisController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Adaptez les noms des permissions
        // $this->middleware(['permission:manage research_axes'])->except(['show']);
        // $this->middleware(['permission:view research_axes'])->only(['show']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    // Déplacer validationRules dans ResearchAxisStoreRequest & ResearchAxisUpdateRequest
    private function validationRules(ResearchAxis $researchAxis = null): array
    {
        $primaryLocale = config('app.locale', 'fr');
        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                $researchAxis ? Rule::unique('research_axes', 'slug')->ignore($researchAxis->id) : 'unique:research_axes,slug',
            ],
            'icon_class' => 'nullable|string|max:100',
            'color_hex' => 'nullable|string|max:7', // ex: #RRGGBB
            'research_axis_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048', // Pour Spatie
            'remove_research_axis_cover_image' => 'nullable|boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['subtitle_' . $locale] = 'nullable|string|max:255';
            $rules['description_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['icon_svg_' . $locale] = 'nullable|string'; // Pour le code SVG brut
            $rules['cover_image_alt_text_' . $locale] = 'nullable|string|max:255';
        }
        return $rules;
    }

    public function index()
    {
        $primaryLocale = app()->getLocale();
        $researchAxes = ResearchAxis::orderBy('display_order', 'asc')
                                   ->orderBy('name_' . $primaryLocale, 'asc')
                                   ->paginate(15);
        return view('admin.research_axes.index', compact('researchAxes'));
    }

    public function create()
    {
        $researchAxis = new ResearchAxis(['is_active' => true, 'display_order' => 0]);
        $availableLocales = $this->availableLocales;
        return view('admin.research_axes.create', compact('researchAxis', 'availableLocales'));
    }

    public function store(Request $request) // Remplacer par ResearchAxisStoreRequest $request
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = config('app.locale', 'fr');

        $axisData = [];
        foreach ($this->availableLocales as $locale) {
            $axisData['name_' . $locale] = $validatedData['name_' . $locale] ?? null;
            $axisData['subtitle_' . $locale] = $validatedData['subtitle_' . $locale] ?? null;
            $axisData['description_' . $locale] = $validatedData['description_' . $locale] ?? null;
            $axisData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['name_' . $locale] ?? null;
            $axisData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['description_' . $locale] ?? ''), 160);
            $axisData['icon_svg_' . $locale] = $validatedData['icon_svg_' . $locale] ?? null;
            $axisData['cover_image_alt_text_' . $locale] = $validatedData['cover_image_alt_text_' . $locale] ?? $validatedData['name_' . $locale] ?? null;
        }
        
        // Le slug est géré par la méthode boot() du modèle ResearchAxis

        $axisData['icon_class'] = $validatedData['icon_class'] ?? null;
        $axisData['color_hex'] = $validatedData['color_hex'] ?? null;
        $axisData['is_active'] = $request->boolean('is_active');
        $axisData['display_order'] = $validatedData['display_order'] ?? 0;
        // Le slug sera généré automatiquement par le modèle si non fourni et $validatedData['slug'] est vide
        if (!empty($validatedData['slug'])) {
            $axisData['slug'] = Str::slug($validatedData['slug']);
        }
        
        $researchAxis = ResearchAxis::create($axisData);

        if ($request->hasFile('research_axis_cover_image')) {
            $researchAxis->addMediaFromRequest('research_axis_cover_image')->toMediaCollection('research_axis_cover_image');
        }
        
        $displayName = $researchAxis->getTranslation('name', $primaryLocale, false) ?: 'Nouvel Axe';
        return redirect()->route('admin.research-axes.index')
                         ->with('success', "Axe de recherche \"{$displayName}\" créé avec succès.");
    }

    public function show(ResearchAxis $researchAxis) // Route Model Binding par slug (assuré par getRouteKeyName dans le modèle)
    {
        $researchAxis->load('media');
        $availableLocales = $this->availableLocales;
        return view('admin.research_axes.show', compact('researchAxis', 'availableLocales'));
    }

    public function edit(ResearchAxis $researchAxis)
    {
        $researchAxis->load('media');
        $availableLocales = $this->availableLocales;
        return view('admin.research_axes.edit', compact('researchAxis', 'availableLocales'));
    }

    public function update(Request $request, ResearchAxis $researchAxis) // Remplacer par ResearchAxisUpdateRequest $request
    {
        $validatedData = $request->validate($this->validationRules($researchAxis));
        $primaryLocale = config('app.locale', 'fr');

        $updateData = [];
        foreach ($this->availableLocales as $locale) {
            if ($request->filled('name_' . $locale)) $updateData['name_' . $locale] = $validatedData['name_' . $locale];
            if ($request->filled('subtitle_' . $locale)) $updateData['subtitle_' . $locale] = $validatedData['subtitle_' . $locale];
            if ($request->filled('description_' . $locale)) $updateData['description_' . $locale] = $validatedData['description_' . $locale];
            
            $updateData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['name_' . $locale] ?? $researchAxis->getTranslation('name', $locale, false);
            $updateData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['description_' . $locale] ?? $researchAxis->getTranslation('description', $locale, false)), 160);
            $updateData['icon_svg_' . $locale] = $validatedData['icon_svg_' . $locale] ?? null; // Permettre de vider
            $updateData['cover_image_alt_text_' . $locale] = $validatedData['cover_image_alt_text_' . $locale] ?? $validatedData['name_' . $locale] ?? $researchAxis->getTranslation('name', $locale, false);
        }

        // Le slug est géré par la méthode boot() du modèle si le nom change ou si le slug soumis est différent
        if (!empty($validatedData['slug']) && $validatedData['slug'] !== $researchAxis->slug) {
            $updateData['slug'] = Str::slug($validatedData['slug']);
        } elseif (empty($validatedData['slug']) && ($researchAxis->isDirty('name_'.$primaryLocale) || !$researchAxis->slug)) {
            // Si slug est vide et nom primaire a changé, ou si pas de slug du tout, laisser le modèle le générer
            $updateData['slug'] = null; // Permet au modèle de le regénérer
        }


        $updateData['icon_class'] = $validatedData['icon_class'] ?? null;
        $updateData['color_hex'] = $validatedData['color_hex'] ?? null;
        $updateData['is_active'] = $request->boolean('is_active');
        $updateData['display_order'] = $validatedData['display_order'] ?? 0;

        $researchAxis->update($updateData);

        if ($request->hasFile('research_axis_cover_image')) {
            $researchAxis->clearMediaCollection('research_axis_cover_image');
            $researchAxis->addMediaFromRequest('research_axis_cover_image')->toMediaCollection('research_axis_cover_image');
        } elseif ($request->boolean('remove_research_axis_cover_image')) {
            $researchAxis->clearMediaCollection('research_axis_cover_image');
        }
        
        $displayName = $researchAxis->getTranslation('name', $primaryLocale, false) ?: 'Axe #' . $researchAxis->id;
        return redirect()->route('admin.research-axes.index')
                         ->with('success', "Axe de recherche \"{$displayName}\" mis à jour avec succès.");
    }

    public function destroy(ResearchAxis $researchAxis)
    {
        $primaryLocale = config('app.locale', 'fr');
        $displayName = $researchAxis->getTranslation('name', $primaryLocale, false) ?: 'Axe #' . $researchAxis->id;

        $researchAxis->clearMediaCollection('research_axis_cover_image');
        $researchAxis->delete();

        return redirect()->route('admin.research-axes.index')
                         ->with('success', "Axe de recherche \"{$displayName}\" et son image associée ont été supprimés.");
    }
}
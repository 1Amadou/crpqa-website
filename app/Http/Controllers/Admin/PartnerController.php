<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;

use App\Http\Requests\Admin\PartnerStoreRequest;
use App\Http\Requests\Admin\PartnerUpdateRequest;
use Illuminate\Http\Request; // À remplacer par les FormRequests spécifiques
use Illuminate\Support\Facades\Auth; // Si vous enregistrez created_by_user_id
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Adaptez les noms des permissions à votre configuration Spatie Permission
        // $this->middleware(['permission:manage partners'])->except(['show']);
        // $this->middleware(['permission:view partners'])->only(['show']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    // Déplacer validationRules dans PartnerStoreRequest & PartnerUpdateRequest
    private function validationRules(Partner $partner = null): array
    {
        $primaryLocale = config('app.locale', 'fr');
        $rules = [
            'website_url' => 'nullable|url|max:255',
            'type' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'partner_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048', // Pour Spatie
            'remove_partner_logo' => 'nullable|boolean',
            // 'slug' => ... si vous ajoutez un slug au modèle Partner
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['description_' . $locale] = 'nullable|string';
            $rules['logo_alt_text_' . $locale] = 'nullable|string|max:255';
        }
        return $rules;
    }

    public function index()
    {
        $primaryLocale = app()->getLocale();
        // Tri par nom dans la locale actuelle, puis par ordre d'affichage
        $partners = Partner::orderBy('display_order', 'asc')
                           ->orderBy('name_' . $primaryLocale, 'asc')
                           ->paginate(15);
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        $partner = new Partner(['is_active' => true, 'display_order' => 0]);
        $availableLocales = $this->availableLocales;
        return view('admin.partners.create', compact('partner', 'availableLocales'));
    }

    public function store(Request $request) // Remplacer par PartnerStoreRequest $request
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = config('app.locale', 'fr');

        $partnerData = [];
        foreach ($this->availableLocales as $locale) {
            $partnerData['name_' . $locale] = $validatedData['name_' . $locale] ?? null;
            $partnerData['description_' . $locale] = $validatedData['description_' . $locale] ?? null;
            $partnerData['logo_alt_text_' . $locale] = $validatedData['logo_alt_text_' . $locale] ?? $validatedData['name_' . $locale] ?? null;
        }

        $partnerData['website_url'] = $validatedData['website_url'] ?? null;
        $partnerData['type'] = $validatedData['type'] ?? null;
        $partnerData['display_order'] = $validatedData['display_order'] ?? 0;
        $partnerData['is_active'] = $request->boolean('is_active');
        // Si vous ajoutez un slug, gérez-le ici comme pour les autres modèles

        $partner = Partner::create($partnerData);

        if ($request->hasFile('partner_logo')) {
            $partner->addMediaFromRequest('partner_logo')->toMediaCollection('partner_logo');
        }
        
        $displayName = $partner->getTranslation('name', $primaryLocale, false) ?: 'Nouveau Partenaire';
        return redirect()->route('admin.partners.index')
                         ->with('success', "Partenaire \"{$displayName}\" créé avec succès.");
    }

    public function show(Partner $partner)
    {
        // Charger les médias si nécessaire pour la vue show
        // $partner->load('media'); 
        $availableLocales = $this->availableLocales;
        return view('admin.partners.show', compact('partner', 'availableLocales'));
    }

    public function edit(Partner $partner)
    {
        $partner->load('media'); // Pour afficher le logo actuel et le texte alternatif
        $availableLocales = $this->availableLocales;
        return view('admin.partners.edit', compact('partner', 'availableLocales'));
    }

    public function update(Request $request, Partner $partner) // Remplacer par PartnerUpdateRequest $request
    {
        $validatedData = $request->validate($this->validationRules($partner));
        $primaryLocale = config('app.locale', 'fr');

        $updateData = [];
        foreach ($this->availableLocales as $locale) {
            if ($request->filled('name_' . $locale)) $updateData['name_' . $locale] = $validatedData['name_' . $locale];
            if ($request->filled('description_' . $locale)) $updateData['description_' . $locale] = $validatedData['description_' . $locale];
            $updateData['logo_alt_text_' . $locale] = $validatedData['logo_alt_text_' . $locale] ?? $validatedData['name_' . $locale] ?? $partner->getTranslation('name', $locale, false);
        }

        $updateData['website_url'] = $validatedData['website_url'] ?? null;
        $updateData['type'] = $validatedData['type'] ?? null;
        $updateData['display_order'] = $validatedData['display_order'] ?? 0;
        $updateData['is_active'] = $request->boolean('is_active');
        // Mettre à jour le slug ici si vous en avez un et que le nom a changé

        $partner->update($updateData);

        if ($request->hasFile('partner_logo')) {
            $partner->clearMediaCollection('partner_logo');
            $partner->addMediaFromRequest('partner_logo')->toMediaCollection('partner_logo');
        } elseif ($request->boolean('remove_partner_logo')) {
            $partner->clearMediaCollection('partner_logo');
        }
        
        $displayName = $partner->getTranslation('name', $primaryLocale, false) ?: 'Partenaire #' . $partner->id;
        return redirect()->route('admin.partners.index')
                         ->with('success', "Partenaire \"{$displayName}\" mis à jour avec succès.");
    }

    public function destroy(Partner $partner)
    {
        $primaryLocale = config('app.locale', 'fr');
        $displayName = $partner->getTranslation('name', $primaryLocale, false) ?: 'Partenaire #' . $partner->id;

        $partner->clearMediaCollection('partner_logo'); // Supprimer le logo associé
        $partner->events()->detach(); // Détacher des événements si la relation est bidirectionnelle et gérée ici
        $partner->delete();

        return redirect()->route('admin.partners.index')
                         ->with('success', "Partenaire \"{$displayName}\" et son logo associé ont été supprimés.");
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Researcher;
use App\Models\User; // Pour lier un chercheur à un compte utilisateur
// Pour une meilleure pratique, déplacez les règles de validation dans ces Form Requests :
use App\Http\Requests\Admin\ResearcherStoreRequest;
use App\Http\Requests\Admin\ResearcherUpdateRequest;
use Illuminate\Http\Request; // À remplacer par les FormRequests spécifiques
use Illuminate\Support\Facades\Auth; // Si vous voulez lier à l'utilisateur connecté par défaut
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResearcherController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Adaptez les noms des permissions à votre configuration
        // $this->middleware(['permission:manage researchers'])->except(['show']);
        // $this->middleware(['permission:view researchers'])->only(['show']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    // Déplacer validationRules dans ResearcherStoreRequest & ResearcherUpdateRequest
    private function validationRules(Researcher $researcher = null): array
    {
        $primaryLocale = config('app.locale', 'fr');
        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                $researcher ? Rule::unique('researchers', 'slug')->ignore($researcher->id) : 'unique:researchers,slug',
            ],
            'email' => [
                'nullable', 'string', 'email', 'max:255',
                $researcher ? Rule::unique('researchers', 'email')->ignore($researcher->id) : 'unique:researchers,email',
            ],
            'phone' => 'nullable|string|max:50', // 'phone' au lieu de 'phone_number'
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'researchgate_url' => 'nullable|url|max:255',
            'google_scholar_url' => 'nullable|url|max:255',
            'orcid_id' => 'nullable|string|max:100', // Ajustez la taille si besoin
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
            'display_order' => 'nullable|integer|min:0',
            'researcher_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Pour Spatie
            'remove_researcher_photo' => 'nullable|boolean',
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['first_name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['last_name_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['title_position_' . $locale] = 'nullable|string|max:255'; // 'title_position' au lieu de 'title' et 'position' séparés
            $rules['biography_' . $locale] = 'nullable|string';
            $rules['research_interests_' . $locale] = 'nullable|string'; // 'research_interests' au lieu de 'research_areas'
            $rules['photo_alt_text_' . $locale] = 'nullable|string|max:255'; // Pour le texte alternatif de la photo
        }
        return $rules;
    }

    public function index()
    {
        $primaryLocale = app()->getLocale();
        $researchers = Researcher::with('user', 'media') // Charger l'utilisateur et les médias (photo)
            ->orderBy('display_order', 'asc')
            ->orderBy('last_name_' . $primaryLocale, 'asc')
            ->orderBy('first_name_' . $primaryLocale, 'asc')
            ->paginate(15);

        return view('admin.researchers.index', compact('researchers'));
    }

    public function create()
    {
        $researcher = new Researcher(['is_active' => true, 'display_order' => 0]);
        $availableLocales = $this->availableLocales;
        $users = User::orderBy('name')->pluck('name', 'id'); // Pour lier à un compte utilisateur

        return view('admin.researchers.create', compact('researcher', 'availableLocales', 'users'));
    }

    public function store(Request $request) // Remplacer par ResearcherStoreRequest $request
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = config('app.locale', 'fr');

        $researcherData = [];
        foreach ($this->availableLocales as $locale) {
            $researcherData['first_name_' . $locale] = $validatedData['first_name_' . $locale] ?? null;
            $researcherData['last_name_' . $locale] = $validatedData['last_name_' . $locale] ?? null;
            $researcherData['title_position_' . $locale] = $validatedData['title_position_' . $locale] ?? null;
            $researcherData['biography_' . $locale] = $validatedData['biography_' . $locale] ?? null;
            $researcherData['research_interests_' . $locale] = $validatedData['research_interests_' . $locale] ?? null;
            $researcherData['photo_alt_text_' . $locale] = $validatedData['photo_alt_text_' . $locale] ?? ($validatedData['first_name_' . $locale] ?? '').' '.($validatedData['last_name_' . $locale] ?? '') ;
        }

        // Génération du slug à partir du nom complet de la langue par défaut
        $firstNameForSlug = $validatedData['first_name_' . $primaryLocale] ?? '';
        $lastNameForSlug = $validatedData['last_name_' . $primaryLocale] ?? '';
        $nameForSlug = trim($firstNameForSlug . ' ' . $lastNameForSlug) ?: 'chercheur-' . time();

        if (empty($validatedData['slug'])) {
            $slug = Str::slug($nameForSlug);
            $originalSlug = $slug;
            $count = 1;
            while (Researcher::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $researcherData['slug'] = $slug;
        } else {
            $researcherData['slug'] = Str::slug($validatedData['slug']);
        }
        
        $researcherData['email'] = $validatedData['email'] ?? null;
        $researcherData['phone'] = $validatedData['phone'] ?? null;
        $researcherData['website_url'] = $validatedData['website_url'] ?? null;
        $researcherData['linkedin_url'] = $validatedData['linkedin_url'] ?? null;
        $researcherData['researchgate_url'] = $validatedData['researchgate_url'] ?? null;
        $researcherData['google_scholar_url'] = $validatedData['google_scholar_url'] ?? null;
        $researcherData['orcid_id'] = $validatedData['orcid_id'] ?? null;
        $researcherData['is_active'] = $request->boolean('is_active');
        $researcherData['user_id'] = $validatedData['user_id'] ?? null;
        $researcherData['display_order'] = $validatedData['display_order'] ?? 0;
        
        $researcher = Researcher::create($researcherData);

        if ($request->hasFile('researcher_photo')) {
            $researcher->addMediaFromRequest('researcher_photo')->toMediaCollection('researcher_photo');
        }

        $displayName = $researcher->getFullNameAttribute(); // Utilise l'accesseur
        return redirect()->route('admin.researchers.index')
                         ->with('success', "Chercheur \"{$displayName}\" créé avec succès.");
    }

    public function show(Researcher $researcher)
    {
        $researcher->load(['user', 'media', 'publications']); // Charger les relations
        $availableLocales = $this->availableLocales;
        return view('admin.researchers.show', compact('researcher', 'availableLocales'));
    }

    public function edit(Researcher $researcher)
    {
        $researcher->load('media'); // Charger la photo pour affichage/suppression
        $availableLocales = $this->availableLocales;
        $users = User::orderBy('name')->pluck('name', 'id');

        return view('admin.researchers.edit', compact('researcher', 'availableLocales', 'users'));
    }

    public function update(Request $request, Researcher $researcher) // Remplacer par ResearcherUpdateRequest $request
    {
        $validatedData = $request->validate($this->validationRules($researcher));
        $primaryLocale = config('app.locale', 'fr');

        $updateData = [];
         foreach ($this->availableLocales as $locale) {
            if ($request->filled('first_name_' . $locale)) $updateData['first_name_' . $locale] = $validatedData['first_name_' . $locale];
            if ($request->filled('last_name_' . $locale)) $updateData['last_name_' . $locale] = $validatedData['last_name_' . $locale];
            if ($request->filled('title_position_' . $locale)) $updateData['title_position_' . $locale] = $validatedData['title_position_' . $locale];
            if ($request->filled('biography_' . $locale)) $updateData['biography_' . $locale] = $validatedData['biography_' . $locale];
            if ($request->filled('research_interests_' . $locale)) $updateData['research_interests_' . $locale] = $validatedData['research_interests_' . $locale];
            $updateData['photo_alt_text_' . $locale] = $validatedData['photo_alt_text_' . $locale] ?? ($validatedData['first_name_' . $locale] ?? $researcher->getTranslation('first_name', $locale, false)).' '.($validatedData['last_name_' . $locale] ?? $researcher->getTranslation('last_name', $locale, false));
        }

        $currentFullNameDefaultLocale = trim(($researcher->getTranslation('first_name', $primaryLocale, false) ?? '') . ' ' . ($researcher->getTranslation('last_name', $primaryLocale, false) ?? ''));
        $newFirstNameDefaultLocale = $validatedData['first_name_' . $primaryLocale] ?? $researcher->getTranslation('first_name', $primaryLocale, false);
        $newLastNameDefaultLocale = $validatedData['last_name_' . $primaryLocale] ?? $researcher->getTranslation('last_name', $primaryLocale, false);
        $newNameForSlug = trim($newFirstNameDefaultLocale . ' ' . $newLastNameDefaultLocale);

        if (empty($validatedData['slug'])) {
            if ($currentFullNameDefaultLocale !== $newNameForSlug || !$researcher->slug) {
                if(!empty($newNameForSlug)){
                    $slug = Str::slug($newNameForSlug);
                    $originalSlug = $slug;
                    $count = 1;
                    while (Researcher::where('slug', $slug)->where('id', '!=', $researcher->id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $updateData['slug'] = $slug;
                }
            }
        } elseif ($validatedData['slug'] !== $researcher->slug) {
            $slug = Str::slug($validatedData['slug']);
            $originalSlug = $slug;
            $count = 1;
            while (Researcher::where('slug', $slug)->where('id', '!=', $researcher->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $updateData['slug'] = $slug;
        }

        $updateData['email'] = $validatedData['email'] ?? null;
        $updateData['phone'] = $validatedData['phone'] ?? null;
        $updateData['website_url'] = $validatedData['website_url'] ?? null;
        $updateData['linkedin_url'] = $validatedData['linkedin_url'] ?? null;
        $updateData['researchgate_url'] = $validatedData['researchgate_url'] ?? null;
        $updateData['google_scholar_url'] = $validatedData['google_scholar_url'] ?? null;
        $updateData['orcid_id'] = $validatedData['orcid_id'] ?? null;
        $updateData['is_active'] = $request->boolean('is_active');
        $updateData['user_id'] = $validatedData['user_id'] ?? null;
        $updateData['display_order'] = $validatedData['display_order'] ?? 0;

        $researcher->update($updateData);

        if ($request->hasFile('researcher_photo')) {
            $researcher->clearMediaCollection('researcher_photo');
            $researcher->addMediaFromRequest('researcher_photo')->toMediaCollection('researcher_photo');
        } elseif ($request->boolean('remove_researcher_photo')) {
            $researcher->clearMediaCollection('researcher_photo');
        }

        $displayName = $researcher->getFullNameAttribute();
        return redirect()->route('admin.researchers.index')
                         ->with('success', "Chercheur \"{$displayName}\" mis à jour avec succès.");
    }

    public function destroy(Researcher $researcher)
    {
        $displayName = $researcher->getFullNameAttribute();

        $researcher->clearMediaCollection('researcher_photo'); // Supprimer la photo associée
        $researcher->publications()->detach(); // Détacher des publications
        $researcher->delete();

        return redirect()->route('admin.researchers.index')
                         ->with('success', "Chercheur \"{$displayName}\" et sa photo associée ont été supprimés.");
    }
}
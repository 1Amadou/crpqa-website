<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request; 
use App\Http\Requests\Admin\StaticPageStoreRequest; 
use App\Http\Requests\Admin\StaticPageUpdateRequest; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaticPageController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Adaptez les noms des permissions à votre configuration
        // $this->middleware(['permission:manage static_pages'])->except(['show']);
        // $this->middleware(['permission:view static_pages'])->only(['show']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    // Il est fortement recommandé de déplacer cette logique dans des Form Requests dédiés
    private function validationRules(StaticPage $staticPage = null): array
    {
        $primaryLocale = config('app.locale', 'fr');
        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                $staticPage ? Rule::unique('static_pages', 'slug')->ignore($staticPage->id) : 'unique:static_pages,slug',
            ],
            'is_published' => 'boolean',
            'user_id' => 'required|exists:users,id', // Assumant que user_id est assigné via le formulaire
            'static_page_cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Pour Spatie
            'remove_static_page_cover' => 'nullable|boolean',
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['content_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['cover_image_alt_text_' . $locale] = 'nullable|string|max:255'; // Pour l'alt de l'image de couverture
        }
        return $rules;
    }

    public function index()
    {
        $primaryLocale = app()->getLocale();
        // Tri par titre dans la locale actuelle
        $pages = StaticPage::with('user', 'media') // 'user' est la relation dans StaticPage.php
                           ->orderBy('title_' . $primaryLocale, 'asc')
                           ->paginate(15);
        return view('admin.static_pages.index', compact('pages'));
    }

    public function create()
    {
        $staticPage = new StaticPage(['is_published' => true]); // Valeurs par défaut
        $availableLocales = $this->availableLocales;
        // Vous pourriez vouloir passer les utilisateurs ici si 'user_id' est un select dans le formulaire
        // $users = \App\Models\User::orderBy('name')->pluck('name', 'id');
        // return view('admin.static_pages.create', compact('staticPage', 'availableLocales', 'users'));
        return view('admin.static_pages.create', compact('staticPage', 'availableLocales'));
    }

    public function store(Request $request) // Remplacer par StaticPageStoreRequest $request
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = config('app.locale', 'fr');

        $pageData = [];
        foreach ($this->availableLocales as $locale) {
            $pageData['title_' . $locale] = $validatedData['title_' . $locale] ?? null;
            $pageData['content_' . $locale] = $validatedData['content_' . $locale] ?? null;
            $pageData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
            $pageData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['content_' . $locale] ?? ''), 160);
            $pageData['cover_image_alt_text_' . $locale] = $validatedData['cover_image_alt_text_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
        }

        $titleForSlug = $validatedData['title_' . $primaryLocale] ?? 'page-statique-' . time();
        if (empty($validatedData['slug'])) {
            $slug = Str::slug($titleForSlug);
            $originalSlug = $slug;
            $count = 1;
            while (StaticPage::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $pageData['slug'] = $slug;
        } else {
            $pageData['slug'] = Str::slug($validatedData['slug']);
        }

        $pageData['is_published'] = $request->boolean('is_published');
        // Assigner user_id : soit l'utilisateur connecté, soit celui du formulaire s'il est validé
        $pageData['user_id'] = $validatedData['user_id'] ?? Auth::id(); 
        
        $staticPage = StaticPage::create($pageData);

        if ($request->hasFile('static_page_cover')) {
            $staticPage->addMediaFromRequest('static_page_cover')->toMediaCollection('static_page_cover');
        }

        $displayTitle = $staticPage->getTranslation('title', $primaryLocale, false) ?: $staticPage->slug;
        return redirect()->route('admin.static-pages.index')
                         ->with('success', "La page statique \"{$displayTitle}\" a été créée.");
    }

    public function show(StaticPage $staticPage)
    {
        $staticPage->load(['user', 'media']); // 'user' est la relation
        $availableLocales = $this->availableLocales;
        return view('admin.static_pages.show', compact('staticPage', 'availableLocales'));
    }

    public function edit(StaticPage $staticPage)
    {
        $staticPage->load('media');
        $availableLocales = $this->availableLocales;
        // $users = \App\Models\User::orderBy('name')->pluck('name', 'id');
        // return view('admin.static_pages.edit', compact('staticPage', 'availableLocales', 'users'));
        return view('admin.static_pages.edit', compact('staticPage', 'availableLocales'));
    }

    public function update(Request $request, StaticPage $staticPage) // Remplacer par StaticPageUpdateRequest $request
    {
        $validatedData = $request->validate($this->validationRules($staticPage));
        $primaryLocale = config('app.locale', 'fr');

        $updateData = [];
        foreach ($this->availableLocales as $locale) {
            if ($request->filled('title_' . $locale)) $updateData['title_' . $locale] = $validatedData['title_' . $locale];
            if ($request->filled('content_' . $locale)) $updateData['content_' . $locale] = $validatedData['content_' . $locale];
            
            $updateData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? $staticPage->getTranslation('title', $locale, false);
            $updateData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['content_' . $locale] ?? $staticPage->getTranslation('content', $locale, false)), 160);
            $updateData['cover_image_alt_text_' . $locale] = $validatedData['cover_image_alt_text_' . $locale] ?? $validatedData['title_' . $locale] ?? $staticPage->getTranslation('title', $locale, false);
        }
        
        $currentTitleDefaultLocale = $staticPage->getTranslation('title', $primaryLocale, false);
        $newTitleDefaultLocale = $validatedData['title_' . $primaryLocale] ?? $currentTitleDefaultLocale;

        if (empty($validatedData['slug'])) {
            if ($currentTitleDefaultLocale !== $newTitleDefaultLocale || !$staticPage->slug) {
                if(!empty($newTitleDefaultLocale)){
                    $slug = Str::slug($newTitleDefaultLocale);
                    $originalSlug = $slug;
                    $count = 1;
                    while (StaticPage::where('slug', $slug)->where('id', '!=', $staticPage->id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $updateData['slug'] = $slug;
                }
            }
        } elseif ($validatedData['slug'] !== $staticPage->slug) {
            $slug = Str::slug($validatedData['slug']);
            $originalSlug = $slug;
            $count = 1;
            while (StaticPage::where('slug', $slug)->where('id', '!=', $staticPage->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $updateData['slug'] = $slug;
        }
        
        $updateData['is_published'] = $request->boolean('is_published');
        // On met à jour user_id seulement s'il est explicitement fourni dans la requête et validé
        if (isset($validatedData['user_id'])) {
            $updateData['user_id'] = $validatedData['user_id'];
        } else {
            $updateData['user_id'] = Auth::id(); // Ou garder l'ancien si on ne veut pas le changer implicitement
        }

        $staticPage->update($updateData);

        if ($request->hasFile('static_page_cover')) {
            $staticPage->clearMediaCollection('static_page_cover');
            $staticPage->addMediaFromRequest('static_page_cover')->toMediaCollection('static_page_cover');
        } elseif ($request->boolean('remove_static_page_cover')) {
            $staticPage->clearMediaCollection('static_page_cover');
        }

        $displayTitle = $staticPage->getTranslation('title', $primaryLocale, false) ?: $staticPage->slug;
        return redirect()->route('admin.static-pages.index')
                         ->with('success', "Page statique \"{$displayTitle}\" mise à jour.");
    }

    public function destroy(StaticPage $staticPage)
    {
        $primaryLocale = config('app.locale', 'fr');
        $displayTitle = $staticPage->getTranslation('title', $primaryLocale, false) ?: $staticPage->slug;

        $staticPage->clearMediaCollection('static_page_cover');
        $staticPage->delete();

        return redirect()->route('admin.static-pages.index')
                         ->with('success', "Page statique \"{$displayTitle}\" supprimée.");
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Pour Str::slug
use Illuminate\Validation\Rule; // Pour Rule::unique

class StaticPageController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Permissions via Route::resource et middleware de groupe
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    public function index()
    {
        // Tri par le titre dans la première langue disponible pour la cohérence
        $sortLocale = $this->availableLocales[0] ?? 'fr';
        $pages = StaticPage::orderBy('title_' . $sortLocale)->paginate(15);
        return view('admin.static_pages.index', compact('pages'));
    }

    public function create()
    {
        $availableLocales = $this->availableLocales;
        return view('admin.static_pages.create', compact('availableLocales'));
    }

    public function store(Request $request)
    {
        $rules = [
            'slug' => 'nullable|string|max:255|alpha_dash|unique:static_pages,slug',
            'is_published' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Pour Spatie Media Library
        ];

        $localizedDataToSave = [];

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale == $this->availableLocales[0] ? 'required' : 'nullable') . '|string|max:255';
            $rules['content_' . $locale] = ($locale == $this->availableLocales[0] ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:600';

            if ($request->has('title_' . $locale)) {
                $localizedDataToSave['title_' . $locale] = $request->input('title_' . $locale);
            }
            if ($request->has('content_' . $locale)) {
                $localizedDataToSave['content_' . $locale] = $request->input('content_' . $locale);
            }
            if ($request->has('meta_title_' . $locale)) {
                $localizedDataToSave['meta_title_' . $locale] = $request->input('meta_title_' . $locale);
            }
            if ($request->has('meta_description_' . $locale)) {
                $localizedDataToSave['meta_description_' . $locale] = $request->input('meta_description_' . $locale);
            }
        }

        $validatedData = $request->validate($rules);

        $staticPage = new StaticPage();

        // Assigner les champs traduits
        foreach ($this->availableLocales as $locale) {
            if (isset($validatedData['title_' . $locale])) {
                $staticPage->{'title_' . $locale} = $validatedData['title_' . $locale];
            }
            if (isset($validatedData['content_' . $locale])) {
                $staticPage->{'content_' . $locale} = $validatedData['content_' . $locale];
            }
            if (isset($validatedData['meta_title_' . $locale])) {
                $staticPage->{'meta_title_' . $locale} = $validatedData['meta_title_' . $locale] ?: $validatedData['title_' . $locale];
            }
            if (isset($validatedData['meta_description_' . $locale])) {
                $staticPage->{'meta_description_' . $locale} = $validatedData['meta_description_' . $locale];
            }
        }

        // Slug: générer à partir du titre de la langue principale si non fourni ou vide
        $slugInput = $request->input('slug');
        if (empty($slugInput)) {
            $titleForSlug = $validatedData['title_' . $this->availableLocales[0]] ?? 'untitled-page-' . time();
            $staticPage->slug = Str::slug($titleForSlug);
            // Vérifier l'unicité si généré automatiquement
            $originalSlug = $staticPage->slug;
            $counter = 1;
            while (StaticPage::where('slug', $staticPage->slug)->exists()) {
                $staticPage->slug = $originalSlug . '-' . $counter++;
            }
        } else {
            $staticPage->slug = $slugInput;
        }

        $staticPage->is_published = $request->boolean('is_published');
        $staticPage->user_id = Auth::id();
        $staticPage->save(); // Sauvegarder pour obtenir un ID avant d'ajouter des médias

        if ($request->hasFile('cover_image')) {
            $staticPage->addMediaFromRequest('cover_image')->toMediaCollection('static_page_cover_image');
        }

        $displayTitle = $staticPage->getTranslation('title', $this->availableLocales[0]) ?: $staticPage->slug;
        return redirect()->route('admin.static-pages.index')
                         ->with('success', "La page statique \"{$displayTitle}\" a été créée avec succès !");
    }

    public function show(StaticPage $staticPage)
    {
        // Passer les locales pour que la vue show puisse afficher le contenu traduit
        $availableLocales = $this->availableLocales;
        return view('admin.static_pages.show', compact('staticPage', 'availableLocales'));
    }

    public function edit(StaticPage $staticPage)
    {
        $availableLocales = $this->availableLocales;
        return view('admin.static_pages.edit', compact('staticPage', 'availableLocales'));
    }

    public function update(Request $request, StaticPage $staticPage)
    {
        $rules = [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('static_pages')->ignore($staticPage->id)],
            'is_published' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $localizedDataToSave = [];

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale == $this->availableLocales[0] ? 'required' : 'nullable') . '|string|max:255';
            $rules['content_' . $locale] = ($locale == $this->availableLocales[0] ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:600';
        }

        $validatedData = $request->validate($rules);

        foreach ($this->availableLocales as $locale) {
             if (isset($validatedData['title_' . $locale])) {
                $staticPage->{'title_' . $locale} = $validatedData['title_' . $locale];
            }
            if (isset($validatedData['content_' . $locale])) {
                $staticPage->{'content_' . $locale} = $validatedData['content_' . $locale];
            }
            if (isset($validatedData['meta_title_' . $locale])) {
                $staticPage->{'meta_title_' . $locale} = $validatedData['meta_title_' . $locale] ?: $validatedData['title_' . $locale];
            }
            if (isset($validatedData['meta_description_' . $locale])) {
                $staticPage->{'meta_description_' . $locale} = $validatedData['meta_description_' . $locale];
            }
        }

        $slugInput = $request->input('slug');
        if (empty($slugInput)) {
            if ($staticPage->isDirty('title_' . $this->availableLocales[0])) { // Si le titre principal a changé
                $titleForSlug = $validatedData['title_' . $this->availableLocales[0]] ?? 'untitled-page-' . time();
                $newSlug = Str::slug($titleForSlug);
                 // Vérifier l'unicité si généré automatiquement et différent de l'ancien
                if ($newSlug !== $staticPage->slug) {
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (StaticPage::where('slug', $newSlug)->where('id', '!=', $staticPage->id)->exists()) {
                        $newSlug = $originalSlug . '-' . $counter++;
                    }
                    $staticPage->slug = $newSlug;
                }
            }
        } else {
            $staticPage->slug = $slugInput;
        }

        $staticPage->is_published = $request->boolean('is_published');
        $staticPage->user_id = Auth::id();

        if ($request->hasFile('cover_image')) {
            $staticPage->clearMediaCollection('static_page_cover_image'); // Supprime l'ancienne image
            $staticPage->addMediaFromRequest('cover_image')->toMediaCollection('static_page_cover_image');
        } elseif ($request->boolean('remove_cover_image')) {
            $staticPage->clearMediaCollection('static_page_cover_image');
        }

        $staticPage->save();
        $displayTitle = $staticPage->getTranslation('title', $this->availableLocales[0]) ?: $staticPage->slug;

        return redirect()->route('admin.static-pages.index')
                         ->with('success', "La page statique \"{$displayTitle}\" a été mise à jour avec succès !");
    }

    public function destroy(StaticPage $staticPage)
    {
        $displayTitle = $staticPage->getTranslation('title', $this->availableLocales[0]) ?: $staticPage->slug;
        
        // Supprimer les médias associés avant de supprimer la page
        $staticPage->clearMediaCollection('static_page_cover_image');
        $staticPage->delete();

        return redirect()->route('admin.static-pages.index')
                         ->with('success', "La page statique \"{$displayTitle}\" a été supprimée avec succès !");
    }
}
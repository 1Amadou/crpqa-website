<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News; // Utilisation du modèle consolidé
use App\Models\NewsCategory;
use App\Models\User; // Bien que non explicitement utilisé dans les méthodes create/edit pour lister les auteurs, Auth::id() l'est.
use Illuminate\Http\Request; // Remplacer par FormRequests si vous les créez
use App\Http\Requests\Admin\NewsStoreRequest; // À utiliser
use App\Http\Requests\Admin\NewsUpdateRequest; // À utiliser
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class NewsController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        // Assurez-vous que cette permission est définie dans votre système de rôles/permissions
        // $this->middleware(['permission:manage news'])->except(['show']); 
        // $this->middleware(['permission:view news'])->only(['show']); // Exemple si vous avez une permission distincte pour voir
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    private function validationRules(News $newsItem = null): array
    {
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                $newsItem ? Rule::unique('news', 'slug')->ignore($newsItem->id) : 'unique:news,slug',
            ],
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'is_published' => 'boolean', // Sera traité avec $request->boolean()
            'is_featured' => 'boolean',  // Sera traité avec $request->boolean()
            'news_category_id' => 'nullable|exists:news_categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048', // max 2MB
            'remove_cover_image' => 'nullable|boolean', // Pour la suppression de l'image
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['summary_' . $locale] = 'nullable|string|max:1000'; // 'summary' au lieu de 'excerpt'
            $rules['content_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:600';
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
        }
        return $rules;
    }

    public function index()
    {
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        // La colonne de tri doit exister et être celle de la langue par défaut pour la cohérence
        $sortColumn = 'title_' . $primaryLocale; 
        
        $newsItems = News::with(['createdBy', 'category']) // 'createdBy' au lieu de 'user' pour la relation
            ->orderBy($sortColumn, 'asc') // Tri par titre par défaut
            ->orderBy('published_at', 'desc') // Puis par date de publication
            ->paginate(15);

        return view('admin.news.index', compact('newsItems'));
    }

    public function create()
    {
        $availableLocales = $this->availableLocales;
        $newsItem = new News([ // Pré-remplir avec des valeurs par défaut
            'is_published' => true,
            'published_at' => now(),
        ]);
        $categories = NewsCategory::orderBy('name')->get()->pluck('name', 'id');
        $primaryLocale = app()->getLocale(); // Ou votre logique pour la locale de tri
        // $categories = NewsCategory::orderBy('name_' . $primaryLocale)->get()->pluck('name', 'id');

        return view('admin.news.create', compact('availableLocales', 'newsItem', 'categories'));
    }

    public function store(Request $request) // Remplacer Request par NewsStoreRequest
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        
        $newsItemData = [];
        foreach ($this->availableLocales as $locale) {
            $newsItemData['title_' . $locale] = $validatedData['title_' . $locale] ?? null;
            $newsItemData['summary_' . $locale] = $validatedData['summary_' . $locale] ?? null;
            $newsItemData['content_' . $locale] = $validatedData['content_' . $locale] ?? null;
            $newsItemData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
            $newsItemData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['summary_' . $locale] ?? ''), 160);
            $newsItemData['cover_image_alt_' . $locale] = $validatedData['cover_image_alt_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
        }

        if (empty($validatedData['slug']) && !empty($validatedData['title_' . $primaryLocale])) {
            $slug = Str::slug($validatedData['title_' . $primaryLocale]);
            $originalSlug = $slug;
            $count = 1;
            while (News::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $newsItemData['slug'] = $slug;
        } elseif (!empty($validatedData['slug'])) {
            $newsItemData['slug'] = Str::slug($validatedData['slug']);
        } else {
            // Fallback si le titre primaire est vide et aucun slug n'est fourni
            $newsItemData['slug'] = Str::slug('actualite-' . time());
        }

        if ($request->filled('published_at_date')) {
            $time = $request->input('published_at_time', '00:00'); // H:i
            $newsItemData['published_at'] = Carbon::createFromFormat('Y-m-d H:i', $request->input('published_at_date') . ' ' . $time);
        } else {
            $newsItemData['published_at'] = $request->boolean('is_published') ? now() : null;
        }

        $newsItemData['is_published'] = $request->boolean('is_published');
        $newsItemData['is_featured'] = $request->boolean('is_featured');
        $newsItemData['created_by_user_id'] = Auth::id();
        $newsItemData['news_category_id'] = $validatedData['news_category_id'] ?? null;
        
        $newsItem = News::create($newsItemData);

        if ($request->hasFile('cover_image')) {
            $newsItem->addMediaFromRequest('cover_image')->toMediaCollection('news_cover_image');
        }

        $displayTitle = $newsItem->getTranslation('title', $primaryLocale, false) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été créée.");
    }

    public function show(News $newsItem) // Route Model Binding
    {
        $newsItem->load(['createdBy', 'category', 'media']); // 'createdBy' au lieu de 'user'
        $availableLocales = $this->availableLocales;
        return view('admin.news.show', compact('newsItem', 'availableLocales'));
    }

    public function edit(News $newsItem) // Route Model Binding
    {
        $newsItem->load('media'); // Charger les médias pour l'affichage/suppression
        $availableLocales = $this->availableLocales;
        $categories = NewsCategory::orderBy('name')->get()->pluck('name', 'id');
        $primaryLocale = app()->getLocale(); 
        $categories = NewsCategory::orderBy('name_' . $primaryLocale)->get()->pluck('name', 'id');

        return view('admin.news.edit', compact('newsItem', 'availableLocales', 'categories'));
    }

    public function update(Request $request, News $newsItem) // Remplacer Request par NewsUpdateRequest
    {
        $validatedData = $request->validate($this->validationRules($newsItem));
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');

        $updateData = [];
        foreach ($this->availableLocales as $locale) {
            if ($request->filled('title_' . $locale)) $updateData['title_' . $locale] = $validatedData['title_' . $locale];
            if ($request->filled('summary_' . $locale)) $updateData['summary_' . $locale] = $validatedData['summary_' . $locale];
            if ($request->filled('content_' . $locale)) $updateData['content_' . $locale] = $validatedData['content_' . $locale];
            
            $updateData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? $newsItem->getTranslation('title', $locale, false);
            $updateData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['summary_' . $locale] ?? $newsItem->getTranslation('summary', $locale, false)), 160);
            $updateData['cover_image_alt_' . $locale] = $validatedData['cover_image_alt_' . $locale] ?? $validatedData['title_' . $locale] ?? $newsItem->getTranslation('title', $locale, false);
        }
        
        $currentTitleDefaultLocale = $newsItem->getTranslation('title', $primaryLocale, false);
        $newTitleDefaultLocale = $validatedData['title_' . $primaryLocale] ?? $currentTitleDefaultLocale;

        if (empty($validatedData['slug'])) {
            if ($currentTitleDefaultLocale !== $newTitleDefaultLocale || !$newsItem->slug) {
                $slug = Str::slug($newTitleDefaultLocale);
                $originalSlug = $slug;
                $count = 1;
                while (News::where('slug', $slug)->where('id', '!=', $newsItem->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $updateData['slug'] = $slug;
            }
        } elseif ($validatedData['slug'] !== $newsItem->slug) {
            $slug = Str::slug($validatedData['slug']);
            $originalSlug = $slug;
            $count = 1;
            while (News::where('slug', $slug)->where('id', '!=', $newsItem->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $updateData['slug'] = $slug;
        }

        if ($request->filled('published_at_date')) {
            $time = $request->input('published_at_time', '00:00');
            $updateData['published_at'] = Carbon::createFromFormat('Y-m-d H:i', $request->input('published_at_date') . ' ' . $time);
        } else {
            // Si la date est effacée, et qu'on dépublie, mettre published_at à null
            // Si la date est effacée, mais qu'on publie, mettre à now() si pas déjà publié
            $updateData['published_at'] = $request->boolean('is_published') ? ($newsItem->published_at ?? now()) : null;
        }

        $updateData['is_published'] = $request->boolean('is_published');
        $updateData['is_featured'] = $request->boolean('is_featured');
        $updateData['news_category_id'] = $validatedData['news_category_id'] ?? null;
        // On ne met généralement pas à jour created_by_user_id

        $newsItem->update($updateData);

        if ($request->hasFile('cover_image')) {
            $newsItem->clearMediaCollection('news_cover_image');
            $newsItem->addMediaFromRequest('cover_image')->toMediaCollection('news_cover_image');
        } elseif ($request->boolean('remove_cover_image')) {
            $newsItem->clearMediaCollection('news_cover_image');
        }

        $displayTitle = $newsItem->getTranslation('title', $primaryLocale, false) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été mise à jour.");
    }

    public function destroy(News $newsItem) // Route Model Binding
    {
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale, false) ?: $newsItem->slug;

        $newsItem->clearMediaCollection('news_cover_image'); // Supprimer le média associé
        $newsItem->delete();

        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été supprimée.");
    }

    public function publish(News $newsItem) // Route Model Binding
    {
        $newsItem->is_published = true;
        if (is_null($newsItem->published_at)) {
            $newsItem->published_at = now();
        }
        $newsItem->save();
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale, false) ?: $newsItem->slug;
        return redirect()->back()->with('success', "L'actualité \"{$displayTitle}\" a été publiée.");
    }

    public function unpublish(News $newsItem) // Route Model Binding
    {
        $newsItem->is_published = false;
        // Optionnel : $newsItem->published_at = null; si la dépublication doit effacer la date
        $newsItem->save();
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale, false) ?: $newsItem->slug;
        return redirect()->back()->with('success', "L'actualité \"{$displayTitle}\" a été dépubliée.");
    }
}
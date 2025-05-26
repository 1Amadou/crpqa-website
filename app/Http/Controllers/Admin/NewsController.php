<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory; // Assurez-vous que ce modèle existe et est au bon namespace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema; // Pour Schema::hasColumn()

class NewsController extends Controller
{
    protected array $availableLocales;

    public function __construct()
    {
        $this->middleware(['permission:manage news']);
        $this->availableLocales = config('app.available_locales', ['fr', 'en']);
    }

    public function index()
    {
        $primaryLocale = $this->availableLocales[0] ?? config('app.fallback_locale', 'fr');
        $sortColumn = 'title_' . $primaryLocale;

        if (!Schema::hasColumn('news', $sortColumn)) {
            $sortColumn = 'published_at';
        }
        $orderByDirection = ($sortColumn === 'published_at' || $sortColumn === 'created_at') ? 'desc' : 'asc';

        $newsItems = News::with(['user', 'category'])
            ->orderBy($sortColumn, $orderByDirection)
            ->when($sortColumn !== 'created_at' && $sortColumn !== 'published_at', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(15);

        return view('admin.news.index', compact('newsItems'));
    }

    public function create()
    {
        $availableLocales = $this->availableLocales;
        $newsItem = new News([
            'is_published' => true,
            'published_at' => now()
        ]);
        $categories = NewsCategory::orderBy('name')->pluck('name', 'id');

        return view('admin.news.create', compact('availableLocales', 'newsItem', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'slug' => 'nullable|string|max:255|alpha_dash|unique:news,slug',
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $primaryLocale = $this->availableLocales[0] ?? 'fr';

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale == $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['summary_' . $locale] = 'nullable|string|max:1000';
            $rules['content_' . $locale] = ($locale == $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:600';
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
        }

        $validatedData = $request->validate($rules);

        $newsItem = new News();

        foreach ($this->availableLocales as $locale) {
            $newsItem->{'title_' . $locale} = $validatedData['title_' . $locale] ?? null;
            $newsItem->{'summary_' . $locale} = $validatedData['summary_' . $locale] ?? null;
            $newsItem->{'content_' . $locale} = $validatedData['content_' . $locale] ?? null;
            $newsItem->{'meta_title_' . $locale} = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
            $newsItem->{'meta_description_' . $locale} = $validatedData['meta_description_' . $locale] ?? null;
            $newsItem->{'cover_image_alt_' . $locale} = $validatedData['cover_image_alt_' . $locale] ?? null;
        }

        $slugInput = $request->input('slug');
        if (empty($slugInput)) {
            $titleForSlug = $validatedData['title_' . $primaryLocale] ?? 'actualite-' . time();
            $newsItem->slug = Str::slug($titleForSlug);
            $originalSlug = $newsItem->slug;
            $counter = 1;
            while (News::where('slug', $newsItem->slug)->exists()) {
                $newsItem->slug = $originalSlug . '-' . $counter++;
            }
        } else {
            $newsItem->slug = Str::slug($slugInput);
        }

        if ($request->filled('published_at_date')) {
            $time = $request->input('published_at_time', '00:00:00');
            try {
                 $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i', $request->input('published_at_date') . ' ' . $time);
            } catch (\Exception $e) {
                 $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('published_at_date') . ' ' . $time)->format('Y-m-d H:i:s');
            }
        } else {
            $newsItem->published_at = $request->boolean('is_published') ? now() : null;
        }

        $newsItem->is_published = $request->boolean('is_published');
        $newsItem->is_featured = $request->boolean('is_featured');
        // VÉRIFIEZ ATTENTIVEMENT CETTE LIGNE : elle doit utiliser created_by_user_id
        $newsItem->created_by_user_id = Auth::id();
        $newsItem->news_category_id = $request->input('news_category_id');

        $newsItem->save(); // C'est ici que l'erreur se produisait

        if ($request->hasFile('cover_image')) {
            $newsItem->addMediaFromRequest('cover_image')->toMediaCollection('news_cover_image');
        }

        $displayTitle = $newsItem->getTranslation('title', $primaryLocale) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été créée avec succès !");
    }

    public function show(News $newsItem)
    {
        $newsItem->load(['user', 'category']);
        $availableLocales = $this->availableLocales;
        return view('admin.news.show', compact('newsItem', 'availableLocales'));
    }

    public function edit(News $newsItem)
    {
        $availableLocales = $this->availableLocales;
        $categories = NewsCategory::orderBy('name')->pluck('name', 'id');
        return view('admin.news.edit', compact('newsItem', 'availableLocales', 'categories'));
    }

    public function update(Request $request, News $newsItem)
    {
        $rules = [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('news')->ignore($newsItem->id)],
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $primaryLocale = $this->availableLocales[0] ?? 'fr';

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale == $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['summary_' . $locale] = 'nullable|string|max:1000';
            $rules['content_' . $locale] = ($locale == $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:600';
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
        }

        $validatedData = $request->validate($rules);

        foreach ($this->availableLocales as $locale) {
            if ($request->has('title_' . $locale)) {
                $newsItem->{'title_' . $locale} = $validatedData['title_' . $locale] ?? null;
            }
            if ($request->has('summary_' . $locale)) {
                $newsItem->{'summary_' . $locale} = $validatedData['summary_' . $locale] ?? null;
            }
            if ($request->has('content_' . $locale)) {
                $newsItem->{'content_' . $locale} = $validatedData['content_' . $locale] ?? null;
            }
            if ($request->has('meta_title_' . $locale)) {
                $newsItem->{'meta_title_' . $locale} = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
            }
             if ($request->has('meta_description_' . $locale)) {
                $newsItem->{'meta_description_' . $locale} = $validatedData['meta_description_' . $locale] ?? null;
            }
            if ($request->has('cover_image_alt_' . $locale)) {
                $newsItem->{'cover_image_alt_' . $locale} = $validatedData['cover_image_alt_' . $locale] ?? null;
            }
        }

        $slugInput = $request->input('slug');
        if (empty($slugInput)) {
            $primaryTitleKeyDB = 'title_' . $primaryLocale;
            if ($request->has('title_' . $primaryLocale) && $newsItem->isDirty($primaryTitleKeyDB)) {
                $titleForSlug = $validatedData['title_' . $primaryLocale] ?? 'actualite-' . time();
                $newSlug = Str::slug($titleForSlug);
                if ($newSlug !== $newsItem->slug) {
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (News::where('slug', $newSlug)->where('id', '!=', $newsItem->id)->exists()) {
                        $newSlug = $originalSlug . '-' . $counter++;
                    }
                    $newsItem->slug = $newSlug;
                }
            }
        } else {
            $newsItem->slug = Str::slug($slugInput);
        }

        if ($request->filled('published_at_date')) {
            $time = $request->input('published_at_time', '00:00:00');
             try {
                 $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i', $request->input('published_at_date') . ' ' . $time);
            } catch (\Exception $e) {
                 $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('published_at_date') . ' ' . $time)->format('Y-m-d H:i:s');
            }
        } else {
            $newsItem->published_at = $request->boolean('is_published') ? ($newsItem->published_at ?? now()) : null;
        }

        $newsItem->is_published = $request->boolean('is_published');
        $newsItem->is_featured = $request->boolean('is_featured');
        // $newsItem->created_by_user_id = Auth::id(); // On ne met généralement pas à jour le créateur original
        $newsItem->news_category_id = $request->input('news_category_id');

        if ($request->hasFile('cover_image')) {
            $newsItem->clearMediaCollection('news_cover_image');
            $newsItem->addMediaFromRequest('cover_image')->toMediaCollection('news_cover_image');
        } elseif ($request->boolean('remove_cover_image')) {
            $newsItem->clearMediaCollection('news_cover_image');
        }

        $newsItem->save();

        $displayTitle = $newsItem->getTranslation('title', $primaryLocale) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été mise à jour avec succès !");
    }

    public function destroy(News $newsItem)
    {
        $primaryLocale = $this->availableLocales[0] ?? 'fr';
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale) ?: $newsItem->slug;

        $newsItem->clearMediaCollection('news_cover_image');
        $newsItem->delete();

        return redirect()->route('admin.news.index')
                         ->with('success', "L'actualité \"{$displayTitle}\" a été supprimée avec succès !");
    }

    public function publish(News $newsItem)
    {
        $newsItem->is_published = true;
        if (is_null($newsItem->published_at)) {
            $newsItem->published_at = now();
        }
        $newsItem->save();
        $primaryLocale = $this->availableLocales[0] ?? 'fr';
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')->with('success', "L'actualité \"{$displayTitle}\" a été publiée.");
    }

    public function unpublish(News $newsItem)
    {
        $newsItem->is_published = false;
        $newsItem->save();
        $primaryLocale = $this->availableLocales[0] ?? 'fr';
        $displayTitle = $newsItem->getTranslation('title', $primaryLocale) ?: $newsItem->slug;
        return redirect()->route('admin.news.index')->with('success', "L'actualité \"{$displayTitle}\" a été dépubliée.");
    }
}
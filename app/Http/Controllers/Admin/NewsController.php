<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon; // Pour la gestion des dates
use Illuminate\Validation\Rule;
use App\Models\NewsItem;

class NewsController extends Controller
{
    /**
     * Helper function to define validation rules.
     * @param News|null $newsItem
     * @return array
     */
    private function validationRules(News $newsItem = null): array
    {
        $imageRule = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'; // 2MB Max

            return [
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('news')->ignore($newsItem ? $newsItem->id : null)],
            'meta_title' => 'nullable|string|max:255',             
            'meta_description' => 'nullable|string|max:1000',    // <--  (max 1000 pour plus de flexibilité)
            'summary' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'cover_image' => $imageRule,
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'is_featured' => 'nullable|boolean',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newsItems = News::with('user') // Charger l'auteur
                         ->orderBy('published_at', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        return view('admin.news.index', compact('newsItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Tes règles de validation doivent être adaptées pour les champs traduisibles
    // Par exemple, 'title.fr' => 'required|string|max:255',
    // Mais pour simplifier ici, je vais supposer que $request->title est un tableau associatif { 'fr': '...', 'en': '...' }
    // Ou bien tu gères la validation des traductions dans validationRules() plus tard.

    // On valide d'abord les champs non-traduisibles et le slug unique.
    $request->validate([
        'slug' => 'required|string|max:255|alpha_dash|unique:news,slug',
        'published_at_date' => 'nullable|date_format:Y-m-d',
        'published_at_time' => 'nullable|date_format:H:i',
        'is_featured' => 'nullable|boolean',
        'news_category_id' => 'nullable|exists:news_categories,id', // Ajoute la validation de la catégorie
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'cover_image_alt' => 'nullable|string|max:255', // Ajoute l'alt pour l'image de couverture
        'gallery_images_json' => 'nullable|array', // Si tu as un champ JSON pour la galerie
    ]);

    // Validation des champs traduisibles
    // Assure-toi que ton formulaire envoie 'title.fr', 'title.en', etc.
    $translatableRules = [
        'title' => 'required|array',
        'title.fr' => 'required|string|max:255',
        'title.en' => 'nullable|string|max:255', // Exemple si l'anglais est optionnel
        'content' => 'required|array',
        'content.fr' => 'required|string',
        'content.en' => 'nullable|string',
        'summary' => 'nullable|array',
        'summary.fr' => 'nullable|string|max:1000',
        'summary.en' => 'nullable|string|max:1000',
        'meta_title' => 'nullable|array',
        'meta_title.fr' => 'nullable|string|max:255',
        'meta_title.en' => 'nullable|string|max:255',
        'meta_description' => 'nullable|array',
        'meta_description.fr' => 'nullable|string|max:1000',
        'meta_description.en' => 'nullable|string|max:1000',
    ];
    $validatedTranslations = $request->validate($translatableRules);


    $newsItem = new News();
    $newsItem->user_id = Auth::id();
    $newsItem->slug = $request->slug;
    $newsItem->news_category_id = $request->news_category_id; // Assignation de la catégorie
    $newsItem->is_featured = $request->boolean('is_featured');
    $newsItem->is_published = $request->boolean('is_published'); // Assure-toi d'avoir ce champ dans ton formulaire aussi.

    // Assignation des champs traduisibles (Spatie Translatable gère le JSON en interne)
    $newsItem->setTranslations('title', $validatedTranslations['title']);
    $newsItem->setTranslations('content', $validatedTranslations['content']);
    $newsItem->setTranslations('summary', $validatedTranslations['summary'] ?? []);

    // Gestion des méta-titres et descriptions
    // Si meta_title est fourni pour une langue, on l'utilise, sinon on prend le titre de cette langue
    foreach (config('app.locales') as $locale) { // Suppose que tu as un tableau de locales dans config/app.php
        $metaTitle = $validatedTranslations['meta_title'][$locale] ?? null;
        if (empty($metaTitle)) {
            $metaTitle = Str::limit(strip_tags($validatedTranslations['title'][$locale] ?? ''), 70, '');
        }
        $newsItem->setTranslation('meta_title', $locale, $metaTitle);

        $metaDescription = $validatedTranslations['meta_description'][$locale] ?? null;
        if (empty($metaDescription)) {
            $summaryForMeta = strip_tags($validatedTranslations['summary'][$locale] ?? $validatedTranslations['content'][$locale] ?? '');
            $metaDescription = Str::limit($summaryForMeta, 160, '...');
        }
        $newsItem->setTranslation('meta_description', $locale, $metaDescription);
    }


    // Gestion de la date et heure de publication
    if ($request->filled('published_at_date') && $request->filled('published_at_time')) {
        $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i', $request->published_at_date . ' ' . $request->published_at_time);
    } elseif ($request->filled('published_at_date')) {
        $newsItem->published_at = Carbon::createFromFormat('Y-m-d', $request->published_at_date)->startOfDay();
    } else {
        $newsItem->published_at = null;
    }

    // Gestion de l'image de couverture
    if ($request->hasFile('cover_image')) {
        // Supprime l'ancienne image si elle existe
        if ($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path)) {
            Storage::disk('public')->delete($newsItem->cover_image_path);
        }
        $fileName = Str::slug($newsItem->getTranslation('title', 'fr', false)) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
        $path = $request->file('cover_image')->storeAs('news_covers', $fileName, 'public');
        $newsItem->cover_image_path = $path;
        $newsItem->cover_image_alt = $request->cover_image_alt ?? $newsItem->getTranslation('title', 'fr', false); // Utilise l'alt fourni ou le titre
    }

    // Gestion de la galerie d'images (si tu as un champ JSON pour ça)
    // IMPORTANT : la structure du JSON de la galerie doit potentiellement contenir les traductions.
    // Par exemple : [{"url": "...", "alt": {"fr": "...", "en": "..."}, "caption": {"fr": "...", "en": "..."}}]
    // Ou si tu gères les traductions de caption via getLocalizedField() dans la vue, le JSON peut être simple:
    // [{"url": "...", "alt": "...", "caption": "..."}]
    if ($request->has('gallery_images_json')) {
        $newsItem->gallery_images_json = $request->gallery_images_json;
    } else {
        $newsItem->gallery_images_json = []; // Assure-toi que c'est un tableau vide par défaut si pas de galerie
    }


    $newsItem->save();

    return redirect()->route('admin.news.index')
        ->with('success', 'Actualité "' . $newsItem->getTranslation('title', 'fr', false) . '" créée avec succès.');
}

    /**
     * Display the specified resource.
     */
    public function show(News $news) // Laravel va automatiquement trouver l'actualité par son ID ou slug si configuré
    {
        $news->load('user');
        return view('admin.news.show', compact('news'));// Renommer en $newsItem pour la vue
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        // $newsItem = NewsItem::where('slug', $newsItem)->firstOrFail();
        return view('admin.news.edit', compact('news'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $newsItem) // Utilise News au lieu de NewsItem pour le route model binding
{
    // Validation du slug unique, en ignorant l'article actuel
    $request->validate([
        'slug' => 'required|string|max:255|alpha_dash|unique:news,slug,' . $newsItem->id,
        'published_at_date' => 'nullable|date_format:Y-m-d',
        'published_at_time' => 'nullable|date_format:H:i',
        'is_featured' => 'nullable|boolean',
        'news_category_id' => 'nullable|exists:news_categories,id',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'cover_image_alt' => 'nullable|string|max:255',
        'gallery_images_json' => 'nullable|array',
    ]);

    // Validation des champs traduisibles
    $translatableRules = [
        'title' => 'required|array',
        'title.fr' => 'required|string|max:255',
        'title.en' => 'nullable|string|max:255',
        'content' => 'required|array',
        'content.fr' => 'required|string',
        'content.en' => 'nullable|string',
        'summary' => 'nullable|array',
        'summary.fr' => 'nullable|string|max:1000',
        'summary.en' => 'nullable|string|max:1000',
        'meta_title' => 'nullable|array',
        'meta_title.fr' => 'nullable|string|max:255',
        'meta_title.en' => 'nullable|string|max:255',
        'meta_description' => 'nullable|array',
        'meta_description.fr' => 'nullable|string|max:1000',
        'meta_description.en' => 'nullable|string|max:1000',
    ];
    $validatedTranslations = $request->validate($translatableRules);


    $newsItem->slug = $request->slug;
    $newsItem->news_category_id = $request->news_category_id;
    $newsItem->is_featured = $request->boolean('is_featured');
    $newsItem->is_published = $request->boolean('is_published');


    // Assignation des champs traduisibles
    $newsItem->setTranslations('title', $validatedTranslations['title']);
    $newsItem->setTranslations('content', $validatedTranslations['content']);
    $newsItem->setTranslations('summary', $validatedTranslations['summary'] ?? []);

    // Gestion des méta-titres et descriptions
    foreach (config('app.locales') as $locale) {
        $metaTitle = $validatedTranslations['meta_title'][$locale] ?? null;
        if (empty($metaTitle)) {
            $metaTitle = Str::limit(strip_tags($validatedTranslations['title'][$locale] ?? ''), 70, '');
        }
        $newsItem->setTranslation('meta_title', $locale, $metaTitle);

        $metaDescription = $validatedTranslations['meta_description'][$locale] ?? null;
        if (empty($metaDescription)) {
            $summaryForMeta = strip_tags($validatedTranslations['summary'][$locale] ?? $validatedTranslations['content'][$locale] ?? '');
            $metaDescription = Str::limit($summaryForMeta, 160, '...');
        }
        $newsItem->setTranslation('meta_description', $locale, $metaDescription);
    }

    // Gestion de la date et heure de publication
    if ($request->filled('published_at_date') && $request->filled('published_at_time')) {
        $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i', $request->published_at_date . ' ' . $request->published_at_time);
    } elseif ($request->filled('published_at_date')) {
        $newsItem->published_at = Carbon::createFromFormat('Y-m-d', $request->published_at_date)->startOfDay();
    } else {
        $newsItem->published_at = null;
    }

    // Gestion de l'image de couverture
    if ($request->hasFile('cover_image')) {
        if ($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path)) {
            Storage::disk('public')->delete($newsItem->cover_image_path);
        }
        $fileName = Str::slug($newsItem->getTranslation('title', 'fr', false)) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
        $path = $request->file('cover_image')->storeAs('news_covers', $fileName, 'public');
        $newsItem->cover_image_path = $path;
        $newsItem->cover_image_alt = $request->cover_image_alt ?? $newsItem->getTranslation('title', 'fr', false);
    } elseif ($request->boolean('remove_cover_image')) {
        if ($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path)) {
            Storage::disk('public')->delete($newsItem->cover_image_path);
        }
        $newsItem->cover_image_path = null;
        $newsItem->cover_image_alt = null;
    }

    // Gestion de la galerie d'images
    if ($request->has('gallery_images_json')) {
        $newsItem->gallery_images_json = $request->gallery_images_json;
    } else {
        $newsItem->gallery_images_json = [];
    }

    $news->save();
    return redirect()->route('admin.news.index')->with('success', 'Actualité "' . $news->getTranslation('title', 'fr', false) . '" mise à jour avec succès.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $newsTitle = $news->title;

        if ($news->cover_image_path && Storage::disk('public')->exists($news->cover_image_path)) {
            Storage::disk('public')->delete($news->cover_image_path);
        }
        
        $news->delete();
    return redirect()->route('admin.news.index')->with('success', 'Actualité "' . $news->getTranslation('title', 'fr', false) . '" supprimée avec succès.');
    }
}
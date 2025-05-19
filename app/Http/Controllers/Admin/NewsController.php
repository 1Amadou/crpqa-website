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
        $validatedData = $request->validate($this->validationRules());

        $newsItem = new News();
        $newsItem->title = $validatedData['title'];
        $newsItem->slug = $validatedData['slug'];
        // $newsItem->summary = $validatedData['summary']; // Déplacé pour la logique meta_description
        // $newsItem->content = $validatedData['content']; // Déplacé pour la logique meta_description
        $newsItem->is_featured = $request->boolean('is_featured');
        $newsItem->user_id = Auth::id();

        // Gestion de la date et heure de publication (ce code reste le même)
        if ($request->filled('published_at_date') && $request->filled('published_at_time')) {
            $newsItem->published_at = Carbon::createFromFormat('Y-m-d H:i', $validatedData['published_at_date'] . ' ' . $validatedData['published_at_time']);
        } elseif ($request->filled('published_at_date')) {
            $newsItem->published_at = Carbon::createFromFormat('Y-m-d', $validatedData['published_at_date'])->startOfDay();
        } else {
            $newsItem->published_at = null;
        }

        if ($request->hasFile('cover_image')) {
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('news_covers', $fileName, 'public');
            $newsItem->cover_image_path = $path;
        }

        //  SEO ET LES AUTRES TEXTES :
        $newsItem->summary = $validatedData['summary']; // Assignation du résumé
        $newsItem->content = $validatedData['content']; // Assignation du contenu

        // Si meta_title est fourni, on l'utilise, sinon on prend les 60 premiers caractères du titre.
        $newsItem->meta_title = $validatedData['meta_title'] ?? Str::limit(strip_tags($validatedData['title']), 70, '');
        // Si meta_description est fournie, on l'utilise, sinon on prend les 160 premiers caractères du résumé (s'il existe) ou du contenu.
        $summaryForMeta = $validatedData['summary'] ? strip_tags($validatedData['summary']) : strip_tags($validatedData['content']);
        $newsItem->meta_description = $validatedData['meta_description'] ?? Str::limit($summaryForMeta, 160, '...');


        $newsItem->save();

        return redirect()->route('admin.news.index')
                        ->with('success', 'Actualité "' . $newsItem->title . '" créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news) // Laravel va automatiquement trouver l'actualité par son ID ou slug si configuré
    {
        $news->load('user'); // Charger l'auteur
        return view('admin.news.show', ['newsItem' => $news]); // Renommer en $newsItem pour la vue
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', ['newsItem' => $news]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $validatedData = $request->validate($this->validationRules($news));

        $news->title = $validatedData['title'];
        $news->slug = $validatedData['slug'];
        // $news->summary = $validatedData['summary']; // Déplacé
        // $news->content = $validatedData['content']; // Déplacé
        $news->is_featured = $request->boolean('is_featured');

        // Gestion de la date et heure de publication (ce code reste le même)
        if ($request->filled('published_at_date') && $request->filled('published_at_time')) {
            $news->published_at = Carbon::createFromFormat('Y-m-d H:i', $validatedData['published_at_date'] . ' ' . $validatedData['published_at_time']);
        } elseif ($request->filled('published_at_date')) {
            $news->published_at = Carbon::createFromFormat('Y-m-d', $validatedData['published_at_date'])->startOfDay();
        } else {
            $news->published_at = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image_path && Storage::disk('public')->exists($news->cover_image_path)) {
                Storage::disk('public')->delete($news->cover_image_path);
            }
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('news_covers', $fileName, 'public');
            $news->cover_image_path = $path;
        } elseif ($request->boolean('remove_cover_image')) {
            if ($news->cover_image_path && Storage::disk('public')->exists($news->cover_image_path)) {
                Storage::disk('public')->delete($news->cover_image_path);
            }
            $news->cover_image_path = null;
        }

        //  LES CHAMPS SEO ET LES AUTRES TEXTES :
        $news->summary = $validatedData['summary'];
        $news->content = $validatedData['content'];

        $news->meta_title = $validatedData['meta_title'] ?? Str::limit(strip_tags($validatedData['title']), 70, '');
        $summaryForMeta = $validatedData['summary'] ? strip_tags($validatedData['summary']) : strip_tags($validatedData['content']);
        $news->meta_description = $validatedData['meta_description'] ?? Str::limit($summaryForMeta, 160, '...');

        $news->save();

        return redirect()->route('admin.news.index')
                        ->with('success', 'Actualité "' . $news->title . '" mise à jour avec succès.');
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

        return redirect()->route('admin.news.index')
                         ->with('success', 'Actualité "' . $newsTitle . '" supprimée avec succès.');
    }
}
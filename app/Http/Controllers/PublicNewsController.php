<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PublicNewsController extends Controller
{
    /**
     * Affiche la liste paginée des actualités.
     */
    public function index(Request $request)
    {
        $pageTitle = "Actualités du CRPQA";

        $query = News::query()
            ->where('is_published', true)
            ->where('published_at', '<=', now());

        if ($searchTerm = $request->input('search')) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title_fr', 'like', "%{$searchTerm}%")
                  ->orWhere('content_fr', 'like', "%{$searchTerm}%");
            });
        }

        if ($categorySlug = $request->input('category')) {
            $category = NewsCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('news_category_id', $category->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $sortOrder = $request->input('sort', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        $query->orderBy('published_at', $sortOrder);

        $newsItems = $query->paginate(10)->withQueryString();

        $categories = NewsCategory::where('is_active', true)
            ->withCount(['news' => function ($q) {
                $q->where('is_published', true)
                  ->where('published_at', '<=', now());
            }])
            ->orderBy('name')
            ->get()
            ->filter(fn($category) => $category->news_count > 0);

        $archives = News::select(
                DB::raw('YEAR(published_at) as year'),
                DB::raw('MONTH(published_at) as month_number'),
                DB::raw('COUNT(*) as count')
            )
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->groupBy('year', 'month_number')
            ->orderBy('year', 'desc')
            ->orderBy('month_number', 'desc')
            ->get()
            ->map(fn($item) => tap($item, function ($i) {
                $date = Carbon::createFromDate($i->year, $i->month_number, 1);
                $i->month_name_fr = ucfirst($date->translatedFormat('F'));
            }));

        return view('public.news.index', compact(
            'pageTitle',
            'newsItems',
            'categories',
            'archives',
            'searchTerm',
            'categorySlug',
            'sortOrder'
        ));
    }

    /**
     * Affiche le détail d'une actualité.
     *
     * @param  \App\Models\News  $news
     */
    public function show(News $news)
    {
        $pageTitle = $news->getTranslation('title', app()->getLocale());

        // Articles similaires (jusqu'à 3)
        $similarNews = collect();
        if ($news->news_category_id) {
            $similarNews = News::where('news_category_id', $news->news_category_id)
                ->where('id', '!=', $news->id)
                ->where('is_published', true)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
        }

        if ($similarNews->count() < 3) {
            $needed = 3 - $similarNews->count();
            $recentNews = News::where('id', '!=', $news->id)
                ->whereNotIn('id', $similarNews->pluck('id')->all())
                ->where('is_published', true)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->take($needed)
                ->get();
            $similarNews = $similarNews->merge($recentNews);
        }

        // URL de l'article pour les partages
        $articleUrl = route('public.news.show', ['news' => $news->slug]);

        $shareLinks = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($articleUrl),
            'twitter'  => 'https://twitter.com/intent/tweet?url=' . urlencode($articleUrl) . '&text=' . urlencode($pageTitle),
            'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($articleUrl) . '&title=' . urlencode($pageTitle),
        ];

        return view('public.news.show', compact(
            'pageTitle',
            'news',
            'similarNews',
            'shareLinks'
        ));
    }
}

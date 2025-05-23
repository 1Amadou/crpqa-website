@extends('layouts.public')

@section('title', $pageTitle ?? "Actualités du CRPQA")

@push('page-styles')
<style>
    /* Styles pour les filtres et la sidebar, si non définis globalement */
    .news-hero { padding: 3rem 0; background-color: var(--first-color-lighten); text-align: center; }
    .news-hero__title { font-size: var(--h1-font-size); color: var(--title-color); margin-bottom: var(--mb-0-5); }
    .news-hero .breadcrumb { background-color: transparent; justify-content: center; padding: 0; margin-bottom: 0; }
    .news-hero .breadcrumb-item a { color: var(--first-color); }
    .news-hero .breadcrumb-item.active { color: var(--text-color-light); }

    .news-filters { background-color: #fff; padding: var(--mb-1-5); border-radius: var(--radius); box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: var(--mb-2); }
    .news-filters .form-group { margin-bottom: var(--mb-1); }
    .news-filters .form-group:last-child { margin-bottom: 0; }
    .news-filters label { display: block; font-weight: var(--font-medium); color: var(--title-color); margin-bottom: var(--mb-0-25); font-size: var(--small-font-size); }
    .news-filters .form__input, .news-filters .form__select {
        width: 100%; padding: var(--mb-0-75) var(--mb-1); border: 1px solid var(--border-color, #ccc);
        border-radius: var(--radius-small, .25rem); font-size: var(--normal-font-size); background-color: #fff;
        color: var(--text-color); line-height: 1.5;
    }
    .news-filters .form__input:focus, .news-filters .form__select:focus {
        border-color: var(--first-color); outline: 0; box-shadow: 0 0 0 0.2rem rgba(var(--first-color-rgb), 0.25);
    }
    .news-filters .grid { align-items: flex-end; gap: var(--mb-1); }
    .news-filters .button { width: 100%; }

    .news-layout__grid { display: grid; gap: var(--mb-2, 2rem); }
    @media (min-width: 992px) { .news-layout__grid { grid-template-columns: 3fr 1fr; } }
    
    .sidebar__widget { background-color: var(--first-color-lightest, #f8faff); padding: var(--mb-1-5); border-radius: var(--radius); margin-bottom: var(--mb-1-5); }
    .sidebar__widget-title { font-size: var(--h3-font-size); color: var(--title-color); margin-bottom: var(--mb-1); padding-bottom: var(--mb-0-75); border-bottom: 1px solid var(--border-color, #eee); }
    .sidebar__widget ul { list-style: none; padding: 0; }
    .sidebar__widget ul li a { color: var(--text-color); text-decoration: none; font-size: var(--normal-font-size); padding: var(--mb-0-5) 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dotted var(--border-color-light, #e9e9e9); }
    .sidebar__widget ul li a:hover, .sidebar__widget ul li a.active { color: var(--first-color); }
    .sidebar__widget ul li:last-child a { border-bottom: none; }
    .sidebar__widget ul li .count { color: var(--text-color-light); font-size: var(--small-font-size); background-color: var(--body-color); padding: 2px 6px; border-radius: var(--radius-small); }
    
    .alert-info { background-color: var(--first-color-lighter); color: var(--text-color); padding: var(--mb-1-5); border-radius: var(--radius); text-align: center; border: 1px solid var(--first-color-lighten); }

    /* Les classes .news__* doivent être définies dans votre style.css ou home.css */
    /* Si elles ne le sont pas, la mise en page des cartes d'actualités sera basique. */
    .news-list__main-container .news__card { margin-bottom: var(--mb-2); } /* Espace entre les cartes si .grid ne le fait pas */

</style>
@endpush

@section('content')
    <main class="main">
        <section class="news-hero section--bg">
            <div class="container">
                <h1 class="news-hero__title" data-aos="fade-down">{{ $pageTitle }}</h1>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Accueil</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="padding-left: .5rem;">&nbsp;/ Actualités</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="news-listing-section section">
            <div class="container">
                <form action="{{ route('public.news.index') }}" method="GET" class="news-filters" data-aos="fade-up">
                    <div class="grid"> 
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" name="search" id="search" class="form__input" placeholder="Mots-clés..." value="{{ $searchTerm ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select name="category" id="category" class="form__select">
                                <option value="">Toutes</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ ($categorySlug ?? '') == $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name_fr ?? $cat->name }} {{-- Adaptez si NewsCategory est localisée --}}
                                        ({{ $cat->news_items_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sort">Trier par</label>
                            <select name="sort" id="sort" class="form__select">
                                <option value="desc" {{ ($sortOrder ?? 'desc') == 'desc' ? 'selected' : '' }}>Plus récentes</option>
                                <option value="asc" {{ ($sortOrder ?? 'desc') == 'asc' ? 'selected' : '' }}>Plus anciennes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="button button--flex">
                                <i class="uil uil-filter button__icon"></i>Filtrer
                            </button>
                        </div>
                         @if($searchTerm || $categorySlug)
                        <div class="form-group">
                            <a href="{{ route('public.news.index') }}" class="button button--white button--flex">
                                <i class="uil uil-times button__icon"></i>Effacer
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                <div class="news-layout__grid">
                    <div class="news-list__main-container">
                        @if ($newsItems->count() > 0)
                            {{-- Utilisation de la structure de la page d'accueil pour la grille d'actualités --}}
                            <div class="news__container grid"> 
                                @foreach ($newsItems as $newsItem)
                                    <article class="news__card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                                        @if($newsItem->cover_image_url)
                                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__image-link">
                                            <img src="{{ $newsItem->cover_image_url }}" 
                                                 alt="{{ $newsItem->cover_image_alt_text ?? $newsItem->title }}" class="news__img">
                                        </a>
                                        @else
                                        {{-- Fallback si pas d'image --}}
                                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__image-link" style="background-color: var(--first-color-lightest); display:flex; align-items:center; justify-content:center; height:200px;">
                                            <img src="{{ asset('img/placeholders/news_default.jpg') }}" alt="{{ $newsItem->title }}" class="news__img" style="height:auto; width:auto; max-height:100px;">
                                        </a>
                                        @endif
                                        <div class="news__data">
                                            <span class="news__meta">
                                                {{ $newsItem->published_at ? $newsItem->published_at->translatedFormat('d M Y') : '' }}
                                                @if($newsItem->category)
                                                    &nbsp;|&nbsp;<a href="{{ route('public.news.index', array_merge(request()->except(['category', 'page']), ['category' => $newsItem->category->slug])) }}" class="news__category">
                                                        {{ $newsItem->category->name_fr ?? $newsItem->category->name }} {{-- Adaptez à votre champ de nom de catégorie --}}
                                                    </a>
                                                @endif
                                            </span>
                                            <h3 class="news__title">
                                                <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                                    {{ $newsItem->title }} {{-- Accès direct grâce à la surcharge getAttribute() --}}
                                                </a>
                                            </h3>
                                            <p class="news__description">
                                                {!! Str::limit(strip_tags($newsItem->excerpt), 100) !!} {{-- Accès direct --}}
                                            </p>
                                            <a href="{{ route('public.news.show', $newsItem->slug) }}" class="button button--flex button--small news__button">
                                                Lire la suite <i class="uil uil-arrow-right button__icon"></i>
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            
                            @if ($newsItems->hasPages())
                                <div class="pagination-links mt-5" data-aos="fade-up">
                                    {{ $newsItems->links('vendor.pagination.tailwind') }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info" data-aos="fade-up">
                                <i class="uil uil-info-circle" style="font-size: 1.5rem; margin-right: 0.5rem;"></i>
                                Aucune actualité disponible pour le moment ou correspondant à vos critères.
                                @if($searchTerm || $categorySlug)
                                    <br><a href="{{ route('public.news.index') }}" class="button button--small" style="margin-top: var(--mb-1);">Voir toutes les actualités</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <aside class="sidebar__container" data-aos="fade-left" data-aos-delay="200">
                        @if($categories->isNotEmpty())
                        <div class="sidebar__widget">
                            <h3 class="sidebar__widget-title">Catégories</h3>
                            <ul>
                                <li>
                                    <a href="{{ route('public.news.index', array_filter(request()->except(['category', 'page']))) }}"
                                       class="{{ empty($categorySlug) ? 'active' : '' }}">
                                        Toutes les catégories
                                    </a>
                                </li>
                                @foreach ($categories as $cat)
                                    <li>
                                        <a href="{{ route('public.news.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}" 
                                           class="{{ ($categorySlug ?? '') == $cat->slug ? 'active' : '' }}">
                                            {{ $cat->name_fr ?? $cat->name }} {{-- Adaptez à votre champ de nom de catégorie --}}
                                            <span class="count">{{ $cat->news_items_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($archives->isNotEmpty())
                        <div class="sidebar__widget">
                            <h3 class="sidebar__widget-title">Archives</h3>
                            <ul>
                                @foreach ($archives as $archive)
                                    <li>
                                        <span>{{ $archive->month_name_fr }} {{ $archive->year }} <span class="count">{{ $archive->count }}</span></span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </aside>
                </div>
            </div>
        </section>
    </main>
@endsection
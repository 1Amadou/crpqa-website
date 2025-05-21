@extends('layouts.public')

{{-- Définition des métadonnées pour la page --}}
@php
    $siteSettings = app('siteSettings'); // Assurez-vous que les paramètres du site sont bien injectés
    $pageTitle = $siteSettings['news_page_title'] ?? 'Actualités du CRPQA';
    $metaDescription = $siteSettings['news_page_meta_description'] ?? 'Suivez les dernières actualités, découvertes et annonces du Centre de Recherche en Physique Quantique et ses Applications.';
    
    // Logique robuste pour l'image Open Graph
    $ogImageCandidate = $siteSettings['news_page_og_image_url'] ?? ($siteSettings['og_image_url'] ?? null);
    $ogImage = $ogImageCandidate ? (Str::startsWith($ogImageCandidate, ['http://', 'https://']) ? $ogImageCandidate : Storage::url($ogImageCandidate)) : asset('assets/images/crpqa_og_default.jpg');
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)

@push('styles')
    {{-- IMPORTANT : Déplacez ces styles dans votre fichier CSS principal (e.g., public/assets/css/style.css) --}}
    {{-- La balise <style> ici est juste pour la démonstration --}}
    <style>
       
    </style>
@endpush

@section('content')
    {{-- Section Héro de la page --}}
    <section class="page-hero"
             style="background-image: linear-gradient(rgba(10, 42, 77, 0.8), rgba(29, 44, 90, 0.75)), 
                    url('{{ !empty($siteSettings['news_hero_bg_image_url']) ? (Str::startsWith($siteSettings['news_hero_bg_image_url'], ['http://', 'https://']) ? $siteSettings['news_hero_bg_image_url'] : Storage::url($siteSettings['news_hero_bg_image_url'])) : asset('assets/images/backgrounds/news_hero_default.jpg') }}');">
        <div class="container" data-aos="fade-up">
            <h1 class="page-hero__title">{{ $pageTitle }}</h1>
            <p class="page-hero__subtitle">
                {{ $siteSettings['news_hero_subtitle'] ?? 'Restez informé des dernières avancées, événements et initiatives du CRPQA.' }}
            </p>
        </div>
    </section>

    {{-- Section Principale de la Liste des Actualités --}}
    <section class="section news-list-section">
        <div class="container">
            <div class="grid lg:grid-cols-12 gap-x-sp-3 items-start">
                {{-- Contenu principal (Actualités et Filtres) --}}
                <div class="lg:col-span-8 xl:col-span-9">
                    {{-- Section des Filtres --}}
                    <div class="news-filters" data-aos="fade-up">
                        <form action="{{ route('public.news.index') }}" method="GET" class="items-stretch md:items-end">
                            {{-- Champ de recherche --}}
                            <div class="form__group">
                                <label for="search_term" class="form__label">Rechercher une actualité</label>
                                <input type="search" name="search_term" id="search_term" class="form__input"
                                       placeholder="Entrez un mot-clé..." value="{{ request('search_term') }}">
                            </div>

                            {{-- Filtre par catégorie (si des catégories existent) --}}
                            @if($categories->count() > 0)
                                <div class="form__group">
                                    <label for="category_slug" class="form__label">Filtrer par catégorie</label>
                                    <select name="category_slug" id="category_slug" class="form__select" onchange="this.form.submit()">
                                        <option value="">Toutes les catégories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->slug }}" {{ request('category_slug') == $category->slug ? 'selected' : '' }}>
                                                {{ $category->getLocalizedField('name') }} ({{ $category->news_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Bouton de Réinitialisation des filtres (affiche si des filtres sont actifs) --}}
                            @if(request('search_term') || request('category_slug'))
                                <div class="form__group">
                                    <label class="form__label">&nbsp;</label> {{-- Pour alignement visuel --}}
                                    <a href="{{ route('public.news.index', ['search_term' => request('search_term')]) }}" class="button button--outline button--small w-full md:w-auto">
                                        Réinitialiser les filtres
                                    </a>
                                </div>
                            @else
                                {{-- Si aucun filtre n'est appliqué, on peut montrer un bouton de soumission pour la recherche textuelle --}}
                                <div class="form__group">
                                    <label class="form__label">&nbsp;</label>
                                    <button type="submit" class="button button--primary button--flex w-full md:w-auto">
                                        <ion-icon name="search-outline" class="button__icon"></ion-icon> Rechercher
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>

                    {{-- Liste des Actualités --}}
                    @if($newsItems->count() > 0)
                        <div class="news__container grid md:grid-cols-2 gap-sp-2" data-aos="fade-up" data-aos-delay="100">
                            @foreach($newsItems as $newsItem)
                                <article class="news__card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}"> {{-- Délai d'animation léger --}}
                                    {{-- Lien et image de couverture --}}
                                    <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__img-link" aria-label="Lire : {{ $newsItem->getLocalizedField('title') }}">
                                        <img src="{{ $newsItem->cover_image_url }}" 
                                             alt="{{ $newsItem->cover_image_alt }}" 
                                             class="news__img">
                                    </a>
                                    
                                    <div class="news__data">
                                        {{-- Métadonnées (Date et Catégorie) --}}
                                        <p class="news__meta">
                                            @if($newsItem->published_at) 
                                                <time datetime="{{ $newsItem->published_at->toDateString() }}">{{ $newsItem->published_at->isoFormat('D MMM YYYY') }}</time> 
                                            @endif
                                            @if($newsItem->category)
                                                <span class="news__category-separator">|</span>
                                                <a href="{{ route('public.news.index', ['category_slug' => $newsItem->category->slug]) }}" 
                                                   class="news__category-link"
                                                   style="color: {{ $newsItem->category->color ?? 'var(--accent-color-cyan)' }};">
                                                    {{ $newsItem->category->getLocalizedField('name') }}
                                                </a>
                                            @endif
                                        </p>
                                        
                                        {{-- Titre de l'actualité --}}
                                        <h3 class="news__title">
                                            <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                                {{ Str::limit($newsItem->getLocalizedField('title'), 60) }}
                                            </a>
                                        </h3>
                                        
                                        {{-- Description courte / Résumé --}}
                                        @if($newsItem->getLocalizedField('short_content'))
                                            <p class="news__description">
                                                {{ Str::limit(strip_tags($newsItem->getLocalizedField('short_content')), 120) }} {{-- Ajusté à 120 caractères --}}
                                            </p>
                                        @endif
                                        
                                        {{-- Lien "Lire la suite" --}}
                                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__link">
                                            Lire la suite <ion-icon name="arrow-forward-outline"></ion-icon>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Section de Pagination --}}
                        @if ($newsItems->hasPages())
                            <div class="pagination-nav mt-sp-3" data-aos="fade-up">
                                {{ $newsItems->links('vendor.pagination.tailwind') }} {{-- Assurez-vous que cette vue de pagination existe --}}
                            </div>
                        @endif
                    @else
                        {{-- Message si aucune actualité n'est trouvée --}}
                        <div class="news-list-empty py-sp-3" data-aos="fade-up">
                            <ion-icon name="newspaper-outline"></ion-icon>
                            <p>
                                Aucune actualité disponible pour le moment 
                                @if(request('search_term') || request('category_slug')) 
                                    correspondant à vos critères.
                                @endif
                            </p>
                            @if(request('search_term') || request('category_slug'))
                                <p class="mt-sp-1">
                                    <a href="{{ route('public.news.index') }}" class="button button--outline button--small">
                                        Voir toutes les actualités
                                    </a>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="lg:col-span-4 xl:col-span-3 news-sidebar mt-sp-3 lg:mt-0" data-aos="fade-left" data-aos-delay="200">
                    @if($categories->count() > 0)
                        <div class="sidebar-widget">
                            <h3 class="sidebar-widget__title">Catégories</h3>
                            <ul>
                                <li>
                                    <a href="{{ route('public.news.index', array_filter(request()->except(['category_slug', 'page']))) }}" {{-- Conserve les autres filtres sauf catégorie et page --}}
                                       class="sidebar-widget__link {{ !request('category_slug') ? 'active-category' : '' }}">
                                       Toutes les catégories
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('public.news.index', array_merge(request()->except(['category_slug', 'page']), ['category_slug' => $category->slug])) }}"
                                           class="sidebar-widget__link {{ request('category_slug') == $category->slug ? 'active-category' : '' }}">
                                            <span>{{ $category->getLocalizedField('name') }}</span>
                                            <span class="count">{{ $category->news_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
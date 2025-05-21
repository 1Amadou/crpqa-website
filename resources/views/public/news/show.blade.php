@extends('layouts.public')

{{-- Définition des métadonnées pour la page --}}
@php
    $siteSettings = app('siteSettings'); // Accès aux settings globaux
    $pageTitle = $news->getLocalizedField('meta_title') ?: $news->getLocalizedField('title');
    $metaDescription = $news->getLocalizedField('meta_description') ?: Str::limit(strip_tags($news->getLocalizedField('short_content') ?: $news->getLocalizedField('content')), 160);

    // Logique robuste pour l'image Open Graph
    $ogImageCandidate = $news->cover_image_url ?? ($siteSettings['og_image_url'] ?? null);
    $ogImage = $ogImageCandidate ? (Str::startsWith($ogImageCandidate, ['http://', 'https://']) ? $ogImageCandidate : Storage::url($ogImageCandidate)) : asset('assets/images/crpqa_og_default.jpg');
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)
<meta property="og:type" content="article">
@if($news->published_at) <meta property="article:published_time" content="{{ $news->published_at->toIso8601String() }}"> @endif
@if($news->user) <meta property="article:author" content="{{ $news->user->name }}"> @endif
@if($news->category) <meta property="article:section" content="{{ $news->category->getLocalizedField('name') }}"> @endif

@push('styles')
    {{-- IMPORTANT : Déplacez ces styles dans votre fichier CSS principal (e.g., public/assets/css/style.css) --}}
    {{-- Les styles de .wysiwyg-content devraient être dans un fichier dédié si réutilisé (e.g., public/assets/css/wysiwyg-content.css) --}}
    <style>
        
    </style>
@endpush

@section('content')
    <section class="section news-article-section">
        <div class="container">
            {{-- Fil d'Ariane --}}
            <nav aria-label="Fil d'Ariane" class="breadcrumb" data-aos="fade-down">
                <ol>
                    <li><a href="{{ route('public.home') }}">Accueil</a><ion-icon name="chevron-forward-outline"></ion-icon></li>
                    <li><a href="{{ route('public.news.index') }}">Actualités</a><ion-icon name="chevron-forward-outline"></ion-icon></li>
                    {{-- Limite le titre pour éviter un fil d'Ariane trop long --}}
                    <li><span aria-current="page">{{ Str::limit($news->getLocalizedField('title'), 60) }}</span></li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-12 gap-x-sp-3 items-start">
                {{-- Contenu principal de l'article --}}
                <article class="lg:col-span-8 xl:col-span-9 news-article__main-content" data-aos="fade-up">
                    <header class="news-article__header">
                        <h1 class="news-article__title">{{ $news->getLocalizedField('title') }}</h1>
                        <div class="news-article__meta">
                            {{-- Date de publication --}}
                            @if($news->published_at)
                            <span class="news-article__meta-item" title="Date de publication">
                                <ion-icon name="calendar-clear-outline"></ion-icon>
                                <time datetime="{{ $news->published_at->toDateString() }}">{{ $news->published_at->isoFormat('D MMMM YYYY') }}</time>
                            </span>
                            @endif
                            {{-- Auteur --}}
                            @if($news->user)
                            <span class="news-article__meta-item" title="Auteur">
                                <ion-icon name="person-circle-outline"></ion-icon>
                                <span>Par {{ $news->user->name }}</span>
                            </span>
                            @endif
                            {{-- Catégorie --}}
                            @if($news->category)
                            <span class="news-article__meta-item" title="Catégorie">
                                <ion-icon name="pricetag-outline"></ion-icon>
                                <a href="{{ route('public.news.index', ['category_slug' => $news->category->slug]) }}" class="hover:underline"
                                   style="color: {{ $news->category->color ?? 'var(--accent-color-cyan)' }}; font-weight: var(--font-semi-bold);">
                                    {{ $news->category->getLocalizedField('name') }}
                                </a>
                            </span>
                            @endif
                        </div>
                    </header>

                    {{-- Image de couverture de l'article --}}
                    @if($news->cover_image_url)
                    <img src="{{ $news->cover_image_url }}"
                         alt="{{ $news->cover_image_alt }}"
                         class="news-article__cover-image" data-aos="zoom-in" data-aos-delay="100">
                    @endif

                    {{-- Contenu principal (HTML généré par WYSIWYG) --}}
                    <div class="news-article__content wysiwyg-content mt-sp-2" data-aos="fade-up" data-aos-delay="200">
                        {!! $news->getLocalizedField('content') !!}
                    </div>

                    {{-- Galerie d'images (si des images sont présentes) --}}
                    @php
                        // Assurez-vous que $news->gallery_images_json est bien un tableau de données,
                        // et que les URLs sont gérées correctement (relatives ou absolues)
                        $galleryImagesData = $news->gallery_images_json ?? [];
                    @endphp
                    @if(!empty($galleryImagesData) && count($galleryImagesData) > 0)
                    <div class="news-gallery mt-sp-3" data-aos="fade-up">
                        <h3 class="news-gallery__title">Galerie d'Images</h3>
                        <div class="news-gallery__grid">
                            @foreach($galleryImagesData as $imageData)
                                @php
                                    $imageUrl = $imageData['url'] ?? null;
                                    // Assure-toi que l'URL est complète si elle ne l'est pas déjà
                                    if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
                                        $imageUrl = Storage::url($imageUrl);
                                    }
                                    $imageAlt = $imageData['alt'] ?? ('Image de la galerie pour ' . $news->getLocalizedField('title'));
                                    $imageCaption = $imageData['caption'] ?? $imageAlt;
                                @endphp
                                @if($imageUrl)
                                <div class="news-gallery__item">
                                    {{-- Utilisation de fslightbox avec data-fslightbox --}}
                                    <a href="{{ $imageUrl }}" data-fslightbox="news-gallery-{{ $news->id }}" data-caption="{{ $imageCaption }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}">
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Section de partage social --}}
                    @if($siteSettings['enable_social_sharing_news'] ?? true) {{-- Activer par défaut si non défini --}}
                    <div class="news-article__share" data-aos="fade-up">
                        <strong class="news-article__share-label">Partager cet article :</strong>
                        <div class="news-article__share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Partager sur Facebook"><ion-icon name="logo-facebook"></ion-icon></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->getLocalizedField('title')) }}" target="_blank" rel="noopener noreferrer" title="Partager sur Twitter"><ion-icon name="logo-twitter"></ion-icon></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($news->getLocalizedField('title')) }}&summary={{ urlencode($metaDescription) }}" target="_blank" rel="noopener noreferrer" title="Partager sur LinkedIn"><ion-icon name="logo-linkedin"></ion-icon></a>
                            <a href="mailto:?subject={{ urlencode($news->getLocalizedField('title')) }}&body={{ urlencode('J\'ai trouvé cet article intéressant : ' . url()->current()) }}" title="Partager par Email"><ion-icon name="mail-outline"></ion-icon></a>
                            {{-- Bouton Copier le lien avec feedback visuel --}}
                            <button type="button" class="copy-link-button" title="Copier le lien"
                                    onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { this.innerHTML = '<ion-icon name=\'checkmark-done-outline\' style=\'color:green;\'></ion-icon>'; setTimeout(() => { this.innerHTML = '<ion-icon name=\'link-outline\'></ion-icon>'; }, 2500); }).catch(err => console.error('Erreur copie lien: ', err));">
                                <ion-icon name="link-outline"></ion-icon>
                            </button>
                        </div>
                    </div>
                    @endif
                </article>

                {{-- Sidebar --}}
                <aside class="lg:col-span-4 xl:col-span-3 news-sidebar mt-sp-3 lg:mt-0" data-aos="fade-left" data-aos-delay="300">
                    {{-- IMPORTANT: Déplacez ces requêtes dans le contrôleur (NewsController) --}}
                    @php
                        // Récupération des catégories pour la sidebar
                        $sidebarCategories = \App\Models\NewsCategory::where('is_active', true)
                            ->whereHas('news', fn($q) => $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now()))
                            ->withCount(['news' => fn($q) => $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())])
                            ->orderBy('name->'.app()->getLocale())->get();

                        // Récupération des actualités récentes pour la sidebar
                        $sidebarRecentNews = \App\Models\News::where('is_published', true)
                            ->whereNotNull('published_at')->where('published_at', '<=', now())
                            ->where('id', '!=', $news->id) // Exclut l'article actuel
                            // Exclut également les articles déjà affichés dans la section "Articles Liés" si $relatedNews est défini
                            ->when(isset($relatedNews) && $relatedNews->count() > 0, function ($query) use ($relatedNews) {
                                $query->whereNotIn('id', $relatedNews->pluck('id')->toArray());
                            })
                            ->orderBy('published_at', 'desc')
                            ->take(5)->get();
                    @endphp

                    {{-- Widget Catégories --}}
                    @if($sidebarCategories->count() > 0)
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget__title">Catégories</h3>
                        <ul>
                            {{-- Lien vers toutes les actualités --}}
                            <li>
                                <a href="{{ route('public.news.index') }}" 
                                   class="sidebar-widget__link {{ !request('category_slug') && Route::currentRouteName() == 'public.news.index' ? 'active-category' : '' }}">
                                   Toutes les catégories
                                </a>
                            </li>
                            {{-- Liste des catégories --}}
                            @foreach($sidebarCategories as $category)
                            <li>
                                <a href="{{ route('public.news.index', ['category_slug' => $category->slug]) }}"
                                   class="sidebar-widget__link {{ isset($news->category) && $news->category->slug == $category->slug ? 'active-category' : '' }}">
                                    <span>{{ $category->getLocalizedField('name') }}</span>
                                    <span class="count">{{ $category->news_count }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Widget Autres Actualités / Actualités Récentes --}}
                    @if($sidebarRecentNews->count() > 0)
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget__title">Autres Actualités</h3>
                        <ul>
                            @foreach($sidebarRecentNews as $recentNewsItem)
                            <li>
                                <a href="{{ route('public.news.show', $recentNewsItem->slug) }}" class="sidebar-widget__link">
                                    {{ Str::limit($recentNewsItem->getLocalizedField('title'), 45) }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </aside>
            </div>

            {{-- Section "Vous Pourriez Aussi Aimer" (Articles liés) --}}
            @if(isset($relatedNews) && $relatedNews->count() > 0)
            <section class="related-articles__section" data-aos="fade-up">
                <h2 class="related-articles__title">Vous Pourriez Aussi Aimer</h2>
                <div class="news__container grid md:grid-cols-2 lg:grid-cols-3 gap-sp-2">
                    @foreach($relatedNews as $relatedItem)
                        <article class="news__card">
                            <a href="{{ route('public.news.show', $relatedItem->slug) }}" class="news__img-link" aria-label="Lire : {{ $relatedItem->getLocalizedField('title') }}">
                                <img src="{{ $relatedItem->cover_image_url }}"
                                     alt="{{ $relatedItem->cover_image_alt }}"
                                     class="news__img">
                            </a>
                            <div class="news__data">
                                <p class="news__meta">
                                    @if($relatedItem->published_at) <time datetime="{{ $relatedItem->published_at->toDateString() }}">{{ $relatedItem->published_at->isoFormat('D MMM YYYY') }}</time> @endif
                                    @if($relatedItem->category)
                                        <span class="news__category-separator">|</span>
                                        <a href="{{ route('public.news.index', ['category_slug' => $relatedItem->category->slug]) }}" class="news__category-link"
                                           style="color: {{ $relatedItem->category->color ?? 'var(--accent-color-cyan)' }};">
                                            {{ $relatedItem->category->getLocalizedField('name') }}
                                        </a>
                                    @endif
                                </p>
                                <h3 class="news__title">
                                    <a href="{{ route('public.news.show', $relatedItem->slug) }}">
                                        {{ Str::limit($relatedItem->getLocalizedField('title'), 60) }}
                                    </a>
                                </h3>
                                {{-- La description est cachée pour les articles liés via CSS --}}
                                <a href="{{ route('public.news.show', $relatedItem->slug) }}" class="news__link mt-auto">
                                    Lire la suite <ion-icon name="arrow-forward-outline"></ion-icon>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Si vous utilisez fslightbox.js pour la galerie, assurez-vous qu'il est chargé. --}}
    {{-- Exemple (si non chargé globalement): <script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.4.1/fslightbox.min.js" defer></script> --}}
@endpush
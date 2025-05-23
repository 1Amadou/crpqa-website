@extends('layouts.public')

@section('title', $pageTitle ?? "Détail de l'Actualité")

@push('page-styles')
<style>
    /* Styles spécifiques pour la page de détail d'actualité */
    .news-detail-hero {
        padding: 3rem 0;
        background-color: var(--first-color-lighten);
        text-align: center;
    }
    .news-detail-hero__title {
        font-size: var(--h1-font-size);
        color: var(--title-color);
        margin-bottom: var(--mb-0-5);
    }
    .news-detail-hero .breadcrumb-item a { color: var(--first-color); }
    .news-detail-hero .breadcrumb-item.active { color: var(--text-color-light); }

    .news-detail__header { margin-bottom: var(--mb-2); }
    .news-detail__title {
        font-size: calc(var(--h1-font-size) * 1.4); /* Titre principal plus grand */
        color: var(--title-color);
        margin-bottom: var(--mb-1);
        line-height: 1.3;
        font-weight: var(--font-bold);
    }
    .news-detail__meta {
        font-size: var(--small-font-size);
        color: var(--text-color-light);
        margin-bottom: var(--mb-1-5);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--mb-0-5) var(--mb-1-5); /* Espace entre les métadonnées */
    }
    .news-detail__meta .meta-item { display: inline-flex; align-items: center; }
    .news-detail__meta .meta-item .uil {
        margin-right: 0.5em;
        color: var(--first-color);
        font-size: 1.3em;
    }
    .news-detail__meta .meta-item a { color: var(--first-color); text-decoration: none; }
    .news-detail__meta .meta-item a:hover { text-decoration: underline; }
    
    .news-detail__cover-image {
        width: 100%;
        max-height: 500px; /* Limiter la hauteur de l'image de couverture */
        object-fit: cover;
        border-radius: var(--radius);
        margin-bottom: var(--mb-2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .news-detail__content {
        font-size: var(--normal-font-size); /* Ou var(--p-font-size) si défini */
        line-height: 1.8;
        color: var(--text-color);
    }
    /* Styles pour le contenu WYSIWYG (à mettre dans wysiwyg-content.css idéalement) */
    .news-detail__content h1, .news-detail__content h2, .news-detail__content h3, 
    .news-detail__content h4, .news-detail__content h5, .news-detail__content h6 {
        color: var(--title-color);
        margin-top: var(--mb-1-5);
        margin-bottom: var(--mb-0-75);
        font-weight: var(--font-semi-bold);
    }
    .news-detail__content h1 { font-size: calc(var(--h1-font-size) * 0.9); }
    .news-detail__content h2 { font-size: calc(var(--h2-font-size) * 0.9); }
    .news-detail__content h3 { font-size: calc(var(--h3-font-size) * 0.9); }
    .news-detail__content p { margin-bottom: var(--mb-1); }
    .news-detail__content ul, .news-detail__content ol { margin-bottom: var(--mb-1); padding-left: var(--mb-1-5); }
    .news-detail__content ul li { list-style: disc; }
    .news-detail__content ol li { list-style: decimal; }
    .news-detail__content li { margin-bottom: var(--mb-0-25); }
    .news-detail__content a { color: var(--first-color); text-decoration: underline; }
    .news-detail__content img { max-width: 100%; height: auto; border-radius: var(--radius-small); margin: var(--mb-1) 0; }
    .news-detail__content blockquote {
        border-left: 4px solid var(--first-color-lighter);
        padding-left: var(--mb-1);
        margin: var(--mb-1-5) 0;
        font-style: italic;
        color: var(--text-color-light);
    }
    /* Fin styles WYSIWYG */

    .news-detail__share { margin-top: var(--mb-2); padding-top: var(--mb-1-5); border-top: 1px solid var(--border-color, #eee); }
    .news-detail__share-title { font-size: var(--normal-font-size); font-weight: var(--font-medium); color: var(--text-color); margin-bottom: var(--mb-0-75); }
    .news-detail__share-links a {
        display: inline-block;
        margin-right: var(--mb-0-5);
        padding: 0.5rem 0.8rem;
        border-radius: var(--radius-small);
        color: #fff;
        text-decoration: none;
        font-size: var(--small-font-size);
        transition: opacity 0.2s ease;
    }
    .news-detail__share-links a:hover { opacity: 0.85; }
    .share-facebook { background-color: #3b5998; }
    .share-twitter { background-color: #1da1f2; }
    .share-linkedin { background-color: #0077b5; }
    /* .share-whatsapp { background-color: #25d366; } */
    .news-detail__share-links .uil { margin-right: 0.3em; font-size: 1.1em; vertical-align: middle;}

    .similar-news__title { /* Pour "Actualités Similaires" */
        font-size: var(--h2-font-size);
        color: var(--title-color);
        text-align: center;
        margin-top: var(--mb-3);
        margin-bottom: var(--mb-2);
    }
    /* Utilisation des classes .news__* de la page d'accueil pour les actualités similaires */
</style>
@endpush

@section('content')
    <main class="main">
        {{-- Section Hero Titre --}}
        <section class="news-detail-hero section--bg" data-aos="fade-in">
            <div class="container">
                {{-- Titre de l'actualité peut aussi être ici si plus approprié pour le design --}}
                {{-- <h1 class="news-detail-hero__title" data-aos="fade-down">{{ $newsItem->getTranslation('title', app()->getLocale()) }}</h1> --}}
                <h2 class="news-detail-hero__title" data-aos="fade-down">Actualité</h2> {{-- Titre générique de section --}}
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('public.news.index') }}">Actualités</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="padding-left: .5rem;">&nbsp;/ {{ Str::limit($newsItem->getTranslation('title', app()->getLocale()), 30) }}</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="news-detail-section section">
            <div class="container">
                <div class="row justify-content-center"> {{-- Centre le contenu si la colonne est plus étroite --}}
                    <div class="col-lg-9"> {{-- Colonne principale pour le contenu de l'actualité --}}
                        <article class="news-detail__article">
                            <header class="news-detail__header" data-aos="fade-up">
                                <h1 class="news-detail__title">{{ $newsItem->getTranslation('title', app()->getLocale()) }}</h1>
                                <div class="news-detail__meta">
                                    @if($newsItem->published_at)
                                    <span class="meta-item"><i class="uil uil-calendar-alt"></i>Publié le {{ $newsItem->published_at->translatedFormat('d F Y') }}</span>
                                    @endif
                                    @if($newsItem->category)
                                    <span class="meta-item">
                                        <i class="uil uil-folder"></i>
                                        <a href="{{ route('public.news.index', ['category' => $newsItem->category->slug]) }}">
                                            {{ $newsItem->category->name_fr ?? $newsItem->category->name }}
                                        </a>
                                    </span>
                                    @endif
                                    @if($newsItem->createdBy)
                                    <span class="meta-item"><i class="uil uil-user"></i>Par {{ $newsItem->createdBy->name }}</span>
                                    @endif
                                </div>
                            </header>

                            @if($newsItem->cover_image_path)
                            <img src="{{ $newsItem->cover_image_url }}" 
                                 alt="{{ $newsItem->getTranslation('cover_image_alt_text', app()->getLocale()) ?? $newsItem->getTranslation('title', app()->getLocale()) }}" 
                                 class="news-detail__cover-image" data-aos="zoom-in-up">
                            @endif

                            <div class="news-detail__content wysiwyg-content" data-aos="fade-up" data-aos-delay="100">
                                {{-- Le contenu HTML de l'actualité sera rendu ici --}}
                                {!! $newsItem->getTranslation('content', app()->getLocale()) !!}
                            </div>

                            {{-- Boutons de Partage --}}
                            <div class="news-detail__share" data-aos="fade-up">
                                <h4 class="news-detail__share-title">Partager cette actualité :</h4>
                                <div class="news-detail__share-links">
                                    <a href="{{ $shareLinks['facebook'] }}" target="_blank" class="share-facebook" title="Partager sur Facebook"><i class="uil uil-facebook-f"></i> Facebook</a>
                                    <a href="{{ $shareLinks['twitter'] }}" target="_blank" class="share-twitter" title="Partager sur Twitter"><i class="uil uil-twitter"></i> Twitter</a>
                                    <a href="{{ $shareLinks['linkedin'] }}" target="_blank" class="share-linkedin" title="Partager sur LinkedIn"><i class="uil uil-linkedin"></i> LinkedIn</a>
                                    {{-- <a href="{{ $shareLinks['whatsapp'] }}" target="_blank" class="share-whatsapp" title="Partager sur WhatsApp"><i class="uil uil-whatsapp"></i> WhatsApp</a> --}}
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                {{-- Section Actualités Similaires --}}
                @if ($similarNews->isNotEmpty())
                    <section class="similar-news-section mt-5 pt-4" data-aos="fade-up">
                        <hr class="section-divider">
                        <h2 class="similar-news__title">Actualités Similaires</h2>
                        <div class="news__container grid"> {{-- Utilisation des classes de home.blade.php --}}
                            @foreach ($similarNews as $similarNewsItem)
                                <article class="news__card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                    @if($similarNewsItem->cover_image_path)
                                    <a href="{{ route('public.news.show', $similarNewsItem->slug) }}" class="news__image-link">
                                        <img src="{{ $similarNewsItem->cover_image_url }}" 
                                             alt="{{ $similarNewsItem->getTranslation('cover_image_alt_text', app()->getLocale()) ?? $similarNewsItem->getTranslation('title', app()->getLocale()) }}" class="news__img">
                                    </a>
                                    @else
                                    <a href="{{ route('public.news.show', $similarNewsItem->slug) }}" class="news__image-link">
                                         <img src="{{ asset('img/placeholders/news_default.jpg') }}" alt="{{ $similarNewsItem->getTranslation('title', app()->getLocale()) }}" class="news__img">
                                    </a>
                                    @endif
                                    <div class="news__data">
                                        <span class="news__meta">
                                            {{ $similarNewsItem->published_at ? $similarNewsItem->published_at->translatedFormat('d M Y') : '' }}
                                            @if($similarNewsItem->category)
                                                &nbsp;|&nbsp;<a href="{{ route('public.news.index', ['category' => $similarNewsItem->category->slug]) }}" class="news__category">{{ $similarNewsItem->category->name_fr ?? $similarNewsItem->category->name }}</a>
                                            @endif
                                        </span>
                                        <h3 class="news__title">
                                            <a href="{{ route('public.news.show', $similarNewsItem->slug) }}">
                                                {{ $similarNewsItem->getTranslation('title', app()->getLocale()) }}
                                            </a>
                                        </h3>
                                        {{-- Pas d'extrait ici pour garder les cartes plus compactes, ou un extrait plus court --}}
                                        {{-- <p class="news__description">{!! Str::limit(strip_tags($similarNewsItem->getTranslation('excerpt', app()->getLocale())), 70) !!}</p> --}}
                                        <a href="{{ route('public.news.show', $similarNewsItem->slug) }}" class="button button--link button--small news__button" style="align-self: flex-start; margin-top: var(--mb-0-75);">
                                            Lire la suite
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </section>
    </main>
@endsection

@push('page-scripts')
{{-- Scripts spécifiques si besoin (par exemple, pour des galeries d'images si implémentées) --}}
@endpush
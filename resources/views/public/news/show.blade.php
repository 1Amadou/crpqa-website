@extends('layouts.public')

{{-- $news (instance de News), $metaTitle, $metaDescription, $ogImage, $relatedNews sont passés par le contrôleur --}}
{{-- $siteSettings est global --}}

@section('title', $metaTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'article')
@if($news->published_at)
    @section('article_published_time', $news->published_at->toIso8601String())
@endif
@if($news->updated_at)
    @section('article_modified_time', $news->updated_at->toIso8601String())
@endif
{{-- @section('article_author', $news->user?->name) --}}
{{-- @section('article_section', $news->category?->name) --}}


@push('styles')
<style>
    .news-show-hero { padding-top: calc(var(--header-height, 4rem) + 1rem); padding-bottom: 2rem; text-align: center; position: relative; background-size:cover; background-position: center; color:white; }
    .news-show-hero__overlay { position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)); z-index: 0;}
    .news-show-hero .container { position: relative; z-index: 1; }
    .news-show-hero__title { font-size: clamp(1.8rem, 4vw, 2.75rem); font-weight: 700; line-height: 1.2; margin-bottom: 0.75rem; text-shadow: 0 2px 4px rgba(0,0,0,0.6); }
    .news-show-hero__meta { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9; }
    .news-show-hero__meta a { color: inherit; text-decoration:none; }
    .news-show-hero__meta a:hover { text-decoration:underline; }
    .news-show-content { padding-top: 2.5rem; padding-bottom: 2.5rem; }
    @media(min-width: 768px){ .news-show-content { padding-top: 3.5rem; padding-bottom: 3.5rem; } }
    .news-content-body .featured-image { margin-bottom: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); max-height: 500px; width:auto; margin-left:auto; margin-right:auto; display:block; }
    .related-news-section { background-color: var(--bg-color-light, #f9fafb); }
    .dark .related-news-section { background-color: var(--dark-bg-color-alt, #1f2937); }
</style>
@endpush

@section('content')
    <main class="main">
        {{-- Section Hero de l'Actualité --}}
        <section class="news-show-hero section--bg" 
                 style="background-image: url('{{ $news->cover_image_url ?: ($siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/news_hero_default.jpg')) }}');"
                 data-aos="fade-in">
            <div class="news-show-hero__overlay"></div>
            <div class="container">
                <div class="news-show-hero__data max-w-3xl mx-auto">
                    @if($news->category)
                    <a href="{{ route('public.news.index', ['category' => $news->category->slug]) }}" class="news__category-badge inline-block mb-2 text-xs font-medium px-2 py-1 rounded"
                       style="background-color: {{ $news->category->color ?: 'rgba(var(--color-primary-rgb),0.8)' }}; color: {{ $news->category->text_color ?: 'white' }};">
                        {{ $news->category->name }}
                    </a>
                    @endif
                    <h1 class="news-show-hero__title" data-aos="fade-up" data-aos-delay="100">{{ $news->title }}</h1>
                    <p class="news-show-hero__meta text-gray-300" data-aos="fade-up" data-aos-delay="200">
                        @if($news->published_at)
                            <time datetime="{{ $news->published_at->toIso8601String() }}">{{ $news->published_at->translatedFormat('d F Y') }}</time>
                        @endif
                        @if($news->user)
                            <span class="mx-1">&bull;</span> {{ __('Par') }} {{ $news->user->name }}
                        @endif
                    </p>
                </div>
            </div>
        </section>

        {{-- Contenu de l'Actualité --}}
        <section class="news-show-content bg-white dark:bg-gray-800">
            <div class="container max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Image de couverture principale dans le contenu si différente du hero ou si pas de hero image --}}
                @if($news->cover_image_url && request()->route()->getName() !== 'public.home') {{-- Éviter double image si extrait sur home --}}
                    {{-- Si vous avez déjà l'image en grand dans le hero, cette image pourrait être plus petite ou différente --}}
                    {{-- Si vous n'utilisez pas de hero avec image, décommentez ceci :
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_image_alt ?: $news->title }}" class="featured-image mb-8">
                    --}}
                @endif

                @if($news->summary)
                    <p class="prose dark:prose-invert text-lg font-semibold text-gray-600 dark:text-gray-300 mb-6 leading-relaxed text-justify" data-aos="fade-up">
                        {!! nl2br(e($news->summary)) !!}
                    </p>
                @endif

                <div class="prose prose-lg lg:prose-xl dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify" data-aos="fade-up" data-aos-delay="100">
                    {!! $news->content !!} {{-- Le contenu principal, géré par WYSIWYG --}}
                </div>

                {{-- Partage sur les réseaux sociaux (optionnel) --}}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700" data-aos="fade-up">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{__('Partager cet article :')}}</h3>
                    <div class="flex space-x-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400" aria-label="Partager sur Facebook">
                            <ion-icon name="logo-facebook" class="text-2xl"></ion-icon>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-sky-500 dark:text-gray-400 dark:hover:text-sky-400" aria-label="Partager sur Twitter">
                            <ion-icon name="logo-twitter" class="text-2xl"></ion-icon>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($news->title) }}&summary={{ urlencode(Str::limit(strip_tags($news->summary ?: $news->content),100)) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-700 dark:text-gray-400 dark:hover:text-blue-500" aria-label="Partager sur LinkedIn">
                            <ion-icon name="logo-linkedin" class="text-2xl"></ion-icon>
                        </a>
                        <a href="mailto:?subject={{ urlencode($news->title) }}&body={{ urlencode(__('Découvrez cet article intéressant: ') . url()->current()) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" aria-label="Partager par email">
                            <ion-icon name="mail-outline" class="text-2xl"></ion-icon>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Actualités Similaires --}}
        @if(isset($relatedNews) && $relatedNews->count() > 0)
        <section class="section related-news-section" id="related-news">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section__title text-center" data-aos="fade-up">{{ __('Actualités Similaires') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($relatedNews as $index => $relatedItem)
                    <article class="section-card news__card group" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                         <a href="{{ route('public.news.show', $relatedItem->slug) }}" class="section-card__image-link block h-48 overflow-hidden rounded-t-lg" aria-label="{{ __('Lire l\'actualité:') }} {{ $relatedItem->title }}">
                            <img src="{{ $relatedItem->cover_image_thumbnail_url ?? asset('assets/images/placeholders/news_default_'.($index%2+1).'.jpg') }}"
                                 alt="{{ $relatedItem->cover_image_alt ?? $relatedItem->title }}"
                                 class="section-card__image group-hover:scale-105 transition-transform duration-300">
                        </a>
                        <div class="section-card__content">
                            <p class="section-card__meta news__meta">
                                @if($relatedItem->published_at)
                                <time datetime="{{ $relatedItem->published_at->toDateString() }}">{{ $relatedItem->published_at->translatedFormat('d M Y') }}</time>
                                @endif
                                @if($relatedItem->category)
                                    <span class="news__category-separator mx-1">&bull;</span>
                                    <span class="news__category-badge" style="background-color: {{ $relatedItem->category->color ?: 'rgba(var(--color-primary-rgb),0.1)' }}; color: {{ $relatedItem->category->text_color ?: 'rgb(var(--color-primary))' }};">
                                        {{ $relatedItem->category->name }}
                                    </span>
                                @endif
                            </p>
                            <h3 class="section-card__title text-md news__title">
                                <a href="{{ route('public.news.show', $relatedItem->slug) }}">
                                    {{ Str::limit($relatedItem->title, 55) }}
                                </a>
                            </h3>
                             <a href="{{ route('public.news.show', $relatedItem->slug) }}" class="section-card__link news__link text-sm mt-auto">
                                {{ __('Lire la suite') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection
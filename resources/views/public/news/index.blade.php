@extends('layouts.public')

{{-- $pageTitle, $newsItems (paginated), $currentCategory (optionnel), $searchTerm (optionnel) sont passés par le contrôleur --}}

@section('title', $pageTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@if($currentCategory)
    @section('meta_description', __('Actualités et articles sur :categoryName.', ['categoryName' => $currentCategory->name]))
@else
    @section('meta_description', $siteSettings->getTranslation('site_description', app()->getLocale(), false) ?: __('Suivez les dernières actualités et découvertes du CRPQA.'))
@endif

@push('styles')
<style>
    /* Repris de votre home.blade.php, à déplacer dans un CSS global si possible */
    .section-card { background-color: var(--card-bg-color, white); color: var(--card-text-color, inherit); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; overflow: hidden; display: flex; flex-direction: column; }
    .dark .section-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color, #d1d5db); }
    .section-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-6px); }
    .section-card__image-link { display: block; aspect-ratio: 16 / 10; overflow: hidden; }
    .section-card__image { width: 100%; height: 100%; object-fit: cover; }
    .section-card__content { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
    .section-card__meta { font-size: 0.75rem; color: var(--text-color-light, #6b7280); margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap-x: 0.75rem; gap-y: 0.25rem; align-items: center;}
    .dark .section-card__meta { color: var(--dark-text-color-light, #9ca3af); }
    .news__category-badge { font-weight: 500; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.7rem; }
    .section-card__title { font-size: 1.125rem; font-semibold; line-height: 1.4; margin-bottom: 0.5rem; }
    .section-card__title a { color: var(--title-color, #1f2937); text-decoration: none; }
    .dark .section-card__title a { color: var(--dark-title-color, #f3f4f6); }
    .section-card__title a:hover { color: rgb(var(--color-primary)); }
    .section-card__description { font-size: 0.875rem; line-height: 1.625; margin-bottom: 1rem; flex-grow: 1; }
    .section-card__link { margin-top: auto; display: inline-flex; align-items: center; font-size: 0.875rem; font-semibold; color: rgb(var(--color-primary)); text-decoration:none; }
    .section-card__link ion-icon, .section-card__link svg { margin-left: 0.25rem; width: 1em; height: 1em; }
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/news_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ $pageTitle }}
            </h1>
            @if($currentCategory)
            <p class="mt-2 text-lg text-gray-200" data-aos="fade-up" data-aos-delay="100">{{ __('Filtré par catégorie') }}</p>
            @endif
            @if($searchTerm)
            <p class="mt-2 text-lg text-gray-200" data-aos="fade-up" data-aos-delay="100">{{ __('Résultats de recherche pour : ') }} "{{ $searchTerm }}"</p>
            @endif
        </div>
    </section>

    <section class="section news-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Optionnel: Formulaire de recherche et filtre par catégorie --}}
            <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <form action="{{ route('public.news.index') }}" method="GET" class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Rechercher une actualité')}}</label>
                        <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" placeholder="{{__('Mots-clés...')}}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        @php
                            $newsCategories = \App\Models\NewsCategory::where('is_active', true)->orderBy('name')->pluck('name', 'slug');
                        @endphp
                        @if($newsCategories->count() > 0)
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Filtrer par catégorie')}}</label>
                        <select name="category" id="category" class="mt-1 block w-full form-select">
                            <option value="">{{__('Toutes les catégories')}}</option>
                            @foreach($newsCategories as $slug => $name)
                                <option value="{{ $slug }}" {{ ($currentCategory && $currentCategory->slug == $slug) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="button button--primary w-full sm:w-auto justify-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2"/> {{__('Rechercher')}}
                        </button>
                        @if($searchTerm || $currentCategory)
                        <a href="{{ route('public.news.index') }}" class="button button--outline w-full sm:w-auto justify-center">
                            {{__('Réinitialiser')}}
                        </a>
                        @endif
                    </div>
                </form>
            </div>


            @if($newsItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($newsItems as $index => $newsItem)
                    <article class="section-card news__card group" data-aos="fade-up" data-aos-delay="{{ ($index % 3 * 100) }}">
                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="section-card__image-link block h-56 overflow-hidden rounded-t-lg" aria-label="{{ __('Lire l\'actualité:') }} {{ $newsItem->title }}">
                            <img src="{{ $newsItem->cover_image_thumbnail_url ?? asset('assets/images/placeholders/news_default_'.($index%2+1).'.jpg') }}"
                                 alt="{{ $newsItem->cover_image_alt ?? $newsItem->title }}"
                                 class="section-card__image group-hover:scale-105 transition-transform duration-300">
                        </a>
                        <div class="section-card__content">
                            <p class="section-card__meta news__meta">
                                @if($newsItem->published_at)
                                <time datetime="{{ $newsItem->published_at->toDateString() }}">{{ $newsItem->published_at->translatedFormat('d M Y') }}</time>
                                @endif
                                @if($newsItem->category)
                                    <span class="news__category-separator mx-1">&bull;</span>
                                    <span class="news__category-badge" 
                                          style="background-color: {{ $newsItem->category->color ?: 'rgba(var(--color-primary-rgb),0.1)' }}; color: {{ $newsItem->category->text_color ?: 'rgb(var(--color-primary))' }};">
                                        {{ $newsItem->category->name }}
                                    </span>
                                @endif
                            </p>
                            <h3 class="section-card__title text-lg news__title">
                                <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                    {{ Str::limit($newsItem->title, 60) }}
                                </a>
                            </h3>
                            <p class="section-card__description news__description">
                                {{ Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 110) }}
                            </p>
                            <a href="{{ route('public.news.show', $newsItem->slug) }}" class="section-card__link news__link">
                                {{ __('Lire la suite') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $newsItems->appends(request()->query())->links() }} {{-- Ajout de appends pour conserver les filtres/recherche --}}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-newspaper class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                        @if($searchTerm)
                            {{ __('Aucune actualité trouvée pour votre recherche.') }}
                        @elseif($currentCategory)
                            {{ __('Aucune actualité dans cette catégorie pour le moment.') }}
                        @else
                            {{ __('Aucune actualité à afficher pour le moment.') }}
                        @endif
                    </h3>
                    @if($searchTerm || $currentCategory)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('public.news.index') }}" class="text-primary-600 hover:underline">{{__('Voir toutes les actualités')}}</a>
                    </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
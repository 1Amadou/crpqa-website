@extends('layouts.public')

@section('title', $pageTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Découvrez les principaux axes de recherche et domaines d\'expertise du CRPQA.'))

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
{{-- Styles repris de home.blade.php, à déplacer dans un CSS global --}}
<style>
    .section-card { background-color: var(--card-bg-color, white); color: var(--card-text-color, inherit); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; overflow: hidden; display: flex; flex-direction: column; }
    .dark .section-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color, #d1d5db); }
    .section-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-6px); }
    .section-card__image-link { display: block; aspect-ratio: 16 / 9; overflow: hidden; }
    .section-card__image { width: 100%; height: 100%; object-fit: cover; }
    .section-card__content { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
    .section-card__title { font-size: 1.125rem; font-semibold; line-height: 1.4; margin-bottom: 0.5rem; }
    .section-card__title a { color: var(--title-color, #1f2937); text-decoration: none; }
    .dark .section-card__title a { color: var(--dark-title-color, #f3f4f6); }
    .section-card__title a:hover { color: rgb(var(--color-primary)); }
    .section-card__description { font-size: 0.875rem; line-height: 1.625; margin-bottom: 1rem; flex-grow: 1; }
    .section-card__link { margin-top: auto; display: inline-flex; align-items: center; font-size: 0.875rem; font-semibold; color: rgb(var(--color-primary)); text-decoration:none; }
    .section-card__link ion-icon, .section-card__link svg { margin-left: 0.25rem; width: 1em; height: 1em; }

    .research-axis-card { /* Alias pour section-card ou styles spécifiques */ }
    .research-axis-card__icon-wrapper { width: 3.5rem; height: 3.5rem; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md); margin-bottom: 1rem; padding: 0.5rem; }
    .research-axis-card__icon-wrapper > svg, .research-axis-card__icon-wrapper > i { width: 100%; height: 100%; }
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/research_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ $pageTitle }}
            </h1>
            <nav aria-label="breadcrumb" class="mt-3" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">/ {{ __('Axes de Recherche') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section research-axes-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('public.research_axes.index') }}" method="GET" class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <div class="grid sm:grid-cols-2 md:grid-cols-[1fr,auto] gap-4 items-end">
                    <div>
                        <label for="search" class="sr-only">{{__('Rechercher un axe de recherche')}}</label>
                        <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" placeholder="{{__('Rechercher par mots-clés...')}}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="button button--primary w-full sm:w-auto justify-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2"/> {{__('Rechercher')}}
                        </button>
                         @if($searchTerm)
                            <a href="{{ route('public.research_axes.index') }}" class="button button--outline w-full sm:w-auto justify-center" title="{{__('Réinitialiser la recherche')}}">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($researchAxes->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($researchAxes as $index => $axis)
                    <article class="section-card research-axis-card group" data-aos="fade-up" data-aos-delay="{{ ($index % 3 * 100) }}">
                        @if($axis->cover_image_thumbnail_url)
                            <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="section-card__image-link block overflow-hidden rounded-t-lg" aria-label="{{ __('Explorer l\'axe:') }} {{ $axis->name }}">
                                <img src="{{ $axis->cover_image_thumbnail_url }}"
                                     alt="{{ $axis->getTranslation('cover_image_alt_text', $currentLocale, false) ?: $axis->name }}"
                                     class="section-card__image group-hover:scale-105 transition-transform duration-300">
                            </a>
                        @endif
                        <div class="section-card__content text-center">
                             <div class="research-axis-card__icon-wrapper inline-flex items-center justify-center mx-auto"
                                 style="background-color: {{ $axis->color_hex ? \App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.15) : 'rgba(var(--color-primary-rgb), 0.1)' }}; 
                                        color: {{ $axis->color_hex ?: 'rgb(var(--color-primary))' }};">
                                @if($axis->getTranslation('icon_svg', $currentLocale, false))
                                    {!! $axis->getTranslation('icon_svg', $currentLocale, false) !!}
                                @elseif($axis->icon_class)
                                    <i class="{{ $axis->icon_class }} text-2xl"></i>
                                @else
                                     <x-heroicon-o-academic-cap class="w-6 h-6"/>
                                @endif
                            </div>
                            <h3 class="section-card__title text-lg research-axis-card__title mt-2">
                                <a href="{{ route('public.research_axes.show', $axis->slug) }}">
                                    {{ $axis->name }}
                                </a>
                            </h3>
                            @if($axis->subtitle)
                            <p class="text-sm text-primary-700 dark:text-primary-300 font-medium mb-2">
                                {{ $axis->subtitle }}
                            </p>
                            @endif
                            <p class="section-card__description research-axis-card__description">
                                {{ Str::limit(strip_tags($axis->description), 100) }}
                            </p>
                            <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="section-card__link research-axis-card__link">
                                {{ __('Explorer cet Axe') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $researchAxes->links() }}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                        @if($searchTerm)
                            {{ __('Aucun axe de recherche trouvé pour votre recherche.') }}
                        @else
                            {{ __('Aucun axe de recherche à afficher pour le moment.') }}
                        @endif
                    </h3>
                     @if($searchTerm)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('public.research_axes.index') }}" class="text-primary-600 hover:underline">{{__('Voir tous les axes')}}</a>
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
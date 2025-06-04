@extends('layouts.public')

@section('title', __('Nos Publications Scientifiques') . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Découvrez les travaux de recherche et les publications scientifiques du CRPQA et de ses chercheurs.'))

@push('styles')
<style>
    /* Styles repris de home.blade.php, à déplacer dans un CSS global */
    .section-card { background-color: var(--card-bg-color, white); color: var(--card-text-color, inherit); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; overflow: hidden; display: flex; flex-direction: column; }
    .dark .section-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color, #d1d5db); }
    .section-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-6px); }
    .section-card__image-link { display: block; aspect-ratio: 16 / 10; overflow: hidden; }
    .section-card__image { width: 100%; height: 100%; object-fit: cover; }
    .section-card__content { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
    .section-card__meta { font-size: 0.75rem; color: var(--text-color-light, #6b7280); margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap-x: 0.75rem; gap-y: 0.25rem; align-items: center;}
    .dark .section-card__meta { color: var(--dark-text-color-light, #9ca3af); }
    .section-card__title { font-size: 1.125rem; font-semibold; line-height: 1.4; margin-bottom: 0.5rem; }
    .section-card__title a { color: var(--title-color, #1f2937); text-decoration: none; }
    .dark .section-card__title a { color: var(--dark-title-color, #f3f4f6); }
    .section-card__title a:hover { color: rgb(var(--color-primary)); }
    .section-card__description { font-size: 0.875rem; line-height: 1.625; margin-bottom: 1rem; flex-grow: 1; }
    .section-card__link { margin-top: auto; display: inline-flex; align-items: center; font-size: 0.875rem; font-semibold; color: rgb(var(--color-primary)); text-decoration:none; }
    .section-card__link ion-icon, .section-card__link svg { margin-left: 0.25rem; width: 1em; height: 1em; }
    .publication-filters select, .publication-filters input[type="text"] { /* Styles pour les filtres */ }
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/publications_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ __('Nos Publications Scientifiques') }}
            </h1>
            <nav aria-label="breadcrumb" class="mt-3" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">/ {{ __('Publications') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section publications-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Filtres --}}
            <form action="{{ route('public.publications.index') }}" method="GET" class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Rechercher (titre, résumé, mots-clés...)')}}</label>
                        <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" class="mt-1 block w-full form-input-sm">
                    </div>
                    <div>
                        <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Type de publication')}}</label>
                        <select name="type" id="type" class="mt-1 block w-full form-select-sm">
                            <option value="">{{__('Tous les types')}}</option>
                            @foreach($types as $typeKey => $typeName)
                                <option value="{{ $typeKey }}" {{ $typeFilter == $typeKey ? 'selected' : '' }}>{{ $typeName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Année de publication')}}</label>
                        <select name="year" id="year" class="mt-1 block w-full form-select-sm">
                            <option value="">{{__('Toutes les années')}}</option>
                            @foreach($years as $yearOption)
                                <option value="{{ $yearOption }}" {{ $yearFilter == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                     <div>
                        <label for="researcher" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Chercheur')}}</label>
                        <select name="researcher" id="researcher" class="mt-1 block w-full form-select-sm">
                            <option value="">{{__('Tous les chercheurs')}}</option>
                            @foreach($researchersForFilter as $id => $name)
                                <option value="{{ $id }}" {{ $researcherFilter == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 md:col-span-1 flex space-x-2">
                        <button type="submit" class="button button--primary w-full justify-center">
                            <x-heroicon-o-funnel class="w-5 h-5 mr-2"/> {{__('Filtrer')}}
                        </button>
                         @if($searchTerm || $typeFilter || $yearFilter || $researcherFilter)
                            <a href="{{ route('public.publications.index') }}" class="button button--outline w-full justify-center" title="{{__('Réinitialiser les filtres')}}">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($publications->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($publications as $index => $publication)
                    <article class="section-card publication__card group" data-aos="fade-up" data-aos-delay="{{ ($index % 3 * 100) }}">
                        <div class="section-card__content">
                            <p class="section-card__meta publication__meta">
                                <span>{{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }}</span>
                                @if($publication->publication_date)<span class="mx-1">&bull;</span> <time datetime="{{ $publication->publication_date->toDateString() }}">{{ $publication->publication_date->translatedFormat('M Y') }}</time>@endif
                            </p>
                            <h3 class="section-card__title text-lg publication__title">
                                <a href="{{ route('public.publications.show', $publication->slug) }}">
                                    {{ Str::limit($publication->title, 80) }}
                                </a>
                            </h3>
                            @php
                                $authorsDisplay = [];
                                if ($publication->researchers->isNotEmpty()) { $authorsDisplay[] = $publication->researchers->take(2)->pluck('full_name')->join(', '); if($publication->researchers->count() > 2) $authorsDisplay[] = 'et al.';}
                                if ($publication->authors_external) { $authorsDisplay[] = e(Str::limit($publication->authors_external, 40)); }
                            @endphp
                            @if(!empty($authorsDisplay))
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                               {{ implode('; ', $authorsDisplay) }}
                            </p>
                            @endif
                            <p class="section-card__description publication__abstract">
                                {{ Str::limit(strip_tags($publication->abstract), 150) }}
                            </p>
                            <div class="mt-auto pt-3">
                                <a href="{{ $publication->doi_url ?: route('public.publications.show', $publication->slug) }}" 
                                   target="{{ $publication->doi_url ? '_blank' : '_self' }}" rel="noopener noreferrer" 
                                   class="section-card__link publication__link">
                                    {{ $publication->doi_url ? __('Consulter sur DOI') : __('Lire la suite') }} <ion-icon name="open-outline"></ion-icon>
                                </a>
                                @if($publication->getFirstMediaUrl('publication_pdf'))
                                <a href="{{ route('public.publications.download', $publication->slug) }}"  {{-- Assurez-vous que cette route existe --}}
                                   class="section-card__link publication__link ml-4 text-sm text-gray-600 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400" title="{{__('Télécharger le PDF')}}">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1"/> PDF
                                </a>
                                @endif
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $publications->links() }}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                        {{ __('Aucune publication trouvée correspondant à vos critères.') }}
                    </h3>
                     @if($searchTerm || $typeFilter || $yearFilter || $researcherFilter)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('public.publications.index') }}" class="text-primary-600 hover:underline">{{__('Voir toutes les publications')}}</a>
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
@extends('layouts.public')

@section('title', $pageTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Découvrez les membres de notre équipe de recherche, leurs expertises et leurs contributions.'))

@push('styles')
<style>
    /* Styles pour les cartes de chercheurs et la page index */
    .researcher-card { background-color: var(--card-bg-color, white); color: var(--card-text-color); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; text-align: center; padding: 1.5rem; display:flex; flex-direction:column; align-items:center; }
    .dark .researcher-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color); }
    .researcher-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-5px); }
    .researcher-card__image { width: 9rem; height: 9rem; /* w-36 h-36 */ border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 4px solid white; box-shadow: var(--shadow-md); transition: transform 0.3s ease; }
    .dark .researcher-card__image { border-color: var(--dark-card-bg-color); }
    .researcher-card:hover .researcher-card__image { transform: scale(1.05); }
    .researcher-card__name { font-size: 1.125rem; /* text-lg */ font-semibold; color: var(--title-color); margin-bottom: 0.25rem; }
    .dark .researcher-card__name { color: var(--dark-title-color); }
    .researcher-card__position { font-size: 0.875rem; /* text-sm */ color: rgb(var(--color-primary)); font-medium; }
    .researcher-card__link { display: block; text-decoration: none; }
    .researcher-card__link:hover .researcher-card__name { color: rgb(var(--color-primary)); }
    .dark .researcher-card__link:hover .researcher-card__name { color: rgb(var(--color-primary-light)); }
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/team_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ $pageTitle }}
            </h1>
            <nav aria-label="breadcrumb" class="mt-3" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">/ {{ __('Notre Équipe') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section researchers-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('public.researchers.index') }}" method="GET" class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <div class="grid sm:grid-cols-2 md:grid-cols-[1fr,auto] gap-4 items-end">
                    <div>
                        <label for="search" class="sr-only">{{__('Rechercher un membre de l\'équipe')}}</label>
                        <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" placeholder="{{__('Rechercher par nom, titre, domaine...')}}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="button button--primary w-full sm:w-auto justify-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2"/> {{__('Rechercher')}}
                        </button>
                         @if($searchTerm)
                            <a href="{{ route('public.researchers.index') }}" class="button button--outline w-full sm:w-auto justify-center" title="{{__('Réinitialiser la recherche')}}">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($researchers->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                    @foreach($researchers as $index => $researcherItem)
                    <article class="researcher-card" data-aos="fade-up" data-aos-delay="{{ ($index % 4 * 100) }}">
                        <a href="{{ route('public.researchers.show', $researcherItem->slug) }}" class="researcher-card__link">
                            <img src="{{ $researcherItem->photo_thumbnail_url ?? asset('assets/images/placeholders/researcher_default_'.($index%2+1).'.png') }}" 
                                 alt="{{ $researcherItem->getTranslation('photo_alt_text', app()->getLocale(), false) ?: $researcherItem->full_name }}"
                                 class="researcher-card__image">
                            <h2 class="researcher-card__name">
                                {{ $researcherItem->full_name }}
                            </h2>
                            @if($researcherItem->title_position)
                                <p class="researcher-card__position">
                                    {{ $researcherItem->title_position }}
                                </p>
                            @endif
                        </a>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $researchers->links() }}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                        @if($searchTerm)
                            {{ __('Aucun membre de l\'équipe trouvé pour votre recherche.') }}
                        @else
                            {{ __('Aucun membre de l\'équipe à afficher pour le moment.') }}
                        @endif
                    </h3>
                     @if($searchTerm)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('public.researchers.index') }}" class="text-primary-600 hover:underline">{{__('Voir toute l\'équipe')}}</a>
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
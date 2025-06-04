@extends('layouts.public')

@section('title', $pageTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Découvrez nos prochains et passés événements, conférences, séminaires et ateliers au CRPQA.'))

@push('styles')
<style>
    .section-card { background-color: var(--card-bg-color, white); color: var(--card-text-color, inherit); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; overflow: hidden; display: flex; flex-direction: column; }
    .dark .section-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color, #d1d5db); }
    .section-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-6px); }
    .section-card__image-link { display: block; aspect-ratio: 16 / 9; overflow: hidden; }
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
    .event-filters a { font-size: 0.95rem; padding-bottom: 0.5rem; border-bottom: 2px solid transparent; transition: all 0.2s ease-in-out; }
    .event-filters a.active-filter { font-weight: 600; color: rgb(var(--color-primary)); border-bottom-color: rgb(var(--color-primary)); }
    .dark .event-filters a.active-filter { color: rgb(var(--color-primary-light, #93c5fd)); border-bottom-color: rgb(var(--color-primary-light, #93c5fd));}
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/events_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ $pageTitle }}
            </h1>
            <nav aria-label="breadcrumb" class="mt-3" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">/ {{ __('Événements') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section events-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <div class="grid sm:grid-cols-2 md:grid-cols-[1fr,auto] gap-4 items-end">
                    <form action="{{ route('public.events.index') }}" method="GET" class="flex gap-2 items-end">
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <div>
                            <label for="search" class="sr-only">{{__('Rechercher un événement')}}</label>
                            <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" placeholder="{{__('Rechercher par mots-clés...')}}" class="form-input w-full">
                        </div>
                        <button type="submit" class="button button--primary !py-2.5">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5"/>
                        </button>
                         @if($searchTerm)
                            <a href="{{ route('public.events.index', ['filter' => $filter]) }}" class="button button--outline !py-2.5" title="{{__('Réinitialiser la recherche')}}">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </a>
                        @endif
                    </form>
                    <div class="event-filters flex justify-center sm:justify-end space-x-3 md:space-x-4 border-t sm:border-t-0 pt-4 sm:pt-0">
                        <a href="{{ route('public.events.index', ['filter' => 'upcoming', 'search' => $searchTerm]) }}" class="{{ $filter === 'upcoming' ? 'active-filter' : 'text-gray-500 dark:text-gray-400' }}">{{ __('À Venir') }}</a>
                        <a href="{{ route('public.events.index', ['filter' => 'past', 'search' => $searchTerm]) }}" class="{{ $filter === 'past' ? 'active-filter' : 'text-gray-500 dark:text-gray-400' }}">{{ __('Passés') }}</a>
                        <a href="{{ route('public.events.index', ['filter' => 'all', 'search' => $searchTerm]) }}" class="{{ $filter === 'all' ? 'active-filter' : 'text-gray-500 dark:text-gray-400' }}">{{ __('Tous') }}</a>
                    </div>
                </div>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($events as $index => $eventItem)
                    <article class="section-card event__card group" data-aos="fade-up" data-aos-delay="{{ ($index % 3 * 100) }}">
                        <a href="{{ route('public.events.show', $eventItem->slug) }}" class="section-card__image-link block overflow-hidden rounded-t-lg" aria-label="{{ __('Voir l\'événement:') }} {{ $eventItem->title }}">
                            <img src="{{ $eventItem->cover_image_thumbnail_url ?? asset('assets/images/placeholders/event_default_'.($index%2+1).'.jpg') }}"
                                 alt="{{ $eventItem->getTranslation('cover_image_alt_text', $currentLocale, false) ?: $eventItem->title }}"
                                 class="section-card__image group-hover:scale-105 transition-transform duration-300">
                        </a>
                        <div class="section-card__content">
                            <p class="section-card__meta event__meta">
                                @if($eventItem->start_datetime)
                                <time datetime="{{ $eventItem->start_datetime->toDateString() }}">
                                    {{ $eventItem->start_datetime->translatedFormat('d M Y') }}
                                    @if($eventItem->end_datetime && !$eventItem->start_datetime->isSameDay($eventItem->end_datetime))
                                        - {{ $eventItem->end_datetime->translatedFormat('d M Y') }}
                                    @endif
                                </time>
                                @endif
                            </p>
                            <h3 class="section-card__title text-lg event__title">
                                <a href="{{ route('public.events.show', $eventItem->slug) }}">
                                    {{ Str::limit($eventItem->title, 60) }}
                                </a>
                            </h3>
                            @if($eventItem->location)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 inline-flex items-center">
                                <x-heroicon-o-map-pin class="w-3.5 h-3.5 mr-1 flex-shrink-0"/> {{ Str::limit($eventItem->location, 40) }}
                            </p>
                            @endif
                            <p class="section-card__description event__description">
                                {{ Str::limit(strip_tags($eventItem->description), 110) }}
                            </p>
                            <a href="{{ route('public.events.show', $eventItem->slug) }}" class="section-card__link event__link">
                                {{ __('Voir les détails') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-calendar-days class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                        @if($searchTerm)
                            {{ __('Aucun événement trouvé pour votre recherche.') }}
                        @elseif($filter === 'upcoming')
                            {{ __('Aucun événement à venir pour le moment.') }}
                        @elseif($filter === 'past')
                            {{ __('Aucun événement passé à afficher.') }}
                        @else
                            {{ __('Aucun événement à afficher pour le moment.') }}
                        @endif
                    </h3>
                     @if($searchTerm)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('public.events.index', ['filter' => $filter]) }}" class="text-primary-600 hover:underline">{{__('Réinitialiser la recherche')}}</a>
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
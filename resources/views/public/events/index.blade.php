@extends('layouts.public')

@section('title', __('Nos Événements') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', __('Découvrez et participez à nos prochains événements, conférences, séminaires et ateliers au CRPQA.'))

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <header class="mb-10 md:mb-12 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight">
                {{ __('Nos Événements') }}
            </h1>
            <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('Restez informés de nos prochains séminaires, conférences, ateliers et autres manifestations scientifiques.') }}
            </p>
        </header>

        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                @foreach($events as $eventItem)
                    <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col transition-all duration-300 ease-in-out hover:shadow-2xl group">
                        @if($eventItem->cover_image_thumbnail_url)
                            <a href="{{ route('public.events.show', $eventItem->slug) }}" class="block h-48 overflow-hidden">
                                <img src="{{ $eventItem->cover_image_thumbnail_url }}" 
                                     alt="{{ $eventItem->getTranslation('cover_image_alt', app()->getLocale(), false) ?: __('Image pour l\'événement') . ' ' . $eventItem->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </a>
                        @else
                            {{-- Fallback si pas d'image de couverture --}}
                            <a href="{{ route('public.events.show', $eventItem->slug) }}" class="block h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </a>
                        @endif
                        
                        <div class="p-5 sm:p-6 flex flex-col flex-grow">
                            <header class="mb-3">
                                <h2 class="text-lg sm:text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                                    <a href="{{ route('public.events.show', $eventItem->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                                        {{ $eventItem->title }} {{-- Géré par HasLocalizedFields --}}
                                    </a>
                                </h2>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <time datetime="{{ $eventItem->start_datetime->toDateString() }}">
                                        {{ $eventItem->start_datetime->translatedFormat('D d M Y, H:i') }}
                                    </time>
                                    @if($eventItem->end_datetime && $eventItem->end_datetime->notEqualTo($eventItem->start_datetime))
                                        - 
                                        <time datetime="{{ $eventItem->end_datetime->toDateString() }}">
                                            @if($eventItem->end_datetime->isSameDay($eventItem->start_datetime))
                                                {{ $eventItem->end_datetime->translatedFormat('H:i') }}
                                            @else
                                                {{ $eventItem->end_datetime->translatedFormat('D d M Y, H:i') }}
                                            @endif
                                        </time>
                                    @endif
                                </p>
                                @if($eventItem->location)
                                <p class="mt-1 text-xs text-primary-600 dark:text-primary-400 font-medium">
                                    <x-heroicon-o-map-pin class="h-3.5 w-3.5 inline-block mr-1 align-text-bottom"/>
                                    {{ $eventItem->location }} {{-- Géré par HasLocalizedFields --}}
                                </p>
                                @endif
                            </header>

                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-4 flex-grow">
                                {!! Str::limit(strip_tags($eventItem->description), 150) !!} {{-- Géré par HasLocalizedFields --}}
                            </div>

                            <footer class="mt-auto">
                                <a href="{{ route('public.events.show', $eventItem->slug) }}" 
                                   class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                                    {{ __('Voir les détails') }}
                                    <svg class="ml-1.5 w-4 h-4 transform transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                {{ $events->links() }}
            </div>
        @else
            <div class="text-center py-12">
                 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun événement à afficher') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Il n\'y a actuellement aucun événement programmé. Revenez bientôt !') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
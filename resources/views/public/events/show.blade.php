@extends('layouts.public')

@section('title', $event->title . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', Str::limit(strip_tags($event->description), 160))
@section('og_title', $event->title)
@section('og_description', Str::limit(strip_tags($event->description), 160))
@if($event->cover_image_url)
    @section('og_image', $event->cover_image_url)
@else
    {{-- @section('og_image', $siteSettings['default_og_image_url'] ?? asset('assets/images/default_og_image.jpg')) --}}
@endif
@section('og_type', 'event')


@section('content')
<div class="bg-white dark:bg-gray-800 py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <article class="max-w-4xl mx-auto">
            
            <header class="mb-8 md:mb-10">
                <nav class="mb-6 text-sm" aria-label="Breadcrumb">
                    <ol class="list-none p-0 inline-flex flex-wrap">
                        <li class="flex items-center">
                            <a href="{{ route('public.home') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Accueil') }}</a>
                            <svg class="fill-current w-3 h-3 mx-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569 9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                        </li>
                        <li class="flex items-center">
                            <a href="{{ route('public.events.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Événements') }}</a>
                        </li>
                    </ol>
                </nav>

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-3">
                    {{ $event->title }} {{-- Géré par HasLocalizedFields --}}
                </h1>

                <div class="text-md text-gray-600 dark:text-gray-400 space-y-1">
                    <p>
                        <x-heroicon-o-calendar-days class="h-5 w-5 inline-block mr-1.5 align-text-bottom text-primary-500"/>
                        <strong>{{ __('Date') }}:</strong> 
                        <time datetime="{{ $event->start_datetime->toDateString() }}">{{ $event->start_datetime->translatedFormat('l d F Y, H:i') }}</time>
                        @if($event->end_datetime && $event->end_datetime->notEqualTo($event->start_datetime))
                            {{ __('au') }} 
                            <time datetime="{{ $event->end_datetime->toDateString() }}">
                                @if($event->end_datetime->isSameDay($event->start_datetime))
                                    {{ $event->end_datetime->translatedFormat('H:i') }}
                                @else
                                    {{ $event->end_datetime->translatedFormat('l d F Y, H:i') }}
                                @endif
                            </time>
                        @endif
                    </p>
                    @if($event->location)
                    <p>
                        <x-heroicon-o-map-pin class="h-5 w-5 inline-block mr-1.5 align-text-bottom text-primary-500"/>
                        <strong>{{ __('Lieu') }}:</strong> {{ $event->location }} {{-- Géré par HasLocalizedFields --}}
                    </p>
                    @endif
                </div>
            </header>

            @if($event->cover_image_url)
                <div class="my-8 rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ $event->cover_image_url }}" 
                         alt="{{ $event->getTranslation('cover_image_alt', app()->getLocale(), false) ?: $event->title }}" 
                         class="w-full h-auto object-contain max-h-[600px] bg-gray-100 dark:bg-gray-700">
                </div>
            @endif
            
            @if($event->description)
            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 content-styles mb-8">
                {!! $event->description !!} {{-- Géré par HasLocalizedFields. Si HTML de WYSIWYG. --}}
            </div>
            @endif

            @if($event->target_audience)
            <div class="mb-8 p-4 sm:p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ __('Public Cible') }}</h3>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($event->target_audience)) !!} {{-- Géré par HasLocalizedFields. nl2br si texte simple. --}}
                </div>
            </div>
            @endif

            @if($event->registration_url)
                <div class="my-8 text-center">
                    <a href="{{ $event->registration_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-8 py-3 bg-primary-600 text-white text-base font-semibold rounded-lg shadow-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <x-heroicon-o-link class="h-5 w-5 mr-2"/>
                        {{ __('S\'inscrire à l\'événement') }}
                    </a>
                </div>
            @endif

            @if($event->partners->isNotEmpty())
                <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('Partenaires de l\'Événement') }}</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($event->partners as $partner)
                            <div class="flex items-center space-x-3 p-3 bg-gray-100 dark:bg-gray-700/50 rounded-md">
                                @if($partner->logo_url)
                                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="h-10 w-auto object-contain">
                                @else
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $partner->name }}</span>
                                @endif
                                {{-- Si vous voulez afficher le nom même avec le logo :
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $partner->name }}</span>
                                --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Galerie d'images (si vous l'implémentez) --}}
            {{-- @if($event->gallery_images->isNotEmpty())
                <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('Galerie Photos') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($event->gallery_images as $image)
                            <a href="{{ $image->getUrl() }}" data-fslightbox="event-gallery" class="block rounded-lg overflow-hidden shadow-md">
                                <img src="{{ $image->getUrl('thumbnail') }}" alt="{{ $image->name ?: __('Image de la galerie') }}" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif --}}
            
        </article>

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('public.events.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 transform transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à tous les événements') }}
            </a>
        </footer>

    </div>
</div>
@endsection

{{-- @push('scripts')
    Si vous utilisez fslightbox pour la galerie :
    <script src="{{ asset('path/to/fslightbox.min.js') }}"></script>
@endpush --}}
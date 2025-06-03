@extends('layouts.public')

@php
    // Supposons que $partner est passé par le contrôleur
    $primaryLocale = app()->getLocale();
@endphp

@section('title', $partner->getTranslation('name', $primaryLocale, false) . ' - ' . __('Partenaire') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', Str::limit(strip_tags($partner->getTranslation('description', $primaryLocale, false)), 160))
@section('og_title', $partner->getTranslation('name', $primaryLocale, false))
@section('og_description', Str::limit(strip_tags($partner->getTranslation('description', $primaryLocale, false)), 160))
@if($partner->logo_url)
    @section('og_image', $partner->logo_url)
@endif

@section('content')
<div class="bg-white dark:bg-gray-800 py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <article class="max-w-3xl mx-auto">
            
            <header class="mb-8 md:mb-10 text-center">
                 @if($partner->logo_url)
                    <img src="{{ $partner->logo_url }}" 
                         alt="{{ $partner->getTranslation('logo_alt_text', $primaryLocale, false) ?: $partner->getTranslation('name', $primaryLocale, false) }}"
                         class="max-h-40 w-auto mx-auto mb-6 rounded shadow-sm object-contain bg-gray-100 dark:bg-gray-700 p-2">
                @endif
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $partner->name }} {{-- Géré par HasLocalizedFields --}}
                </h1>
                @if($partner->type)
                    <p class="mt-2 text-md text-primary-600 dark:text-primary-400 font-semibold uppercase tracking-wider">
                        {{ Str::title(str_replace('_', ' ', $partner->type)) }}
                    </p>
                @endif
            </header>

            @if($partner->description)
            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 content-styles mb-8">
                {!! nl2br(e($partner->description)) !!} {{-- Géré par HasLocalizedFields. Ou {!! ... !!} si HTML --}}
            </div>
            @endif

            @if($partner->website_url)
                <div class="my-8 text-center">
                    <a href="{{ $partner->website_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-6 py-3 bg-primary-600 text-white text-base font-semibold rounded-lg shadow-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5 mr-2"/>
                        {{ __('Visiter le site web') }}
                    </a>
                </div>
            @endif
            
            {{-- Vous pourriez lister ici les événements associés à ce partenaire si pertinent --}}
            {{-- @if($partner->events->isNotEmpty())
                <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('Événements en collaboration') }}</h3>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        @foreach($partner->events as $event)
                            <li>
                                <a href="{{ route('public.events.show', $event->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 hover:underline">
                                    {{ $event->title }} ({{ $event->start_datetime->translatedFormat('F Y') }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

        </article>

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('public.partners.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 transform transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à tous les partenaires') }}
            </a>
        </footer>
    </div>
</div>
@endsection
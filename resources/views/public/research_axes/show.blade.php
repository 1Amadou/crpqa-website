@extends('layouts.public')

@php
    // $researchAxis est passé par le contrôleur
    $primaryLocale = app()->getLocale();
@endphp

@section('title', $researchAxis->name . ' - ' . __('Axe de Recherche') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', $researchAxis->getTranslation('meta_description', $primaryLocale, false) ?: Str::limit(strip_tags($researchAxis->description), 160))
@section('og_title', $researchAxis->name)
@section('og_description', $researchAxis->getTranslation('meta_description', $primaryLocale, false) ?: Str::limit(strip_tags($researchAxis->description), 160))
@if($researchAxis->cover_image_url)
    @section('og_image', $researchAxis->cover_image_url)
@else
    {{-- @section('og_image', $siteSettings['default_og_image_url'] ?? asset('assets/images/default_og_image.jpg')) --}}
@endif

@section('content')
<div class="bg-white dark:bg-gray-800">
    {{-- Section Bannière avec Image de Couverture et Titre --}}
    <section class="relative py-20 md:py-28 bg-gray-700 text-white text-center overflow-hidden">
        @if($researchAxis->cover_image_url)
            <div class="absolute inset-0">
                <img src="{{ $researchAxis->cover_image_url }}" 
                     alt="{{ $researchAxis->getTranslation('cover_image_alt_text', $primaryLocale, false) ?: $researchAxis->name }}" 
                     class="w-full h-full object-cover opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-900/50 to-transparent"></div>
            </div>
        @else
             <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600 opacity-90"></div>
        @endif

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col items-center justify-center">
                @if($researchAxis->getTranslation('icon_svg', $primaryLocale, false) || $researchAxis->icon_class)
                    <div class="w-16 h-16 md:w-20 md:h-20 p-3 mb-4 rounded-lg shadow-lg flex items-center justify-center" style="background-color: {{ $researchAxis->color_hex ?? 'rgba(255,255,255,0.2)' }}; color: {{ $researchAxis->color_hex ? (\App\Helpers\ColorHelper::isLightColor($researchAxis->color_hex) ? '#333' : '#fff') : '#fff' }};">
                        @if($researchAxis->getTranslation('icon_svg', $primaryLocale, false))
                            {!! $researchAxis->getTranslation('icon_svg', $primaryLocale, false) !!}
                        @elseif($researchAxis->icon_class)
                            <i class="{{ $researchAxis->icon_class }} text-4xl md:text-5xl"></i>
                        @endif
                    </div>
                @endif
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-3">
                    {{ $researchAxis->name }}
                </h1>
                @if($researchAxis->subtitle)
                    <p class="text-lg md:text-xl text-gray-200 max-w-2xl">
                        {{ $researchAxis->subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <article class="max-w-4xl mx-auto">
            <nav class="mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex flex-wrap">
                    <li class="flex items-center">
                        <a href="{{ route('public.home') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Accueil') }}</a>
                        <svg class="fill-current w-3 h-3 mx-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569 9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ route('public.research_axes.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Axes de Recherche') }}</a>
                    </li>
                </ol>
            </nav>
            
            @if($researchAxis->description)
            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 content-styles">
                {!! $researchAxis->description !!} {{-- Géré par HasLocalizedFields. Si HTML de WYSIWYG. --}}
            </div>
            @endif
            
            {{-- Vous pourriez ajouter ici des sections pour lister les chercheurs ou projets liés à cet axe --}}
            {{-- Exemple :
            @if($researchAxis->researchers->isNotEmpty())
                <div class="my-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">{{ __('Chercheurs Principaux') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($researchAxis->researchers as $researcher)
                            <a href="{{ route('public.researchers.show', $researcher->slug) }}" class="block p-4 bg-slate-50 dark:bg-gray-700/50 rounded-lg hover:shadow-md transition-shadow">
                                @if($researcher->photo_thumbnail_url)
                                <img src="{{ $researcher->photo_thumbnail_url }}" alt="{{ $researcher->full_name }}" class="w-16 h-16 rounded-full object-cover mx-auto mb-2">
                                @endif
                                <h4 class="text-md font-semibold text-primary-600 dark:text-primary-400 text-center">{{ $researcher->full_name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">{{ $researcher->title_position }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            --}}

        </article>

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('public.research_axes.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 transform transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à tous les axes de recherche') }}
            </a>
        </footer>
    </div>
</div>
@endsection
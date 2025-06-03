@extends('layouts.public')

@php
    // $researcher est passé par le contrôleur
    $primaryLocale = app()->getLocale();
@endphp

@section('title', $researcher->full_name . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', Str::limit(strip_tags($researcher->getTranslation('biography', $primaryLocale, false)), 160))
@section('og_title', $researcher->full_name)
@section('og_description', Str::limit(strip_tags($researcher->getTranslation('biography', $primaryLocale, false)), 160))
@if($researcher->photo_profile_url)
    @section('og_image', $researcher->photo_profile_url)
@else
    {{-- @section('og_image', $siteSettings['default_og_image_url'] ?? asset('assets/images/default_og_image.jpg')) --}}
@endif
@section('og_type', 'profile')


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
                            <a href="{{ route('public.researchers.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Notre Équipe') }}</a>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    @if($researcher->photo_profile_url)
                        <img src="{{ $researcher->photo_profile_url }}" 
                             alt="{{ $researcher->getTranslation('photo_alt_text', $primaryLocale, false) ?: $researcher->full_name }}"
                             class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover shadow-lg border-4 border-white dark:border-gray-700 flex-shrink-0">
                    @else
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 shadow-lg border-4 border-white dark:border-gray-700 flex-shrink-0">
                            <x-heroicon-o-user-circle class="w-24 h-24 sm:w-28 sm:h-28"/>
                        </div>
                    @endif
                    <div class="text-center sm:text-left">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $researcher->full_name }}
                        </h1>
                        @if($researcher->title_position)
                            <p class="mt-1 text-lg text-primary-600 dark:text-primary-400 font-semibold">
                                {{ $researcher->title_position }}
                            </p>
                        @endif
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            @if($researcher->email)
                                <p><x-heroicon-s-envelope class="h-4 w-4 inline mr-1.5 align-text-bottom"/> <a href="mailto:{{ $researcher->email }}" class="hover:underline">{{ $researcher->email }}</a></p>
                            @endif
                            @if($researcher->phone)
                                <p><x-heroicon-s-phone class="h-4 w-4 inline mr-1.5 align-text-bottom"/> {{ $researcher->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </header>
            
            {{-- Section pour onglets de langue si biographie ou intérêts de recherche sont longs et spécifiques à la langue --}}
            {{-- Pour simplifier, on affiche directement le contenu de la langue actuelle --}}
            
            @if($researcher->biography)
            <div class="my-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('Biographie') }}</h2>
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 content-styles">
                    {!! $researcher->biography !!} {{-- Si HTML de WYSIWYG --}}
                </div>
            </div>
            @endif

            @if($researcher->research_interests)
            <div class="my-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('Domaines de Recherche') }}</h2>
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 content-styles">
                     {!! $researcher->research_interests !!} {{-- Si HTML de WYSIWYG --}}
                </div>
            </div>
            @endif
            
            @php
                $externalLinks = [
                    ['url' => $researcher->website_url, 'label' => __('Site Web Personnel'), 'icon' => 'heroicon-o-globe-alt'],
                    ['url' => $researcher->linkedin_url, 'label' => 'LinkedIn', 'icon' => 'heroicon-s-link'], // Faute d'icône spécifique LinkedIn
                    ['url' => $researcher->researchgate_url, 'label' => 'ResearchGate', 'icon' => 'heroicon-s-link'],
                    ['url' => $researcher->google_scholar_url, 'label' => 'Google Scholar', 'icon' => 'heroicon-s-academic-cap'],
                ];
                $externalLinks = array_filter($externalLinks, fn($link) => !empty($link['url']));
            @endphp

            @if(!empty($externalLinks) || $researcher->orcid_id)
            <div class="my-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('Profils Externes et Identifiants') }}</h3>
                <div class="space-y-3 text-sm">
                    @foreach($externalLinks as $link)
                        <p>
                            <x-dynamic-component :component="$link['icon']" class="h-5 w-5 inline-block mr-2 align-text-bottom text-primary-500"/>
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline">
                                {{ $link['label'] }}
                            </a>
                        </p>
                    @endforeach
                    @if($researcher->orcid_id)
                         <p>
                            <x-heroicon-s-identification class="h-5 w-5 inline-block mr-2 align-text-bottom text-primary-500"/>
                            ORCID iD: <span class="font-mono">{{ $researcher->orcid_id }}</span>
                        </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Publications associées au chercheur --}}
            @if($researcher->publications->isNotEmpty())
                <div class="my-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        {{ __('Publications Récentes de :researcherName', ['researcherName' => $researcher->first_name]) }} 
                        ({{ $researcher->publications->count() }})
                    </h3>
                    <ul class="space-y-3">
                        @foreach($researcher->publications->take(5) as $publication) {{-- Afficher les 5 plus récentes par exemple --}}
                            <li class="pb-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <a href="{{ route('public.publications.show', $publication->slug) }}" class="text-md font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $publication->title }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }} 
                                    @if($publication->publication_date)
                                        - {{ $publication->publication_date->translatedFormat('Y') }}
                                    @endif
                                </p>
                            </li>
                        @endforeach
                        {{-- Lien pour voir toutes les publications du chercheur si une telle page existe --}}
                    </ul>
                </div>
            @endif
            
        </article>

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('public.researchers.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 transform transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à l\'équipe') }}
            </a>
        </footer>
    </div>
</div>
@endsection
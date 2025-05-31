@extends('layouts.public')

@section('title', $publication->title . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', $metaDescription ?? Str::limit(strip_tags($publication->abstract), 160))
{{-- Open Graph / Twitter Card Meta Tags --}}
@section('og_title', $publication->title)
@section('og_description', $metaDescription ?? Str::limit(strip_tags($publication->abstract), 160))
{{-- @if($publication->getFirstMediaUrl('publication_cover_image'))
    @section('og_image', $publication->getFirstMediaUrl('publication_cover_image'))
@else
    @section('og_image', $siteSettings['default_og_image_url'] ?? asset('assets/images/default_og_image.jpg'))
@endif --}}
{{-- @section('og_type', 'article') --}}
{{-- @section('article_published_time', $publication->publication_date->toIso8601String()) --}}
{{-- @section('article_author', $publication->researchers->isNotEmpty() ? $publication->researchers->pluck('full_name')->join(', ') : $publication->authors_external) --}}


@section('content')
<div class="bg-white dark:bg-gray-800 py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <article class="max-w-3xl mx-auto">
            <header class="mb-8 md:mb-10">
                <nav class="mb-6 text-sm" aria-label="Breadcrumb">
                    <ol class="list-none p-0 inline-flex">
                        <li class="flex items-center">
                            <a href="{{ route('public.home') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Accueil') }}</a>
                            <svg class="fill-current w-3 h-3 mx-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                        </li>
                        <li class="flex items-center">
                            <a href="{{ route('public.publications.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Publications') }}</a>
                        </li>
                    </ol>
                </nav>

                <div class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider">{{ $publicationTypeDisplay }}</span>
                    <span class="mx-1.5">&bull;</span>
                    <span>{{ __('Publié le') }}: <time datetime="{{ $publication->publication_date->toDateString() }}">{{ $publication->publication_date->translatedFormat('d F Y') }}</time></span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $publication->title }}
                </h1>
            </header>

            @if($publication->researchers->isNotEmpty() || !empty($publication->authors_external))
            <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-1">{{ __('Auteurs') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    @php
                        $authors = [];
                        if ($publication->researchers->isNotEmpty()) {
                            $authors[] = $publication->researchers->map(function ($researcher) {
                                return e($researcher->full_name);
                            })->join(', ');
                        }
                        if (!empty($publication->authors_external)) {
                            $authors[] = nl2br(e($publication->authors_external));
                        }
                    @endphp
                    {!! implode(' ;<br class="sm:hidden"> ', $authors) !!}
                </p>
            </div>
            @endif

            @if($publication->abstract)
            <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-2 sr-only">{{ __('Résumé') }}</h2>
                {!! $publication->abstract !!}
            </div>
            @endif

            <div class="p-4 sm:p-6 bg-slate-50 dark:bg-gray-700/50 rounded-md text-sm space-y-2 mb-8">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-3">{{ __('Informations de Publication') }}</h3>
                @if($publication->journal_name)<p><strong class="text-gray-600 dark:text-gray-300">{{ __('Journal/Revue') }}:</strong> {{ $publication->journal_name }}</p>@endif
                @if($publication->conference_name)<p><strong class="text-gray-600 dark:text-gray-300">{{ __('Conférence') }}:</strong> {{ $publication->conference_name }}</p>@endif
                @if($publication->volume || $publication->issue || $publication->pages)
                    <p>
                        @if($publication->volume)<strong class="text-gray-600 dark:text-gray-300">{{ __('Volume') }}:</strong> {{ $publication->volume }}@endif
                        @if($publication->issue)<span class="text-gray-400 mx-1.5">|</span> <strong class="text-gray-600 dark:text-gray-300">{{ __('Numéro/Issue') }}:</strong> {{ $publication->issue }}@endif
                        @if($publication->pages)<span class="text-gray-400 mx-1.5">|</span> <strong class="text-gray-600 dark:text-gray-300">{{ __('Pages') }}:</strong> {{ $publication->pages }}@endif
                    </p>
                @endif
                @if($publication->doi_url)
                    <p><strong class="text-gray-600 dark:text-gray-300">{{ __('DOI') }}:</strong> <a href="{{ $publication->doi_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline break-all">{{ $publication->doi_url }}</a></p>
                @endif
                @if($publication->external_url)
                    <p><strong class="text-gray-600 dark:text-gray-300">{{ __('Lien Externe') }}:</strong> <a href="{{ $publication->external_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline break-all">{{ $publication->external_url }}</a></p>
                @endif
            </div>

            @if($publication->pdf_url)
                <div class="mt-8 mb-6 text-center sm:text-left">
                    <a href="{{ $publication->pdf_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-6 py-3 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Télécharger le PDF') }}
                        @if($publication->pdf_media)
                            <span class="ml-1.5 text-xs opacity-75">({{ $publication->pdf_media->human_readable_size }})</span>
                        @endif
                    </a>
                </div>
            @endif
            
        </article>

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('public.publications.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 transform transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à toutes les publications') }}
            </a>
        </footer>
    </div>
</div>
@endsection
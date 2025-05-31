@extends('layouts.public')

@section('title', __('Publications') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', __('Découvrez les dernières recherches et publications scientifiques du CRPQA.'))

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <header class="mb-10 md:mb-12 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight">
                {{ __('Nos Publications') }}
            </h1>
            <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('Explorez nos contributions à la recherche scientifique et aux avancées technologiques.') }}
            </p>
        </header>

        @if($publications->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                @foreach($publications as $publicationItem)
                    <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col transition-all duration-300 ease-in-out hover:shadow-2xl group">
                        {{-- Image de couverture (si vous en ajoutez une via Spatie Media Library) --}}
                        {{-- @if($publicationItem->getFirstMediaUrl('publication_cover_image'))
                            <a href="{{ route('public.publications.show', $publicationItem->slug) }}" class="block h-48 overflow-hidden">
                                <img src="{{ $publicationItem->getFirstMediaUrl('publication_cover_image', 'card') }}" alt="{{ __('Image de couverture pour') }} {{ $publicationItem->title }}" class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">
                            </a>
                        @endif --}}
                        
                        <div class="p-6 flex flex-col flex-grow">
                            <header class="mb-3">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider">
                                        {{ $publicationItem->type_display ?? Str::title(str_replace('_', ' ', $publicationItem->type)) }}
                                    </span>
                                    <span class="mx-1.5">&bull;</span>
                                    <time datetime="{{ $publicationItem->publication_date->toDateString() }}">
                                        {{ $publicationItem->publication_date->translatedFormat('F Y') }}
                                    </time>
                                </div>
                                <h2 class="mt-1 text-lg font-semibold leading-tight text-gray-800 dark:text-white">
                                    <a href="{{ route('public.publications.show', $publicationItem->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                                        {{ $publicationItem->title }}
                                    </a>
                                </h2>
                            </header>

                            @if($publicationItem->researchers->isNotEmpty() || !empty($publicationItem->authors_external))
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                                    <strong class="font-medium">{{ __('Auteurs') }}:</strong>
                                    @php
                                        $authors = [];
                                        if ($publicationItem->researchers->isNotEmpty()) {
                                            $authors[] = $publicationItem->researchers->pluck('full_name')->join(', ');
                                        }
                                        if (!empty($publicationItem->authors_external)) {
                                            $authors[] = e($publicationItem->authors_external);
                                        }
                                    @endphp
                                    {!! implode(' ; ', $authors) !!}
                                </p>
                            @endif

                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-4 flex-grow">
                                {!! Str::limit(strip_tags($publicationItem->abstract), 180) !!}
                            </div>

                            <footer class="mt-auto">
                                <a href="{{ route('public.publications.show', $publicationItem->slug) }}" 
                                   class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                                    {{ __('Lire la suite') }}
                                    <svg class="ml-1.5 w-4 h-4 transform transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                {{ $publications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucune publication') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Il n\'y a actuellement aucune publication à afficher. Revenez bientôt !') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
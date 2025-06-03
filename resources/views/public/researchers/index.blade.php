@extends('layouts.public')

@section('title', __('Notre Équipe de Chercheurs') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', __('Découvrez les membres de notre équipe de recherche, leurs expertises et leurs contributions au domaine de la physique quantique.'))

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <header class="mb-10 md:mb-12 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight">
                {{ __('Notre Équipe de Chercheurs') }}
            </h1>
            <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('Rencontrez les esprits brillants qui animent la recherche et l\'innovation au sein du CRPQA.') }}
            </p>
        </header>

        @if($researchers->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                @foreach($researchers as $researcherItem)
                    <article class="text-center group">
                        <a href="{{ route('public.researchers.show', $researcherItem->slug) }}" class="block bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out">
                            <div class="mb-4">
                                @if($researcherItem->photo_thumbnail_url)
                                    <img src="{{ $researcherItem->photo_thumbnail_url }}" 
                                         alt="{{ $researcherItem->getTranslation('photo_alt_text', app()->getLocale(), false) ?: $researcherItem->full_name }}"
                                         class="w-32 h-32 md:w-36 md:h-36 rounded-full object-cover mx-auto shadow-md border-4 border-white dark:border-gray-700 transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-32 h-32 md:w-36 md:h-36 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mx-auto text-gray-400 dark:text-gray-500 shadow-md border-4 border-white dark:border-gray-700">
                                        <x-heroicon-o-user-circle class="w-20 h-20"/>
                                    </div>
                                @endif
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $researcherItem->full_name }} {{-- Utilise l'accesseur qui gère la traduction --}}
                            </h2>
                            @if($researcherItem->title_position)
                                <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">
                                    {{ $researcherItem->title_position }} {{-- Géré par HasLocalizedFields --}}
                                </p>
                            @endif
                            {{-- Vous pouvez ajouter un extrait de la biographie ou des domaines de recherche si souhaité --}}
                            {{-- <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ Str::limit(strip_tags($researcherItem->research_interests), 70) }}
                            </p> --}}
                        </a>
                    </article>
                @endforeach
            </div>

            @if($researchers instanceof \Illuminate\Pagination\LengthAwarePaginator && $researchers->hasPages())
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $researchers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun chercheur à afficher') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Notre équipe de recherche est en cours de constitution. Revenez bientôt !') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
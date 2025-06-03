@extends('layouts.public')

@section('title', __('Axes de Recherche') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', __('Découvrez les principaux axes de recherche et domaines d\'expertise du CRPQA.'))

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <header class="mb-10 md:mb-12 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight">
                {{ __('Nos Axes de Recherche') }}
            </h1>
            <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('Plongez au cœur de nos domaines d\'expertise et découvrez les thématiques qui animent nos équipes de recherche.') }}
            </p>
        </header>

        @if($researchAxes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($researchAxes as $axis)
                    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl group">
                        @if($axis->cover_image_thumbnail_url)
                            <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="block h-48 sm:h-52 overflow-hidden">
                                <img src="{{ $axis->cover_image_thumbnail_url }}" 
                                     alt="{{ $axis->getTranslation('cover_image_alt_text', app()->getLocale(), false) ?: __('Image pour') . ' ' . $axis->name }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </a>
                        @endif
                        
                        <div class="p-5 sm:p-6 flex flex-col flex-grow">
                            <header class="mb-3 flex items-start space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 p-2 rounded-lg shadow-md flex items-center justify-center" style="background-color: {{ $axis->color_hex ?? 'rgba(var(--color-primary-rgb), 0.1)' }}; color: {{ $axis->color_hex ? (\App\Helpers\ColorHelper::isLightColor($axis->color_hex) ? '#333' : '#fff') : 'rgb(var(--color-primary))' }};">
                                    @if($axis->getTranslation('icon_svg', app()->getLocale(), false))
                                        {!! $axis->getTranslation('icon_svg', app()->getLocale(), false) !!}
                                    @elseif($axis->icon_class)
                                        <i class="{{ $axis->icon_class }} text-2xl"></i>
                                    @else
                                        <x-heroicon-o-academic-cap class="w-7 h-7"/> {{-- Icône par défaut --}}
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                                        <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                                            {{ $axis->name }} {{-- Géré par HasLocalizedFields --}}
                                        </a>
                                    </h2>
                                    @if($axis->subtitle)
                                    <p class="text-sm text-primary-700 dark:text-primary-300 font-medium">
                                        {{ $axis->subtitle }} {{-- Géré par HasLocalizedFields --}}
                                    </p>
                                    @endif
                                </div>
                            </header>

                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-4 flex-grow">
                                {!! Str::limit(strip_tags($axis->description), 150) !!} {{-- Géré par HasLocalizedFields --}}
                            </div>

                            <footer class="mt-auto">
                                <a href="{{ route('public.research_axes.show', $axis->slug) }}" 
                                   class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 group">
                                    {{ __('Explorer cet Axe') }}
                                    <svg class="ml-1.5 w-4 h-4 transform transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($researchAxes instanceof \Illuminate\Pagination\LengthAwarePaginator && $researchAxes->hasPages())
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $researchAxes->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <x-heroicon-o-magnifying-glass-circle class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun axe de recherche à afficher') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Nos domaines d\'expertise seront bientôt détaillés ici.') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
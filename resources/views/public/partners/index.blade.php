@extends('layouts.public')

@section('title', __('Nos Partenaires') . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', __('Découvrez les organisations et institutions qui collaborent avec le CRPQA pour faire avancer la recherche et l\'innovation.'))

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <header class="mb-10 md:mb-12 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight">
                {{ __('Nos Partenaires') }}
            </h1>
            <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('Le CRPQA collabore avec un réseau dynamique d\'institutions académiques, de centres de recherche et d\'entreprises pour mener à bien ses missions et amplifier son impact.') }}
            </p>
        </header>

        @if($partners->count() > 0)
            {{-- Si vous voulez grouper par type de partenaire : --}}
            {{-- @php
                $groupedPartners = $partners->groupBy('type'); // Assurez-vous que 'type' est un champ pertinent
            @endphp

            @foreach($groupedPartners as $type => $partnersOfType)
                @if($type)
                    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mt-12 mb-6 capitalize">
                        {{ Str::title(str_replace('_', ' ', $type)) }}
                    </h2>
                @else
                     <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mt-12 mb-6">
                        {{ __('Autres Partenaires') }}
                    </h2>
                @endif --}}
                
                {{-- Affichage simple sans groupement pour commencer : --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 sm:gap-8 items-center">
                    @foreach($partners as $partnerItem)
                        <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <a href="{{ $partnerItem->website_url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="block group">
                                @if($partnerItem->logo_url)
                                    <img src="{{ $partnerItem->logo_thumbnail_url ?: $partnerItem->logo_url }}" 
                                         alt="{{ $partnerItem->getTranslation('logo_alt_text', app()->getLocale(), false) ?: __('Logo de') . ' ' . $partnerItem->name }}"
                                         class="h-20 md:h-24 w-auto max-w-full mx-auto object-contain transition-transform duration-300 group-hover:scale-105 mb-3">
                                @else
                                    <div class="h-20 md:h-24 w-full mx-auto bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center mb-3 text-gray-400 dark:text-gray-500">
                                        <x-heroicon-o-building-office-2 class="h-12 w-12"/>
                                    </div>
                                @endif
                                <h3 class="text-sm sm:text-md font-semibold text-gray-700 dark:text-gray-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $partnerItem->name }} {{-- Géré par HasLocalizedFields --}}
                                </h3>
                                @if($partnerItem->type)
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ Str::title(str_replace('_', ' ', $partnerItem->type)) }}</p>
                                @endif
                            </a>
                            {{-- Vous pourriez ajouter un lien vers une page de détail si elle existe --}}
                            {{-- @if(Route::has('public.partners.show') && $partnerItem->slug)
                                <a href="{{ route('public.partners.show', $partnerItem->slug) }}" class="text-xs text-primary-500 hover:underline mt-1 inline-block">
                                    {{ __('Voir détails') }}
                                </a>
                            @endif --}}
                        </div>
                    @endforeach
                </div>
            {{-- @endforeach --}} {{-- Fin de la boucle si groupé par type --}}

            @if($partners instanceof \Illuminate\Pagination\LengthAwarePaginator && $partners->hasPages())
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $partners->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun partenaire à afficher') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Nous travaillons activement à l\'établissement de collaborations. Revenez bientôt !') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
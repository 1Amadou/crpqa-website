@extends('layouts.admin')

@php
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails de l\'Axe de Recherche') . ': ' . $researchAxis->getTranslation('name', $primaryLocale, false))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails de l\'Axe de Recherche') }}
            </h1>
            <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">{{ $researchAxis->getTranslation('name', $primaryLocale, false) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage research_axes')
            <a href="{{ route('admin.research-axes.edit', $researchAxis->slug) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
            <a href="{{ route('admin.research-axes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
            {{-- Colonne Icône/Image et Infos Générales --}}
            <div class="md:col-span-1 space-y-6">
                @if($researchAxis->cover_image_url)
                    <div class="mb-6 rounded-lg overflow-hidden shadow-lg flex justify-center bg-gray-100 dark:bg-gray-700 p-2">
                        <img src="{{ $researchAxis->cover_image_url }}" 
                             alt="{{ $researchAxis->getTranslation('cover_image_alt_text', $primaryLocale, false) ?: $researchAxis->getTranslation('name', $primaryLocale, false) }}"
                             class="w-auto max-h-64 object-contain">
                    </div>
                @endif

                <div class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-md">
                    @if($researchAxis->getTranslation('icon_svg', $primaryLocale, false))
                        <div class="w-12 h-12 flex-shrink-0 p-2 rounded-lg shadow" style="background-color: {{ $researchAxis->color_hex ?? 'transparent' }}; color: {{ \App\Helpers\ColorHelper::isLightColor($researchAxis->color_hex ?? '#ffffff') ? '#000000' : '#ffffff' }};">
                            {!! $researchAxis->getTranslation('icon_svg', $primaryLocale, false) !!}
                        </div>
                    @elseif($researchAxis->icon_class)
                         <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-lg shadow" style="background-color: {{ $researchAxis->color_hex ?? 'transparent' }};">
                            <i class="{{ $researchAxis->icon_class }} text-3xl" style="color: {{ \App\Helpers\ColorHelper::isLightColor($researchAxis->color_hex ?? '#ffffff') ? '#000000' : '#ffffff' }};"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-600" style="background-color: {{ $researchAxis->color_hex ?? '' }}"></div>
                    @endif
                    @if($researchAxis->color_hex)
                    <div>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{__('Couleur')}}</span>
                        <span class="font-mono text-sm px-2 py-1 rounded" style="background-color: {{ $researchAxis->color_hex }}; color: {{ \App\Helpers\ColorHelper::isLightColor($researchAxis->color_hex) ? '#000' : '#FFF' }}; border: 1px solid rgba(0,0,0,0.1);">
                            {{ $researchAxis->color_hex }}
                        </span>
                    </div>
                    @endif
                </div>
                
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Statut') }}</h3>
                    <p class="mt-1">
                        @if($researchAxis->is_active)
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Actif') }}</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Inactif') }}</span>
                        @endif
                    </p>
                </div>
                 <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Ordre d\'affichage') }}</h3>
                    <p class="mt-1 text-md text-gray-700 dark:text-gray-200">{{ $researchAxis->display_order }}</p>
                </div>

            </div>
            
            {{-- Colonne Informations Localisées --}}
            <div class="md:col-span-2">
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsResearchAxisShow" role="tablist">
                        @foreach($availableLocales as $locale)
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                        id="tab-ra-show-{{ $locale }}"
                                        data-tabs-target="#content-ra-show-{{ $locale }}"
                                        type="button" role="tab" aria-controls="content-ra-show-{{ $locale }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ strtoupper($locale) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div id="languageTabContentResearchAxisShow">
                    @foreach($availableLocales as $locale)
                        <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-ra-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-ra-show-{{ $locale }}">
                            
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1 break-words leading-tight">
                                {{ $researchAxis->getTranslation('name', $locale, false) }}
                            </h2>
                            @if($researchAxis->getTranslation('subtitle', $locale, false))
                                <p class="text-lg text-primary-600 dark:text-primary-400 mb-4">{{ $researchAxis->getTranslation('subtitle', $locale, false) }}</p>
                            @endif
                            
                            @if($researchAxis->getTranslation('description', $locale, false))
                                <div class="mt-4">
                                    <h4 class="sr-only">{{ __('Description') }}</h4>
                                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-100 content-styles">
                                        {!! $researchAxis->getTranslation('description', $locale, false) !!}
                                    </div>
                                </div>
                            @endif

                            @if($researchAxis->getTranslation('meta_title', $locale, false) && $researchAxis->getTranslation('meta_title', $locale, false) !== $researchAxis->getTranslation('name', $locale, false))
                                <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Titre (SEO)') }}:</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $researchAxis->getTranslation('meta_title', $locale, false) }}</p>
                                </div>
                            @endif

                            @if($researchAxis->getTranslation('meta_description', $locale, false))
                                <div class="mt-2 pt-2 @if(!($researchAxis->getTranslation('meta_title', $locale, false) && $researchAxis->getTranslation('meta_title', $locale, false) !== $researchAxis->getTranslation('name', $locale, false))) border-t border-gray-100 dark:border-gray-700 @endif">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Description (SEO)') }}:</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $researchAxis->getTranslation('meta_description', $locale, false) }}</p>
                                </div>
                            @endif
                            
                            @if($researchAxis->getTranslation('icon_svg', $locale, false) && !$researchAxis->icon_class) {{-- Afficher seulement si pas de classe CSS et que SVG existe pour cette langue --}}
                                <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Icône SVG (code brut)') }}:</h4>
                                    <pre class="text-xs bg-gray-100 dark:bg-gray-900 p-2 rounded overflow-x-auto"><code class="language-svg">{{ $researchAxis->getTranslation('icon_svg', $locale, false) }}</code></pre>
                                </div>
                            @endif

                            @if($researchAxis->cover_image_url && $researchAxis->getTranslation('cover_image_alt_text', $locale, false))
                                <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif de l\'Image de Couverture') }}:</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $researchAxis->getTranslation('cover_image_alt_text', $locale, false) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <span>{{ __('Slug') }}: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">{{ $researchAxis->slug }}</span></span>
                <span>{{ __('Créé le') }}: {{ $researchAxis->created_at->translatedFormat('d M Y à H:i') }}</span>
                <span>{{ __('Dernière mise à jour') }}: {{ $researchAxis->updated_at->translatedFormat('d M Y à H:i') }}</span>
            </div>
        </div>
        
        @can('manage research_axes')
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
            <form action="{{ route('admin.research-axes.destroy', $researchAxis->slug) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cet axe de recherche :name ?', ['name' => addslashes($researchAxis->getTranslation('name', $primaryLocale, false))]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer cet Axe') }}
                </button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
{{-- Script pour les onglets --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsRAShow = document.querySelectorAll('#languageTabsResearchAxisShow button');
    const tabContentsRAShow = document.querySelectorAll('#languageTabContentResearchAxisShow > div');

    tabButtonsRAShow.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsRAShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsRAShow.forEach(content => {
                content.classList.add('hidden');
            });

            button.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
            button.classList.remove('border-transparent');
            button.setAttribute('aria-selected', 'true');
            const target = document.querySelector(button.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });
    if (tabButtonsRAShow.length > 0 && !document.querySelector('#languageTabsResearchAxisShow button.active')) {
         if (tabButtonsRAShow[0]) tabButtonsRAShow[0].click();
    }
});
</script>
@endpush
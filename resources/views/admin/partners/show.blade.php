@extends('layouts.admin')

@php
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails du Partenaire') . ': ' . $partner->getTranslation('name', $primaryLocale, false))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails du Partenaire') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($partner->getTranslation('name', $primaryLocale, false), 70) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage partners')
            <a href="{{ route('admin.partners.edit', $partner) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
            <a href="{{ route('admin.partners.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
            {{-- Colonne Logo et Infos Générales --}}
            <div class="md:col-span-1 space-y-6">
                @if($partner->logo_url) {{-- Utilisation de l'accesseur du modèle --}}
                    <div class="mb-6 rounded-lg overflow-hidden shadow-lg flex justify-center bg-gray-100 dark:bg-gray-700 p-4">
                        <img src="{{ $partner->logo_url }}" 
                             alt="{{ $partner->getTranslation('logo_alt_text', $primaryLocale, false) ?: $partner->getTranslation('name', $primaryLocale, false) }}"
                             class="max-w-full h-auto max-h-48 object-contain">
                    </div>
                @endif

                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Type de Partenaire') }}</h3>
                    <p class="mt-1 text-md text-gray-700 dark:text-gray-200">{{ $partner->type ?: __('Non spécifié') }}</p>
                </div>

                 @if($partner->website_url)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Site Web') }}</h3>
                    <p class="mt-1 text-md text-primary-600 dark:text-primary-400 hover:underline break-all">
                        <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer">{{ $partner->website_url }}</a>
                    </p>
                </div>
                @endif
                
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Statut') }}</h3>
                    <p class="mt-1">
                        @if($partner->is_active)
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Actif') }}</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Inactif') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Ordre d\'affichage') }}</h3>
                    <p class="mt-1 text-md text-gray-700 dark:text-gray-200">{{ $partner->display_order }}</p>
                </div>
            </div>
            
            {{-- Colonne Informations Localisées --}}
            <div class="md:col-span-2">
                {{-- Système d'onglets pour la localisation --}}
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsPartnerShow" role="tablist">
                        @foreach($availableLocales as $locale)
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                        id="tab-partner-show-{{ $locale }}"
                                        data-tabs-target="#content-partner-show-{{ $locale }}"
                                        type="button" role="tab" aria-controls="content-partner-show-{{ $locale }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ strtoupper($locale) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div id="languageTabContentPartnerShow">
                    @foreach($availableLocales as $locale)
                        <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-partner-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-partner-show-{{ $locale }}">
                            
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-2 break-words leading-tight">
                                {{ $partner->getTranslation('name', $locale, false) }}
                            </h3>
                            
                            @if($partner->getTranslation('description', $locale, false))
                                <div class="mt-4">
                                    <h4 class="sr-only">{{ __('Description') }}</h4>
                                    <div class="prose prose-sm lg:prose-base max-w-none text-gray-800 dark:text-gray-100 dark:prose-invert content-styles">
                                        {!! nl2br(e($partner->getTranslation('description', $locale, false))) !!} {{-- Ou {!! ... !!} si HTML --}}
                                    </div>
                                </div>
                            @endif

                            @if($partner->logo_url && $partner->getTranslation('logo_alt_text', $locale, false))
                                <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif du Logo') }}:</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $partner->getTranslation('logo_alt_text', $locale, false) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{__('Métadonnées')}}</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('ID') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $partner->id }}</dd>
                </div>
                 {{-- Si vous ajoutez un slug au modèle Partner --}}
                {{-- <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">Slug :</dt>
                    <dd class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs text-gray-700 dark:text-gray-200 inline-block">{{ $partner->slug }}</dd>
                </div> --}}
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Créé le') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $partner->created_at->translatedFormat('d F Y à H:i') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Dernière mise à jour') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $partner->updated_at->translatedFormat('d F Y à H:i') }}</dd>
                </div>
            </dl>
        </div>
        
        @can('manage partners')
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
            <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce partenaire :name ?', ['name' => addslashes($partner->getTranslation('name', $primaryLocale, false))]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer ce Partenaire') }}
                </button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsPartnerShow = document.querySelectorAll('#languageTabsPartnerShow button');
    const tabContentsPartnerShow = document.querySelectorAll('#languageTabContentPartnerShow > div');

    tabButtonsPartnerShow.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsPartnerShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsPartnerShow.forEach(content => {
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
    if (tabButtonsPartnerShow.length > 0 && !document.querySelector('#languageTabsPartnerShow button.active')) {
         if (tabButtonsPartnerShow[0]) tabButtonsPartnerShow[0].click();
    }
});
</script>
@endpush
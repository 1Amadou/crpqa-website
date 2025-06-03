@extends('layouts.admin')

@php
    // $availableLocales et $staticPage sont passés par le contrôleur
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails de la Page Statique') . ': ' . $staticPage->getTranslation('title', $primaryLocale, false))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails de la Page Statique') }}
            </h1>
            <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">{{ $staticPage->getTranslation('title', $primaryLocale, false) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage static_pages')
            <a href="{{ route('admin.static-pages.edit', $staticPage->slug) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
             @if($staticPage->is_published && !in_array($staticPage->slug, ['presentation-crpqa', 'appel-collaboration'])) {{-- Condition reprise de votre code original --}}
                <a href="{{ route('public.page', $staticPage->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-secondary-500 text-white text-sm font-medium rounded-md hover:bg-secondary-600 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-eye class="h-4 w-4 mr-1.5"/>
                    {{ __('Voir (Public)') }}
                </a>
            @endif
            <a href="{{ route('admin.static-pages.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        @if($staticPage->cover_image_url) {{-- Utilise l'accesseur du modèle --}}
            <div class="mb-6 rounded-lg overflow-hidden shadow-lg flex justify-center bg-gray-100 dark:bg-gray-700 p-2">
                <img src="{{ $staticPage->cover_image_url }}" 
                     alt="{{ $staticPage->getTranslation('cover_image_alt_text', $primaryLocale, false) ?: $staticPage->getTranslation('title', $primaryLocale, false) }}"
                     class="w-auto max-h-96 object-contain">
            </div>
        @endif

        {{-- Système d'onglets pour la localisation --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsStaticPageShow" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-sp-show-{{ $locale }}"
                                data-tabs-target="#content-sp-show-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-sp-show-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="languageTabContentStaticPageShow">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-sp-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-sp-show-{{ $locale }}">
                    
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 break-words leading-tight">
                        {{ $staticPage->getTranslation('title', $locale, false) }}
                    </h2>
                    
                    @if($staticPage->getTranslation('content', $locale, false))
                        <div class="mt-4">
                            <h3 class="sr-only">{{ __('Contenu') }}</h3>
                            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-100 content-styles">
                                {!! $staticPage->getTranslation('content', $locale, false) !!} {{-- Si HTML de WYSIWYG --}}
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">{{__('Aucun contenu pour cette langue.')}}</p>
                    @endif

                    @if($staticPage->getTranslation('meta_title', $locale, false) && $staticPage->getTranslation('meta_title', $locale, false) !== $staticPage->getTranslation('title', $locale, false))
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Titre (SEO)') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $staticPage->getTranslation('meta_title', $locale, false) }}</p>
                        </div>
                    @endif

                    @if($staticPage->getTranslation('meta_description', $locale, false))
                        <div class="mt-2 pt-2 @if(!($staticPage->getTranslation('meta_title', $locale, false) && $staticPage->getTranslation('meta_title', $locale, false) !== $staticPage->getTranslation('title', $locale, false))) border-t border-gray-100 dark:border-gray-700 @endif">
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Description (SEO)') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $staticPage->getTranslation('meta_description', $locale, false) }}</p>
                        </div>
                    @endif
                    
                    @if($staticPage->cover_image_url && $staticPage->getTranslation('cover_image_alt_text', $locale, false))
                        <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif de l\'Image de Couverture') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $staticPage->getTranslation('cover_image_alt_text', $locale, false) }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{__('Informations Générales')}}</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Auteur/Éditeur') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $staticPage->user->name ?? __('N/A') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Statut') }}:</dt>
                    <dd>
                        @if($staticPage->is_published)
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée') }}</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Brouillon') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">Slug :</dt>
                    <dd class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs text-gray-700 dark:text-gray-200 inline-block">{{ $staticPage->slug }}</dd>
                </div>
                 <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Créée le') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $staticPage->created_at->translatedFormat('d F Y à H:i') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Dernière mise à jour') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $staticPage->updated_at->translatedFormat('d F Y à H:i') }}</dd>
                </div>
            </dl>
        </div>
        
        @can('manage static_pages')
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
            <form action="{{ route('admin.static-pages.destroy', $staticPage->slug) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette page :name ?', ['name' => addslashes($staticPage->getTranslation('title', $primaryLocale, false))]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer cette Page') }}
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
    const tabButtonsSPShow = document.querySelectorAll('#languageTabsStaticPageShow button');
    const tabContentsSPShow = document.querySelectorAll('#languageTabContentStaticPageShow > div');

    tabButtonsSPShow.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsSPShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsSPShow.forEach(content => {
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
    if (tabButtonsSPShow.length > 0 && !document.querySelector('#languageTabsStaticPageShow button.active')) {
         if (tabButtonsSPShow[0]) tabButtonsSPShow[0].click();
    }
});
</script>
@endpush
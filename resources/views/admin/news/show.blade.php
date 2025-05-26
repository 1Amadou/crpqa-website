@extends('layouts.admin')

@php
    // Définir la locale primaire pour l'affichage des titres etc.
    // Utilise la locale de l'application ou 'fr' par défaut si non définie dans availableLocales.
    // $availableLocales est passé par le contrôleur.
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails de l\'Actualité') . ': ' . $newsItem->getTranslation('title', $primaryLocale))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Détails de l\'Actualité') }}
        </h1>
        <div>
            
            <a href="{{ route('admin.news.edit', $newsItem) }}" class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700 text-sm font-medium mr-2 shadow-sm transition ease-in-out duration-150 flex items-center">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
                
            <a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        {{-- Image de couverture --}}
        @if($newsItem->hasMedia('news_cover_image'))
            <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
                <img src="{{ $newsItem->getFirstMediaUrl('news_cover_image') }}"
                     alt="{{ $newsItem->getTranslation('cover_image_alt', $primaryLocale) ?: $newsItem->getTranslation('title', $primaryLocale) }}"
                     class="w-full h-auto max-h-[500px] object-contain mx-auto">
            </div>
        @endif

        {{-- Système d'onglets pour la localisation du contenu affiché --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsNewsShow" role="tablist">
                @foreach($availableLocales as $index => $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-news-show-{{ $locale }}"
                                data-tabs-target="#content-news-show-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-news-show-{{ $locale }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="languageTabContentNewsShow">
            @foreach($availableLocales as $index => $locale)
                <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-news-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-news-show-{{ $locale }}">
                    
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 break-words leading-tight">
                        {{ $newsItem->getTranslation('title', $locale) }}
                    </h3>

                    @if($newsItem->getTranslation('meta_title', $locale) && $newsItem->getTranslation('meta_title', $locale) !== $newsItem->getTranslation('title', $locale))
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3"><em>Meta Titre: {{ $newsItem->getTranslation('meta_title', $locale) }}</em></p>
                    @endif
                    
                    @if($newsItem->getTranslation('summary', $locale))
                        <div class="mb-6 mt-4 p-4 bg-slate-50 dark:bg-gray-700 rounded-md border border-slate-200 dark:border-gray-600 shadow-sm">
                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-1">{{ __('Résumé') }}</h4>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-800 dark:text-gray-100">{!! $newsItem->getTranslation('summary', $locale) !!}</div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Contenu Complet') }}</h4>
                        <div class="prose prose-sm lg:prose-base max-w-none text-gray-800 dark:text-gray-100 dark:prose-invert">
                            {!! $newsItem->getTranslation('content', $locale) !!}
                        </div>
                    </div>

                    @if($newsItem->getTranslation('meta_description', $locale))
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Meta Description (SEO):</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $newsItem->getTranslation('meta_description', $locale) }}</p>
                        </div>
                    @endif

                     @if($newsItem->hasMedia('news_cover_image') && $newsItem->getTranslation('cover_image_alt', $locale))
                        <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Texte Alternatif de l'Image de Couverture:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $newsItem->getTranslation('cover_image_alt', $locale) }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        {{-- Fin des onglets de langue --}}

        {{-- Métadonnées Générales --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{__('Informations Complémentaires')}}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Auteur') }} :</span>
                    <span class="text-gray-700 dark:text-gray-200 ml-1">{{ $newsItem->user->name ?? __('N/A') }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Catégorie') }} :</span>
                    <span class="text-gray-700 dark:text-gray-200 ml-1">{{ $newsItem->category->name ?? __('Non catégorisé') }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Statut') }} :</span>
                    @if($newsItem->is_published)
                        @if($newsItem->published_at && \Carbon\Carbon::parse($newsItem->published_at)->isFuture())
                            <span class="ml-1 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">{{ __('Planifiée pour le') }} {{ \Carbon\Carbon::parse($newsItem->published_at)->translatedFormat('LL à HH[h]mm') }}</span>
                        @elseif($newsItem->published_at)
                            <span class="ml-1 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée le') }} {{ \Carbon\Carbon::parse($newsItem->published_at)->translatedFormat('LL') }}</span>
                        @else
                             <span class="ml-1 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée (date non spécifiée)') }}</span>
                        @endif
                    @else
                        <span class="ml-1 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Brouillon') }}</span>
                    @endif
                </div>
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-300">{{ __('En Vedette') }} :</span>
                    @if($newsItem->is_featured)
                        <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-700 dark:text-yellow-100 dark:border-yellow-600">✔️ {{ __('Oui') }}</span>
                    @else
                        <span class="ml-1 text-gray-700 dark:text-gray-200">❌ {{ __('Non') }}</span>
                    @endif
                </div>
                 <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-300">Slug :</span>
                    <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-gray-700 dark:text-gray-200 ml-1">{{ $newsItem->slug }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                <p>{{ __('Actualité créée le') }} : {{ $newsItem->created_at->translatedFormat('LLLL') }}</p>
                <p>{{ __('Dernière mise à jour') }} : {{ $newsItem->updated_at->translatedFormat('LLLL') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Les scripts pour les onglets sont gérés globalement via app/resources/js/admin/app-admin.js --}}
{{-- Assurez-vous que les IDs #languageTabsNewsShow et #languageTabContentNewsShow sont utilisés dans cette vue --}}
{{-- et que initHorizontalTabs est appelé pour eux dans app-admin.js --}}
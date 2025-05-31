@extends('layouts.admin')

@php
    // La locale primaire pour afficher le titre principal et les informations fallback.
    // $availableLocales est passé par NewsController@show.
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails de l\'Actualité') . ': ' . $newsItem->getTranslation('title', $primaryLocale, false))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Détails de l\'Actualité') }}
        </h1>
        <div>
            @can('manage news')
            <a href="{{ route('admin.news.edit', $newsItem) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150 mr-2">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
            <a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        @if($newsItem->cover_image_url) {{-- Utilisation de l'accesseur du modèle --}}
            <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
                <img src="{{ $newsItem->cover_image_url }}" 
                     alt="{{ $newsItem->getTranslation('cover_image_alt', $primaryLocale, false) ?: $newsItem->getTranslation('title', $primaryLocale, false) }}"
                     class="w-full h-auto max-h-[500px] object-contain mx-auto">
            </div>
        @endif

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsNewsShow" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-news-show-{{ $locale }}"
                                data-tabs-target="#content-news-show-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-news-show-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="languageTabContentNewsShow">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-news-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-news-show-{{ $locale }}">
                    
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2 break-words leading-tight">
                        {{ $newsItem->getTranslation('title', $locale, false) }}
                    </h2>

                    @if($newsItem->getTranslation('meta_title', $locale, false) && $newsItem->getTranslation('meta_title', $locale, false) !== $newsItem->getTranslation('title', $locale, false))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4"><em>{{ __('Meta Titre') }}: {{ $newsItem->getTranslation('meta_title', $locale, false) }}</em></p>
                    @endif
                    
                    @if($newsItem->getTranslation('summary', $locale, false))
                        <div class="mb-6 mt-2 p-4 bg-slate-50 dark:bg-gray-700/50 rounded-md border border-slate-200 dark:border-gray-600 shadow-sm">
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">{{ __('Résumé') }}</h3>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-800 dark:text-gray-100">{!! nl2br(e($newsItem->getTranslation('summary', $locale, false))) !!}</div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Contenu Complet') }}</h3>
                        <div class="prose prose-sm lg:prose-base max-w-none text-gray-800 dark:text-gray-100 dark:prose-invert content-styles">
                            {!! $newsItem->getTranslation('content', $locale, false) !!}
                        </div>
                    </div>

                    @if($newsItem->getTranslation('meta_description', $locale, false))
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Description (SEO)') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $newsItem->getTranslation('meta_description', $locale, false) }}</p>
                        </div>
                    @endif

                    @if($newsItem->cover_image_url && $newsItem->getTranslation('cover_image_alt', $locale, false))
                        <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif de l\'Image de Couverture') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $newsItem->getTranslation('cover_image_alt', $locale, false) }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{__('Informations Complémentaires')}}</h4>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Auteur') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $newsItem->createdBy->name ?? __('N/A') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Catégorie') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $newsItem->category->name ?? __('Non catégorisé') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Statut') }}:</dt>
                    <dd>
                        @if($newsItem->is_published)
                            @if($newsItem->published_at && $newsItem->published_at->isFuture())
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100">{{ __('Planifiée pour le') }} {{ $newsItem->published_at->translatedFormat('d/m/Y à H:i') }}</span>
                            @elseif($newsItem->published_at)
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée le') }} {{ $newsItem->published_at->translatedFormat('d/m/Y') }}</span>
                            @else
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée') }}</span>
                            @endif
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Brouillon') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('En Vedette') }}:</dt>
                    <dd class="{{ $newsItem->is_featured ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-200' }}">
                        {{ $newsItem->is_featured ? __('Oui') : __('Non') }}
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">Slug :</dt>
                    <dd class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs text-gray-700 dark:text-gray-200 inline-block">{{ $newsItem->slug }}</dd>
                </div>
            </dl>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                <p>{{ __('Créée le') }}: {{ $newsItem->created_at->translatedFormat('d F Y à H:i') }}</p>
                <p>{{ __('Dernière mise à jour') }}: {{ $newsItem->updated_at->translatedFormat('d F Y à H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Réutilisation du script d'onglets du _form.blade.php, ou un script global --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsNewsShow = document.querySelectorAll('#languageTabsNewsShow button');
    const tabContentsNewsShow = document.querySelectorAll('#languageTabContentNewsShow > div');

    tabButtonsNewsShow.forEach(button => {
        button.addEventListener('click', () => {
            tabButtonsNewsShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsNewsShow.forEach(content => {
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
    if (tabButtonsNewsShow.length > 0 && !document.querySelector('#languageTabsNewsShow button.active')) {
        tabButtonsNewsShow[0].click();
    }
});
</script>
@endpush
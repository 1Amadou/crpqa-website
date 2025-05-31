@extends('layouts.admin')

@section('title', 'Détails de la Publication')

@section('content')
<div class="container mx-auto px-4 py-8">
    @php
        $defaultLocale = $availableLocales[0] ?? 'fr';
    @endphp
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Détails : {{ $publication->getTranslation('title', $defaultLocale, false) ?: $publication->slug }}
        </h1>
        <div>
            <a href="{{ route('admin.publications.edit', $publication) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-150 mr-2">
                Modifier
            </a>
            <a href="{{ route('admin.publications.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-150">
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <!-- Onglets de langue -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsShow" role="tablist">
                @foreach ($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-show-{{ $locale }}" data-tabs-target="#content-show-{{ $locale }}" type="button" role="tab"
                                aria-controls="content-show-{{ $locale }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Contenu des onglets -->
        <div id="languageTabContentShow">
            @foreach ($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} p-4 rounded-lg bg-gray-50 dark:bg-gray-700"
                     id="content-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-show-{{ $locale }}">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Titre ({{ strtoupper($locale) }}):</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $publication->getTranslation('title', $locale) }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Résumé ({{ strtoupper($locale) }}):</h3>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {!! $publication->getTranslation('abstract', $locale) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <hr class="my-6 border-gray-300 dark:border-gray-600">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Slug:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->slug }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Date de Publication:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->publication_date ? $publication->publication_date->format('d F Y') : 'N/A' }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Type:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->type ?: 'N/A' }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Journal/Conférence:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->journal_conference_name ?: 'N/A' }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">DOI URL:</strong>
                <p class="text-gray-600 dark:text-gray-400">
                    @if($publication->doi_url)
                        <a href="{{ $publication->doi_url }}" target="_blank" class="text-blue-500 hover:underline">{{ $publication->doi_url }}</a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">URL Externe:</strong>
                <p class="text-gray-600 dark:text-gray-400">
                    @if($publication->external_url)
                        <a href="{{ $publication->external_url }}" target="_blank" class="text-blue-500 hover:underline">{{ $publication->external_url }}</a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
             <div>
                <strong class="text-gray-700 dark:text-gray-300">Auteurs Externes:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->authors_external ?: 'N/A' }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">PDF:</strong>
                @if ($publication->getFirstMediaUrl('publication_pdf'))
                    <p class="text-gray-600 dark:text-gray-400">
                        <a href="{{ $publication->getFirstMediaUrl('publication_pdf') }}" target="_blank" class="text-blue-500 hover:underline">
                            Télécharger/Voir PDF ({{ $publication->getFirstMedia('publication_pdf')->name }})
                        </a>
                    </p>
                @else
                    <p class="text-gray-600 dark:text-gray-400">Aucun PDF attaché.</p>
                @endif
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Créé par:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->createdBy->name ?? 'N/A' }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Créé le:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Dernière modification:</strong>
                <p class="text-gray-600 dark:text-gray-400">{{ $publication->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Statut:</strong>
                <p class="text-gray-600 dark:text-gray-400">
                    @if ($publication->is_published) Publié @else Brouillon @endif
                    @if ($publication->is_featured) (En Vedette) @endif
                </p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // JS pour les onglets de la page show (similaire à _form)
    document.addEventListener('DOMContentLoaded', function () {
        const tabsShow = [];
        document.querySelectorAll('[data-tabs-target^="#content-show-"]').forEach(button => {
            tabsShow.push({
                trigger: button,
                target: document.querySelector(button.dataset.tabsTarget)
            });
            button.addEventListener('click', function () {
                tabsShow.forEach(tab => {
                    const isSelected = tab.trigger === this;
                    tab.trigger.setAttribute('aria-selected', isSelected);
                    tab.trigger.classList.toggle('border-blue-500', isSelected);
                    tab.trigger.classList.toggle('text-blue-600', isSelected);
                    tab.trigger.classList.toggle('border-transparent', !isSelected);
                    tab.trigger.classList.toggle('hover:text-gray-600', !isSelected);
                    tab.trigger.classList.toggle('hover:border-gray-300', !isSelected);
                    if (tab.target) {
                        tab.target.classList.toggle('hidden', !isSelected);
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
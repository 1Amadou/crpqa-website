@extends('layouts.admin')

@php
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Profil de') . ': ' . $researcher->getFullNameAttribute()) {{-- Utilise l'accesseur full_name --}}

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Profil du Chercheur') }}
            </h1>
            <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">{{ $researcher->getFullNameAttribute() }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage researchers')
            <a href="{{ route('admin.researchers.edit', $researcher->slug) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier ce Profil') }}
            </a>
            @endcan
            <a href="{{ route('admin.researchers.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            {{-- Colonne Photo et Liens Externes --}}
            <div class="flex-shrink-0 w-full lg:w-1/3 text-center lg:text-left">
                @if($researcher->photo_profile_url) {{-- Utilise l'accesseur pour la version 'profile' de la photo --}}
                    <img src="{{ $researcher->photo_profile_url }}" 
                         alt="{{ $researcher->getTranslation('photo_alt_text', $primaryLocale, false) ?: $researcher->full_name }}" 
                         class="w-48 h-48 md:w-56 md:h-56 rounded-full object-cover shadow-lg mx-auto lg:mx-0 border-4 border-white dark:border-gray-700">
                @else
                    <div class="w-48 h-48 md:w-56 md:h-56 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 shadow-lg mx-auto lg:mx-0 border-4 border-white dark:border-gray-700">
                        <x-heroicon-o-user-circle class="h-24 w-24"/>
                    </div>
                @endif
                <div class="mt-4 text-center lg:text-left">
                    @if($researcher->is_active)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Profil Actif') }}</span>
                    @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">{{ __('Profil Inactif') }}</span>
                    @endif
                </div>

                <div class="mt-6 space-y-3 text-sm text-center lg:text-left">
                    @if($researcher->email)
                        <p><x-heroicon-o-envelope class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> <a href="mailto:{{ $researcher->email }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $researcher->email }}</a></p>
                    @endif
                    @if($researcher->phone)
                        <p><x-heroicon-o-phone class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> {{ $researcher->phone }}</p>
                    @endif
                    @if($researcher->website_url)
                        <p><x-heroicon-o-globe-alt class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> <a href="{{ $researcher->website_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('Site Web') }}</a></p>
                    @endif
                    @if($researcher->linkedin_url)
                        <p><x-heroicon-s-link class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> <a href="{{ $researcher->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline">LinkedIn</a></p>
                    @endif
                    @if($researcher->researchgate_url)
                        <p><x-heroicon-s-link class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> <a href="{{ $researcher->researchgate_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline">ResearchGate</a></p>
                    @endif
                    @if($researcher->google_scholar_url)
                        <p><x-heroicon-s-academic-cap class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> <a href="{{ $researcher->google_scholar_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline">Google Scholar</a></p>
                    @endif
                    @if($researcher->orcid_id)
                        <p><x-heroicon-s-identification class="h-4 w-4 inline mr-1 text-gray-400 dark:text-gray-500"/> ORCID: {{ $researcher->orcid_id }}</p>
                    @endif
                </div>
                 @if($researcher->user)
                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-sm text-center lg:text-left">
                        <span class="font-medium text-gray-500 dark:text-gray-400">{{__('Compte utilisateur lié:')}}</span> <a href="{{ route('admin.users.show', $researcher->user_id) }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $researcher->user->name }}</a>
                    </div>
                @endif
            </div>

            {{-- Colonne Informations Localisées --}}
            <div class="flex-grow mt-6 lg:mt-0 md:col-span-2">
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsResearcherShow" role="tablist">
                        @foreach($availableLocales as $locale)
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                        id="tab-researcher-show-{{ $locale }}"
                                        data-tabs-target="#content-researcher-show-{{ $locale }}"
                                        type="button" role="tab" aria-controls="content-researcher-show-{{ $locale }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ strtoupper($locale) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div id="languageTabContentResearcherShow">
                    @foreach($availableLocales as $locale)
                        <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-researcher-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-researcher-show-{{ $locale }}">
                            
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $researcher->getTranslation('first_name', $locale, false) }} {{ $researcher->getTranslation('last_name', $locale, false) }}
                            </h3>
                            @if($researcher->getTranslation('title_position', $locale, false))
                                <p class="text-md text-primary-600 dark:text-primary-400 mb-4">{{ $researcher->getTranslation('title_position', $locale, false) }}</p>
                            @endif
                            
                            @if($researcher->getTranslation('biography', $locale, false))
                                <div class="mt-4">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1 uppercase tracking-wider">{{ __('Biographie') }}</h4>
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 content-styles">
                                        {!! $researcher->getTranslation('biography', $locale, false) !!} {{-- Si HTML --}}
                                    </div>
                                </div>
                            @endif

                            @if($researcher->getTranslation('research_interests', $locale, false))
                                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1 uppercase tracking-wider">{{ __('Domaines de Recherche') }}</h4>
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 content-styles">
                                        {!! $researcher->getTranslation('research_interests', $locale, false) !!} {{-- Si HTML --}}
                                    </div>
                                </div>
                            @endif

                            @if($researcher->photo_url && $researcher->getTranslation('photo_alt_text', $locale, false))
                                <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif de la Photo') }}:</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $researcher->getTranslation('photo_alt_text', $locale, false) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Publications Associées --}}
        @if($researcher->publications->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{ __('Publications Associées') }} ({{ $researcher->publications->count() }})</h3>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($researcher->publications as $publication)
                        <li>
                            <a href="{{ route('admin.publications.show', $publication->id) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                {{ $publication->getTranslation('title', app()->getLocale(), false) }}
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400"> ({{ $publication->publication_date?->format('Y') }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <span>{{ __('Ordre d\'affichage') }}: {{ $researcher->display_order }}</span>
                <span>{{ __('Profil créé le') }}: {{ $researcher->created_at->translatedFormat('d M Y à H:i') }}</span>
                <span>{{ __('Dernière mise à jour') }}: {{ $researcher->updated_at->translatedFormat('d M Y à H:i') }}</span>
            </div>
        </div>
        
        @can('manage researchers')
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
            <form action="{{ route('admin.researchers.destroy', $researcher->slug) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce profil :name ? Ceci est irréversible.', ['name' => addslashes($researcher->full_name)]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer ce Profil') }}
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
    const tabButtonsResearcherShow = document.querySelectorAll('#languageTabsResearcherShow button');
    const tabContentsResearcherShow = document.querySelectorAll('#languageTabContentResearcherShow > div');

    tabButtonsResearcherShow.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsResearcherShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsResearcherShow.forEach(content => {
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
    if (tabButtonsResearcherShow.length > 0 && !document.querySelector('#languageTabsResearcherShow button.active')) {
         if (tabButtonsResearcherShow[0]) tabButtonsResearcherShow[0].click();
    }
});
</script>
@endpush
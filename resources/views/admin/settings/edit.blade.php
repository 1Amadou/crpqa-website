@extends('layouts.admin') {{-- Ou votre layout d'administration principal --}}

@section('title', __('Paramètres du Site'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold text-gray-800 dark:text-white mb-6">
        {{ __('Paramètres du Site') }}
    </h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">{{ __('Succès!') }}</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">{{ __('Oups! Il y a eu des erreurs avec votre soumission.') }}</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex flex-col md:flex-row md:space-x-6">
            {{-- Colonne de Navigation Verticale --}}
            <div class="w-full md:w-1/4 mb-6 md:mb-0">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <nav class="space-y-1" aria-label="Tabs">
                        {{-- L'icône est un exemple, ajustez avec Blade Heroicons ou supprimez --}}
                        <a href="#general" data-vertical-tab-target="general" class="vertical-tab-item group active-vertical-tab flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">{{ __('Général & SEO') }}</span>
                        </a>
                        <a href="#appearance" data-vertical-tab-target="appearance" class="vertical-tab-item group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="truncate">{{ __('Apparence & Médias') }}</span>
                        </a>
                        <a href="#contact" data-vertical-tab-target="contact" class="vertical-tab-item group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.46 4.4a1 1 0 01-.293 1.091l-1.41 1.104a1 1 0 00-.28 1.036l1.403 3.554a1 1 0 001.036.281l1.103-1.41a1 1 0 011.09.292l4.402 1.46a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="truncate">{{ __('Contact & Réseaux') }}</span>
                        </a>
                        <a href="#legal" data-vertical-tab-target="legal" class="vertical-tab-item group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="truncate">{{ __('Légal & Consentement') }}</span>
                        </a>
                        <a href="#technical" data-vertical-tab-target="technical" class="vertical-tab-item group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2.293-2.293c-.63-.63-.184-1.707.707-1.707H10m0 0l2.293-2.293c.63-.63 1.707-.184 1.707.707V17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="truncate">{{ __('Technique & Système') }}</span>
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Colonne de Contenu des Onglets --}}
            <div class="w-full md:w-3/4">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    {{-- Conteneur pour le contenu des onglets verticaux --}}
                    <div id="verticalTabContentContainer">
                        {{-- Onglet Vertical: Général & SEO --}}
                        <div id="general" class="vertical-tab-pane active-vertical-pane">
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('Général & SEO') }}</h2>

                            {{-- Système d'onglets horizontaux pour la localisation (FR/EN) --}}
                            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs-general" role="tablist">
                                    @foreach($availableLocales as $index => $locale)
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                                    id="tab-general-{{ $locale }}"
                                                    data-tabs-target="#content-general-{{ $locale }}"
                                                    type="button" role="tab" aria-controls="content-general-{{ $locale }}"
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                {{ strtoupper($locale) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Contenu des onglets de langue pour "Général & SEO" --}}
                            <div id="languageTabContent-general">
                                @foreach($availableLocales as $index => $locale)
                                    <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-general-{{ $locale }}" role="tabpanel" aria-labelledby="tab-general-{{ $locale }}">
                                        <fieldset class="mb-6">
                                            {{-- Nom du site --}}
                                            <div class="mb-4">
                                                <label for="site_name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom du Site') }} ({{ strtoupper($locale) }}) <span class="text-red-500">*</span></label>
                                                <input type="text" name="site_name_{{ $locale }}" id="site_name_{{ $locale }}"
                                                       value="{{ old('site_name_' . $locale, $settings->{'site_name_' . $locale} ?? $settings->getTranslation('site_name', $locale) ?? '') }}"
                                                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                       {{ $locale == ($availableLocales[0] ?? 'fr') ? 'required' : '' }}>
                                                @error('site_name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>

                                            {{-- Titre SEO Meta --}}
                                            <div class="mb-4">
                                                <label for="seo_meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre SEO Meta') }} ({{ strtoupper($locale) }})</label>
                                                <input type="text" name="seo_meta_title_{{ $locale }}" id="seo_meta_title_{{ $locale }}"
                                                       value="{{ old('seo_meta_title_' . $locale, $settings->{'seo_meta_title_' . $locale} ?? $settings->getTranslation('seo_meta_title', $locale) ?? '') }}"
                                                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                                @error('seo_meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>

                                            {{-- Description SEO Meta --}}
                                            <div class="mb-4">
                                                <label for="seo_meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description SEO Meta') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="seo_meta_description_{{ $locale }}" id="seo_meta_description_{{ $locale }}" rows="3"
                                                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('seo_meta_description_' . $locale, $settings->{'seo_meta_description_' . $locale} ?? $settings->getTranslation('seo_meta_description', $locale) ?? '') }}</textarea>
                                                @error('seo_meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>

                                            {{-- Section Héros Titre --}}
                                            <div class="mb-4">
                                                <label for="hero_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre du Héros (Page d\'accueil)') }} ({{ strtoupper($locale) }})</label>
                                                <input type="text" name="hero_title_{{ $locale }}" id="hero_title_{{ $locale }}"
                                                       value="{{ old('hero_title_' . $locale, $settings->{'hero_title_' . $locale} ?? $settings->getTranslation('hero_title', $locale) ?? '') }}"
                                                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                                @error('hero_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>

                                            {{-- Section Héros Sous-titre --}}
                                            <div class="mb-4">
                                                <label for="hero_subtitle_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sous-titre du Héros (Page d\'accueil)') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="hero_subtitle_{{ $locale }}" id="hero_subtitle_{{ $locale }}" rows="3"
                                                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('hero_subtitle_' . $locale, $settings->{'hero_subtitle_' . $locale} ?? $settings->getTranslation('hero_subtitle', $locale) ?? '') }}</textarea>
                                                @error('hero_subtitle_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Fin des onglets de langue pour "Général & SEO" --}}
                        </div>
                        </div>
                        {{-- Onglet Vertical: Apparence & Médias --}}
                        <div id="appearance" class="vertical-tab-pane hidden">
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('Apparence & Médias') }}</h2>

                            {{-- Note: Les champs médias ne sont généralement pas traduisibles, --}}
                            {{-- donc pas besoin d'onglets de langue FR/EN ici pour les uploads eux-mêmes. --}}
                            {{-- Les textes 'alt' des images, s'ils étaient gérés ici, pourraient l'être. --}}

                            <fieldset class="mb-6 p-4 border-t border-gray-200 dark:border-gray-700">
                                <legend class="sr-only">{{ __('Gestion des Médias') }}</legend> {{-- sr-only car le h2 sert de titre --}}

                                {{-- Logo --}}
                                <div class="mb-6">
                                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Logo du Site (pour fonds clairs)') }}</label>
                                    @if($settings->getFirstMediaUrl('logo'))
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $settings->logo_url }}" alt="{{ __('Logo actuel') }}" class="h-16 object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1 bg-gray-100 dark:bg-gray-600">
                                            <label for="remove_logo" class="inline-flex items-center mt-1">
                                                <input type="checkbox" name="remove_logo" id="remove_logo" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Supprimer le logo actuel') }}</span>
                                            </label>
                                        </div>
                                    @else
                                        <div class="mt-2 mb-2 p-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-md">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aucun logo clair actuel.') }}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="logo" id="logo"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
                                    @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats recommandés : PNG, SVG, WEBP. Max 2Mo.') }}</p>
                                </div>

                                {{-- Logo Sombre (Optionnel) --}}
                                <div class="mb-6">
                                    <label for="logo_dark" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Logo du Site (pour fonds sombres - Optionnel)') }}</label>
                                     @if($settings->getFirstMediaUrl('logo_dark'))
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $settings->logo_dark_url }}" alt="{{ __('Logo sombre actuel') }}" class="h-16 object-contain rounded-md border bg-gray-700 border-gray-600 p-1">
                                            <label for="remove_logo_dark" class="inline-flex items-center mt-1">
                                                <input type="checkbox" name="remove_logo_dark" id="remove_logo_dark" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Supprimer le logo sombre actuel') }}</span>
                                            </label>
                                        </div>
                                    @else
                                        <div class="mt-2 mb-2 p-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-md">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aucun logo sombre actuel.') }}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="logo_dark" id="logo_dark"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
                                    @error('logo_dark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                     <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Pour affichage sur fonds sombres. Mêmes formats.') }}</p>
                                </div>

                                {{-- Favicon --}}
                                <div class="mb-6">
                                    <label for="favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Favicon') }}</label>
                                    @if($settings->getFirstMediaUrl('favicon'))
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $settings->favicon_url }}" alt="{{ __('Favicon actuel') }}" class="h-8 w-8 object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                                             <label for="remove_favicon" class="inline-flex items-center mt-1">
                                                <input type="checkbox" name="remove_favicon" id="remove_favicon" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Supprimer le favicon actuel') }}</span>
                                            </label>
                                        </div>
                                    @else
                                        <div class="mt-2 mb-2 p-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-md">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aucun favicon actuel.') }}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="favicon" id="favicon"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
                                    @error('favicon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats : ICO, PNG, SVG. Max 512Ko.') }}</p>
                                </div>

                                {{-- Image de Fond du Héros --}}
                                <div class="mb-4">
                                    <label for="hero_background" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Fond Section Héros (Page d\'accueil)') }}</label>
                                    @if($settings->getFirstMediaUrl('hero_background'))
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $settings->hero_background_url }}" alt="{{ __('Image de fond actuelle') }}" class="h-24 w-auto max-w-xs object-cover rounded-md border border-gray-200 dark:border-gray-700 p-1">
                                            <label for="remove_hero_background" class="inline-flex items-center mt-1">
                                                <input type="checkbox" name="remove_hero_background" id="remove_hero_background" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de fond actuelle') }}</span>
                                            </label>
                                        </div>
                                    @else
                                        <div class="mt-2 mb-2 p-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-md">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aucune image de fond pour le héros actuellement.') }}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="hero_background" id="hero_background"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
                                    @error('hero_background') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Image large. Formats : JPG, PNG, WEBP. Max 4Mo.') }}</p>
                                </div>
                            </fieldset>
                        </div>
                        {{-- Onglet Vertical: Contact & Réseaux --}}
                        <div id="contact" class="vertical-tab-pane hidden">
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('Contact & Réseaux Sociaux') }}</h2>

                            {{-- Onglets de langue pour les champs traduisibles comme l'adresse --}}
                            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs-contact" role="tablist">
                                    @foreach($availableLocales as $index => $locale)
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                                    id="tab-contact-{{ $locale }}"
                                                    data-tabs-target="#content-contact-{{ $locale }}"
                                                    type="button" role="tab" aria-controls="content-contact-{{ $locale }}"
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                {{ strtoupper($locale) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div id="languageTabContent-contact">
                                @foreach($availableLocales as $index => $locale)
                                    <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-contact-{{ $locale }}" role="tabpanel" aria-labelledby="tab-contact-{{ $locale }}">
                                        <fieldset class="mb-6">
                                            {{-- Adresse --}}
                                            <div class="mb-4">
                                                <label for="address_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Adresse Postale') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="address_{{ $locale }}" id="address_{{ $locale }}" rows="3"
                                                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('address_' . $locale, $settings->{'address_' . $locale} ?? $settings->getTranslation('address', $locale) ?? '') }}</textarea>
                                                @error('address_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Fin des onglets de langue pour l'adresse --}}


                            <fieldset class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <legend class="sr-only">{{ __('Coordonnées non traduisibles et Réseaux Sociaux') }}</legend>

                                {{-- Email de contact --}}
                                <div class="mb-4">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email de Contact Principal') }}</label>
                                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email) }}"
                                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Téléphone --}}
                                <div class="mb-4">
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Numéro de Téléphone Principal') }}</label>
                                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}"
                                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                           placeholder="+223 XX XX XX XX">
                                    @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- URL Google Maps --}}
                                <div class="mb-4">
                                    <label for="maps_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lien vers Google Maps (URL complète)') }}</label>
                                    <input type="url" name="maps_url" id="maps_url" value="{{ old('maps_url', $settings->maps_url) }}"
                                           placeholder="https://www.google.com/maps/embed?pb=..."
                                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    @error('maps_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Réseaux Sociaux --}}
                                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mt-6 mb-2">{{ __('Liens des Réseaux Sociaux') }}</h3>
                                @php
                                    $socialPlatforms = [
                                        'facebook' => 'Facebook',
                                        'twitter' => 'Twitter (X)',
                                        'linkedin' => 'LinkedIn',
                                        'youtube' => 'YouTube',
                                        'instagram' => 'Instagram',
                                        // Ajoutez d'autres plateformes ici si besoin
                                    ];
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                    @foreach($socialPlatforms as $platform => $label)
                                    <div>
                                        <label for="social_media_links_{{ $platform }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($label) }}</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 text-sm">
                                                https://
                                            </span>
                                            <input type="text" name="social_media_links[{{ $platform }}]" id="social_media_links_{{ $platform }}"
                                                   value="{{ old('social_media_links.' . $platform, $settings->social_media_links[$platform] ?? '') }}"
                                                   placeholder="{{ $platform }}.com/votrepage"
                                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        </div>
                                        @error('social_media_links.' . $platform) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                        {{-- Onglet Vertical: Légal & Consentement --}}
                        <div id="legal" class="vertical-tab-pane hidden">
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('Légal & Consentement') }}</h2>

                            {{-- Onglets de langue pour les messages traduisibles --}}
                            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs-legal" role="tablist">
                                    @foreach($availableLocales as $index => $locale)
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                                    id="tab-legal-{{ $locale }}"
                                                    data-tabs-target="#content-legal-{{ $locale }}"
                                                    type="button" role="tab" aria-controls="content-legal-{{ $locale }}"
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                {{ strtoupper($locale) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div id="languageTabContent-legal">
                                @foreach($availableLocales as $index => $locale)
                                    <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-legal-{{ $locale }}" role="tabpanel" aria-labelledby="tab-legal-{{ $locale }}">
                                        <fieldset class="mb-6">
                                            {{-- Message de Consentement aux Cookies --}}
                                            <div class="mb-4">
                                                <label for="cookie_consent_message_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Message de Consentement aux Cookies') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="cookie_consent_message_{{ $locale }}" id="cookie_consent_message_{{ $locale }}" rows="4"
                                                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('cookie_consent_message_' . $locale, $settings->{'cookie_consent_message_' . $locale} ?? $settings->getTranslation('cookie_consent_message', $locale) ?? '') }}</textarea>
                                                @error('cookie_consent_message_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>

                                            {{-- Vous pourriez ajouter ici d'autres champs légaux traduisibles si nécessaire --}}
                                        </fieldset>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Fin des onglets de langue pour "Légal & Consentement" --}}


                            <fieldset class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <legend class="sr-only">{{ __('Paramètres de consentement et liens vers les politiques') }}</legend>

                                <div class="mb-6">
                                    <label for="cookie_consent_enabled" class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="cookie_consent_enabled" id="cookie_consent_enabled" value="1"
                                               {{ old('cookie_consent_enabled', $settings->cookie_consent_enabled ?? false) ? 'checked' : '' }}
                                               class="rounded h-4 w-4 border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Activer la bannière de consentement aux cookies') }}</span>
                                    </label>
                                    @error('cookie_consent_enabled') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="cookie_policy_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Page de Politique des Cookies') }}</label>
                                        <select name="cookie_policy_url" id="cookie_policy_url" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                            <option value="">{{ __('-- Sélectionner une page --') }}</option>
                                            @foreach($staticPagesForSelect as $slug => $title)
                                                <option value="{{ $slug }}" {{ old('cookie_policy_url', $settings->cookie_policy_url) == $slug ? 'selected' : '' }}>{{ $title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="cookie_policy_url_external" placeholder="{{__('Ou URL externe complète')}}"
                                               value="{{ old('cookie_policy_url_external', (!array_key_exists($settings->cookie_policy_url ?? '', $staticPagesForSelect) && !empty($settings->cookie_policy_url)) ? $settings->cookie_policy_url : '' ) }}"
                                               class="mt-2 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @error('cookie_policy_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        @error('cookie_policy_url_external') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="privacy_policy_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Page de Politique de Confidentialité') }}</label>
                                        <select name="privacy_policy_url" id="privacy_policy_url" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                            <option value="">{{ __('-- Sélectionner une page --') }}</option>
                                            @foreach($staticPagesForSelect as $slug => $title)
                                                <option value="{{ $slug }}" {{ old('privacy_policy_url', $settings->privacy_policy_url) == $slug ? 'selected' : '' }}>{{ $title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="privacy_policy_url_external" placeholder="{{__('Ou URL externe complète')}}"
                                               value="{{ old('privacy_policy_url_external', (!array_key_exists($settings->privacy_policy_url ?? '', $staticPagesForSelect) && !empty($settings->privacy_policy_url)) ? $settings->privacy_policy_url : '' ) }}"
                                               class="mt-2 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @error('privacy_policy_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        @error('privacy_policy_url_external') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="terms_of_service_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Page des Conditions d\'Utilisation') }}</label>
                                        <select name="terms_of_service_url" id="terms_of_service_url" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                            <option value="">{{ __('-- Sélectionner une page --') }}</option>
                                            @foreach($staticPagesForSelect as $slug => $title)
                                                <option value="{{ $slug }}" {{ old('terms_of_service_url', $settings->terms_of_service_url) == $slug ? 'selected' : '' }}>{{ $title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="terms_of_service_url_external" placeholder="{{__('Ou URL externe complète')}}"
                                               value="{{ old('terms_of_service_url_external', (!array_key_exists($settings->terms_of_service_url ?? '', $staticPagesForSelect) && !empty($settings->terms_of_service_url)) ? $settings->terms_of_service_url : '' ) }}"
                                               class="mt-2 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @error('terms_of_service_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        @error('terms_of_service_url_external') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        {{-- Onglet Vertical: Technique & Système --}}
                        <div id="technical" class="vertical-tab-pane hidden">
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('Technique & Système') }}</h2>

                            {{-- Onglets de langue pour le message de maintenance --}}
                            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs-technical" role="tablist">
                                    @foreach($availableLocales as $index => $locale)
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                                    id="tab-technical-{{ $locale }}"
                                                    data-tabs-target="#content-technical-{{ $locale }}"
                                                    type="button" role="tab" aria-controls="content-technical-{{ $locale }}"
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                {{ strtoupper($locale) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div id="languageTabContent-technical">
                                @foreach($availableLocales as $index => $locale)
                                    <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-technical-{{ $locale }}" role="tabpanel" aria-labelledby="tab-technical-{{ $locale }}">
                                        <fieldset class="mb-6">
                                            {{-- Message de Maintenance --}}
                                            <div class="mb-4">
                                                <label for="maintenance_message_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Message de Maintenance (si activé)') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="maintenance_message_{{ $locale }}" id="maintenance_message_{{ $locale }}" rows="3"
                                                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('maintenance_message_' . $locale, $settings->{'maintenance_message_' . $locale} ?? $settings->getTranslation('maintenance_message', $locale) ?? '') }}</textarea>
                                                @error('maintenance_message_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Fin des onglets de langue pour "Technique & Système" --}}

                            <fieldset class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <legend class="sr-only">{{ __('Paramètres techniques non traduisibles') }}</legend>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="default_sender_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email Expéditeur par Défaut (pour les notifications)') }}</label>
                                        <input type="email" name="default_sender_email" id="default_sender_email" value="{{ old('default_sender_email', $settings->default_sender_email) }}"
                                               placeholder="noreply@votredomaine.com"
                                               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @error('default_sender_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="default_sender_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom Expéditeur par Défaut') }}</label>
                                        <input type="text" name="default_sender_name" id="default_sender_name" value="{{ old('default_sender_name', $settings->default_sender_name) }}"
                                               placeholder="{{ $settings->getTranslation('site_name', $availableLocales[0] ?? 'fr') ?: config('app.name') }}"
                                               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @error('default_sender_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ID Google Analytics (ex: UA-XXXXX-Y ou G-XXXXXXX)') }}</label>
                                    <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings->google_analytics_id) }}"
                                           placeholder="G-XXXXXXXXXX"
                                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    @error('google_analytics_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mt-6">
                                    <label for="maintenance_mode" class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                                               {{ old('maintenance_mode', $settings->maintenance_mode ?? false) ? 'checked' : '' }}
                                               class="rounded h-4 w-4 border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Activer le mode maintenance pour le site public') }}</span>
                                    </label>
                                    @error('maintenance_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('Si activé, seuls les administrateurs connectés pourront voir le site. Les visiteurs verront le message de maintenance (configurable ci-dessus).') }}
                                    </p>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                     <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Enregistrer tous les Paramètres') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Styles pour l'onglet vertical actif */
    .active-vertical-tab {
        background-color: {{ config('tailwind.theme.extend.colors.primary.100', '#E0E7FF') }}; /* Ajustez avec votre couleur primary-100 */
        color: {{ config('tailwind.theme.extend.colors.primary.700', '#4338CA') }}; /* Ajustez avec votre couleur primary-700 */
        font-weight: 500;
    }
    .dark .active-vertical-tab {
        background-color: {{ config('tailwind.theme.extend.colors.primary.700', '#4338CA') }}; /* Couleur pour le mode sombre */
        color: {{ config('tailwind.theme.extend.colors.primary.100', '#E0E7FF') }};
    }
    .vertical-tab-item:not(.active-vertical-tab) {
        color: #4b5563; /* text-gray-600 */
    }
    .dark .vertical-tab-item:not(.active-vertical-tab) {
         color: #d1d5db; /* dark:text-gray-300 */
    }
    .vertical-tab-item:not(.active-vertical-tab):hover {
        background-color: #f3f4f6; /* hover:bg-gray-100 */
        color: #1f2937; /* hover:text-gray-900 */
    }
    .dark .vertical-tab-item:not(.active-vertical-tab):hover {
         background-color: #374151; /* dark:hover:bg-gray-700 */
         color: #f9fafb; /* dark:hover:text-gray-100 */
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const verticalTabs = document.querySelectorAll('[data-vertical-tab-target]');
    const verticalTabPanes = document.querySelectorAll('.vertical-tab-pane');

    // Fonction pour activer un onglet vertical
    function activateVerticalTab(tabLink) {
        const targetId = tabLink.dataset.verticalTabTarget;

        verticalTabs.forEach(t => {
            t.classList.remove('active-vertical-tab');
            t.setAttribute('aria-selected', 'false');
        });
        tabLink.classList.add('active-vertical-tab');
        tabLink.setAttribute('aria-selected', 'true');

        verticalTabPanes.forEach(pane => {
            if (pane.id === targetId) {
                pane.classList.remove('hidden');
                pane.classList.add('active-vertical-pane'); // Juste pour marquer, 'hidden' gère l'affichage
            } else {
                pane.classList.add('hidden');
                pane.classList.remove('active-vertical-pane');
            }
        });

        // Sauvegarder l'onglet actif dans localStorage
        localStorage.setItem('activeVerticalSettingsTab', targetId);
    }

    verticalTabs.forEach(tabLink => {
        tabLink.addEventListener('click', function (event) {
            event.preventDefault();
            activateVerticalTab(this);

            // Réinitialiser les onglets de langue au premier onglet quand on change d'onglet vertical
            const languageTabsContainer = document.querySelector('#' + this.dataset.verticalTabTarget + ' [data-tabs-target]');
            if (languageTabsContainer) {
                const firstLanguageTabButton = languageTabsContainer.querySelector('ul > li:first-child button');
                if (firstLanguageTabButton) {
                    firstLanguageTabButton.click();
                }
            }
        });
    });

    // Restaurer l'onglet vertical actif depuis localStorage
    const activeVerticalTabId = localStorage.getItem('activeVerticalSettingsTab');
    if (activeVerticalTabId) {
        const activeTabLink = document.querySelector(`[data-vertical-tab-target="${activeVerticalTabId}"]`);
        if (activeTabLink) {
            activateVerticalTab(activeTabLink);
        } else {
             // Si l'onglet sauvegardé n'existe plus, active le premier
            if(verticalTabs.length > 0) activateVerticalTab(verticalTabs[0]);
        }
    } else {
        // Activer le premier onglet vertical par défaut si aucun n'est sauvegardé
       if(verticalTabs.length > 0)  activateVerticalTab(verticalTabs[0]);
    }


    // Système d'onglets horizontaux pour la langue (à l'intérieur de chaque onglet vertical)
    // Ce script doit être adapté pour fonctionner avec plusieurs ensembles d'onglets de langue.
    // Il est préférable de l'instancier pour chaque ensemble d'onglets de langue au fur et à mesure que nous construisons le contenu.
    // Pour l'instant, nous le laissons tel quel, et nous l'adapterons si nécessaire.
    const languageTabContainers = document.querySelectorAll('[id^="languageTabs-"]'); // Modifié pour cibler plusieurs conteneurs

    languageTabContainers.forEach(container => {
        const langTabs = container.querySelectorAll('button[role="tab"]');
        const langTabContents = [];
        langTabs.forEach(tab => {
            const content = document.querySelector(tab.dataset.tabsTarget);
            if(content) langTabContents.push(content);
        });

        langTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                langTabs.forEach(t => {
                    t.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500');
                    t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                    t.setAttribute('aria-selected', 'false');
                });
                 langTabContents.forEach(c => { // Utiliser les contenus associés à CE conteneur d'onglets
                    if(c) c.classList.add('hidden');
                });

                this.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500');
                this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                this.setAttribute('aria-selected', 'true');
                
                const targetContent = document.querySelector(this.dataset.tabsTarget);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    });

    // Activer le premier onglet de langue pour chaque ensemble d'onglets horizontaux au chargement.
     languageTabContainers.forEach(container => {
        const firstLangTab = container.querySelector('ul > li:first-child button[role="tab"]');
        if (firstLangTab) {
            // Activer le contenu du premier onglet seulement s'il est dans un onglet vertical actif
            const parentVerticalPane = container.closest('.vertical-tab-pane');
            if (parentVerticalPane && !parentVerticalPane.classList.contains('hidden')) {
                 firstLangTab.click();
            }
        }
    });


});
</script>
@endpush
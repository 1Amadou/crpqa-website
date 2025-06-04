@extends('layouts.admin')

@section('title', __('Paramètres Généraux du Site'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Paramètres Généraux du Site') }}
        </h1>
        {{-- Pas de bouton "retour à la liste" car il n'y a qu'une page de settings --}}
    </div>
@endsection

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="space-y-8">
        {{-- Messages de session --}}
        @if (session('success'))
            <div class="p-4 text-sm bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 bg-red-100 dark:bg-red-700/50 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600 rounded-md">
                <strong class="font-bold">{{ __('Oups! Il y a eu des erreurs avec votre soumission.') }}</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Structure d'onglets principaux pour organiser les sections de paramètres --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="settingsMainTabs" role="tablist">
                @php
                    $mainTabs = [
                        ['id' => 'general', 'label' => __('Général & SEO')],
                        ['id' => 'hero', 'label' => __('Section Héros')],
                        ['id' => 'home_content', 'label' => __('Contenus Accueil')],
                        ['id' => 'contacts', 'label' => __('Contacts & Sociaux')],
                        ['id' => 'media', 'label' => __('Médias du Site')],
                        ['id' => 'advanced', 'label' => __('Config. Avancée')],
                    ];
                @endphp
                @foreach($mainTabs as $tab)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="main-tab-{{ $tab['id'] }}" data-tabs-target="#main-content-{{ $tab['id'] }}"
                                type="button" role="tab" aria-controls="main-content-{{ $tab['id'] }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="settingsMainTabContent">
            @php $primaryLocale = config('app.locale', 'fr'); @endphp

            {{-- Contenu Onglet Général & SEO --}}
            <div class="{{ isset($loop) && $loop->first ? '' : 'hidden' }} p-1" id="main-content-general" role="tabpanel" aria-labelledby="main-tab-general">
                @includeIf('admin.settings.partials._general_seo_settings_form')
            </div>


            {{-- Contenu Onglet Section Héros --}}
            <div class="hidden p-1" id="main-content-hero" role="tabpanel" aria-labelledby="main-tab-hero">
                @include('admin.settings.partials._hero_section_settings_form')
            </div>

            {{-- Contenu Onglet Contenus Accueil --}}
            <div class="hidden p-1" id="main-content-home_content" role="tabpanel" aria-labelledby="main-tab-home_content">
                @include('admin.settings.partials._home_content_settings_form')
                <!-- @include('admin.settings.partials._about_page_content_settings_form', [
    'settings' => $settings,
    'availableLocales' => $availableLocales,
    'primaryLocale' => $primaryLocale 
    // $staticPagesForSelect n'est pas directement utilisé ici mais est disponible si besoin pour d'autres champs
]) -->
            </div>
            
            {{-- Contenu Onglet Contacts & Sociaux --}}
            <div class="hidden p-1" id="main-content-contacts" role="tabpanel" aria-labelledby="main-tab-contacts">
                @include('admin.settings.partials._contact_social_settings_form')
            </div>
            
            {{-- Contenu Onglet Gestion des Médias --}}
            <div class="hidden p-1" id="main-content-media" role="tabpanel" aria-labelledby="main-tab-media">
                @include('admin.settings.partials._media_settings_form')
            </div>

            {{-- Contenu Onglet Configuration Avancée --}}
            <div class="hidden p-1" id="main-content-advanced" role="tabpanel" aria-labelledby="main-tab-advanced">
                @include('admin.settings.partials._advanced_config_settings_form')
            </div>
        </div>

        {{-- Bouton de sauvegarde général --}}
        <div class="flex items-center justify-end mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-semibold text-sm leading-tight uppercase rounded-md shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out">
                <x-heroicon-o-check-circle class="h-5 w-5 mr-2 inline-block"/>
                {{ __('Enregistrer les Paramètres') }}
            </button>
        </div>
    </div> {{-- Fin de space-y-10 --}}
</form>
</div> {{-- Fin de bg-white --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialisation des onglets principaux
    const mainTabButtons = document.querySelectorAll('#settingsMainTabs button[data-tabs-target]');
    const mainTabContents = document.querySelectorAll('#settingsMainTabContent > div[role="tabpanel"]');

    mainTabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            mainTabButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            mainTabContents.forEach(content => {
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

    // Activer le premier onglet principal par défaut
    if (mainTabButtons.length > 0 && !document.querySelector('#settingsMainTabs button.active')) {
         if (mainTabButtons[0]) mainTabButtons[0].click();
    }

    // Initialisation des onglets de langue internes (si vous en avez plusieurs par section)
    // Exemple pour la section "Général & SEO" qui pourrait avoir ses propres onglets de langue
    const initLanguageTabs = (tabsContainerId, tabContentContainerId) => {
        const langTabButtons = document.querySelectorAll(`#${tabsContainerId} button[data-tabs-target]`);
        const langTabContents = document.querySelectorAll(`#${tabContentContainerId} > div[role="tabpanel"]`);

        if (langTabButtons.length === 0) return;

        langTabButtons.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                langTabButtons.forEach(btn => {
                    btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                    btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                    btn.setAttribute('aria-selected', 'false');
                });
                langTabContents.forEach(content => {
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
        if (langTabButtons.length > 0 && !document.querySelector(`#${tabsContainerId} button.active`)) {
            if(langTabButtons[0]) langTabButtons[0].click();
        }
    };

    // Initialiser les onglets de langue pour chaque section qui en utilise
    // (Les partiels devront avoir des IDs uniques pour leurs conteneurs d'onglets et de contenu)
    // Exemple: initLanguageTabs('languageTabsGeneralSeo', 'languageTabContentGeneralSeo');
    //          initLanguageTabs('languageTabsHero', 'languageTabContentHero');
    //          ...etc.
    // Cette initialisation sera faite DANS les fichiers partiels eux-mêmes pour la propreté.
    // Assurez-vous que le script ci-dessus est disponible globalement ou répétez-le dans chaque partiel.
    // Pour l'instant, le script général pour les onglets de la page est dans _form.blade.php de chaque module.
    // Ici, nous gérons les onglets principaux de la page des settings.

    // Gestion de l'upload multiple pour hero_banner_images et des alt texts associés
    const heroBannerImagesInput = document.getElementById('hero_banner_images');
    const heroBannerAltTextContainer = document.getElementById('hero_banner_alt_text_fields'); // Un div qui contiendra les champs d'alt text
    const availableLocalesForJs = @json($availableLocales);

    if (heroBannerImagesInput && heroBannerAltTextContainer) {
        heroBannerImagesInput.addEventListener('change', function(event) {
            heroBannerAltTextContainer.innerHTML = ''; // Vider les anciens champs d'alt text
            if (event.target.files.length > 0) {
                const fileCount = event.target.files.length;
                let fieldsHtml = `<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 mt-2">${fileCount} {{ __('nouvelle(s) image(s) sélectionnée(s). Veuillez fournir les textes alternatifs :') }}</p>`;
                
                for (let i = 0; i < fileCount; i++) {
                    fieldsHtml += `<div class="mb-2 p-2 border border-gray-200 dark:border-gray-600 rounded-md">`;
                    fieldsHtml += `<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">${event.target.files[i].name}</p>`;
                    availableLocalesForJs.forEach(locale => {
                        fieldsHtml += `
                            <label for="hero_banner_alt_text_${i}_${locale}" class="sr-only">{{__('Alt Image')}} ${i + 1} (${locale.toUpperCase()})</label>
                            <input type="text" name="hero_banner_alt_text[${i}][${locale}]" id="hero_banner_alt_text_${i}_${locale}"
                                   class="mt-1 block w-full form-input-sm mb-1" placeholder="{{__('Texte alternatif')}} (${locale.toUpperCase()})">
                        `;
                    });
                    fieldsHtml += `</div>`;
                }
                heroBannerAltTextContainer.innerHTML = fieldsHtml;
            }
        });
    }
});
</script>
@endpush
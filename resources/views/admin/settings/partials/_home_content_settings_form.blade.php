{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale, $staticPagesForSelect --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="space-y-8">
    {{-- Section "À Propos" sur l'Accueil --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Contenu Section "À Propos" (Page d\'Accueil)') }}
        </h3>

        {{-- Onglets de langue pour cette sous-section --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsAboutHome" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-abouthome-{{ $locale }}" data-tabs-target="#content-abouthome-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-abouthome-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="langTabContentAboutHome">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-abouthome-{{ $locale }}" role="tabpanel" aria-labelledby="tab-abouthome-{{ $locale }}">
                    <div class="space-y-4">
                        <div>
                            <label for="about_home_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Section "À Propos" Accueil') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="about_home_title_{{ $locale }}" id="about_home_title_{{ $locale }}" value="{{ old('about_home_title_'.$locale, $settings->getTranslation('about_home_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('about_home_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="about_home_subtitle_{{ $locale }}" class="block text-sm font-medium">{{ __('Sous-titre Section "À Propos" Accueil') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="about_home_subtitle_{{ $locale }}" id="about_home_subtitle_{{ $locale }}" value="{{ old('about_home_subtitle_'.$locale, $settings->getTranslation('about_home_subtitle', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('about_home_subtitle_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="about_home_short_description_{{ $locale }}" class="block text-sm font-medium">{{ __('Description Courte Section "À Propos" Accueil') }} ({{ strtoupper($locale) }})</label>
                            <textarea name="about_home_short_description_{{ $locale }}" id="about_home_short_description_{{ $locale }}" rows="4" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('about_home_short_description_'.$locale, $settings->getTranslation('about_home_short_description', $locale, false)) }}</textarea>
                            @error('about_home_short_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <label for="about_home_points" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Points Clés (Format JSON)') }}</label>
            <textarea name="about_home_points" id="about_home_points" rows="6" class="mt-1 block w-full form-textarea font-mono text-xs" placeholder='[{"icon":"icon-name", "text_fr":"Texte FR", "text_en":"Texte EN"}, ...]'>{{ old('about_home_points', is_array($settings->about_home_points) ? json_encode($settings->about_home_points, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $settings->about_home_points) }}</textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{__('Chaque point doit avoir "icon" (nom ionicon), "text_fr", "text_en" (et autres locales).')}} <br> {{__('Exemple: [{"icon":"rocket-outline", "text_fr":"Recherche de pointe", "text_en":"Cutting-edge research"}]')}}</p>
            @error('about_home_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
         <div class="mt-6">
            <label for="about_home_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Lien "En savoir plus" (slug de la page "À Propos" dédiée)')}}</label>
            <select name="about_home_page_slug" id="about_home_page_slug" class="mt-1 block w-full form-select">
                <option value="">{{ __('-- Sélectionner une page statique --') }}</option>
                @foreach($staticPagesForSelect as $slug => $title)
                    <option value="{{ $slug }}" {{ old('about_home_page_slug', $settings->about_home_page_slug) == $slug ? 'selected' : '' }}>
                        {{ $title }}
                    </option>
                @endforeach
            </select>
            @error('about_home_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>


    {{-- Section Appel à l'Action (CTA) sur l'Accueil --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Contenu Section "Appel à l\'Action" (Page d\'Accueil)') }}
        </h3>

        {{-- Onglets de langue pour cette sous-section --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsHomeCta" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-homecta-{{ $locale }}" data-tabs-target="#content-homecta-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-homecta-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="langTabContentHomeCta">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-homecta-{{ $locale }}" role="tabpanel" aria-labelledby="tab-homecta-{{ $locale }}">
                    <div class="space-y-4">
                        <div>
                            <label for="home_cta_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre CTA Accueil') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="home_cta_title_{{ $locale }}" id="home_cta_title_{{ $locale }}" value="{{ old('home_cta_title_'.$locale, $settings->getTranslation('home_cta_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="home_cta_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte CTA Accueil') }} ({{ strtoupper($locale) }})</label>
                            <textarea name="home_cta_text_{{ $locale }}" id="home_cta_text_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea">{{ old('home_cta_text_'.$locale, $settings->getTranslation('home_cta_text', $locale, false)) }}</textarea>
                        </div>
                        <div>
                            <label for="home_cta_button1_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte Bouton 1 CTA Accueil') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="home_cta_button1_text_{{ $locale }}" id="home_cta_button1_text_{{ $locale }}" value="{{ old('home_cta_button1_text_'.$locale, $settings->getTranslation('home_cta_button1_text', $locale, false)) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="home_cta_button2_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte Bouton 2 CTA Accueil') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="home_cta_button2_text_{{ $locale }}" id="home_cta_button2_text_{{ $locale }}" value="{{ old('home_cta_button2_text_'.$locale, $settings->getTranslation('home_cta_button2_text', $locale, false)) }}" class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">{{__('Configuration des Boutons CTA Accueil (URLs et Icônes)')}}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="home_cta_button1_url" class="block text-sm font-medium">{{ __('URL Bouton 1 CTA Accueil') }}</label>
                    <input type="text" name="home_cta_button1_url" id="home_cta_button1_url" value="{{ old('home_cta_button1_url', $settings->home_cta_button1_url) }}" placeholder="/page/slug ou https://..." class="mt-1 block w-full form-input">
                </div>
                <div>
                    <label for="home_cta_button1_icon" class="block text-sm font-medium">{{ __('Icône Bouton 1 CTA Accueil (nom ionicon)') }}</label>
                    <input type="text" name="home_cta_button1_icon" id="home_cta_button1_icon" value="{{ old('home_cta_button1_icon', $settings->home_cta_button1_icon) }}" placeholder="people-circle-outline" class="mt-1 block w-full form-input">
                </div>
                <div>
                    <label for="home_cta_button2_url" class="block text-sm font-medium">{{ __('URL Bouton 2 CTA Accueil') }}</label>
                    <input type="text" name="home_cta_button2_url" id="home_cta_button2_url" value="{{ old('home_cta_button2_url', $settings->home_cta_button2_url) }}" class="mt-1 block w-full form-input">
                </div>
                <div>
                    <label for="home_cta_button2_icon" class="block text-sm font-medium">{{ __('Icône Bouton 2 CTA Accueil (nom ionicon)') }}</label>
                    <input type="text" name="home_cta_button2_icon" id="home_cta_button2_icon" value="{{ old('home_cta_button2_icon', $settings->home_cta_button2_icon) }}" class="mt-1 block w-full form-input">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initLanguageTabsScoped = (tabsContainerId, tabContentContainerId) => {
        const langTabButtons = document.querySelectorAll(`#${tabsContainerId} button`);
        const langTabContents = document.querySelectorAll(`#${tabContentContainerId} > div`);

        if (langTabButtons.length > 0) {
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
            if (!document.querySelector(`#${tabsContainerId} button.active`) && langTabButtons[0]) {
                langTabButtons[0].click(); 
            }
        }
    };

    initLanguageTabsScoped('langTabsAboutHome', 'langTabContentAboutHome');
    initLanguageTabsScoped('langTabsHomeCta', 'langTabContentHomeCta');
});
</script>
@endpush
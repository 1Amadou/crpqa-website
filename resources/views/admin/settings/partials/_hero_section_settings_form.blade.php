{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="space-y-8">
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Configuration de la Section Héros (Page d\'Accueil)') }}
        </h3>

        {{-- Onglets de langue pour les textes du Héros --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsHero" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-hero-{{ $locale }}" data-tabs-target="#content-hero-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-hero-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="langTabContentHero">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-hero-{{ $locale }}" role="tabpanel" aria-labelledby="tab-hero-{{ $locale }}">
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="hero_main_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Principal du Héros') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="hero_main_title_{{ $locale }}" id="hero_main_title_{{ $locale }}" value="{{ old('hero_main_title_'.$locale, $settings->getTranslation('hero_main_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('hero_main_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hero_highlight_word_{{ $locale }}" class="block text-sm font-medium">{{ __('Mot en Évidence (Titre Héros)') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="hero_highlight_word_{{ $locale }}" id="hero_highlight_word_{{ $locale }}" value="{{ old('hero_highlight_word_'.$locale, $settings->getTranslation('hero_highlight_word', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('hero_highlight_word_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hero_subtitle_line2_{{ $locale }}" class="block text-sm font-medium">{{ __('Deuxième Ligne Titre/Sous-titre Héros') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="hero_subtitle_line2_{{ $locale }}" id="hero_subtitle_line2_{{ $locale }}" value="{{ old('hero_subtitle_line2_'.$locale, $settings->getTranslation('hero_subtitle_line2', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('hero_subtitle_line2_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hero_description_{{ $locale }}" class="block text-sm font-medium">{{ __('Description du Héros') }} ({{ strtoupper($locale) }})</label>
                            <textarea name="hero_description_{{ $locale }}" id="hero_description_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('hero_description_'.$locale, $settings->getTranslation('hero_description', $locale, false)) }}</textarea>
                            @error('hero_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hero_button1_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte du Bouton Héros 1') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="hero_button1_text_{{ $locale }}" id="hero_button1_text_{{ $locale }}" value="{{ old('hero_button1_text_'.$locale, $settings->getTranslation('hero_button1_text', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('hero_button1_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                         <div>
                            <label for="hero_button2_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte du Bouton Héros 2') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="hero_button2_text_{{ $locale }}" id="hero_button2_text_{{ $locale }}" value="{{ old('hero_button2_text_'.$locale, $settings->getTranslation('hero_button2_text', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('hero_button2_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                         <div> {{-- Texte ALT pour la collection hero_banner_images --}}
                            <label for="hero_banner_image_alt_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte Alternatif global pour Bannière Héros (Slider)') }} ({{strtoupper($locale)}})</label>
                            <input type="text" name="hero_banner_image_alt_{{ $locale }}" id="hero_banner_image_alt_{{ $locale }}" value="{{ old('hero_banner_image_alt_'.$locale, $settings->getTranslation('hero_banner_image_alt', $locale, false)) }}" class="mt-1 block w-full form-input">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Ce texte sera utilisé par défaut si aucun alt spécifique n\'est fourni par image du slider.')}}</p>
                            @error('hero_banner_image_alt_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Champs non traduits pour les boutons du Héros --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">{{__('Configuration des Boutons du Héros (URLs et Icônes)')}}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="hero_button1_url" class="block text-sm font-medium">{{ __('URL Bouton Héros 1') }}</label>
                    <input type="text" name="hero_button1_url" id="hero_button1_url" value="{{ old('hero_button1_url', $settings->hero_button1_url) }}" placeholder="/page/slug ou https://..." class="mt-1 block w-full form-input">
                    @error('hero_button1_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div>
                    <label for="hero_button1_icon" class="block text-sm font-medium">{{ __('Icône Bouton Héros 1 (nom ionicon)') }}</label>
                    <input type="text" name="hero_button1_icon" id="hero_button1_icon" value="{{ old('hero_button1_icon', $settings->hero_button1_icon) }}" placeholder="arrow-forward-outline" class="mt-1 block w-full form-input">
                    @error('hero_button1_icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div>
                    <label for="hero_button2_url" class="block text-sm font-medium">{{ __('URL Bouton Héros 2') }}</label>
                    <input type="text" name="hero_button2_url" id="hero_button2_url" value="{{ old('hero_button2_url', $settings->hero_button2_url) }}" class="mt-1 block w-full form-input">
                    @error('hero_button2_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div>
                    <label for="hero_button2_icon" class="block text-sm font-medium">{{ __('Icône Bouton Héros 2 (nom ionicon)') }}</label>
                    <input type="text" name="hero_button2_icon" id="hero_button2_icon" value="{{ old('hero_button2_icon', $settings->hero_button2_icon) }}" class="mt-1 block w-full form-input">
                    @error('hero_button2_icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts') {{-- Ce script doit être DANS le fichier partiel pour qu'il soit unique par onglet de section --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const langTabButtonsHero = document.querySelectorAll('#langTabsHero button');
    const langTabContentsHero = document.querySelectorAll('#langTabContentHero > div');

    if (langTabButtonsHero.length > 0) {
        langTabButtonsHero.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                langTabButtonsHero.forEach(btn => {
                    btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                    btn.classList.add('border-transparent'); // Réinitialiser à l'état inactif par défaut
                    btn.setAttribute('aria-selected', 'false');
                });
                langTabContentsHero.forEach(content => {
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
        // Activer le premier onglet de langue par défaut pour cette section
        if (!document.querySelector('#langTabsHero button.active') && langTabButtonsHero[0]) {
            langTabButtonsHero[0].click(); 
        }
    }
});
</script>
@endpush
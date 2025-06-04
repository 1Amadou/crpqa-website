{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
        {{ __('Contenu de la Page "À Propos" Dédiée') }}
    </h3>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        {{ __('Configurez ici les différentes sections qui apparaîtront sur la page "À Propos" détaillée du site public.')}}
    </p>

    {{-- Onglets de langue pour cette section --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsAboutPage" role="tablist">
            @foreach($availableLocales as $locale)
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                            id="tab-aboutpage-{{ $locale }}" data-tabs-target="#content-aboutpage-{{ $locale }}"
                            type="button" role="tab" aria-controls="content-aboutpage-{{ $locale }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ strtoupper($locale) }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div id="langTabContentAboutPage">
        @foreach($availableLocales as $locale)
            <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-aboutpage-{{ $locale }}" role="tabpanel" aria-labelledby="tab-aboutpage-{{ $locale }}">
                <div class="space-y-6">
                    {{-- Héros spécifique à la page "À Propos" --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Bandeau de la Page "À Propos"')}} ({{ strtoupper($locale) }})</legend>
                        <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_page_hero_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre du Bandeau') }}</label>
                                <input type="text" name="about_page_hero_title_{{ $locale }}" id="about_page_hero_title_{{ $locale }}" value="{{ old('about_page_hero_title_'.$locale, $settings->getTranslation('about_page_hero_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_page_hero_subtitle_{{ $locale }}" class="block text-sm font-medium">{{ __('Sous-titre du Bandeau') }}</label>
                                <input type="text" name="about_page_hero_subtitle_{{ $locale }}" id="about_page_hero_subtitle_{{ $locale }}" value="{{ old('about_page_hero_subtitle_'.$locale, $settings->getTranslation('about_page_hero_subtitle', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>
                    </fieldset>

                    {{-- Introduction --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Introduction')}} ({{ strtoupper($locale) }})</legend>
                        <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_introduction_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Introduction') }}</label>
                                <input type="text" name="about_introduction_title_{{ $locale }}" id="about_introduction_title_{{ $locale }}" value="{{ old('about_introduction_title_'.$locale, $settings->getTranslation('about_introduction_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_introduction_content_{{ $locale }}" class="block text-sm font-medium">{{ __('Contenu Introduction') }}</label>
                                <textarea name="about_introduction_content_{{ $locale }}" id="about_introduction_content_{{ $locale }}" rows="5" class="mt-1 block w-full form-textarea wysiwyg">{{ old('about_introduction_content_'.$locale, $settings->getTranslation('about_introduction_content', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>
                    
                    {{-- Histoire --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Histoire')}} ({{ strtoupper($locale) }})</legend>
                        <div class="mt-2">
                            <label for="about_history_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Section Histoire') }}</label>
                            <input type="text" name="about_history_title_{{ $locale }}" id="about_history_title_{{ $locale }}" value="{{ old('about_history_title_'.$locale, $settings->getTranslation('about_history_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- Le champ JSON pour la timeline est non-traduit et sera dans une autre section du formulaire principal --}}
                    </fieldset>

                    {{-- Mission --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Mission')}} ({{ strtoupper($locale) }})</legend>
                        <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_mission_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Mission') }}</label>
                                <input type="text" name="about_mission_title_{{ $locale }}" id="about_mission_title_{{ $locale }}" value="{{ old('about_mission_title_'.$locale, $settings->getTranslation('about_mission_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_mission_content_{{ $locale }}" class="block text-sm font-medium">{{ __('Contenu Mission') }}</label>
                                <textarea name="about_mission_content_{{ $locale }}" id="about_mission_content_{{ $locale }}" rows="4" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('about_mission_content_'.$locale, $settings->getTranslation('about_mission_content', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Vision --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Vision')}} ({{ strtoupper($locale) }})</legend>
                         <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_vision_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Vision') }}</label>
                                <input type="text" name="about_vision_title_{{ $locale }}" id="about_vision_title_{{ $locale }}" value="{{ old('about_vision_title_'.$locale, $settings->getTranslation('about_vision_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_vision_content_{{ $locale }}" class="block text-sm font-medium">{{ __('Contenu Vision') }}</label>
                                <textarea name="about_vision_content_{{ $locale }}" id="about_vision_content_{{ $locale }}" rows="4" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('about_vision_content_'.$locale, $settings->getTranslation('about_vision_content', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Valeurs --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Valeurs')}} ({{ strtoupper($locale) }})</legend>
                        <div class="mt-2">
                            <label for="about_values_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Section Valeurs') }}</label>
                            <input type="text" name="about_values_title_{{ $locale }}" id="about_values_title_{{ $locale }}" value="{{ old('about_values_title_'.$locale, $settings->getTranslation('about_values_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            {{-- Le champ JSON pour la liste des valeurs est non-traduit --}}
                        </div>
                    </fieldset>
                    
                    {{-- Message du Directeur --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Message du Directeur')}} ({{ strtoupper($locale) }})</legend>
                        <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_director_message_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre du Message') }}</label>
                                <input type="text" name="about_director_message_title_{{ $locale }}" id="about_director_message_title_{{ $locale }}" value="{{ old('about_director_message_title_'.$locale, $settings->getTranslation('about_director_message_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                             <div>
                                <label for="about_director_name_{{ $locale }}" class="block text-sm font-medium">{{ __('Nom du Directeur') }}</label>
                                <input type="text" name="about_director_name_{{ $locale }}" id="about_director_name_{{ $locale }}" value="{{ old('about_director_name_'.$locale, $settings->getTranslation('about_director_name', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                             <div>
                                <label for="about_director_position_{{ $locale }}" class="block text-sm font-medium">{{ __('Position du Directeur') }}</label>
                                <input type="text" name="about_director_position_{{ $locale }}" id="about_director_position_{{ $locale }}" value="{{ old('about_director_position_'.$locale, $settings->getTranslation('about_director_position', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_director_message_content_{{ $locale }}" class="block text-sm font-medium">{{ __('Message du Directeur') }}</label>
                                <textarea name="about_director_message_content_{{ $locale }}" id="about_director_message_content_{{ $locale }}" rows="6" class="mt-1 block w-full form-textarea wysiwyg">{{ old('about_director_message_content_'.$locale, $settings->getTranslation('about_director_message_content', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Décret de Création --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section Décret de Création')}} ({{ strtoupper($locale) }})</legend>
                         <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_decree_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Section Décret') }}</label>
                                <input type="text" name="about_decree_title_{{ $locale }}" id="about_decree_title_{{ $locale }}" value="{{ old('about_decree_title_'.$locale, $settings->getTranslation('about_decree_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_decree_intro_text_{{ $locale }}" class="block text-sm font-medium">{{ __('Texte d\'Introduction au Décret') }}</label>
                                <textarea name="about_decree_intro_text_{{ $locale }}" id="about_decree_intro_text_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('about_decree_intro_text_'.$locale, $settings->getTranslation('about_decree_intro_text', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    {{-- FST / USTTB --}}
                    <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                        <legend class="text-md font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Section FST / USTTB')}} ({{ strtoupper($locale) }})</legend>
                        <div class="space-y-4 mt-2">
                            <div>
                                <label for="about_fst_title_{{ $locale }}" class="block text-sm font-medium">{{ __('Titre Section FST') }}</label>
                                <input type="text" name="about_fst_title_{{ $locale }}" id="about_fst_title_{{ $locale }}" value="{{ old('about_fst_title_'.$locale, $settings->getTranslation('about_fst_title', $locale, false)) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="about_fst_content_{{ $locale }}" class="block text-sm font-medium">{{ __('Contenu Section FST (filières, stats, etc.)') }}</label>
                                <textarea name="about_fst_content_{{ $locale }}" id="about_fst_content_{{ $locale }}" rows="6" class="mt-1 block w-full form-textarea wysiwyg">{{ old('about_fst_content_'.$locale, $settings->getTranslation('about_fst_content', $locale, false)) }}</textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>
            @endforeach
        </div>

        {{-- Champs NON TRADUITS spécifiques à la page "À Propos" --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">{{__('Configuration des Sections "À Propos" (Non-traduit)')}}</h4>
            <div class="space-y-6">
                <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Icônes Mission, Vision, Valeurs')}}</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
                        <div>
                            <label for="about_mission_icon_class" class="block text-sm font-medium">{{ __('Classe Icône Mission') }}</label>
                            <input type="text" name="about_mission_icon_class" id="about_mission_icon_class" value="{{ old('about_mission_icon_class', $settings->about_mission_icon_class) }}" placeholder="ex: uil-rocket" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="about_vision_icon_class" class="block text-sm font-medium">{{ __('Classe Icône Vision') }}</label>
                            <input type="text" name="about_vision_icon_class" id="about_vision_icon_class" value="{{ old('about_vision_icon_class', $settings->about_vision_icon_class) }}" placeholder="ex: uil-eye" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="about_values_icon_class" class="block text-sm font-medium">{{ __('Classe Icône Valeurs') }}</label>
                            <input type="text" name="about_values_icon_class" id="about_values_icon_class" value="{{ old('about_values_icon_class', $settings->about_values_icon_class) }}" placeholder="ex: uil-diamond" class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border dark:border-gray-700 p-4 rounded-md">
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 px-2">{{__('Données Structurées (JSON)')}}</legend>
                     <div class="mt-2 space-y-4">
                        <div>
                            <label for="about_history_timeline_json" class="block text-sm font-medium">{{ __('Timeline Historique (Format JSON)') }}</label>
                            <textarea name="about_history_timeline_json" id="about_history_timeline_json" rows="7" class="mt-1 block w-full form-textarea font-mono text-xs" placeholder='[{"year":"2010", "icon":"uil-...", "title_fr":"...", "title_en":"...", "description_fr":"...", "description_en":"..."}, ...]'>{{ old('about_history_timeline_json', is_array($settings->about_history_timeline_json) ? json_encode($settings->about_history_timeline_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $settings->about_history_timeline_json) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{__('Chaque événement : year, icon (optionnel, nom ionicon), title_fr/en, description_fr/en.')}}</p>
                            @error('about_history_timeline_json') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="about_values_list_json" class="block text-sm font-medium">{{ __('Liste des Valeurs (Format JSON)') }}</label>
                            <textarea name="about_values_list_json" id="about_values_list_json" rows="5" class="mt-1 block w-full form-textarea font-mono text-xs" placeholder='[{"text_fr":"Valeur 1 FR", "text_en":"Value 1 EN"}, ...]'>{{ old('about_values_list_json', is_array($settings->about_values_list_json) ? json_encode($settings->about_values_list_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $settings->about_values_list_json) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{__('Chaque valeur : text_fr, text_en.')}}</p>
                            @error('about_values_list_json') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="about_fst_statistics_json" class="block text-sm font-medium">{{ __('Statistiques FST (Format JSON)') }}</label>
                            <textarea name="about_fst_statistics_json" id="about_fst_statistics_json" rows="5" class="mt-1 block w-full form-textarea font-mono text-xs" placeholder='[{"label_fr":"Label FR", "label_en":"Label EN", "value":"150+"}, ...]'>{{ old('about_fst_statistics_json', is_array($settings->about_fst_statistics_json) ? json_encode($settings->about_fst_statistics_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $settings->about_fst_statistics_json) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{__('Chaque statistique : label_fr, label_en, value.')}}</p>
                            @error('about_fst_statistics_json') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initLanguageTabsScopedAboutPage = (tabsContainerId, tabContentContainerId) => {
        const langTabButtons = document.querySelectorAll(`#${tabsContainerId} button`);
        const langTabContents = document.querySelectorAll(`#${tabContentContainerId} > div`);

        if (langTabButtons.length > 0) {
            langTabButtons.forEach((button) => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    langTabButtons.forEach(btn => {
                        btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                        btn.classList.add('border-transparent');
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

    initLanguageTabsScopedAboutPage('langTabsAboutPage', 'langTabContentAboutPage');
});
</script>
@endpush
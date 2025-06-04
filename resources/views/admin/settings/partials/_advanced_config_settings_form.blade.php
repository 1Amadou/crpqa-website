{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale, $staticPagesForSelect --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="space-y-8">
    {{-- Slugs des Pages de Politiques et autres pages système --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Liaison des Pages de Contenu Système') }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ __('Sélectionnez les pages statiques publiées qui serviront de contenu pour les sections de politiques, "à propos", et carrières. Ces slugs seront utilisés pour construire les liens dans le pied de page et ailleurs sur le site.')}}
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label for="cookie_policy_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Page : Politique de Cookies')}}</label>
                <select name="cookie_policy_page_slug" id="cookie_policy_page_slug" class="mt-1 block w-full form-select">
                    <option value="">{{ __('-- Non définie --') }}</option>
                    @foreach($staticPagesForSelect as $slug => $title)
                        <option value="{{ $slug }}" {{ old('cookie_policy_page_slug', $settings->cookie_policy_page_slug) == $slug ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                @error('cookie_policy_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="privacy_policy_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Page : Politique de Confidentialité')}}</label>
                <select name="privacy_policy_page_slug" id="privacy_policy_page_slug" class="mt-1 block w-full form-select">
                    <option value="">{{ __('-- Non définie --') }}</option>
                    @foreach($staticPagesForSelect as $slug => $title)
                        <option value="{{ $slug }}" {{ old('privacy_policy_page_slug', $settings->privacy_policy_page_slug) == $slug ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                @error('privacy_policy_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="terms_of_service_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Page : Conditions d\'Utilisation')}}</label>
                <select name="terms_of_service_page_slug" id="terms_of_service_page_slug" class="mt-1 block w-full form-select">
                    <option value="">{{ __('-- Non définie --') }}</option>
                    @foreach($staticPagesForSelect as $slug => $title)
                        <option value="{{ $slug }}" {{ old('terms_of_service_page_slug', $settings->terms_of_service_page_slug) == $slug ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                @error('terms_of_service_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="about_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Page "À Propos" (Page Dédiée)')}}</label>
                <select name="about_page_slug" id="about_page_slug" class="mt-1 block w-full form-select">
                    <option value="">{{ __('-- Non définie --') }}</option>
                    @foreach($staticPagesForSelect as $slug => $title)
                        <option value="{{ $slug }}" {{ old('about_page_slug', $settings->about_page_slug) == $slug ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                @error('about_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="careers_page_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Page "Carrières"')}}</label>
                <select name="careers_page_slug" id="careers_page_slug" class="mt-1 block w-full form-select">
                    <option value="">{{ __('-- Non définie --') }}</option>
                    @foreach($staticPagesForSelect as $slug => $title)
                        <option value="{{ $slug }}" {{ old('careers_page_slug', $settings->careers_page_slug) == $slug ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                @error('careers_page_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    
    {{-- Paramètres Emails et Système --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Paramètres Système et Emails') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
            <div>
                <label for="default_sender_email" class="block text-sm font-medium">{{__('Email Expéditeur par Défaut')}}</label>
                <input type="email" name="default_sender_email" id="default_sender_email" value="{{ old('default_sender_email', $settings->default_sender_email) }}" class="mt-1 block w-full form-input" placeholder="noreply@votredomaine.com">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Utilisé pour les emails transactionnels.')}}</p>
                @error('default_sender_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="default_sender_name" class="block text-sm font-medium">{{__('Nom Expéditeur par Défaut')}}</label>
                <input type="text" name="default_sender_name" id="default_sender_name" value="{{ old('default_sender_name', $settings->default_sender_name) }}" class="mt-1 block w-full form-input" placeholder="{{ $settings->getTranslation('site_name', $primaryLocale, false) ?: config('app.name') }}">
                @error('default_sender_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
             <div class="mt-4 md:col-span-2">
                <label for="google_analytics_id" class="block text-sm font-medium">{{__('Google Analytics ID')}}</label>
                <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings->google_analytics_id) }}" placeholder="UA-XXXXX-Y ou G-XXXXXXX" class="mt-1 block w-full form-input">
                @error('google_analytics_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Consentement Cookies et Mode Maintenance --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8">
         <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Conformité et Maintenance') }}
        </h3>
        {{-- Onglets de langue pour cette sous-section --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsCompliance" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-compliance-{{ $locale }}" data-tabs-target="#content-compliance-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-compliance-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="langTabContentCompliance">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-compliance-{{ $locale }}" role="tabpanel" aria-labelledby="tab-compliance-{{ $locale }}">
                    <div class="mb-4">
                        <label for="cookie_consent_message_{{ $locale }}" class="block text-sm font-medium">{{ __('Message Consentement Cookies') }} ({{ strtoupper($locale) }})</label>
                        <textarea name="cookie_consent_message_{{ $locale }}" id="cookie_consent_message_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea wysiwyg-very-limited">{{ old('cookie_consent_message_'.$locale, $settings->getTranslation('cookie_consent_message', $locale, false)) }}</textarea>
                        @error('cookie_consent_message_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="maintenance_message_{{ $locale }}" class="block text-sm font-medium">{{ __('Message Mode Maintenance') }} ({{ strtoupper($locale) }})</label>
                        <textarea name="maintenance_message_{{ $locale }}" id="maintenance_message_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('maintenance_message_'.$locale, $settings->getTranslation('maintenance_message', $locale, false)) }}</textarea>
                        @error('maintenance_message_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 mt-6">
            <div>
                <label for="cookie_consent_enabled" class="flex items-center cursor-pointer">
                    <input type="hidden" name="cookie_consent_enabled" value="0">
                    <input type="checkbox" name="cookie_consent_enabled" id="cookie_consent_enabled" value="1"
                           {{ old('cookie_consent_enabled', $settings->cookie_consent_enabled ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Activer le bandeau de consentement aux cookies') }}</span>
                </label>
                 @error('cookie_consent_enabled') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
             <div>
                <label for="maintenance_mode" class="flex items-center cursor-pointer">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                           {{ old('maintenance_mode', $settings->maintenance_mode ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Activer le Mode Maintenance') }}</span>
                </label>
                 @error('maintenance_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initLanguageTabsScopedAdvanced = (tabsContainerId, tabContentContainerId) => {
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

    initLanguageTabsScopedAdvanced('langTabsCompliance', 'langTabContentCompliance');
});
</script>
@endpush
{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8"> {{-- mt-8 pour espacer du partiel précédent --}}
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
        {{ __('Coordonnées et Réseaux Sociaux') }}
    </h3>

    {{-- Section Coordonnées --}}
    <div class="mb-6">
        <h4 class="text-md font-medium text-gray-700 dark:text-gray-200 mb-3">{{ __('Coordonnées Principales') }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="contact_email" class="block text-sm font-medium">{{ __('Email de Contact Principal') }}</label>
                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="mt-1 block w-full form-input">
                @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="contact_phone" class="block text-sm font-medium">{{ __('Téléphone de Contact Principal') }}</label>
                <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="mt-1 block w-full form-input">
                @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-4">
            <label for="maps_url" class="block text-sm font-medium">{{ __('URL Google Maps (ou lien iframe src)') }}</label>
            <input type="url" name="maps_url" id="maps_url" value="{{ old('maps_url', $settings->maps_url) }}" class="mt-1 block w-full form-input" placeholder="https://maps.google.com/maps?q=...">
            @error('maps_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Onglets de langue pour l'adresse --}}
    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
        <h4 class="text-md font-medium text-gray-700 dark:text-gray-200 mb-3">{{ __('Adresse Postale (Traduite)') }}</h4>
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsAddress" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-address-{{ $locale }}" data-tabs-target="#content-address-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-address-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="langTabContentAddress">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-address-{{ $locale }}" role="tabpanel" aria-labelledby="tab-address-{{ $locale }}">
                    <div>
                        <label for="address_{{ $locale }}" class="sr-only">{{ __('Adresse') }} ({{ strtoupper($locale) }})</label>
                        <textarea name="address_{{ $locale }}" id="address_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea">{{ old('address_'.$locale, $settings->getTranslation('address', $locale, false)) }}</textarea>
                        @error('address_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Liens Réseaux Sociaux --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-md font-medium text-gray-700 dark:text-gray-200 mb-3">{{__('Liens vers les Réseaux Sociaux')}}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="facebook_url" class="block text-sm font-medium">{{ __('URL Facebook') }}</label>
                <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="mt-1 block w-full form-input" placeholder="https://facebook.com/...">
                @error('facebook_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="twitter_url" class="block text-sm font-medium">{{ __('URL Twitter (X)') }}</label>
                <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}" class="mt-1 block w-full form-input" placeholder="https://x.com/...">
                @error('twitter_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="linkedin_url" class="block text-sm font-medium">{{ __('URL LinkedIn') }}</label>
                <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $settings->linkedin_url) }}" class="mt-1 block w-full form-input" placeholder="https://linkedin.com/in/... ou /company/...">
                @error('linkedin_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="youtube_url" class="block text-sm font-medium">{{ __('URL YouTube') }}</label>
                <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $settings->youtube_url) }}" class="mt-1 block w-full form-input" placeholder="https://youtube.com/channel/...">
                @error('youtube_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            {{-- Décommentez et adaptez si vous avez ajouté la colonne 'instagram_url' à la migration et au modèle
            <div>
                <label for="instagram_url" class="block text-sm font-medium">{{ __('URL Instagram') }}</label>
                <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" class="mt-1 block w-full form-input" placeholder="https://instagram.com/...">
                @error('instagram_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initLanguageTabsScopedContact = (tabsContainerId, tabContentContainerId) => {
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

    initLanguageTabsScopedContact('langTabsAddress', 'langTabContentAddress');
    // Si vous ajoutez d'autres groupes d'onglets dans ce partiel, initialisez-les ici
});
</script>
@endpush
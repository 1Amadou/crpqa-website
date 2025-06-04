{{-- Props attendues de edit.blade.php : $settings, $availableLocales, $primaryLocale --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="space-y-8">
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Identité du Site et SEO Global') }}
        </h3>

        {{-- Onglets de langue pour cette sous-section --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="langTabsGeneral" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-md {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-general-{{ $locale }}" data-tabs-target="#content-general-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-general-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="langTabContentGeneral">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} py-2 px-1" id="content-general-{{ $locale }}" role="tabpanel" aria-labelledby="tab-general-{{ $locale }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="site_name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom Complet du Site') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                            <input type="text" name="site_name_{{ $locale }}" id="site_name_{{ $locale }}" value="{{ old('site_name_'.$locale, $settings->getTranslation('site_name', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('site_name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="site_name_short_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom Court / Acronyme') }} ({{ strtoupper($locale) }})</label>
                            <input type="text" name="site_name_short_{{ $locale }}" id="site_name_short_{{ $locale }}" value="{{ old('site_name_short_'.$locale, $settings->getTranslation('site_name_short', $locale, false)) }}" class="mt-1 block w-full form-input">
                            @error('site_name_short_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="site_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description Générale du Site (pour SEO et meta tags)') }} ({{ strtoupper($locale) }})</label>
                        <textarea name="site_description_{{ $locale }}" id="site_description_{{ $locale }}" rows="3" class="mt-1 block w-full form-textarea wysiwyg-limited">{{ old('site_description_'.$locale, $settings->getTranslation('site_description', $locale, false)) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Max 160-200 caractères recommandés pour une bonne visibilité SEO.')}}</p>
                        @error('site_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                     <div class="mb-4">
                        <label for="copyright_text_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte de Copyright (Pied de page)') }} ({{ strtoupper($locale) }})</label>
                        <input type="text" name="copyright_text_{{ $locale }}" id="copyright_text_{{ $locale }}" value="{{ old('copyright_text_'.$locale, $settings->getTranslation('copyright_text', $locale, false)) }}" placeholder="Ex: © {{ date('Y') }} {{ $settings->getTranslation('site_name_short', $locale, false) ?: $settings->getTranslation('site_name', $locale, false) }}. {{ __('Tous droits réservés.') }}" class="mt-1 block w-full form-input">
                        @error('copyright_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
            {{ __('Identifiants Externes et Tracking') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Google Analytics ID')}}</label>
                <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings->google_analytics_id) }}" placeholder="UA-XXXXX-Y ou G-XXXXXXX" class="mt-1 block w-full form-input">
                @error('google_analytics_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            {{-- Ajoutez d'autres champs de tracking ici si nécessaire (Meta Pixel ID, etc.) --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const langTabButtonsGeneral = document.querySelectorAll('#langTabsGeneral button');
    const langTabContentsGeneral = document.querySelectorAll('#langTabContentGeneral > div');

    if (langTabButtonsGeneral.length > 0) {
        langTabButtonsGeneral.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                langTabButtonsGeneral.forEach(btn => {
                    btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                    btn.classList.add('border-transparent');
                    btn.setAttribute('aria-selected', 'false');
                });
                langTabContentsGeneral.forEach(content => {
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
        if (!document.querySelector('#langTabsGeneral button.active') && langTabButtonsGeneral[0]) {
            langTabButtonsGeneral[0].click(); 
        }
    }
});
</script>
@endpush
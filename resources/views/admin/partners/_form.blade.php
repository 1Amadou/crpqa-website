@props([
    'partner' => null, // Sera un objet Partner (ou null pour la création)
    'availableLocales',
    // 'partnerTypes' => [], // Décommentez et passez si vous avez des types prédéfinis pour un <select>
])

@php
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsPartner" role="tablist">
        @foreach($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-partner-{{ $locale }}"
                        data-tabs-target="#content-partner-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-partner-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentPartner">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-partner-{{ $locale }}" role="tabpanel" aria-labelledby="tab-partner-{{ $locale }}">
            
            {{-- Nom du Partenaire --}}
            <div class="mb-4">
                <label for="name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom du Partenaire') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="name_{{ $locale }}" id="name_{{ $locale }}"
                       value="{{ old('name_' . $locale, $partner?->getTranslation('name', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description du Partenaire --}}
            <div class="mb-4">
                <label for="description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }} ({{ strtoupper($locale) }})</label>
                <textarea name="description_{{ $locale }}" id="description_{{ $locale }}" rows="6"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('description_' . $locale, $partner?->getTranslation('description', $locale, false)) }}</textarea>
                @error('description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- Texte Alternatif pour le Logo --}}
            <div class="mb-4">
                <label for="logo_alt_text_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour le Logo') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="logo_alt_text_{{ $locale }}" id="logo_alt_text_{{ $locale }}"
                       value="{{ old('logo_alt_text_' . $locale, $partner?->getTranslation('logo_alt_text', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez le logo pour l\'accessibilité et le SEO.')}}</p>
                @error('logo_alt_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- URL du Site Web --}}
        <div class="mb-4">
            <label for="website_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Site Web (URL)') }}</label>
            <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $partner?->website_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="https://example.com">
            @error('website_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Type de Partenaire --}}
        <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Type de Partenaire') }}</label>
            {{-- Si vous avez une liste prédéfinie de types, utilisez un <select> --}}
            {{-- Exemple avec select (décommentez et adaptez si $partnerTypes est fourni) :
            <select name="type" id="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                <option value="">{{ __('-- Sélectionner un type --') }}</option>
                @if(isset($partnerTypes))
                    @foreach($partnerTypes as $key => $value)
                        <option value="{{ $key }}" {{ old('type', $partner?->type) == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                @endif
            </select>
            --}}
            {{-- Version input simple si les types sont libres : --}}
            <input type="text" name="type" id="type" value="{{ old('type', $partner?->type) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="{{ __('Ex: Institutionnel, Entreprise, Académique...') }}">
            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Logo du Partenaire --}}
    <div class="mb-6">
        <label for="partner_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Logo du Partenaire') }}</label>
        @if($partner && $partner->hasMedia('partner_logo'))
            <div class="mt-2 mb-2">
                <img src="{{ $partner->getFirstMediaUrl('partner_logo', 'thumbnail') }}" alt="{{ __('Logo actuel') }}" class="h-20 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $partner->getFirstMedia('partner_logo')->name }} 
                    ({{ round($partner->getFirstMedia('partner_logo')->size / 1024) }} Ko)
                </p>
                <label for="remove_partner_logo" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_partner_logo" id="remove_partner_logo" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer le logo actuel') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="partner_logo" id="partner_logo" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, SVG, WEBP. Max 2Mo.') }}</p>
        @error('partner_logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Ordre d'Affichage --}}
        <div class="mb-4">
            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ordre d\'Affichage') }}</label>
            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $partner?->display_order ?? 0) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   min="0">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Un nombre plus petit s\'affiche en premier.')}}</p>
            @error('display_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Statut Actif --}}
        <div class="mb-4 pt-5">
            <label for="is_active" class="flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $partner?->is_active ?? true) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Partenaire Actif') }}</span>
            </label>
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Champ created_by_user_id n'est pas géré ici, car les partenaires ne sont pas typiquement liés à un "auteur" utilisateur. --}}
{{-- Si vous en avez besoin, ajoutez un champ select comme pour les autres modules. --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsPartner = document.querySelectorAll('#languageTabsPartner button');
    const tabContentsPartner = document.querySelectorAll('#languageTabContentPartner > div');

    tabButtonsPartner.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsPartner.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsPartner.forEach(content => {
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

    if (tabButtonsPartner.length > 0 && !document.querySelector('#languageTabsPartner button.active')) {
         if (tabButtonsPartner[0]) tabButtonsPartner[0].click();
    }
});
</script>
@endpush
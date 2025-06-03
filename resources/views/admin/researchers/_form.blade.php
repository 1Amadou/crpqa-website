@props([
    'researcher', // Sera un objet Researcher (ou null pour la création)
    'availableLocales',
    'users', // Collection des utilisateurs pour le select user_id
])

@php
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsResearcher" role="tablist">
        @foreach($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-researcher-{{ $locale }}"
                        data-tabs-target="#content-researcher-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-researcher-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentResearcher">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-researcher-{{ $locale }}" role="tabpanel" aria-labelledby="tab-researcher-{{ $locale }}">
            
            {{-- Prénom --}}
            <div class="mb-4">
                <label for="first_name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Prénom') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="first_name_{{ $locale }}" id="first_name_{{ $locale }}"
                       value="{{ old('first_name_' . $locale, $researcher?->getTranslation('first_name', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('first_name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nom de Famille --}}
            <div class="mb-4">
                <label for="last_name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom de Famille') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="last_name_{{ $locale }}" id="last_name_{{ $locale }}"
                       value="{{ old('last_name_' . $locale, $researcher?->getTranslation('last_name', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('last_name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Titre/Position --}}
            <div class="mb-4">
                <label for="title_position_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre / Position') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="title_position_{{ $locale }}" id="title_position_{{ $locale }}"
                       value="{{ old('title_position_' . $locale, $researcher?->getTranslation('title_position', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                @error('title_position_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Biographie --}}
            <div class="mb-4">
                <label for="biography_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Biographie') }} ({{ strtoupper($locale) }})</label>
                <textarea name="biography_{{ $locale }}" id="biography_{{ $locale }}" rows="8"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('biography_' . $locale, $researcher?->getTranslation('biography', $locale, false)) }}</textarea>
                @error('biography_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Domaines de Recherche --}}
            <div class="mb-4">
                <label for="research_interests_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Domaines de Recherche') }} ({{ strtoupper($locale) }})</label>
                <textarea name="research_interests_{{ $locale }}" id="research_interests_{{ $locale }}" rows="4"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('research_interests_' . $locale, $researcher?->getTranslation('research_interests', $locale, false)) }}</textarea>
                @error('research_interests_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- Texte Alternatif pour la Photo --}}
            <div class="mb-4">
                <label for="photo_alt_text_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour la Photo') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="photo_alt_text_{{ $locale }}" id="photo_alt_text_{{ $locale }}"
                       value="{{ old('photo_alt_text_' . $locale, $researcher?->getTranslation('photo_alt_text', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez la photo pour l\'accessibilité et le SEO.')}}</p>
                @error('photo_alt_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Slug --}}
        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $researcher?->slug) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="{{ __('Sera généré si laissé vide') }}">
                   <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    {{ __('Basé sur Prénom Nom (' . $primaryLocale . '). Uniquement minuscules, chiffres, tirets.') }}
</p>

            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Adresse Email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email', $researcher?->email) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="nom@example.com">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Téléphone --}}
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Téléphone') }}</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone', $researcher?->phone) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Site Web --}}
        <div class="mb-4">
            <label for="website_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Site Web (URL)') }}</label>
            <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $researcher?->website_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="https://...">
            @error('website_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6">
        {{-- LinkedIn URL --}}
        <div class="mb-4">
            <label for="linkedin_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('LinkedIn URL') }}</label>
            <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $researcher?->linkedin_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('linkedin_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- ResearchGate URL --}}
        <div class="mb-4">
            <label for="researchgate_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ResearchGate URL') }}</label>
            <input type="url" name="researchgate_url" id="researchgate_url" value="{{ old('researchgate_url', $researcher?->researchgate_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('researchgate_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- Google Scholar URL --}}
        <div class="mb-4">
            <label for="google_scholar_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Google Scholar URL') }}</label>
            <input type="url" name="google_scholar_url" id="google_scholar_url" value="{{ old('google_scholar_url', $researcher?->google_scholar_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('google_scholar_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- ORCID iD --}}
        <div class="mb-4">
            <label for="orcid_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ORCID iD') }}</label>
            <input type="text" name="orcid_id" id="orcid_id" value="{{ old('orcid_id', $researcher?->orcid_id) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="0000-0000-0000-0000">
            @error('orcid_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Photo du Chercheur --}}
    <div class="mb-6">
        <label for="researcher_photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Photo du Chercheur') }}</label>
        @if($researcher && $researcher->hasMedia('researcher_photo'))
            <div class="mt-2 mb-2">
                <img src="{{ $researcher->getFirstMediaUrl('researcher_photo', 'thumbnail') }}" alt="{{ __('Photo actuelle') }}" class="h-24 w-24 object-cover rounded-full border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $researcher->getFirstMedia('researcher_photo')->name }} 
                    ({{ round($researcher->getFirstMedia('researcher_photo')->size / 1024) }} Ko)
                </p>
                <label for="remove_researcher_photo" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_researcher_photo" id="remove_researcher_photo" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer la photo actuelle') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="researcher_photo" id="researcher_photo" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, WEBP. Max 2Mo.') }}</p>
        @error('researcher_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6">
        {{-- Lier à un Compte Utilisateur --}}
        <div class="mb-4">
            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lier à un Compte Utilisateur (Optionnel)') }}</label>
            <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                <option value="">-- {{ __('Aucun / Non lié') }} --</option>
                @if(isset($users))
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ old('user_id', $researcher?->user_id) == $id ? 'selected' : '' }}>
                            {{ $name }} ({{ \App\Models\User::find($id)?->email }})
                        </option>
                    @endforeach
                @endif
            </select>
            @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ordre d'Affichage --}}
        <div class="mb-4">
            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ordre d\'Affichage') }}</label>
            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $researcher?->display_order ?? 0) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   min="0">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Plus petit = plus haut dans la liste.')}}</p>
            @error('display_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Statut Actif --}}
        <div class="mb-4 pt-5">
            <label for="is_active" class="flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $researcher?->is_active ?? true) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Profil Actif') }}</span>
            </label>
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsResearcher = document.querySelectorAll('#languageTabsResearcher button');
    const tabContentsResearcher = document.querySelectorAll('#languageTabContentResearcher > div');

    tabButtonsResearcher.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsResearcher.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsResearcher.forEach(content => {
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

    if (tabButtonsResearcher.length > 0 && !document.querySelector('#languageTabsResearcher button.active')) {
         if (tabButtonsResearcher[0]) tabButtonsResearcher[0].click();
    }
});
</script>
@endpush
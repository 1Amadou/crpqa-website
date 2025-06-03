@props([
    'researchAxis', // Sera un objet ResearchAxis (ou null pour la création)
    'availableLocales',
])

@php
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsResearchAxis" role="tablist">
        @foreach($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-ra-{{ $locale }}"
                        data-tabs-target="#content-ra-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-ra-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentResearchAxis">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-ra-{{ $locale }}" role="tabpanel" aria-labelledby="tab-ra-{{ $locale }}">
            
            {{-- Nom de l'Axe de Recherche --}}
            <div class="mb-4">
                <label for="name_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom de l\'Axe de Recherche') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="name_{{ $locale }}" id="name_{{ $locale }}"
                       value="{{ old('name_' . $locale, $researchAxis?->getTranslation('name', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('name_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Sous-titre --}}
            <div class="mb-4">
                <label for="subtitle_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sous-titre / Accroche') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="subtitle_{{ $locale }}" id="subtitle_{{ $locale }}"
                       value="{{ old('subtitle_' . $locale, $researchAxis?->getTranslation('subtitle', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                @error('subtitle_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description Complète') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <textarea name="description_{{ $locale }}" id="description_{{ $locale }}" rows="10"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('description_' . $locale, $researchAxis?->getTranslation('description', $locale, false)) }}</textarea>
                @error('description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Titre (SEO) --}}
            <div class="mb-4">
                <label for="meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Titre (SEO)') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="meta_title_{{ $locale }}" id="meta_title_{{ $locale }}"
                       value="{{ old('meta_title_' . $locale, $researchAxis?->getTranslation('meta_title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Si laissé vide, sera basé sur le nom de l\'axe.')}}</p>
                @error('meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Description (SEO) --}}
            <div class="mb-4">
                <label for="meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description (SEO)') }} ({{ strtoupper($locale) }})</label>
                <textarea name="meta_description_{{ $locale }}" id="meta_description_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('meta_description_' . $locale, $researchAxis?->getTranslation('meta_description', $locale, false)) }}</textarea>
                @error('meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Icône SVG (Code brut) --}}
            <div class="mb-4">
                <label for="icon_svg_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Code SVG de l\'Icône') }} ({{ strtoupper($locale) }})</label>
                <textarea name="icon_svg_{{ $locale }}" id="icon_svg_{{ $locale }}" rows="5"
                          class="mt-1 block w-full font-mono text-xs px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                          placeholder="<svg>...</svg>">{{ old('icon_svg_' . $locale, $researchAxis?->getTranslation('icon_svg', $locale, false)) }}</textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Collez le code SVG complet ici. Peut être spécifique à la langue.')}}</p>
                @error('icon_svg_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- Texte Alternatif pour l'Image de Couverture --}}
            <div class="mb-4">
                <label for="cover_image_alt_text_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour l\'Image de Couverture') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="cover_image_alt_text_{{ $locale }}" id="cover_image_alt_text_{{ $locale }}"
                       value="{{ old('cover_image_alt_text_' . $locale, $researchAxis?->getTranslation('cover_image_alt_text', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez l\'image pour l\'accessibilité et le SEO.')}}</p>
                @error('cover_image_alt_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Slug --}}
        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $researchAxis?->slug) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="{{ __('Sera généré automatiquement si laissé vide') }}">
                   <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    {{ __('Basé sur le Nom (' . $primaryLocale . '). Uniquement minuscules, chiffres, tirets.') }}
</p>

            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Classe d'Icône (CSS) --}}
        <div class="mb-4">
            <label for="icon_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Classe CSS pour l\'Icône (Optionnel)') }}</label>
            <input type="text" name="icon_class" id="icon_class" value="{{ old('icon_class', $researchAxis?->icon_class) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="ex: ion-flask-outline, fa-solid fa-atom">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Utilisé si aucun SVG n\'est fourni, ou en complément.')}}</p>
            @error('icon_class') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Code Couleur Hexadécimal --}}
        <div class="mb-4">
            <label for="color_hex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Code Couleur (Hexadécimal)') }}</label>
            <input type="text" name="color_hex" id="color_hex" value="{{ old('color_hex', $researchAxis?->color_hex) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="#RRGGBB">
            @error('color_hex') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ordre d'Affichage --}}
        <div class="mb-4">
            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ordre d\'Affichage') }}</label>
            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $researchAxis?->display_order ?? 0) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   min="0">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Plus petit = plus haut dans la liste.')}}</p>
            @error('display_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Image de couverture --}}
    <div class="mb-6">
        <label for="research_axis_cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Couverture (Optionnel)') }}</label>
        @if($researchAxis && $researchAxis->hasMedia('research_axis_cover_image'))
            <div class="mt-2 mb-2">
                <img src="{{ $researchAxis->getFirstMediaUrl('research_axis_cover_image', 'thumbnail') }}" alt="{{ __('Image de couverture actuelle') }}" class="h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $researchAxis->getFirstMedia('research_axis_cover_image')->name }} 
                    ({{ round($researchAxis->getFirstMedia('research_axis_cover_image')->size / 1024) }} Ko)
                </p>
                <label for="remove_research_axis_cover_image" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_research_axis_cover_image" id="remove_research_axis_cover_image" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de couverture actuelle') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="research_axis_cover_image" id="research_axis_cover_image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, WEBP, SVG. Max 2Mo.') }}</p>
        @error('research_axis_cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Statut Actif --}}
    <div class="mt-6">
        <label for="is_active" class="flex items-center cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $researchAxis?->is_active ?? true) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Axe de Recherche Actif') }}</span>
        </label>
        @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsResearchAxis = document.querySelectorAll('#languageTabsResearchAxis button');
    const tabContentsResearchAxis = document.querySelectorAll('#languageTabContentResearchAxis > div');

    tabButtonsResearchAxis.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsResearchAxis.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsResearchAxis.forEach(content => {
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

    if (tabButtonsResearchAxis.length > 0 && !document.querySelector('#languageTabsResearchAxis button.active')) {
         if (tabButtonsResearchAxis[0]) tabButtonsResearchAxis[0].click();
    }
});
</script>
@endpush
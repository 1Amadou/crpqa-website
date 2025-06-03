@props([
    'event', // Sera un objet Event (ou null pour la création)
    'availableLocales',
    'partners', // Collection des partenaires pour le select multiple
])

@php
    // Détermine la locale principale pour marquer les champs requis
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsEvent" role="tablist">
        @foreach($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-event-{{ $locale }}"
                        data-tabs-target="#content-event-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-event-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentEvent">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-event-{{ $locale }}" role="tabpanel" aria-labelledby="tab-event-{{ $locale }}">
            
            {{-- Titre --}}
            <div class="mb-4">
                <label for="title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre de l\'Événement') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       value="{{ old('title_' . $locale, $event->getTranslation('title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <textarea name="description_{{ $locale }}" id="description_{{ $locale }}" rows="10"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('description_' . $locale, $event->getTranslation('description', $locale, false)) }}</textarea>
                @error('description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Lieu --}}
            <div class="mb-4">
                <label for="location_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lieu') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="location_{{ $locale }}" id="location_{{ $locale }}"
                       value="{{ old('location_' . $locale, $event->getTranslation('location', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                @error('location_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- Public Cible --}}
            <div class="mb-4">
                <label for="target_audience_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Public Cible') }} ({{ strtoupper($locale) }})</label>
                <textarea name="target_audience_{{ $locale }}" id="target_audience_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('target_audience_' . $locale, $event->getTranslation('target_audience', $locale, false)) }}</textarea>
                @error('target_audience_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Titre (SEO) --}}
            <div class="mb-4">
                <label for="meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Titre (SEO)') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="meta_title_{{ $locale }}" id="meta_title_{{ $locale }}"
                       value="{{ old('meta_title_' . $locale, $event->getTranslation('meta_title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Si laissé vide, sera basé sur le titre de l\'événement.')}}</p>
                @error('meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Description (SEO) --}}
            <div class="mb-4">
                <label for="meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description (SEO)') }} ({{ strtoupper($locale) }})</label>
                <textarea name="meta_description_{{ $locale }}" id="meta_description_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('meta_description_' . $locale, $event->getTranslation('meta_description', $locale, false)) }}</textarea>
                @error('meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Texte Alternatif pour l'Image de Couverture --}}
            <div class="mb-4">
                <label for="cover_image_alt_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour l\'Image de Couverture') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="cover_image_alt_{{ $locale }}" id="cover_image_alt_{{ $locale }}"
                       value="{{ old('cover_image_alt_' . $locale, $event->getTranslation('cover_image_alt', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez l\'image pour l\'accessibilité et le SEO.')}}</p>
                @error('cover_image_alt_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        {{-- Slug --}}
        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $event->slug) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="{{ __('Sera généré automatiquement si laissé vide') }}">
                   <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    {{ __('Ex: mon-super-evenement. Laisser vide pour générer à partir du titre (' . $primaryLocale . ').') }}
</p>

            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- URL d'Inscription --}}
        <div class="mb-4">
            <label for="registration_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('URL d\'Inscription (Optionnel)') }}</label>
            <input type="url" name="registration_url" id="registration_url" value="{{ old('registration_url', $event->registration_url) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="https://example.com/inscription">
            @error('registration_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Dates et Heures --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 mb-4">
        <div>
            <label for="start_datetime_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date de Début') }} <span class="text-red-500">*</span></label>
            <input type="date" name="start_datetime_date" id="start_datetime_date"
                   value="{{ old('start_datetime_date', $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>
            @error('start_datetime_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="start_datetime_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Heure de Début') }} <span class="text-red-500">*</span></label>
            <input type="time" name="start_datetime_time" id="start_datetime_time"
                   value="{{ old('start_datetime_time', $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->format('H:i') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>
            @error('start_datetime_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="end_datetime_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date de Fin (Optionnel)') }}</label>
            <input type="date" name="end_datetime_date" id="end_datetime_date"
                   value="{{ old('end_datetime_date', $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('end_datetime_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="end_datetime_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Heure de Fin (Optionnel)') }}</label>
            <input type="time" name="end_datetime_time" id="end_datetime_time"
                   value="{{ old('end_datetime_time', $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('H:i') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('end_datetime_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Partenaires --}}
    <div class="mb-4">
        <label for="partner_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Partenaires Associés (Optionnel)') }}</label>
        <select name="partner_ids[]" id="partner_ids" multiple
                class="tom-select-multiple mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            @if(isset($partners))
                @php $eventPartnerIds = $event->partners->pluck('id')->toArray(); @endphp
                @foreach($partners as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, old('partner_ids', $eventPartnerIds)) ? 'selected' : '' }}>
                        {{ $name }} {{-- Le nom est déjà traduit par le contrôleur --}}
                    </option>
                @endforeach
            @endif
        </select>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Sélectionnez un ou plusieurs partenaires.')}}</p>
        @error('partner_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('partner_ids.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Image de couverture --}}
    <div class="mb-6">
        <label for="event_cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Couverture (Optionnel)') }}</label>
        @if($event && $event->hasMedia('event_cover_image'))
            <div class="mt-2 mb-2">
                <img src="{{ $event->getFirstMediaUrl('event_cover_image', 'thumbnail') }}" alt="{{ __('Image de couverture actuelle') }}" class="h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $event->getFirstMedia('event_cover_image')->name }} 
                    ({{ round($event->getFirstMedia('event_cover_image')->size / 1024) }} Ko)
                </p>
                <label for="remove_event_cover_image" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_event_cover_image" id="remove_event_cover_image" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de couverture actuelle') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="event_cover_image" id="event_cover_image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, WEBP, GIF. Max 2Mo.') }}</p>
        @error('event_cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Option "En Vedette" --}}
    <div class="mt-6">
        <label for="is_featured" class="flex items-center cursor-pointer">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                   {{ old('is_featured', $event->is_featured ?? false) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mettre en avant (Featured)') }}</span>
        </label>
        @error('is_featured') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Champ created_by_user_id est géré automatiquement dans le contrôleur lors de la création. --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsEvent = document.querySelectorAll('#languageTabsEvent button');
    const tabContentsEvent = document.querySelectorAll('#languageTabContentEvent > div');

    tabButtonsEvent.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsEvent.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsEvent.forEach(content => {
                content.classList.add('hidden');
            });

            button.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
            button.classList.remove('border-transparent'); // Assurez-vous que les classes 'hover' sont aussi enlevées si elles persistent
            button.setAttribute('aria-selected', 'true');
            const target = document.querySelector(button.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // Activer le premier onglet par défaut au chargement de la page
    if (tabButtonsEvent.length > 0 && !document.querySelector('#languageTabsEvent button.active')) {
         if (tabButtonsEvent[0]) tabButtonsEvent[0].click();
    }

    // Initialisation de TomSelect pour le champ des partenaires (si vous l'utilisez)
    // if (document.getElementById('partner_ids')) {
    //     new TomSelect('#partner_ids',{
    //         plugins: ['remove_button'],
    //         create: false,
    //     });
    // }
});
</script>
@endpush
{{-- resources/views/admin/static_pages/_form.blade.php --}}

@csrf

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsStaticPage" role="tablist">
        @foreach($availableLocales as $index => $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-staticpage-{{ $locale }}"
                        data-tabs-target="#content-staticpage-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-staticpage-{{ $locale }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentStaticPage">
    @foreach($availableLocales as $index => $locale)
        <div class="{{ $index === 0 ? '' : 'hidden' }} p-1" id="content-staticpage-{{ $locale }}" role="tabpanel" aria-labelledby="tab-staticpage-{{ $locale }}">
            {{-- Titre --}}
            <div class="mb-4">
                <label for="title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre') }} ({{ strtoupper($locale) }}) <span class="text-red-500">*</span></label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       value="{{ old('title_' . $locale, $staticPage->{'title_' . $locale} ?? ($staticPage->getTranslation('title', $locale, false) ?? '') ) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale == ($availableLocales[0] ?? 'fr') ? 'required' : '' }}>
                @error('title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Contenu --}}
            <div class="mb-4">
                <label for="content_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Contenu') }} ({{ strtoupper($locale) }}) <span class="text-red-500">*</span></label>
                <textarea name="content_{{ $locale }}" id="content_{{ $locale }}" rows="10"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('content_' . $locale, $staticPage->{'content_' . $locale} ?? ($staticPage->getTranslation('content', $locale, false) ?? '')) }}</textarea>
                @error('content_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Titre --}}
            <div class="mb-4">
                <label for="meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Titre (SEO)') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="meta_title_{{ $locale }}" id="meta_title_{{ $locale }}"
                       value="{{ old('meta_title_' . $locale, $staticPage->{'meta_title_' . $locale} ?? ($staticPage->getTranslation('meta_title', $locale, false) ?? '')) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                @error('meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Description --}}
            <div class="mb-4">
                <label for="meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description (SEO)') }} ({{ strtoupper($locale) }})</label>
                <textarea name="meta_description_{{ $locale }}" id="meta_description_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('meta_description_' . $locale, $staticPage->{'meta_description_' . $locale} ?? ($staticPage->getTranslation('meta_description', $locale, false) ?? '')) }}</textarea>
                @error('meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>
{{-- Fin des onglets de langue --}}

{{-- Slug --}}
<div class="mb-4 pt-4 border-t border-gray-200 dark:border-gray-700">
    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
    <input type="text" name="slug" id="slug" value="{{ old('slug', $staticPage->slug ?? '') }}"
           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
           placeholder="{{ __('Sera généré automatiquement si laissé vide') }}">
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Ex: a-propos-de-nous. Uniquement des lettres minuscules, chiffres, tirets et underscores.')}}</p>
    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Image de couverture --}}
<div class="mb-6">
    <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Couverture (Optionnel)') }}</label>
    @if(isset($staticPage) && $staticPage->getFirstMediaUrl('static_page_cover_image'))
        <div class="mt-2 mb-2">
            <img src="{{ $staticPage->getFirstMediaUrl('static_page_cover_image') }}" alt="{{ __('Image de couverture actuelle') }}" class="h-32 object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
            <label for="remove_cover_image" class="inline-flex items-center mt-1">
                <input type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de couverture actuelle') }}</span>
            </label>
        </div>
    @endif
    <input type="file" name="cover_image" id="cover_image"
           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Publié --}}
<div class="mb-4">
    <label for="is_published" class="flex items-center cursor-pointer">
        <input type="checkbox" name="is_published" id="is_published" value="1"
               {{ old('is_published', $staticPage->is_published ?? true) ? 'checked' : '' }}
               class="rounded h-4 w-4 border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Publier cette page') }}</span>
    </label>
    @error('is_published') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Le script pour les onglets sera inclus dans la vue principale (create/edit) --}}
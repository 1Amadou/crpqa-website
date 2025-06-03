@props([
    'newsItem', // Sera un objet News (ou null pour la création)
    'availableLocales',
    'categories',
])

@php
    // Détermine la locale principale pour marquer les champs requis
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsNews" role="tablist">
        @foreach($availableLocales as $index => $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-news-{{ $locale }}"
                        data-tabs-target="#content-news-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-news-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentNews">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-news-{{ $locale }}" role="tabpanel" aria-labelledby="tab-news-{{ $locale }}">
            
            <div class="mb-4">
                <label for="title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       value="{{ old('title_' . $locale, $newsItem->getTranslation('title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="summary_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Résumé / Accroche') }} ({{ strtoupper($locale) }})</label>
                <textarea name="summary_{{ $locale }}" id="summary_{{ $locale }}" rows="4"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('summary_' . $locale, $newsItem->getTranslation('summary', $locale, false)) }}</textarea>
                @error('summary_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="content_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Contenu Principal') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <textarea name="content_{{ $locale }}" id="content_{{ $locale }}" rows="15"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('content_' . $locale, $newsItem->getTranslation('content', $locale, false)) }}</textarea>
                @error('content_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Titre (SEO)') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="meta_title_{{ $locale }}" id="meta_title_{{ $locale }}"
                       value="{{ old('meta_title_' . $locale, $newsItem->getTranslation('meta_title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Si laissé vide, sera basé sur le titre de l\'actualité.')}}</p>
                @error('meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description (SEO)') }} ({{ strtoupper($locale) }})</label>
                <textarea name="meta_description_{{ $locale }}" id="meta_description_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('meta_description_' . $locale, $newsItem->getTranslation('meta_description', $locale, false)) }}</textarea>
                @error('meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="cover_image_alt_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour l\'Image de Couverture') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="cover_image_alt_{{ $locale }}" id="cover_image_alt_{{ $locale }}"
                       value="{{ old('cover_image_alt_' . $locale, $newsItem->getTranslation('cover_image_alt', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez l\'image pour l\'accessibilité et le SEO.')}}</p>
                @error('cover_image_alt_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $newsItem->slug) }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="{{ __('Sera généré automatiquement si laissé vide') }}">
                   <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    {{ __('Ex: ma-super-actualite. Laisser vide pour générer à partir du titre (:locale).', ['locale' => $primaryLocale]) }}
</p>

            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
        <label for="news_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catégorie') }}</label>
        <select name="news_category_id" id="news_category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            <option value="">{{ __('-- Aucune catégorie --') }}</option>
            @if(isset($categories)) {{-- Vérifier que la variable existe --}}
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('news_category_id', $newsItem->news_category_id) == $id ? 'selected' : '' }}>
                        {{ $name }} {{-- Le nom devrait être traduit ici si pluck utilise l'accesseur du modèle --}}
                    </option>
                @endforeach
            @endif
        </select>
        @error('news_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 mt-4">
        <div>
            <label for="published_at_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date de Publication') }}</label>
            <input type="date" name="published_at_date" id="published_at_date"
                   value="{{ old('published_at_date', $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('published_at_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="published_at_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Heure de Publication') }}</label>
            <input type="time" name="published_at_time" id="published_at_time"
                   value="{{ old('published_at_time', $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->format('H:i') : '') }}"
                   class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            @error('published_at_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6">
        <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Couverture') }}</label>
        @if($newsItem && $newsItem->hasMedia('news_cover_image'))
            <div class="mt-2 mb-2">
                <img src="{{ $newsItem->getFirstMediaUrl('news_cover_image', 'thumbnail') }}" alt="{{ __('Image de couverture actuelle') }}" class="h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $newsItem->getFirstMedia('news_cover_image')->name }} 
                    ({{ round($newsItem->getFirstMedia('news_cover_image')->size / 1024) }} Ko)
                </p>
                <label for="remove_cover_image" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de couverture actuelle') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="cover_image" id="cover_image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, WEBP, GIF. Max 2Mo.') }}</p>
        @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 mt-6">
        <div>
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Statut') }}</span>
            <label for="is_published" class="mt-2 inline-flex items-center cursor-pointer">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                       {{ old('is_published', $newsItem->is_published ?? true) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Publié') }}</span>
            </label>
            @error('is_published') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mise en avant') }}</span>
            <label for="is_featured" class="mt-2 inline-flex items-center cursor-pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                       {{ old('is_featured', $newsItem->is_featured ?? false) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('En vedette') }}</span>
            </label>
            @error('is_featured') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Le champ created_by_user_id est géré automatiquement dans le contrôleur pour la création.
     Il n'est généralement pas modifié lors de l'édition. --}}

{{-- Javascript pour les onglets (si non géré globalement) --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsNews = document.querySelectorAll('#languageTabsNews button');
    const tabContentsNews = document.querySelectorAll('#languageTabContentNews > div');

    tabButtonsNews.forEach((button, index) => {
        button.addEventListener('click', () => {
            tabButtonsNews.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsNews.forEach(content => {
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

    // Activer le premier onglet par défaut au chargement de la page (utile si aucun n'est 'active' initialement)
    if (tabButtonsNews.length > 0 && !document.querySelector('#languageTabsNews button.active')) {
        tabButtonsNews[0].click();
    }
});
</script>
@endpush
@props([
    'staticPage', // Sera un objet StaticPage (ou null pour la création)
    'availableLocales',
    // 'users' => [], // Décommentez et passez si user_id devient un select
])

@php
    $primaryLocale = config('app.locale', $availableLocales[0] ?? 'fr');
@endphp

{{-- CSRF est déjà dans le formulaire principal (create.blade.php / edit.blade.php) --}}

{{-- Système d'onglets pour la localisation --}}
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsStaticPage" role="tablist">
        @foreach($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-staticpage-{{ $locale }}"
                        data-tabs-target="#content-staticpage-{{ $locale }}"
                        type="button" role="tab" aria-controls="content-staticpage-{{ $locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContentStaticPage">
    @foreach($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-1" id="content-staticpage-{{ $locale }}" role="tabpanel" aria-labelledby="tab-staticpage-{{ $locale }}">
            
            {{-- Titre --}}
            <div class="mb-4">
                <label for="title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titre de la Page') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       value="{{ old('title_' . $locale, $staticPage?->getTranslation('title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       {{ $locale === $primaryLocale ? 'required' : '' }}>
                @error('title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Contenu --}}
            <div class="mb-4">
                <label for="content_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Contenu de la Page') }} ({{ strtoupper($locale) }}) @if($locale === $primaryLocale)<span class="text-red-500">*</span>@endif</label>
                <textarea name="content_{{ $locale }}" id="content_{{ $locale }}" rows="15"
                          class="wysiwyg mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('content_' . $locale, $staticPage?->getTranslation('content', $locale, false)) }}</textarea>
                @error('content_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Titre (SEO) --}}
            <div class="mb-4">
                <label for="meta_title_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Titre (SEO)') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="meta_title_{{ $locale }}" id="meta_title_{{ $locale }}"
                       value="{{ old('meta_title_' . $locale, $staticPage?->getTranslation('meta_title', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Si laissé vide, sera basé sur le titre de la page.')}}</p>
                @error('meta_title_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Meta Description (SEO) --}}
            <div class="mb-4">
                <label for="meta_description_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description (SEO)') }} ({{ strtoupper($locale) }})</label>
                <textarea name="meta_description_{{ $locale }}" id="meta_description_{{ $locale }}" rows="3"
                          class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ old('meta_description_' . $locale, $staticPage?->getTranslation('meta_description', $locale, false)) }}</textarea>
                @error('meta_description_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Texte Alternatif pour l'Image de Couverture --}}
            <div class="mb-4">
                <label for="cover_image_alt_text_{{ $locale }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texte Alternatif pour l\'Image de Couverture') }} ({{ strtoupper($locale) }})</label>
                <input type="text" name="cover_image_alt_text_{{ $locale }}" id="cover_image_alt_text_{{ $locale }}"
                       value="{{ old('cover_image_alt_text_' . $locale, $staticPage?->getTranslation('cover_image_alt_text', $locale, false)) }}"
                       class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Décrivez l\'image pour l\'accessibilité et le SEO.')}}</p>
                @error('cover_image_alt_text_' . $locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    {{-- Slug --}}
    <div class="mb-4">
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }} <span class="text-red-500">*</span></label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $staticPage?->slug) }}" required
               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
               placeholder="{{ __('Ex: a-propos-de-nous') }}">
               <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    {{ __('Si laissé vide, sera généré à partir du titre (' . $primaryLocale . '). Uniquement minuscules, chiffres, tirets.') }}
</p>

        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Champ User ID (si vous voulez le rendre sélectionnable) --}}
    {{-- Actuellement, le StaticPageController assigne Auth::id() --}}
    {{-- <div class="mb-4">
        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Auteur/Éditeur') }} <span class="text-red-500">*</span></label>
        <select name="user_id" id="user_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
            @if(isset($users))
                @foreach($users as $id => $name)
                    <option value="{{ $id }}" {{ old('user_id', $staticPage?->user_id ?? auth()->id()) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            @else
                 <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option> {{-- Fallback si $users n'est pas passé --}}
            {{-- @endif
        </select>
        @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div> --}}


    {{-- Image de couverture --}}
    <div class="mb-6">
        <label for="static_page_cover" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Image de Couverture (Optionnel)') }}</label>
        @if($staticPage && $staticPage->hasMedia('static_page_cover'))
            <div class="mt-2 mb-2">
                <img src="{{ $staticPage->getFirstMediaUrl('static_page_cover', 'thumbnail') }}" alt="{{ __('Image de couverture actuelle') }}" class="h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $staticPage->getFirstMedia('static_page_cover')->name }} 
                    ({{ round($staticPage->getFirstMedia('static_page_cover')->size / 1024) }} Ko)
                </p>
                <label for="remove_static_page_cover" class="inline-flex items-center mt-1 text-xs">
                    <input type="checkbox" name="remove_static_page_cover" id="remove_static_page_cover" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:focus:ring-offset-gray-900">
                    <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer l\'image de couverture actuelle') }}</span>
                </label>
            </div>
        @endif
        <input type="file" name="static_page_cover" id="static_page_cover" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Formats acceptés : JPG, PNG, WEBP. Max 2Mo.') }}</p>
        @error('static_page_cover') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Statut de Publication --}}
    <div class="mt-6">
        <label for="is_published" class="flex items-center cursor-pointer">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1"
                   {{ old('is_published', $staticPage?->is_published ?? true) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Publier cette page') }}</span>
        </label>
        @error('is_published') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsStaticPage = document.querySelectorAll('#languageTabsStaticPage button');
    const tabContentsStaticPage = document.querySelectorAll('#languageTabContentStaticPage > div');

    tabButtonsStaticPage.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsStaticPage.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsStaticPage.forEach(content => {
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

    if (tabButtonsStaticPage.length > 0 && !document.querySelector('#languageTabsStaticPage button.active')) {
         if (tabButtonsStaticPage[0]) tabButtonsStaticPage[0].click();
    }
});
</script>
@endpush
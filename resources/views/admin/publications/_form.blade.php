@props([
    'publication' => null,
    'availableLocales',
    'users', // Pour created_by_user_id
    'researchers', // Pour la sélection des auteurs chercheurs
    'publicationTypes', // Pour le champ 'type'
])

@php
    // La locale par défaut est déterminée par config('app.locale')
    // $availableLocales est déjà passé et contient les locales ['fr', 'en']
    // Le premier élément de $availableLocales est utilisé pour l'affichage par défaut du slug
    $defaultLocaleForSlugInfo = $availableLocales[0] ?? config('app.locale');
@endphp

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs" role="tablist">
        @foreach ($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:border-primary-500 dark:text-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-{{ $locale }}" data-tabs-target="#content-{{ $locale }}" type="button" role="tab"
                        aria-controls="content-{{ $locale }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<div id="languageTabContent">
    @foreach ($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
             id="content-{{ $locale }}" role="tabpanel" aria-labelledby="tab-{{ $locale }}">

            <div class="mb-6">
                <label for="title_{{ $locale }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Titre ({{ strtoupper($locale) }}) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                       value="{{ old('title_' . $locale, $publication ? $publication->getTranslation('title', $locale, false) : '') }}"
                       @if($loop->first) required @endif>
                @error('title_' . $locale)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="abstract_{{ $locale }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Résumé ({{ strtoupper($locale) }}) <span class="text-red-500">*</span>
                </label>
                <textarea name="abstract_{{ $locale }}" id="abstract_{{ $locale }}" rows="8"
                          class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white tinymce-editor"
                          >{{ old('abstract_' . $locale, $publication ? $publication->getTranslation('abstract', $locale, false) : '') }}</textarea>
                @error('abstract_' . $locale)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endforeach
</div>

<hr class="my-8">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="mb-6">
        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Slug (URL)
        </label>
        <input type="text" name="slug" id="slug"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('slug', $publication->slug ?? '') }}">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sera généré à partir du titre ({{ strtoupper($defaultLocaleForSlugInfo) }}) si laissé vide et différent. Doit être unique.</p>
        @error('slug')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="publication_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Date de Publication <span class="text-red-500">*</span>
        </label>
        <input type="date" name="publication_date" id="publication_date"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('publication_date', $publication && $publication->publication_date ? $publication->publication_date->format('Y-m-d') : '') }}" required>
        @error('publication_date')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Type de Publication <span class="text-red-500">*</span>
    </label>
    <select name="type" id="type" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        <option value="">Sélectionner un type</option>
        @foreach($publicationTypes as $key => $value)
            <option value="{{ $key }}" {{ old('type', $publication->type ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('type')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="mb-6">
        <label for="journal_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom du Journal</label>
        <input type="text" name="journal_name" id="journal_name"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('journal_name', $publication->journal_name ?? '') }}">
        @error('journal_name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="conference_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom de la Conférence</label>
        <input type="text" name="conference_name" id="conference_name"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('conference_name', $publication->conference_name ?? '') }}">
        @error('conference_name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="mb-6">
        <label for="volume" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Volume</label>
        <input type="text" name="volume" id="volume"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('volume', $publication->volume ?? '') }}">
        @error('volume')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="issue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numéro/Issue</label>
        <input type="text" name="issue" id="issue"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('issue', $publication->issue ?? '') }}">
        @error('issue')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="pages" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pages</label>
        <input type="text" name="pages" id="pages"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('pages', $publication->pages ?? '') }}">
        @error('pages')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="mb-6">
        <label for="doi_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL du DOI</label>
        <input type="url" name="doi_url" id="doi_url" placeholder="https://doi.org/..."
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('doi_url', $publication->doi_url ?? '') }}">
        @error('doi_url')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="external_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL Externe</label>
        <input type="url" name="external_url" id="external_url" placeholder="https://..."
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
               value="{{ old('external_url', $publication->external_url ?? '') }}">
        @error('external_url')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label for="researchers" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Auteurs Internes (Chercheurs)</label>
    <select name="researchers[]" id="researchers" multiple
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white tom-select-multiple">
        @php
            $publicationResearchersIds = $publication ? $publication->researchers->pluck('id')->toArray() : [];
        @endphp
        @foreach($researchers as $researcher)
            <option value="{{ $researcher->id }}" {{ in_array($researcher->id, old('researchers', $publicationResearchersIds)) ? 'selected' : '' }}>
                {{ $researcher->full_name }}
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sélectionnez un ou plusieurs chercheurs.</p>
    @error('researchers')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
    @error('researchers.*')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label for="authors_external" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Auteurs Externes (liste libre)</label>
    <textarea name="authors_external" id="authors_external" rows="3"
              class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
              placeholder="Ex: Doe J., Smith A.">{{ old('authors_external', $publication->authors_external ?? '') }}</textarea>
    @error('authors_external')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label for="authors_internal_notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes Internes sur les Auteurs</label>
    <textarea name="authors_internal_notes" id="authors_internal_notes" rows="3"
              class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
              placeholder="Ordre spécifique des auteurs, affiliations non standards, etc.">{{ old('authors_internal_notes', $publication->authors_internal_notes ?? '') }}</textarea>
    @error('authors_internal_notes')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>


<div class="mb-6">
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="publication_pdf">Fichier PDF de la Publication</label>
    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
           id="publication_pdf" name="publication_pdf" type="file" accept="application/pdf">
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">PDF uniquement. Max 10MB. Laisser vide pour conserver le fichier actuel.</p>
    @error('publication_pdf')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror

    @if ($publication && $publication->pdf_media) {{-- Utilisation de l'accesseur que nous avons défini dans le modèle --}}
        <div class="mt-4" id="pdf_preview_wrapper">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Fichier actuel :
                <a href="{{ $publication->pdf_url }}" target="_blank" class="text-primary-600 hover:underline">
                    {{ $publication->pdf_media->name }} ({{ $publication->pdf_media->human_readable_size }})
                </a>
            </p>
            <div class="mt-2">
                <label for="remove_publication_pdf" class="inline-flex items-center">
                    <input type="checkbox" name="remove_publication_pdf" id="remove_publication_pdf" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Supprimer le PDF actuel</span>
                </label>
            </div>
        </div>
    @endif
</div>


<div class="mb-6">
    <label for="created_by_user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Publié par (Membre de l'équipe) <span class="text-red-500">*</span>
    </label>
    <select name="created_by_user_id" id="created_by_user_id"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
            required>
        <option value="">Sélectionner un utilisateur</option>
        @foreach($users as $id => $name)
            <option value="{{ $id }}" {{ (old('created_by_user_id', $publication->created_by_user_id ?? Auth::id())) == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('created_by_user_id')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="is_published" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Statut</label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1" class="sr-only peer"
                   @if(old('is_published', $publication->is_published ?? false)) checked @endif>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publié</span>
        </label>
        @error('is_published')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="is_featured" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mise en avant</label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" id="is_featured" value="1" class="sr-only peer"
                   @if(old('is_featured', $publication->is_featured ?? false)) checked @endif>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">En vedette sur la page d'accueil</span>
        </label>
        @error('is_featured')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>


<div class="flex items-center justify-end mt-8">
    <a href="{{ route('admin.publications.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:underline mr-6">
        Annuler
    </a>
    <button type="submit"
            class="px-6 py-2.5 bg-primary-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
        {{ $publication ? 'Mettre à jour la Publication' : 'Enregistrer la Publication' }}
    </button>
</div>

@push('scripts')
<script>
    // Initialisation des onglets de langue (si vous utilisez Flowbite ou un JS similaire)
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = [];
        document.querySelectorAll('[data-tabs-target]').forEach(button => {
            const targetId = button.getAttribute('data-tabs-target');
            const targetPanel = document.querySelector(targetId);
            if (targetPanel) {
                tabs.push({
                    trigger: button,
                    target: targetPanel,
                    isActive: button.getAttribute('aria-selected') === 'true'
                });
            }
        });

        tabs.forEach(tab => {
            tab.trigger.addEventListener('click', () => {
                tabs.forEach(t => {
                    t.trigger.classList.remove('border-primary-500', 'text-primary-600', 'dark:border-primary-500', 'dark:text-primary-500');
                    t.trigger.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                    t.target.classList.add('hidden');
                    t.trigger.setAttribute('aria-selected', 'false');
                });
                tab.trigger.classList.add('border-primary-500', 'text-primary-600', 'dark:border-primary-500', 'dark:text-primary-500');
                tab.trigger.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                tab.target.classList.remove('hidden');
                tab.trigger.setAttribute('aria-selected', 'true');
            });
        });

        // Initialisation de TomSelect pour le champ des chercheurs (si vous l'utilisez)
        // if (document.getElementById('researchers')) {
        //     new TomSelect('#researchers',{
        //         plugins: ['remove_button'],
        //         create: false,
        //         // ... autres options de TomSelect si besoin
        //     });
        // }
    });
</script>
@endpush
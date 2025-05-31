@props([
    'publication' => null, // Sera null pour la création, et l'objet Publication pour l'édition
    'availableLocales',
    'users',
])

@php
    $defaultLocale = $availableLocales[0] ?? 'fr';
@endphp

<!-- Onglets de langue -->
<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabs" role="tablist">
        @foreach ($availableLocales as $locale)
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                        id="tab-{{ $locale }}" data-tabs-target="#content-{{ $locale }}" type="button" role="tab"
                        aria-controls="content-{{ $locale }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>

<!-- Contenu des onglets -->
<div id="languageTabContent">
    @foreach ($availableLocales as $locale)
        <div class="{{ $loop->first ? '' : 'hidden' }} p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
             id="content-{{ $locale }}" role="tabpanel" aria-labelledby="tab-{{ $locale }}">
            
            <!-- Titre -->
            <div class="mb-4">
                <label for="title_{{ $locale }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Titre ({{ strtoupper($locale) }}) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                       value="{{ old('title_' . $locale, $publication ? $publication->getTranslation('title', $locale, false) : '') }}"
                       required>
                @error('title_' . $locale)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Résumé (Abstract) -->
            <div class="mb-4">
                <label for="abstract_{{ $locale }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Résumé ({{ strtoupper($locale) }}) <span class="text-red-500">*</span>
                </label>
                <textarea name="abstract_{{ $locale }}" id="abstract_{{ $locale }}" rows="6"
                          class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white tinymce-editor"
                          >{{ old('abstract_' . $locale, $publication ? $publication->getTranslation('abstract', $locale, false) : '') }}</textarea>
                @error('abstract_' . $locale)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endforeach
</div>

<hr class="my-6">

<!-- Slug -->
<div class="mb-4">
    <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Slug (URL) <span class="text-red-500">*</span>
    </label>
    <input type="text" name="slug" id="slug"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('slug', $publication->slug ?? '') }}">
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sera généré à partir du titre ({{ strtoupper($defaultLocale) }}) si laissé vide. Doit être unique.</p>
    @error('slug')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- Date de Publication -->
<div class="mb-4">
    <label for="publication_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Date de Publication <span class="text-red-500">*</span>
    </label>
    <input type="date" name="publication_date" id="publication_date"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('publication_date', $publication && $publication->publication_date ? $publication->publication_date->format('Y-m-d') : '') }}" required>
    @error('publication_date')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- Type (ex: Article, Conference Paper, Book Chapter) -->
<div class="mb-4">
    <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
    <input type="text" name="type" id="type"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('type', $publication->type ?? '') }}">
    @error('type')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- Nom du Journal/Conférence -->
<div class="mb-4">
    <label for="journal_conference_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom du Journal / Conférence</label>
    <input type="text" name="journal_conference_name" id="journal_conference_name"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('journal_conference_name', $publication->journal_conference_name ?? '') }}">
    @error('journal_conference_name')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- DOI URL -->
<div class="mb-4">
    <label for="doi_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL du DOI</label>
    <input type="url" name="doi_url" id="doi_url" placeholder="https://doi.org/..."
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('doi_url', $publication->doi_url ?? '') }}">
    @error('doi_url')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- URL Externe (ex: lien vers l'éditeur) -->
<div class="mb-4">
    <label for="external_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL Externe</label>
    <input type="url" name="external_url" id="external_url" placeholder="https://..."
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
           value="{{ old('external_url', $publication->external_url ?? '') }}">
    @error('external_url')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- Auteurs Externes (si vous ne les gérez pas via une relation ManyToMany) -->
<div class="mb-4">
    <label for="authors_external" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Auteurs (externes ou liste manuelle)</label>
    <textarea name="authors_external" id="authors_external" rows="3"
              class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
              placeholder="Ex: Doe J., Smith A.">{{ old('authors_external', $publication->authors_external ?? '') }}</textarea>
    @error('authors_external')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<!-- Fichier PDF -->
<div class="mb-6">
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="publication_pdf">Fichier PDF</label>
    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
           id="publication_pdf" name="publication_pdf" type="file" accept=".pdf">
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">PDF, max 10MB.</p>
    @error('publication_pdf')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror

    @if ($publication && $publication->getFirstMediaUrl('publication_pdf'))
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Fichier actuel :
                <a href="{{ $publication->getFirstMediaUrl('publication_pdf') }}" target="_blank" class="text-blue-600 hover:underline">
                    {{ $publication->getFirstMedia('publication_pdf')->name }} ({{ $publication->getFirstMedia('publication_pdf')->human_readable_size }})
                </a>
            </p>
            <div class="mt-2">
                <label for="remove_publication_pdf" class="inline-flex items-center">
                    <input type="checkbox" name="remove_publication_pdf" id="remove_publication_pdf" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Supprimer le PDF actuel</span>
                </label>
            </div>
        </div>
    @endif
</div>


<!-- Auteur (Utilisateur créateur) -->
<div class="mb-4">
    <label for="created_by_user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Auteur (Membre de l'équipe) <span class="text-red-500">*</span>
    </label>
    <select name="created_by_user_id" id="created_by_user_id"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
            required>
        <option value="">Sélectionner un auteur</option>
        @foreach($users as $id => $name)
            <option value="{{ $id }}" {{ old('created_by_user_id', $publication->created_by_user_id ?? auth()->id()) == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('created_by_user_id')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>


<!-- Statuts -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="is_published" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Statut</label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_published" value="0"> <!-- Valeur par défaut si la case n'est pas cochée -->
            <input type="checkbox" name="is_published" id="is_published" value="1" class="sr-only peer"
                   @if(old('is_published', $publication->is_published ?? false)) checked @endif>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publié</span>
        </label>
    </div>
    <div>
        <label for="is_featured" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mise en avant</label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" id="is_featured" value="1" class="sr-only peer"
                   @if(old('is_featured', $publication->is_featured ?? false)) checked @endif>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">En vedette</span>
        </label>
    </div>
</div>



{{-- <div class="mb-4">
    <label for="researchers" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Auteurs Internes (Chercheurs)</label>
    <select name="researchers[]" id="researchers" multiple
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        @php
            $publicationResearchers = $publication ? $publication->researchers->pluck('id')->toArray() : [];
        @endphp
        @foreach(\App\Models\Researcher::orderBy('last_name')->get() as $researcher)
            <option value="{{ $researcher->id }}" {{ in_array($researcher->id, old('researchers', $publicationResearchers)) ? 'selected' : '' }}>
                {{ $researcher->full_name }} {{-- Supposant une méthode fullName dans Researcher Model --}}
            {{-- </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs auteurs.</p>
</div> --}} 


<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.publications.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-4">
        Annuler
    </a>
    <button type="submit"
            class="px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">
        {{ $publication ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
</div>


@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Modifier la Publication :') }} <span class="italic">{{ Str::limit($publication->title, 50) }}</span>
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md">
                    <strong class="font-bold">{{ __('Oups ! Il y a eu quelques problèmes avec votre saisie.') }}</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.publications.update', $publication) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Méthode HTTP pour la mise à jour --}}

                {{-- Titre --}}
                <div class="mb-4">
                    <label for="title" class="block font-medium text-sm text-gray-700">{{ __('Titre de la publication') }} <span class="text-red-500">*</span></label>
                    <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title', $publication->title) }}" required autofocus />
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-4">
                    <label for="slug" class="block font-medium text-sm text-gray-700">{{ __('Slug (pour l\'URL)') }} <span class="text-red-500">*</span></label>
                    <input id="slug" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="slug" value="{{ old('slug', $publication->slug) }}" required />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Ex: "ma-super-publication-2025". Uniquement minuscules, chiffres, tirets.') }}</p>
                    @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Auteurs Internes (Chercheurs du centre) --}}
                <div class="mb-4">
                    <label for="researcher_ids" class="block font-medium text-sm text-gray-700">{{ __('Auteurs Internes (Chercheurs du CRPQA)') }}</label>
                    <select name="researcher_ids[]" id="researcher_ids" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" size="5">
                        @php
                            // Récupérer les IDs des chercheurs déjà associés à cette publication
                            $selectedResearcherIds = old('researcher_ids', $publication->researchers->pluck('id')->toArray());
                        @endphp
                        @foreach($researchers as $researcher)
                            <option value="{{ $researcher->id }}" {{ (collect($selectedResearcherIds)->contains($researcher->id)) ? 'selected' : '' }}>
                                {{ $researcher->first_name }} {{ $researcher->last_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs auteurs.') }}</p>
                    @error('researcher_ids') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    @error('researcher_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Auteurs Externes --}}
                <div class="mb-4">
                    <label for="authors_external" class="block font-medium text-sm text-gray-700">{{ __('Auteurs Externes (si applicable, un par ligne)') }}</label>
                    <textarea id="authors_external" name="authors_external" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('authors_external', $publication->authors_external) }}</textarea>
                    @error('authors_external') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Résumé (Abstract) --}}
                <div class="mb-4">
                    <label for="abstract" class="block font-medium text-sm text-gray-700">{{ __('Résumé (Abstract)') }} <span class="text-red-500">*</span></label>
                    <textarea id="abstract" name="abstract" rows="8" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('abstract', $publication->abstract) }}</textarea>
                    @error('abstract') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    {{-- Date de Publication --}}
                    <div>
                        <label for="publication_date" class="block font-medium text-sm text-gray-700">{{ __('Date de Publication') }} <span class="text-red-500">*</span></label>
                        <input id="publication_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="date" name="publication_date" value="{{ old('publication_date', $publication->publication_date instanceof \Carbon\Carbon ? $publication->publication_date->format('Y-m-d') : $publication->publication_date) }}" required />
                        @error('publication_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type de Publication --}}
                    <div>
                        <label for="type" class="block font-medium text-sm text-gray-700">{{ __('Type de Publication') }} <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                            <option value="">-- Sélectionnez un type --</option>
                            @foreach($publicationTypes as $key => $value)
                                <option value="{{ $key }}" {{ old('type', $publication->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                 {{-- Champs spécifiques au type (Journal, Conférence etc.) --}}
                <div class="p-4 border border-dashed border-gray-300 rounded-md mb-4 bg-slate-50">
                    <p class="text-sm font-medium text-gray-700 mb-2">Informations Additionnelles :</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label for="journal_name" class="block font-medium text-xs text-gray-600">{{ __('Nom de la Revue / Journal') }}</label>
                            <input id="journal_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" type="text" name="journal_name" value="{{ old('journal_name', $publication->journal_name) }}" />
                            @error('journal_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="conference_name" class="block font-medium text-xs text-gray-600">{{ __('Nom de la Conférence') }}</label>
                            <input id="conference_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" type="text" name="conference_name" value="{{ old('conference_name', $publication->conference_name) }}" />
                            @error('conference_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="volume" class="block font-medium text-xs text-gray-600">{{ __('Volume') }}</label>
                            <input id="volume" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" type="text" name="volume" value="{{ old('volume', $publication->volume) }}" />
                            @error('volume') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="issue" class="block font-medium text-xs text-gray-600">{{ __('Numéro / Issue') }}</label>
                            <input id="issue" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" type="text" name="issue" value="{{ old('issue', $publication->issue) }}" />
                            @error('issue') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="pages" class="block font-medium text-xs text-gray-600">{{ __('Pages (ex: 10-15)') }}</label>
                            <input id="pages" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" type="text" name="pages" value="{{ old('pages', $publication->pages) }}" />
                            @error('pages') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    {{-- Lien DOI --}}
                    <div>
                        <label for="doi_url" class="block font-medium text-sm text-gray-700">{{ __('Lien DOI (URL)') }}</label>
                        <input id="doi_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="url" name="doi_url" value="{{ old('doi_url', $publication->doi_url) }}" placeholder="https://doi.org/..." />
                        @error('doi_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lien Externe --}}
                    <div>
                        <label for="external_url" class="block font-medium text-sm text-gray-700">{{ __('Lien Externe (URL)') }}</label>
                        <input id="external_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="url" name="external_url" value="{{ old('external_url', $publication->external_url) }}" placeholder="https://..." />
                        @error('external_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Gestion du Fichier PDF --}}
                <div class="mb-4">
                    <label for="pdf" class="block font-medium text-sm text-gray-700">{{ __('Changer le fichier PDF') }}</label>
                    @if($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path))
                        <div class="mt-2 mb-2">
                            <p class="text-sm text-gray-600">PDF actuel : 
                                <a href="{{ Storage::url($publication->pdf_path) }}" target="_blank" class="text-indigo-600 hover:underline">{{ Str::afterLast($publication->pdf_path, '/') }}</a>
                            </p>
                            <label for="remove_pdf" class="inline-flex items-center mt-1 text-xs">
                                <input type="checkbox" id="remove_pdf" name="remove_pdf" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                <span class="ms-2 text-gray-700">{{ __('Supprimer le PDF actuel') }}</span>
                            </label>
                        </div>
                    @endif
                    <input id="pdf" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="pdf" />
                    <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver le PDF actuel (sauf si "Supprimer" est coché). PDF uniquement, max 10Mo.</p>
                    @error('pdf') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- En Vedette ? --}}
                <div class="mb-6">
                    <label for="is_featured" class="inline-flex items-center">
                        <input id="is_featured" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_featured" value="1" {{ old('is_featured', $publication->is_featured) ? 'checked' : '' }}>
                        <span class="ms-2 text-sm text-gray-700">{{ __('Mettre cette publication en vedette ?') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.publications.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2 bg-sky-600 text-white font-semibold rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Mettre à Jour la Publication') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
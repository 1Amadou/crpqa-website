@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Ajouter une Nouvelle Actualité') }}
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

            <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Titre --}}
                <div class="mb-4">
                    <label for="news_title" class="block font-medium text-sm text-gray-700">{{ __('Titre de l\'actualité') }} <span class="text-red-500">*</span></label>
                    <input id="news_title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="title" value="{{ old('title') }}" required autofocus />
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-4">
                    <label for="news_slug" class="block font-medium text-sm text-gray-700">{{ __('Slug (pour l\'URL)') }} <span class="text-red-500">*</span></label>
                    <input id="news_slug" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="slug" value="{{ old('slug') }}" required />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Sera auto-généré si laissé vide. Uniquement minuscules, chiffres, tirets.') }}</p>
                    @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Méta Titre (SEO) --}}
                <div class="mb-4">
                    <label for="meta_title" class="block font-medium text-sm text-gray-700">{{ __('Méta Titre (pour le SEO)') }}</label>
                    <input id="meta_title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="meta_title" value="{{ old('meta_title') }}" />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Optimal : 50-70 caractères. Utilisé dans les résultats des moteurs de recherche.') }}</p>
                    @error('meta_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Méta Description (SEO) --}}
                <div class="mb-4">
                    <label for="meta_description" class="block font-medium text-sm text-gray-700">{{ __('Méta Description (pour le SEO)') }}</label>
                    <textarea id="meta_description" name="meta_description" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('meta_description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Optimal : 120-160 caractères. Un résumé pour les moteurs de recherche.') }}</p>
                    @error('meta_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Résumé court (Summary) --}}
                <div class="mb-4">
                    <label for="summary" class="block font-medium text-sm text-gray-700">{{ __('Résumé court (Optionnel)') }}</label>
                    <textarea id="summary" name="summary" rows="3" class="wysiwyg-editor block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('summary') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Un bref aperçu de l\'actualité, affiché dans les listes.') }}</p>
                    @error('summary') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Contenu principal --}}
                <div class="mb-4">
                    <label for="content" class="block font-medium text-sm text-gray-700">{{ __('Contenu principal de l\'actualité') }} <span class="text-red-500">*</span></label>
                    <textarea id="content" name="content" rows="15" class="wysiwyg-editor block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('content') }}</textarea>
                    @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Image de couverture --}}
                <div class="mb-4">
                    <label for="news_cover_image" class="block font-medium text-sm text-gray-700">{{ __('Image de couverture (Optionnel)') }}</label>
                    <input id="news_cover_image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="cover_image" />
                    <img id="news_image_preview" src="#" alt="Aperçu de l'image" class="mt-2 max-h-40 w-auto rounded shadow-sm" style="display: none;"/>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF, SVG, WEBP jusqu'à 2Mo.</p>
                    @error('cover_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Date et Heure de Publication --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="published_at_date" class="block font-medium text-sm text-gray-700">{{ __('Date de publication (Optionnel)') }}</label>
                        <input id="published_at_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="date" name="published_at_date" value="{{ old('published_at_date') }}" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Laissez vide pour enregistrer comme brouillon.') }}</p>
                        @error('published_at_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="published_at_time" class="block font-medium text-sm text-gray-700">{{ __('Heure de publication (Optionnel)') }}</label>
                        <input id="published_at_time" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="time" name="published_at_time" value="{{ old('published_at_time') }}" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Nécessaire si une date de publication est fournie.') }}</p>
                        @error('published_at_time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- En Vedette ? --}}
                <div class="mb-6">
                    <label for="is_featured" class="inline-flex items-center">
                        <input id="is_featured" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="ms-2 text-sm text-gray-700">{{ __('Mettre cette actualité en vedette ?') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.news.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Enregistrer l\'Actualité') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
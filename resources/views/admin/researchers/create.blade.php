@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Ajouter un Nouveau Profil de Chercheur') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            {{-- Affichage des erreurs de validation globales --}}
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

            <form method="POST" action="{{ route('admin.researchers.store') }}" enctype="multipart/form-data"> {{-- N'oubliez pas enctype pour l'upload de fichiers --}}
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Prénom --}}
                    <div>
                        <label for="first_name" class="block font-medium text-sm text-gray-700">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                        <input id="first_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus />
                        @error('first_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nom --}}
                    <div>
                        <label for="last_name" class="block font-medium text-sm text-gray-700">{{ __('Nom de famille') }} <span class="text-red-500">*</span></label>
                        <input id="last_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="last_name" value="{{ old('last_name') }}" required />
                        @error('last_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Titre (Dr., Prof.) --}}
                    <div>
                        <label for="title" class="block font-medium text-sm text-gray-700">{{ __('Titre (ex: Dr., Prof.)') }}</label>
                        <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="title" value="{{ old('title') }}" />
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position/Fonction --}}
                    <div>
                        <label for="position" class="block font-medium text-sm text-gray-700">{{ __('Position / Fonction') }}</label>
                        <input id="position" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="position" value="{{ old('position') }}" />
                        @error('position') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label for="email" class="block font-medium text-sm text-gray-700">{{ __('Adresse Email') }}</label>
                        <input id="email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="email" value="{{ old('email') }}" />
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Numéro de Téléphone --}}
                    <div>
                        <label for="phone_number" class="block font-medium text-sm text-gray-700">{{ __('Numéro de Téléphone') }}</label>
                        <input id="phone_number" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="phone_number" value="{{ old('phone_number') }}" />
                        @error('phone_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label for="photo" class="block font-medium text-sm text-gray-700">{{ __('Photo de profil') }}</label>
                        <input id="photo" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="photo" />
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF, SVG, WEBP jusqu'à 2Mo.</p>
                        @error('photo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Biographie --}}
                <div class="mt-6">
                    <label for="biography" class="block font-medium text-sm text-gray-700">{{ __('Biographie / Présentation') }}</label>
                    <textarea id="biography" name="biography" rows="8" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('biography') }}</textarea>
                    @error('biography') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Domaines de Recherche --}}
                <div class="mt-6">
                    <label for="research_areas" class="block font-medium text-sm text-gray-700">{{ __('Domaines de Recherche (séparés par des virgules ou un par ligne)') }}</label>
                    <textarea id="research_areas" name="research_areas" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('research_areas') }}</textarea>
                    @error('research_areas') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Lien LinkedIn --}}
                    <div>
                        <label for="linkedin_url" class="block font-medium text-sm text-gray-700">{{ __('URL Profil LinkedIn') }}</label>
                        <input id="linkedin_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/..." />
                        @error('linkedin_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lien Google Scholar --}}
                    <div>
                        <label for="google_scholar_url" class="block font-medium text-sm text-gray-700">{{ __('URL Profil Google Scholar') }}</label>
                        <input id="google_scholar_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="url" name="google_scholar_url" value="{{ old('google_scholar_url') }}" placeholder="https://scholar.google.com/citations?user=..." />
                        @error('google_scholar_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ordre d'affichage --}}
                    <div>
                        <label for="display_order" class="block font-medium text-sm text-gray-700">{{ __('Ordre d\'affichage (0 pour premier)') }}</label>
                        <input id="display_order" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0" />
                        @error('display_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actif ? --}}
                    <div class="pt-2">
                        <label for="is_active" class="inline-flex items-center mt-5">
                            <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-700">{{ __('Profil actif et visible sur le site ?') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.researchers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Enregistrer le Profil') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Modifier le Profil de :') }} <span class="italic">{{ $researcher->first_name }} {{ $researcher->last_name }}</span>
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
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

            <form method="POST" action="{{ route('admin.researchers.update', $researcher) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Prénom --}}
                    <div>
                        <label for="first_name" class="block font-medium text-sm text-gray-700">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                        <input id="first_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="first_name" value="{{ old('first_name', $researcher->first_name) }}" required autofocus />
                        @error('first_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nom --}}
                    <div>
                        <label for="last_name" class="block font-medium text-sm text-gray-700">{{ __('Nom de famille') }} <span class="text-red-500">*</span></label>
                        <input id="last_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="last_name" value="{{ old('last_name', $researcher->last_name) }}" required />
                        @error('last_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Titre --}}
                    <div>
                        <label for="title" class="block font-medium text-sm text-gray-700">{{ __('Titre') }}</label>
                        <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title', $researcher->title) }}" />
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position --}}
                    <div>
                        <label for="position" class="block font-medium text-sm text-gray-700">{{ __('Position / Fonction') }}</label>
                        <input id="position" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="position" value="{{ old('position', $researcher->position) }}" />
                        @error('position') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label for="email" class="block font-medium text-sm text-gray-700">{{ __('Adresse Email') }}</label>
                        <input id="email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="email" name="email" value="{{ old('email', $researcher->email) }}" />
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="phone_number" class="block font-medium text-sm text-gray-700">{{ __('Numéro de Téléphone') }}</label>
                        <input id="phone_number" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="phone_number" value="{{ old('phone_number', $researcher->phone_number) }}" />
                        @error('phone_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Gestion de la Photo --}}
                    <div>
                        <label for="researcher_photo" class="block font-medium text-sm text-gray-700">{{ __('Changer la photo de profil') }}</label>
                        @if($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path))
                            <div class="mt-2 mb-2">
                                <img src="{{ Storage::url($researcher->photo_path) }}?t={{ time() }}" alt="Photo actuelle" class="h-24 w-24 rounded-md object-cover shadow-sm">
                                <label for="remove_photo" class="inline-flex items-center mt-2 text-xs">
                                    <input type="checkbox" id="remove_photo" name="remove_photo" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                    <span class="ms-2 text-gray-700">{{ __('Supprimer la photo actuelle') }}</span>
                                </label>
                            </div>
                        @endif
                        <input id="researcher_photo" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="photo" />
                        <img id="researcher_photo_preview" src="#" alt="Aperçu de la nouvelle photo" class="mt-2 max-h-40 w-auto rounded shadow-sm" style="display: none;"/>
                        <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver la photo actuelle. PNG, JPG, GIF, SVG, WEBP jusqu'à 2Mo.</p>
                        @error('photo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Biographie --}}
                <div class="mt-6">
                    <label for="biography" class="block font-medium text-sm text-gray-700">{{ __('Biographie / Présentation') }}</label>
                    <textarea id="biography" name="biography" rows="8" class="wysiwyg-editor block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('biography', $researcher->biography) }}</textarea>
                    @error('biography') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Domaines de Recherche --}}
                <div class="mt-6">
                    <label for="research_areas" class="block font-medium text-sm text-gray-700">{{ __('Domaines de Recherche') }}</label>
                    <textarea id="research_areas" name="research_areas" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('research_areas', $researcher->research_areas) }}</textarea>
                    @error('research_areas') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- LinkedIn --}}
                    <div>
                        <label for="linkedin_url" class="block font-medium text-sm text-gray-700">{{ __('URL Profil LinkedIn') }}</label>
                        <input id="linkedin_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="url" name="linkedin_url" value="{{ old('linkedin_url', $researcher->linkedin_url) }}" placeholder="https://linkedin.com/in/..." />
                        @error('linkedin_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Google Scholar --}}
                    <div>
                        <label for="google_scholar_url" class="block font-medium text-sm text-gray-700">{{ __('URL Profil Google Scholar') }}</label>
                        <input id="google_scholar_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="url" name="google_scholar_url" value="{{ old('google_scholar_url', $researcher->google_scholar_url) }}" placeholder="https://scholar.google.com/citations?user=..." />
                        @error('google_scholar_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ordre d'affichage --}}
                    <div>
                        <label for="display_order" class="block font-medium text-sm text-gray-700">{{ __('Ordre d\'affichage') }}</label>
                        <input id="display_order" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="number" name="display_order" value="{{ old('display_order', $researcher->display_order) }}" min="0" />
                        @error('display_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actif ? --}}
                    <div class="pt-2">
                        <label for="is_active" class="inline-flex items-center mt-5">
                            <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', $researcher->is_active) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-700">{{ __('Profil actif et visible ?') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.researchers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2 bg-sky-600 text-white font-semibold rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Mettre à Jour le Profil') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
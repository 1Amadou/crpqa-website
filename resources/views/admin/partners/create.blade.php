@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un nouveau partenaire') }}
        </h2>
        <a href="{{ route('admin.partners.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Annuler et retourner à la liste') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <form method="POST" action="{{ route('admin.partners.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Colonne Principale (2/3) --}}
                    <div class="md:col-span-2 space-y-6">
                        {{-- Nom du Partenaire --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom du Partenaire') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Site Web --}}
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700">{{ __('Site Web (Optionnel)') }}</label>
                            <input type="url" name="website_url" id="website_url" value="{{ old('website_url') }}" placeholder="https://www.exemple.com" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('website_url') border-red-500 @enderror">
                            @error('website_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type de Partenaire --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type de Partenaire (Optionnel)') }}</label>
                            <input type="text" name="type" id="type" value="{{ old('type') }}" placeholder="Ex: Université, Entreprise, Institution" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('type') border-red-500 @enderror">
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description (Optionnel)') }}</label>
                            <textarea name="description" id="description" rows="6" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Détails sur la collaboration, missions, objectifs, etc. Nous ajouterons un éditeur de texte riche ici plus tard si besoin.') }}</p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Colonne Latérale (1/3) --}}
                    <div class="md:col-span-1 space-y-6">
                        {{-- Logo --}}
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700">{{ __('Logo du Partenaire (Optionnel)') }}</label>
                            <input type="file" name="logo" id="logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('logo') border-red-500 @enderror" onchange="previewPartnerLogo(event)">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max 2MB. Formats : jpg, png, gif, svg, webp.') }}</p>
                            <img id="logo_preview" src="#" alt="Prévisualisation du logo" class="mt-2 max-h-40 w-auto rounded border p-1 shadow-sm" style="display: none;"/>
                            @error('logo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ordre d'affichage --}}
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700">{{ __('Ordre d\'affichage (Optionnel)') }}</label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}" min="0" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('display_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Un nombre plus petit s\'affiche en premier.') }}</p>
                            @error('display_order')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Statut Actif --}}
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">{{ __('Actif') }}</label>
                                <p class="text-gray-500">{{ __('Décocher pour masquer ce partenaire du site public.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('admin.partners.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Enregistrer le partenaire') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{--
        Note pour l'intégration JavaScript :
        La fonction 'previewPartnerLogo(event)' pour la prévisualisation du logo
        devrait être définie dans un fichier JS global (ex: resources/js/admin/image-preview.js)
        et importée via Vite.

        Exemple de fonction (à adapter et à placer dans le fichier JS approprié) :
        function previewPartnerLogo(event) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('logo_preview'); // Assurez-vous que l'ID est unique si cette fonction est réutilisée
            if (imagePreview) { // Vérifier si l'élément existe
                reader.onload = function(){
                    if (reader.readyState === 2) {
                        imagePreview.src = reader.result;
                        imagePreview.style.display = 'block';
                    }
                }
                if(event.target.files[0]){
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    imagePreview.src = '#';
                    imagePreview.style.display = 'none';
                }
            }
        }
        // Assurez-vous que l'écouteur d'événement 'onchange' sur l'input file appelle cette fonction
        // ou attachez l'écouteur d'événement de manière non intrusive dans votre JS global.
    --}}
@endsection
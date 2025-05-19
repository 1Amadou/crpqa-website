@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Domaine de Recherche :') }} <span class="text-blue-600">{{ Str::limit($researchAxis->name, 40) }}</span>
        </h2>
        <a href="{{ route('admin.research-axes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            {{ __('Annuler et retourner à la liste') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <form method="POST" action="{{ route('admin.research-axes.update', $researchAxis) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                    {{-- Colonne Principale (2/3) --}}
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom du Domaine') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="research_axis_name" value="{{ old('name', $researchAxis->name) }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">{{ __('Slug (URL)') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" id="research_axis_slug" value="{{ old('slug', $researchAxis->slug) }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Modifiez avec prudence. Uniquement minuscules, chiffres, tirets.') }}</p>
                            @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">{{ __('Sous-titre / Accroche (Optionnel)') }}</label>
                            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $researchAxis->subtitle) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('subtitle') border-red-500 @enderror">
                            @error('subtitle') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description détaillée') }} <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="10" class="wysiwyg-editor mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('description') border-red-500 @enderror">{{ old('description', $researchAxis->description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">{{_('L\'éditeur de texte riche sera appliqué ici.')}}</p>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Colonne Latérale (1/3) --}}
                    <div class="md:col-span-1 space-y-6">
                        <div>
                            <label for="icon_class" class="block text-sm font-medium text-gray-700">{{ __('Classe de l\'icône (Optionnel)') }}</label>
                            <input type="text" name="icon_class" id="icon_class" value="{{ old('icon_class', $researchAxis->icon_class) }}" placeholder="ex: planet-outline" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('icon_class') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Pour Ionicons. <a href="https://ionic.io/ionicons" target="_blank" class="text-blue-600 hover:underline">Voir les icônes</a>.</p>
                            @error('icon_class') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cover_image" class="block text-sm font-medium text-gray-700">{{ __('Image de Couverture (Optionnel)') }}</label>
                            @if($researchAxis->cover_image_path && Storage::disk('public')->exists($researchAxis->cover_image_path))
                                <div class="mt-2 mb-1">
                                    <img src="{{ Storage::url($researchAxis->cover_image_path) }}?t={{ time() }}" alt="Image actuelle" class="max-h-32 w-auto rounded border p-1 shadow-sm">
                                    <div class="mt-1">
                                        <input type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="remove_cover_image" class="ml-2 text-xs text-gray-600">{{ __('Supprimer l\'image actuelle') }}</label>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="cover_image" id="research_axis_cover_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('cover_image') border-red-500 @enderror">
                            <img id="research_axis_image_preview" src="#" alt="Aperçu de la nouvelle image" class="mt-2 max-h-40 w-auto rounded shadow-sm" style="display:none;"/>
                            <p class="mt-1 text-xs text-gray-500">Max 2MB. Formats : jpg, png, gif, svg, webp.</p>
                            @error('cover_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700">{{ __('Ordre d\'Affichage') }}</label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $researchAxis->display_order) }}" min="0" class="mt-1 block w-40 shadow-sm sm:text-sm border-gray-300 rounded-md @error('display_order') border-red-500 @enderror">
                            @error('display_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $researchAxis->is_active) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">{{ __('Actif (Visible sur le site public)') }}</label>
                            </div>
                        </div>
                        <hr>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Optimisation SEO (Optionnel)') }}</h3>
                         <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">{{ __('Méta Titre') }}</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $researchAxis->meta_title) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_title') border-red-500 @enderror">
                            @error('meta_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">{{ __('Méta Description') }}</label>
                            <textarea name="meta_description" id="meta_description" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $researchAxis->meta_description) }}</textarea>
                            @error('meta_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end md:col-span-3">
                    <a href="{{ route('admin.research-axes.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        {{ __('Mettre à Jour le Domaine') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Le script pour slug et preview sera appelé globalement via app-admin.js --}}
@endsection
@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter une Nouvelle Page Statique') }}
        </h2>
        <a href="{{ route('admin.static-pages.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            {{ __('Annuler et retourner à la liste') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <form method="POST" action="{{ route('admin.static-pages.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                    {{-- Colonne Principale (2/3) --}}
                    <div class="md:col-span-2 space-y-6">
                        {{-- Titre de la Page --}}
                        <div>
                            <label for="static_page_title" class="block text-sm font-medium text-gray-700">{{ __('Titre de la Page') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="static_page_title" value="{{ old('title') }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('title') border-red-500 @enderror">
                            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="static_page_slug" class="block text-sm font-medium text-gray-700">{{ __('Slug (URL)') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" id="static_page_slug" value="{{ old('slug') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sera auto-généré à partir du nom si laissé vide. Uniquement minuscules, chiffres, tirets.') }}</p>
                            @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Contenu de la Page --}}
                        <div>
                            <label for="content_static_page" class="block text-sm font-medium text-gray-700">{{ __('Contenu de la Page') }} <span class="text-red-500">*</span></label>
                            {{-- Ajout de la classe wysiwyg-editor ici --}}
                            <textarea name="content" id="content_static_page" rows="25" class="wysiwyg-editor mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Colonne Latérale (1/3) pour Publication et SEO --}}
                    <div class="md:col-span-1 space-y-6">
                        <div class="p-4 border rounded-md shadow-sm">
                            <h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Publication') }}</h3>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_published" class="font-medium text-gray-700">{{ __('Publier cette page') }}</label>
                                    <p class="text-gray-500">{{ __('Si décoché, la page sera enregistrée comme brouillon.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 border rounded-md shadow-sm">
                            <h3 class="text-md font-medium text-gray-900 mb-3">{{ __('Optimisation SEO (Optionnel)') }}</h3>
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700">{{ __('Méta Titre') }}</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_title') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">{{ __('Max 70 caractères. Si vide, le titre de la page sera utilisé.') }}</p>
                                @error('meta_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700">{{ __('Méta Description') }}</label>
                                <textarea name="meta_description" id="meta_description" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Max 160 caractères. Si vide, un extrait du contenu pourrait être utilisé.') }}</p>
                                @error('meta_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end md:col-span-3">
                    <a href="{{ route('admin.static-pages.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Enregistrer la Page') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Les scripts pour le slug (via IDs 'static_page_title', 'static_page_slug') 
         et pour TinyMCE (via classe 'wysiwyg-editor' sur 'content_static_page')
         sont appelés globalement depuis resources/js/admin/app-admin.js --}}
@endsection
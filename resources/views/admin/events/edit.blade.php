@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'événement :') }} <span class="text-blue-600">{{ Str::limit($event->title, 40) }}</span>
        </h2>
        <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
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
            <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                    {{-- Colonne Principale (2/3) --}}
                    <div class="md:col-span-2 space-y-6">
                        {{-- Titre --}}
                        <div>
                            <label for="event_title" class="block text-sm font-medium text-gray-700">{{ __('Titre de l\'événement') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="event_title" value="{{ old('title', $event->title) }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="event_slug" class="block text-sm font-medium text-gray-700">{{ __('Slug (URL)') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" id="event_slug" value="{{ old('slug', $event->slug) }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sera auto-généré à partir du titre si laissé vide ou si inchangé. Utilisez des tirets et des minuscules.') }}</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description complète') }} <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="10" class="wysiwyg-editor mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('description') border-red-500 @enderror">{{ old('description', $event->description) }}</textarea>
                            {{-- <p class="mt-1 text-xs text-gray-500">{{ __('Nous ajouterons un éditeur de texte riche ici plus tard.') }}</p> --}}
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="target_audience" class="block text-sm font-medium text-gray-700">{{ __('Publics Cibles (Optionnel)') }}</label>
                            <textarea name="target_audience" id="target_audience" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('target_audience') border-red-500 @enderror">{{ old('target_audience', $event->target_audience) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Décrivez le public principal visé par cet événement. Ex: Étudiants, Chercheurs en X, Professionnels de Y...') }}</p>
                            @error('target_audience')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dates et Heures --}}
                        <fieldset class="space-y-4">
                            <legend class="text-base font-medium text-gray-900 mb-1">Dates et Heures</legend>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                {{-- Date de début --}}
                                <div>
                                    <label for="start_datetime_date" class="block text-sm font-medium text-gray-700">{{ __('Date de début') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_datetime_date" id="start_datetime_date" value="{{ old('start_datetime_date', $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->format('Y-m-d') : '') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('start_datetime_date') border-red-500 @enderror">
                                    @error('start_datetime_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                 {{-- Heure de début --}}
                                <div>
                                    <label for="start_datetime_time" class="block text-sm font-medium text-gray-700">{{ __('Heure de début') }} <span class="text-red-500">*</span></label>
                                    <input type="time" name="start_datetime_time" id="start_datetime_time" value="{{ old('start_datetime_time', $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->format('H:i') : '') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('start_datetime_time') border-red-500 @enderror">
                                    @error('start_datetime_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                {{-- Date de fin --}}
                                <div>
                                    <label for="end_datetime_date" class="block text-sm font-medium text-gray-700">{{ __('Date de fin (Optionnel)') }}</label>
                                    <input type="date" name="end_datetime_date" id="end_datetime_date" value="{{ old('end_datetime_date', $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('Y-m-d') : '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('end_datetime_date') border-red-500 @enderror">
                                </div>
                                {{-- Heure de fin --}}
                                <div>
                                    <label for="end_datetime_time" class="block text-sm font-medium text-gray-700">{{ __('Heure de fin (Optionnel)') }}</label>
                                    <input type="time" name="end_datetime_time" id="end_datetime_time" value="{{ old('end_datetime_time', $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('H:i') : '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('end_datetime_time') border-red-500 @enderror">
                                    @error('end_datetime_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                             @error('end_datetime_date') {{-- Pour les erreurs de comparaison de dates globales comme after_or_equal --}}
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        {{-- Lieu --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">{{ __('Lieu (Optionnel)') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}" placeholder="Ex: Salle de conférence X, En ligne (Zoom)" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('location') border-red-500 @enderror">
                            @error('location')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                         {{-- URL d'inscription --}}
                         <div>
                            <label for="registration_url" class="block text-sm font-medium text-gray-700">{{ __('URL d\'inscription Externe (Optionnel)') }}</label>
                            <input type="url" name="registration_url" id="registration_url" value="{{ old('registration_url', $event->registration_url) }}" placeholder="https://exemple.com/inscription-externe" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('registration_url') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Laissez vide si vous utiliserez le système d\'inscription interne (une fois disponible). Remplissez si vous utilisez un lien externe.') }}</p>
                            @error('registration_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Colonne Latérale (1/3) --}}
                    <div class="md:col-span-1 space-y-6">
                        {{-- Image de couverture --}}
                        <div>
                            <label for="event_cover_image" class="block text-sm font-medium text-gray-700">{{ __('Image de couverture (Optionnel)') }}</label>
                            @if($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                <div class="mt-2 mb-1">
                                    <img src="{{ Storage::url($event->cover_image_path) }}?t={{ time() }}" alt="Image actuelle" class="max-h-40 w-auto rounded border p-1 shadow-sm">
                                    <div class="mt-1">
                                        <input type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="remove_cover_image" class="ml-2 text-xs text-gray-600">{{ __('Supprimer l\'image actuelle') }}</label>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Pour remplacer, choisissez une nouvelle image ci-dessous.') }}</p>
                            @endif
                            <input type="file" name="cover_image" id="event_cover_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('cover_image') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max 2MB. Formats : jpg, png, gif, svg, webp.') }}</p>
                            <img id="event_image_preview" src="#" alt="Prévisualisation de la nouvelle image" class="mt-2 max-h-48 w-auto rounded shadow-sm" style="display: none;"/>
                            @error('cover_image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="partner_ids" class="block text-sm font-medium text-gray-700">{{ __('Partenaires Associés à l\'Événement (Optionnel)') }}</label>
                            <select name="partner_ids[]" id="partner_ids" multiple
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                size="5">
                                @if(isset($partners) && $partners->count() > 0)
                                    @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}"
                                            {{ collect(old('partner_ids', $event->associatedPartners->pluck('id')->toArray()))->contains($partner->id) ? 'selected' : '' }}>
                                            {{ $partner->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>{{ __('Aucun partenaire actif trouvé. Veuillez d\'abord en créer.') }}</option>
                                @endif
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs partenaires.') }}</p>
                            @error('partner_ids') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            @error('partner_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Mise en vedette --}}
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_featured" name="is_featured" type="checkbox" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_featured" class="font-medium text-gray-700">{{ __('Mettre en vedette') }}</label>
                                <p class="text-gray-500">{{ __('Cocher pour afficher cet événement en évidence.') }}</p>
                            </div>
                        </div>
                        <hr>
                        {{-- Champs SEO --}}
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Optimisation SEO (Optionnel)') }}</h3>
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">{{ __('Méta Titre') }}</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $event->meta_title) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_title') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max 70 caractères. Si vide, le titre de l\'événement sera utilisé.') }}</p>
                            @error('meta_title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">{{ __('Méta Description') }}</label>
                            <textarea name="meta_description" id="meta_description" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $event->meta_description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max 160 caractères. Si vide, un extrait de la description sera utilisé.') }}</p>
                            @error('meta_description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('admin.events.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Mettre à jour l\'événement') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
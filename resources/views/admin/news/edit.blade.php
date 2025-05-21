@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Modifier l\'Actualité :') }} <span class="italic">{{ isset($newsItem->title) ? Str::limit($newsItem->title, 40) : 'Titre inconnu' }}</span>
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

            <form method="POST" action="{{ route('admin.news.update', ['news' => $newsItem->id]) }}" enctype="multipart/form-data">



                @csrf
                @method('PUT')

                {{-- Titre --}}
                <div class="mb-4">
                    <label for="news_title" class="block font-medium text-sm text-gray-700">{{ __('Titre de l\'actualité') }} <span class="text-red-500">*</span></label>
                    <input id="news_title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title', $newsItem->title) }}" required autofocus />
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-4">
                    <label for="news_slug" class="block font-medium text-sm text-gray-700">{{ __('Slug (pour l\'URL)') }} <span class="text-red-500">*</span></label>
                    <input id="news_slug" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="slug" value="{{ old('slug', $newsItem->slug) }}" required />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Ex: "grande-decouverte-quantique". Uniquement minuscules, chiffres, tirets.') }}</p>
                    @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Méta Titre (SEO) --}}
                <div class="mb-4">
                    <label for="meta_title" class="block font-medium text-sm text-gray-700">{{ __('Méta Titre (pour le SEO)') }}</label>
                    <input id="meta_title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="meta_title" value="{{ old('meta_title', $newsItem->meta_title) }}" />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Optimal : 50-70 caractères.') }}</p>
                    @error('meta_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Méta Description (SEO) --}}
                <div class="mb-4">
                    <label for="meta_description" class="block font-medium text-sm text-gray-700">{{ __('Méta Description (pour le SEO)') }}</label>
                    <textarea id="meta_description" name="meta_description" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('meta_description', $newsItem->meta_description) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Optimal : 120-160 caractères.') }}</p>
                    @error('meta_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Résumé court (Summary) --}}
                <div class="mb-4">
                    <label for="summary" class="block font-medium text-sm text-gray-700">{{ __('Résumé court (Optionnel)') }}</label>
                    <textarea id="summary" name="summary" rows="3" class="wysiwyg-editor block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('summary', $newsItem->summary) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Un bref aperçu de l\'actualité.') }}</p>
                    @error('summary') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Contenu principal --}}
                <div class="mb-4">
                    <label for="content" class="block font-medium text-sm text-gray-700">{{ __('Contenu principal') }} <span class="text-red-500">*</span></label>
                    <textarea id="content" name="content" rows="15" class="wysiwyg-editor block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('content', $newsItem->content) }}</textarea>
                    @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Gestion de l'Image de Couverture --}}
                <div class="mb-4">
                    <label for="news_cover_image" class="block font-medium text-sm text-gray-700">{{ __('Changer l\'image de couverture') }}</label>
                    @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                        <div class="mt-2 mb-2">
                            <img src="{{ Storage::url($newsItem->cover_image_path) }}?t={{ time() }}" alt="Image actuelle" class="h-24 w-auto rounded-md object-cover shadow-sm">
                            <label for="remove_cover_image" class="inline-flex items-center mt-2 text-xs">
                                <input type="checkbox" id="remove_cover_image" name="remove_cover_image" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                <span class="ms-2 text-gray-700">{{ __('Supprimer l\'image actuelle') }}</span>
                            </label>
                        </div>
                    @endif
                    <input id="news_cover_image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="cover_image" />
                    <img id="news_image_preview" src="#" alt="Aperçu de la nouvelle image" class="mt-2 max-h-40 w-auto rounded shadow-sm" style="display: none;"/>
                    <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver l'image actuelle (sauf si "Supprimer" est coché). PNG, JPG, GIF, SVG, WEBP jusqu'à 2Mo.</p>
                    @error('cover_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Date et Heure de Publication --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="published_at_date" class="block font-medium text-sm text-gray-700">{{ __('Date de publication (Optionnel)') }}</label>
                        <input id="published_at_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="date" name="published_at_date" value="{{ old('published_at_date', $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->format('Y-m-d') : '') }}" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Laissez vide pour conserver comme brouillon ou effacer la date.') }}</p>
                        @error('published_at_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="published_at_time" class="block font-medium text-sm text-gray-700">{{ __('Heure de publication (Optionnel)') }}</label>
                        <input id="published_at_time" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="time" name="published_at_time" value="{{ old('published_at_time', $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->format('H:i') : '') }}" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Nécessaire si une date de publication est fournie.') }}</p>
                        @error('published_at_time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- En Vedette ? --}}
                <div class="mb-6">
                    <label for="is_featured" class="inline-flex items-center">
                        <input id="is_featured" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_featured" value="1" {{ old('is_featured', $newsItem->is_featured) ? 'checked' : '' }}>
                        <span class="ms-2 text-sm text-gray-700">{{ __('Mettre cette actualité en vedette ?') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.news.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2 bg-sky-600 text-white font-semibold rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Mettre à Jour l\'Actualité') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
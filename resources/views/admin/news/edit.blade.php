@extends('layouts.admin')

@section('title', __('Modifier l\'Actualité') . ': ' . $newsItem->getTranslation('title', $availableLocales[0] ?? 'fr'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">
            {{ __('Modifier l\'Actualité') }}: <span class="font-normal">{{ $newsItem->getTranslation('title', $availableLocales[0] ?? 'fr') }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.news.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out mr-2">
                {{ __('Retour à la Liste des Actualités') }}
            </a>
            @if($newsItem->is_published && Route::has('public.news.show'))
                <a href="{{ route('public.news.show', $newsItem->slug) }}" target="_blank" class="bg-secondary-500 hover:bg-secondary-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out">
                    {{ __('Voir l\'Actualité (Publique)') }}
                </a>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">{{ __('Oups! Il y a eu des erreurs avec votre soumission.') }}</strong>
            <ul class="list-disc pl-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('admin.news.update', $newsItem->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT') {{-- Important pour la mise à jour --}}

            {{-- Le formulaire partiel s'attend à $newsItem, $availableLocales, et $categories --}}
            {{-- Ces variables sont passées par NewsController@edit --}}
            @include('admin.news._form', [
                'newsItem' => $newsItem,
                'availableLocales' => $availableLocales,
                'categories' => $categories
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Mettre à Jour l\'Actualité') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Les scripts pour les onglets et TinyMCE sont gérés globalement via app/resources/js/admin/app-admin.js --}}
{{-- Assurez-vous que les IDs #languageTabsNews et #languageTabContentNews sont utilisés dans _form.blade.php --}}
{{-- et que initHorizontalTabs est appelé pour eux dans app-admin.js --}}
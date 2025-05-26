@extends('layouts.admin')

@section('title', __('Créer une Nouvelle Actualité'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">
            {{ __('Créer une Nouvelle Actualité') }}
        </h1>
        <a href="{{ route('admin.news.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out">
            {{ __('Retour à la Liste des Actualités') }}
        </a>
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
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            {{-- Le formulaire partiel s'attend à $newsItem, $availableLocales, et $categories --}}
            {{-- $newsItem est initialisé dans NewsController@create --}}
            {{-- $availableLocales et $categories sont passés par NewsController@create --}}
            @include('admin.news._form', [
                'newsItem' => $newsItem,
                'availableLocales' => $availableLocales,
                'categories' => $categories
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Créer l\'Actualité') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Les scripts pour les onglets et TinyMCE sont gérés globalement via app/resources/js/admin/app-admin.js --}}
{{-- Assurez-vous que les IDs #languageTabsNews et #languageTabContentNews sont utilisés dans _form.blade.php --}}
{{-- et que initHorizontalTabs est appelé pour eux dans app-admin.js --}}
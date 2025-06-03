@extends('layouts.admin') {{-- Ou le nom de votre layout admin principal --}}

@section('title', __('Créer une Nouvelle Page Statique'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Créer une Nouvelle Page Statique') }}
        </h1>
        <a href="{{ route('admin.static-pages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            {{ __('Retour à la Liste des Pages') }}
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6 md:p-8">
        {{-- Affichage des erreurs de validation générales (si vous en avez) --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-700/50 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600 rounded-md">
                <strong class="font-bold">{{ __('Oups! Il y a eu des erreurs avec votre soumission.') }}</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.static-pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.static_pages._form', [
                'staticPage' => $staticPage, // $staticPage est une nouvelle instance passée par StaticPageController@create
                'availableLocales' => $availableLocales
                // 'users' => $users, // Décommentez si vous avez un champ de sélection pour user_id dans _form.blade.php
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.static-pages.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">
                    {{ __('Annuler') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Créer la Page') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
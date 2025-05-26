@extends('layouts.admin')

@section('title', __('Créer une Nouvelle Page Statique'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">
            {{ __('Créer une Nouvelle Page Statique') }}
        </h1>
        <a href="{{ route('admin.static-pages.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out">
            {{ __('Retour à la Liste') }}
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
        <form action="{{ route('admin.static-pages.store') }}" method="POST" enctype="multipart/form-data">
            @php
                // Pour le formulaire partiel, 'create' n'a pas d'instance $staticPage existante,
                // mais le partiel s'attend à une variable $staticPage.
                // Nous passons une nouvelle instance pour éviter les erreurs dans le partiel.
                // Les valeurs seront nulles ou gérées par old().
                if (!isset($staticPage)) {
                    $staticPage = new \App\Models\StaticPage();
                    $staticPage->is_published = true; // Par défaut, une nouvelle page est publiée
                }
            @endphp

            @include('admin.static_pages._form', ['staticPage' => $staticPage, 'availableLocales' => $availableLocales])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Créer la Page') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


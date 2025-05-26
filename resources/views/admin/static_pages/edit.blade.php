@extends('layouts.admin')

@section('title', __('Modifier la Page Statique') . ': ' . $staticPage->getTranslation('title', $availableLocales[0] ?? 'fr'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">
            {{ __('Modifier la Page Statique') }}: <span class="font-normal">{{ $staticPage->getTranslation('title', $availableLocales[0] ?? 'fr') }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.static-pages.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out mr-2">
                {{ __('Retour à la Liste') }}
            </a>
            @if($staticPage->is_published && !in_array($staticPage->slug, ['presentation-crpqa', 'appel-collaboration'])) {{-- Empêcher la visualisation directe de slugs spéciaux si besoin --}}
                <a href="{{ route('public.page', $staticPage->slug) }}" target="_blank" class="bg-secondary-500 hover:bg-secondary-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out">
                    {{ __('Voir la Page (Publique)') }}
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
        <form action="{{ route('admin.static-pages.update', $staticPage->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT') {{-- Important pour la mise à jour --}}

            @include('admin.static_pages._form', ['staticPage' => $staticPage, 'availableLocales' => $availableLocales])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Mettre à Jour la Page') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


@extends('layouts.admin') {{-- Ou votre layout d'administration --}}

@section('title', 'Modifier la Publication : ' . $publication->getTranslation('title', $defaultLocale ?? 'fr'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        @php
            $defaultLocale = $availableLocales[0] ?? 'fr';
        @endphp
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Modifier la Publication : <span class="italic">{{ $publication->getTranslation('title', $defaultLocale, false) ?: $publication->slug }}</span>
            </h1>
            <a href="{{ route('admin.publications.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-150">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.publications.update', $publication) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.publications._form', [
                    'publication' => $publication,
                    'availableLocales' => $availableLocales,
                    'users' => $users
                ])
            </form>
        </div>
    </div>
@endsection
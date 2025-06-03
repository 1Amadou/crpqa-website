@extends('layouts.admin')
@section('title', __('Modifier l\'Événement'))
@section('header')
    {{-- ... Titre et boutons retour/voir public ... --}}
     <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ __('Modifier l\'Événement') }}: <span class="font-normal">{{ $event->getTranslation('title', $availableLocales[0] ?? config('app.locale', 'fr'), false) }}</span></h1>
    {{-- ... --}}
@endsection
@section('content')
     <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.events._form', [
                    'event' => $event,
                    'availableLocales' => $availableLocales,
                    'partners' => $partners
                ])
                <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">{{ __('Annuler') }}</a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs ...">{{ __('Mettre à Jour l\'Événement') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
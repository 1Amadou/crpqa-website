@extends('layouts.admin')

@section('title', __('Modifier l\'Actualité'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Modifier l\'Actualité') }}: 
            <span class="font-normal">{{ $newsItem->getTranslation('title', $availableLocales[0] ?? config('app.locale', 'fr'), false) }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium shadow-sm transition ease-in-out duration-150 flex items-center mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à la Liste') }}
            </a>
            @if($newsItem->is_published && Route::has('public.news.show'))
                <a href="{{ route('public.news.show', $newsItem->slug) }}" target="_blank" class="px-4 py-2 bg-secondary-500 hover:bg-secondary-600 text-white rounded-md text-sm font-medium shadow-sm transition ease-in-out duration-150 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    {{ __('Voir (Public)') }}
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('admin.news.update', $newsItem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @include('admin.news._form', [
                'newsItem' => $newsItem,
                'availableLocales' => $availableLocales,
                'categories' => $categories
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">
                    {{ __('Annuler') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Mettre à Jour l\'Actualité') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', __('Modifier la Catégorie d\'Actualité'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Modifier la Catégorie') }}: 
            <span class="font-normal">{{ $category->name }}</span>
        </h1>
        <a href="{{ route('admin.news-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 shadow-sm transition ease-in-out duration-150">
            <x-heroicon-o-arrow-uturn-left class="h-4 w-4 mr-1.5"/>
            {{ __('Retour à la Liste') }}
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6 md:p-8">
         @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-700/50 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600 rounded-md">
                <strong class="font-bold">{{ __('Oups! Il y a eu des erreurs.') }}</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.news-categories.update', $category->id) }}" method="POST">
            @method('PUT')
            
            @include('admin.news_categories._form', [
                'category' => $category
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.news-categories.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">
                    {{ __('Annuler') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Mettre à Jour la Catégorie') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
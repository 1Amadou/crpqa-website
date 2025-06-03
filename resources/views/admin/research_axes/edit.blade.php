@extends('layouts.admin')

@section('title', __('Modifier l\'Axe de Recherche'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Modifier l\'Axe de Recherche') }}: 
            <span class="font-normal">{{ $researchAxis->getTranslation('name', $availableLocales[0] ?? config('app.locale', 'fr'), false) }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.research-axes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 shadow-sm transition ease-in-out duration-150 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour à la Liste') }}
            </a>
            {{-- Vous pouvez ajouter un lien pour voir l'axe sur le site public si pertinent --}}
            {{-- @if($researchAxis->is_active && Route::has('public.research_axes.show'))
                <a href="{{ route('public.research_axes.show', $researchAxis->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-secondary-500 text-white text-sm font-medium rounded-md hover:bg-secondary-600 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-eye class="h-4 w-4 mr-1.5"/>
                    {{ __('Voir (Public)') }}
                </a>
            @endif --}}
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6 md:p-8">
        <form action="{{ route('admin.research-axes.update', $researchAxis->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @include('admin.research_axes._form', [
                'researchAxis' => $researchAxis,
                'availableLocales' => $availableLocales
            ])

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.research-axes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">
                    {{ __('Annuler') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Mettre à Jour l\'Axe de Recherche') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
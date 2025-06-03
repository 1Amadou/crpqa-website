@extends('layouts.admin')

@section('title', __('Détails de la Catégorie') . ': ' . $newsCategory->name)

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails de la Catégorie d\'Actualité') }}
            </h1>
            <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">{{ $newsCategory->name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage news_categories')
            <a href="{{ route('admin.news-categories.edit', $newsCategory->slug) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
            <a href="{{ route('admin.news-categories.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
    <div class="p-6 md:p-8">
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 text-sm">
            <div class="flex flex-col">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Nom') }}:</dt>
                <dd class="text-gray-800 dark:text-gray-100 text-lg">{{ $newsCategory->name }}</dd>
            </div>
            <div class="flex flex-col">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">Slug :</dt>
                <dd class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs text-gray-700 dark:text-gray-200 inline-block">{{ $newsCategory->slug }}</dd>
            </div>
            <div class="flex flex-col">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Statut') }}:</dt>
                <dd>
                    @if($newsCategory->is_active)
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Active') }}</span>
                    @else
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Inactive') }}</span>
                    @endif
                </dd>
            </div>
            @if($newsCategory->color || $newsCategory->text_color)
            <div class="flex flex-col">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Couleurs (Fond/Texte)') }}:</dt>
                <dd class="flex items-center space-x-2 mt-1">
                    <span class="h-6 w-6 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $newsCategory->color ?? 'transparent' }};"></span>
                    <span class="h-6 w-6 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $newsCategory->text_color ?? 'transparent' }};"></span>
                    <span class="px-2 py-1 text-xs font-semibold rounded" 
                          style="background-color: {{ $newsCategory->color ?? 'transparent' }}; color: {{ $newsCategory->text_color ?? '#000000' }}; border: 1px solid {{ $newsCategory->text_color ?? ($newsCategory->color ? '#fff' : '#ccc') }};">
                        {{ __('Aperçu Badge') }}
                    </span>
                </dd>
            </div>
            @endif
            <div class="flex flex-col md:col-span-1">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Créée le') }}:</dt>
                <dd class="text-gray-700 dark:text-gray-200">{{ $newsCategory->created_at->translatedFormat('d F Y à H:i') }}</dd>
            </div>
            <div class="flex flex-col md:col-span-1">
                <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Dernière mise à jour') }}:</dt>
                <dd class="text-gray-700 dark:text-gray-200">{{ $newsCategory->updated_at->translatedFormat('d F Y à H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>

@if($newsCategory->newsItems->count() > 0)
<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
            {{ __('Actualités dans cette catégorie') }} ({{ $newsCategory->newsItems->count() }})
        </h3>
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($newsCategory->newsItems->take(10) as $newsItem) {{-- Limiter pour ne pas surcharger --}}
                <li>
                    <a href="{{ route('admin.news.show', $newsItem->slug) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                        {{ $newsItem->getTranslation('title', app()->getLocale(), false) }}
                    </a>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ({{ $newsItem->is_published ? __('Publiée') : __('Brouillon') }})
                    </span>
                </li>
            @endforeach
            @if($newsCategory->newsItems->count() > 10)
                <li class="text-xs text-gray-500 dark:text-gray-400 italic">{{ __('et plus...') }}</li>
            @endif
        </ul>
    </div>
</div>
@endif

@can('manage news_categories')
<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
    <form action="{{ route('admin.news-categories.destroy', $newsCategory->slug) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette catégorie :name ? Si des actualités y sont associées, cela pourrait causer des problèmes.', ['name' => addslashes($newsCategory->name)]) }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
            <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
            {{ __('Supprimer cette Catégorie') }}
        </button>
    </form>
</div>
@endcan

@endsection
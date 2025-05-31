@extends('layouts.admin')

@section('title', __('Gestion des Actualités'))

@php
    $primaryLocale = app()->getLocale();
@endphp

@section('header')
<div class="flex flex-wrap justify-between items-center gap-4">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
        {{ __('Gestion des Actualités') }}
    </h1>
    <a href="{{ route('admin.news.create') }}"
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                  clip-rule="evenodd" />
        </svg>
        {{ __('Ajouter une Actualité') }}
    </a>
</div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-4 sm:p-6">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 border border-green-300 dark:border-green-600 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 border border-red-300 dark:border-red-600 rounded-md shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($newsItems->count() > 0)
            <div class="overflow-x-auto align-middle inline-block min-w-full">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Image') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Titre') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">
                                {{ __('Auteur') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Statut') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
                                {{ __('Date de Pub.') }}
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">
                                {{ __('Vedette') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($newsItems as $newsItem)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($newsItem->cover_image_thumbnail_url)
                                        <img src="{{ $newsItem->cover_image_thumbnail_url }}"
                                             alt="{{ $newsItem->getTranslation('cover_image_alt', $primaryLocale, false) ?: $newsItem->getTranslation('title', $primaryLocale, false) }}"
                                             class="h-10 w-16 object-cover rounded shadow-sm">
                                    @else
                                        <div class="h-10 w-16 rounded bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs break-words">
                                    {{ Str::limit($newsItem->getTranslation('title', $primaryLocale, false), 60) }}
                                    @if ($newsItem->category)
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $newsItem->category->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                    {{ $newsItem->createdBy->name ?? __('N/A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if ($newsItem->is_published)
                                        @if ($newsItem->published_at && $newsItem->published_at->isFuture())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100">
                                                {{ __('Planifiée') }}
                                            </span>
                                            <a href="{{ route('admin.news.unpublish', $newsItem) }}"
                                               class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                               title="{{ __('Dépublier immédiatement') }}">
                                                &times;
                                            </a>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                {{ __('Publiée') }}
                                            </span>
                                            <a href="{{ route('admin.news.unpublish', $newsItem) }}"
                                               class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                               title="{{ __('Dépublier') }}">
                                                &times;
                                            </a>
                                        @endif
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                            {{ __('Brouillon') }}
                                        </span>
                                        <a href="{{ route('admin.news.publish', $newsItem) }}"
                                           class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                           title="{{ __('Publier') }}">
                                            &#10003;
                                        </a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    {{ $newsItem->published_at
                                        ? $newsItem->published_at->translatedFormat('d/m/y H:i')
                                        : __('N/A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm hidden md:table-cell">
                                    @if ($newsItem->is_featured)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-600 dark:text-amber-100"
                                              title="{{ __('En vedette') }}">
                                            ⭐
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.news.show', $newsItem) }}"
                                       class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 mr-2 p-1 inline-block"
                                       title="{{ __('Voir') }}">
                                        <x-heroicon-o-eye class="h-5 w-5"/>
                                    </a>
                                    <a href="{{ route('admin.news.edit', $newsItem) }}"
                                       class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2 p-1 inline-block"
                                       title="{{ __('Modifier') }}">
                                        <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $newsItem) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer l\'actualité :name ?', ['name' => addslashes($newsItem->getTranslation('title', $primaryLocale, false))]) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1"
                                                title="{{ __('Supprimer') }}">
                                            <x-heroicon-o-trash class="h-5 w-5"/>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $newsItems->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                    {{ __('Aucune actualité trouvée.') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Commencez par en créer une nouvelle.') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.news.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                        {{ __('Ajouter une Actualité') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

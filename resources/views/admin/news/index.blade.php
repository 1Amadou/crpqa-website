@extends('layouts.admin')

@section('title', __('Gestion des Actualités'))

@php
    // Définir la locale primaire pour l'affichage des titres etc.
    // $availableLocales devrait être passé par le contrôleur, mais sinon, fallback.
    // Pour la vue index, on va utiliser la locale actuelle de l'application.
    $primaryLocale = app()->getLocale();
@endphp

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Gestion des Actualités') }}
        </h1>
        
            <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm font-medium shadow-sm transition ease-in-out duration-150 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('Ajouter une Actualité') }}
            </a>
        
    </div>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-2 md:p-6 border-b border-gray-200 dark:border-gray-700">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm animate-pulse delay-1000 duration-1000">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($newsItems->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Image') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Titre') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Auteur') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">{{ __('Date de Pub.') }}</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('En Vedette') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            {{-- $availableLocales est passé à la vue par NewsController@index implicitement si besoin pour getTranslation,
                                sinon on peut utiliser app()->getLocale() ou la première locale configurée.
                                Le contrôleur NewsController@index a été mis à jour pour trier par titre de la locale primaire. --}}
                            @foreach ($newsItems as $newsItem)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($newsItem->hasMedia('news_cover_image'))
                                            <img src="{{ $newsItem->getFirstMediaUrl('news_cover_image', 'thumbnail') }}" {{-- Assurez-vous que la conversion 'thumbnail' est définie dans le modèle News ou retirez-la --}}
                                                 alt="{{ $newsItem->getTranslation('cover_image_alt', $primaryLocale) ?: $newsItem->getTranslation('title', $primaryLocale) }}"
                                                 class="h-10 w-16 object-cover rounded shadow-sm">
                                        @else
                                            <div class="h-10 w-16 rounded bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs text-slate-500 dark:text-slate-400 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-normal text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs">
                                        {{ Str::limit($newsItem->getTranslation('title', $primaryLocale), 60) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                        {{ $newsItem->user->name ?? __('N/A') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($newsItem->is_published)
                                            @if($newsItem->published_at && \Carbon\Carbon::parse($newsItem->published_at)->isFuture())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">{{ __('Planifiée') }}</span>
                                                @can('publish news')<a href="{{ route('admin.news.unpublish', $newsItem) }}" class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{__('Dépublier immédiatement')}}">&times;</a>@endcan
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Publiée') }}</span>
                                                @can('publish news')<a href="{{ route('admin.news.unpublish', $newsItem) }}" class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{__('Dépublier')}}">&times;</a>@endcan
                                            @endif
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ __('Brouillon') }}</span>
                                            @can('publish news')<a href="{{ route('admin.news.publish', $newsItem) }}" class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{__('Publier')}}">&#10003;</a>@endcan
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                        {{ $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->translatedFormat('d/m/Y H:i') : __('N/A') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($newsItem->is_featured)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100" title="{{__('En vedette')}}">⭐</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.news.show', $newsItem) }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 mr-2" title="{{__('Voir')}}">
                                            <x-heroicon-o-eye class="h-5 w-5 inline"/>
                                        </a>
                                        
                                        <a href="{{ route('admin.news.edit', $newsItem) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" title="{{__('Modifier')}}">
                                            <x-heroicon-o-pencil-square class="h-5 w-5 inline"/>
                                        </a>
                                        
                                        
                                        <form action="{{ route('admin.news.destroy', $newsItem) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer l\'actualité :name ?', ['name' => addslashes($newsItem->getTranslation('title', $primaryLocale))]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" title="{{__('Supprimer')}}">
                                                <x-heroicon-o-trash class="h-5 w-5 inline"/>
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
                <p class="text-gray-700 dark:text-gray-300 py-4">{{ __('Aucune actualité trouvée.') }}</p>
                 @can('create news')
                <a href="{{ route('admin.news.create') }}" class="inline-block px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                    {{ __('Ajouter la première actualité') }}
                </a>
                @endcan
            @endif
        </div>
    </div>
@endsection
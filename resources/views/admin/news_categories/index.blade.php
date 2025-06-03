@extends('layouts.admin')

@section('title', __('Catégories d\'Actualités'))

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Catégories d\'Actualités') }}
        </h1>
        @can('manage news_categories')
            <a href="{{ route('admin.news-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-heroicon-o-plus-circle class="h-5 w-5 mr-2"/>
                {{ __('Nouvelle Catégorie') }}
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-4 sm:p-6">
        @if (session('success'))
            <div class="mb-4 p-4 text-sm bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 text-sm bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($categories->count() > 0)
            <div class="overflow-x-auto align-middle inline-block min-w-full">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nom') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Slug') }}</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Couleur') }}</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actif') }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                    @if($category->color || $category->text_color)
                                        <span class="px-2 py-1 text-xs font-semibold rounded" 
                                              style="background-color: {{ $category->color ?? 'transparent' }}; color: {{ $category->text_color ?? '#000000' }}; border: 1px solid {{ $category->text_color ?? ($category->color ? '#fff' : '#ccc') }};">
                                            {{ __('Aperçu') }}
                                        </span>
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                    @if($category->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Oui') }}</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">{{ __('Non') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- La vue "show" pour une catégorie est souvent optionnelle, lien vers edit directement --}}
                                    {{-- @can('view news_categories')
                                    <a href="{{ route('admin.news-categories.show', $category->slug) }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 mr-2 p-1 inline-block" title="{{__('Voir')}}">
                                        <x-heroicon-o-eye class="h-5 w-5"/>
                                    </a>
                                    @endcan --}}
                                    @can('manage news_categories')
                                    <a href="{{ route('admin.news-categories.edit', $category->slug) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2 p-1 inline-block" title="{{__('Modifier')}}">
                                        <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                    </a>
                                    <form action="{{ route('admin.news-categories.destroy', $category->slug) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer la catégorie :name ? Si des actualités y sont associées, cela pourrait causer des problèmes.', ['name' => addslashes($category->name)]) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1" title="{{__('Supprimer')}}">
                                            <x-heroicon-o-trash class="h-5 w-5"/>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-8">
                 <x-heroicon-o-tag class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucune catégorie d\'actualité trouvée.') }}</h3>
                @can('manage news_categories')
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Commencez par en créer une nouvelle.') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.news-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                        {{ __('Créer une Catégorie') }}
                    </a>
                </div>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
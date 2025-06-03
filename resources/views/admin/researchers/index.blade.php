@extends('layouts.admin')

@section('title', __('Gestion des Chercheurs'))

@php
    $primaryLocale = app()->getLocale(); // Locale actuelle pour l'affichage des champs traduits
@endphp

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Gestion des Chercheurs et Membres de l\'Équipe') }}
        </h1>
        @can('manage researchers') {{-- Vérifier la permission --}}
            <a href="{{ route('admin.researchers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-heroicon-o-plus-circle class="h-5 w-5 mr-2"/>
                {{ __('Ajouter un Chercheur') }}
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

        @if($researchers->count() > 0)
            <div class="overflow-x-auto align-middle inline-block min-w-full">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Photo') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nom Complet') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Titre/Position') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">{{ __('Email') }}</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actif') }}</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Ordre') }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($researchers as $researcher)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($researcher->photo_thumbnail_url) {{-- Utilise l'accesseur du modèle Researcher --}}
                                        <img src="{{ $researcher->photo_thumbnail_url }}"
                                             alt="{{ $researcher->getTranslation('photo_alt_text', $primaryLocale, false) ?: $researcher->full_name }}"
                                             class="h-10 w-10 rounded-full object-cover shadow-sm">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 shadow-sm">
                                            <x-heroicon-o-user-circle class="h-7 w-7"/>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $researcher->full_name }}</div>
                                    {{-- Le full_name est un accesseur qui utilise les champs localisés first_name et last_name --}}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell max-w-xs break-words">
                                    {{ Str::limit($researcher->getTranslation('title_position', $primaryLocale, false), 40) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                                    @if($researcher->email)
                                        <a href="mailto:{{ $researcher->email }}" class="hover:text-primary-600 dark:hover:text-primary-400 hover:underline">{{ $researcher->email }}</a>
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                    @if($researcher->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Oui') }}</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">{{ __('Non') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                    {{ $researcher->display_order }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    @can('view researchers')
                                    <a href="{{ route('admin.researchers.show', $researcher->slug) }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 mr-2 p-1 inline-block" title="{{__('Voir')}}">
                                        <x-heroicon-o-eye class="h-5 w-5"/>
                                    </a>
                                    @endcan
                                    @can('manage researchers')
                                    <a href="{{ route('admin.researchers.edit', $researcher->slug) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2 p-1 inline-block" title="{{__('Modifier')}}">
                                        <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                    </a>
                                    <form action="{{ route('admin.researchers.destroy', $researcher->slug) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer le profil de :name ?', ['name' => addslashes($researcher->full_name)]) }}');">
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
                {{ $researchers->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun profil de chercheur trouvé.') }}</h3>
                @can('manage researchers')
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Commencez par en créer un nouveau.') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.researchers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                        {{ __('Ajouter un Chercheur') }}
                    </a>
                </div>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
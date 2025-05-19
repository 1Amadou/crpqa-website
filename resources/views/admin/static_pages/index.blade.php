@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Pages Statiques') }}
        </h2>
        {{-- Lien pour créer une nouvelle page (nous créerons la route et la vue plus tard) --}}
        <a href="{{ route('admin.static-pages.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            {{ __('Nouvelle Page') }}
        </a>
    </div>
@endsection

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm">
        {{ session('success') }}
    </div>
@endif
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            @if($pages->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Titre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Publiée
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dernière modification
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($pages as $page)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $page->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $page->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($page->is_published)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Oui
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Non
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $page->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- Liens d'action (nous les activerons plus tard) --}}
                                    <a href="{{ route('admin.static-pages.show', $page) }}" class="text-emerald-600 hover:text-emerald-900 mr-3">Voir</a>
                                    <a href="{{ route('admin.static-pages.edit', $page) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                    <form action="{{ route('admin.static-pages.destroy', $page) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette page ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $pages->links() }} {{-- Affiche les liens de pagination --}}
                </div>
            @else
                <p class="text-gray-700">Aucune page statique trouvée.</p>
                {{-- Lien pour créer une nouvelle page --}}
                <a href="{{ route('admin.static-pages.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ __('Créer la première page') }}
                </a>
            @endif
        </div>
    </div>
@endsection

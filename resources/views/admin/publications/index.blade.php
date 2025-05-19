@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Publications') }}
        </h2>
        @can('create', App\Models\Publication::class) {{-- Vérifie si l'utilisateur peut créer une publication --}}
        <a href="{{ route('admin.publications.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ __('Ajouter une Publication') }}
        </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-2 md:p-6 border-b border-gray-200">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($publications->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Auteurs Internes</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date de Pub.</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">En Vedette</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($publications as $publication)
                                <tr>
                                    <td class="px-4 py-3 whitespace-normal text-sm font-medium text-gray-900 max-w-md">
                                        {{ Str::limit($publication->title, 70) }}
                                        @if($publication->creator)
                                            <span class="text-xs text-gray-500 block mt-0.5">Créé par: {{ $publication->creator->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        @forelse ($publication->researchers as $researcher)
                                            <span class="text-xs inline-block py-0.5 px-1.5 bg-indigo-100 text-indigo-700 rounded-full mr-1 mb-1">{{ $researcher->getFullNameAttribute() }}</span>
                                        @empty
                                            N/A
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $publication->type }} {{-- Idéalement, afficher la valeur lisible si $publicationTypes est disponible --}}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $publication->publication_date ? \Carbon\Carbon::parse($publication->publication_date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($publication->is_featured)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" title="En vedette">⭐</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        @can('view', $publication)
                                            <a href="{{ route('admin.publications.show', $publication) }}" class="text-emerald-600 hover:text-emerald-700 mr-2" title="Voir">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            </a>
                                        @endcan
                                        @can('update', $publication)
                                            <a href="{{ route('admin.publications.edit', $publication) }}" class="text-indigo-600 hover:text-indigo-700 mr-2" title="Modifier">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </a>
                                        @endcan
                                        @can('delete', $publication)
                                            <form action="{{ route('admin.publications.destroy', $publication) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la publication \'{{ addslashes(Str::limit($publication->title, 30)) }}\' ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700" title="Supprimer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
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
                    {{ $publications->links() }}
                </div>
            @else
                <p class="text-gray-700 py-4">{{ __('Aucune publication trouvée.') }}</p>
                @can('create', App\Models\Publication::class)
                <a href="{{ route('admin.publications.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                    {{ __('Ajouter la première publication') }}
                </a>
                @endcan
            @endif
        </div>
    </div>
@endsection
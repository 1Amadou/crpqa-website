@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Chercheurs et Membres de l\'Équipe') }}
        </h2>
        <a href="{{ route('admin.researchers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ __('Ajouter un Chercheur') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-2 md:p-6 border-b border-gray-200">

            {{-- Messages de session (succès, erreur) --}}
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

            @if($researchers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom & Prénom</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Position</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Email</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actif</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Ordre</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($researchers as $researcher)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path))
                                            <img src="{{ Storage::url($researcher->photo_path) }}" alt="Photo de {{ $researcher->first_name }} {{ $researcher->last_name }}" class="h-10 w-10 rounded-full object-cover shadow-sm">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-500 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $researcher->last_name }}, {{ $researcher->first_name }}</div>
                                        @if($researcher->title)
                                            <div class="text-xs text-gray-500">{{ $researcher->title }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ Str::limit($researcher->position, 40) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        @if($researcher->email)
                                            <a href="mailto:{{ $researcher->email }}" class="hover:underline">{{ $researcher->email }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($researcher->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oui</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500 hidden md:table-cell">
                                        {{ $researcher->display_order }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.researchers.show', $researcher) }}" class="text-emerald-600 hover:text-emerald-700 mr-2" title="Voir">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                        </a>
                                        <a href="{{ route('admin.researchers.edit', $researcher) }}" class="text-indigo-600 hover:text-indigo-700 mr-2" title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </a>
                                        <form action="{{ route('admin.researchers.destroy', $researcher) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement le profil de \'{{ addslashes($researcher->first_name . ' ' . $researcher->last_name) }}\' ? Cette action est irréversible.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" title="Supprimer">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $researchers->links() }} {{-- Affiche les liens de pagination --}}
                </div>
            @else
                <p class="text-gray-700 py-4">{{ __('Aucun profil de chercheur n\'a été trouvé.') }}</p>
                <a href="{{ route('admin.researchers.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                    {{ __('Ajouter le premier chercheur') }}
                </a>
            @endif
        </div>
    </div>
@endsection
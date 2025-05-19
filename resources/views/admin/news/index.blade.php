@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Actualités') }}
        </h2>
        <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ __('Ajouter une Actualité') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-2 md:p-6 border-b border-gray-200">

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
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Auteur</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date de Pub.</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">En Vedette</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($newsItems as $newsItem)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                                            <img src="{{ Storage::url($newsItem->cover_image_path) }}" alt="Image de couverture" class="h-10 w-16 object-cover rounded shadow-sm">
                                        @else
                                            <div class="h-10 w-16 rounded bg-slate-200 flex items-center justify-center text-xs text-slate-500 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-normal text-sm font-medium text-gray-900 max-w-xs">
                                        {{ Str::limit($newsItem->title, 60) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ $newsItem->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($newsItem->published_at && $newsItem->published_at <= now())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Publiée</span>
                                        @elseif($newsItem->published_at && $newsItem->published_at > now())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Planifiée</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Brouillon</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($newsItem->is_featured)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" title="En vedette">⭐</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.news.show', $newsItem) }}" class="text-emerald-600 hover:text-emerald-700 mr-2" title="Voir">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                        </a>
                                        <a href="{{ route('admin.news.edit', $newsItem) }}" class="text-indigo-600 hover:text-indigo-700 mr-2" title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </a>
                                        <form action="{{ route('admin.news.destroy', $newsItem) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'actualité \'{{ addslashes(Str::limit($newsItem->title, 30)) }}\' ?');">
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
                    {{ $newsItems->links() }} {{-- Affiche les liens de pagination --}}
                </div>
            @else
                <p class="text-gray-700 py-4">{{ __('Aucune actualité trouvée.') }}</p>
                <a href="{{ route('admin.news.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                    {{ __('Ajouter la première actualité') }}
                </a>
            @endif
        </div>
    </div>
@endsection
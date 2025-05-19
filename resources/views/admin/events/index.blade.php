@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Événements') }}
        </h2>
        <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ __('Ajouter un Événement') }}
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

            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Organisateur</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date de Début</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Date de Fin</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Lieu</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">En Vedette</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($events as $event)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                            <img src="{{ Storage::url($event->cover_image_path) }}" alt="Image de couverture" class="h-10 w-16 object-cover rounded shadow-sm">
                                        @else
                                            <div class="h-10 w-16 rounded bg-slate-200 flex items-center justify-center text-xs text-slate-500 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-normal text-sm font-medium text-gray-900 max-w-xs">
                                        {{ Str::limit($event->title, 60) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ $event->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @php
                                            $now = now();
                                            $start_datetime = \Carbon\Carbon::parse($event->start_datetime);
                                            $end_datetime = $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime) : null;
                                            $status = '';
                                            $statusClass = '';

                                            if ($start_datetime->isFuture()) {
                                                $status = 'À venir';
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                            } elseif ($end_datetime && $end_datetime->isPast()) {
                                                $status = 'Passé';
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                            } elseif ($start_datetime->isPast() && (!$end_datetime || $end_datetime->isFuture() || $end_datetime->isToday())) {
                                                $status = 'En cours';
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                            } else { // Cas par défaut ou si seulement start_datetime est aujourd'hui et pas d'heure de fin
                                                $status = 'Actif';
                                                $statusClass = 'bg-green-100 text-green-800';
                                                if ($end_datetime && $end_datetime->isPast()){ // Redondance pour s'assurer que si fin passée = Passé
                                                     $status = 'Passé';
                                                     $statusClass = 'bg-gray-100 text-gray-800';
                                                }
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        {{ $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ Str::limit($event->location, 30) ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($event->is_featured)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" title="En vedette">⭐</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.events.registrations.index', $event) }}" class="text-cyan-600 hover:text-cyan-700 mr-2" title="Voir les Inscriptions">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-emerald-600 hover:text-emerald-700 mr-2" title="Voir">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-700 mr-2" title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'événement \'{{ addslashes(Str::limit($event->title, 30)) }}\' ?');">
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
                    {{ $events->links() }}
                </div>
            @else
                <p class="text-gray-700 py-4">{{ __('Aucun événement trouvé.') }}</p>
                <a href="{{ route('admin.events.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                    {{ __('Ajouter le premier événement') }}
                </a>
            @endif
        </div>
    </div>
@endsection
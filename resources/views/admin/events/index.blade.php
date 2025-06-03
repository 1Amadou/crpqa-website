@extends('layouts.admin')

@section('title', __('Gestion des Événements'))

@php
    $primaryLocale = app()->getLocale(); // Locale actuelle pour l'affichage
@endphp

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Gestion des Événements') }}
        </h1>
        @can('manage events') {{-- Vérifier la permission --}}
            <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('Ajouter un Événement') }}
            </a>
        @endcan
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

        @if($events->count() > 0)
            <div class="overflow-x-auto align-middle inline-block min-w-full">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Image') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Titre') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Organisateur') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Statut') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">{{ __('Date Début') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">{{ __('Lieu') }}</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Vedette') }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($events as $event)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($event->cover_image_thumbnail_url) {{-- Utilise l'accesseur du modèle Event --}}
                                        <img src="{{ $event->cover_image_thumbnail_url }}"
                                             alt="{{ $event->getTranslation('cover_image_alt', $primaryLocale, false) ?: $event->getTranslation('title', $primaryLocale, false) }}"
                                             class="h-10 w-16 object-cover rounded shadow-sm">
                                    @else
                                        <div class="h-10 w-16 rounded bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs break-words">
                                    {{ Str::limit($event->getTranslation('title', $primaryLocale, false), 60) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                    {{ $event->createdBy->name ?? __('N/A') }} {{-- Utilisation de createdBy --}}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $now = now();
                                        $start_datetime = $event->start_datetime; // Déjà un objet Carbon
                                        $end_datetime = $event->end_datetime;   // Déjà un objet Carbon ou null
                                        $status_text = '';
                                        $status_class = '';

                                        if ($start_datetime->isFuture()) {
                                            $status_text = __('À venir');
                                            $status_class = 'bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100';
                                        } elseif ($end_datetime && $end_datetime->isPast()) {
                                            $status_text = __('Passé');
                                            $status_class = 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
                                        } elseif ($start_datetime->isPast() && (!$end_datetime || $end_datetime->isFuture() || $end_datetime->isToday())) {
                                            $status_text = __('En cours');
                                            $status_class = 'bg-amber-100 text-amber-800 dark:bg-amber-600 dark:text-amber-100';
                                        } else {
                                            $status_text = __('Actif'); 
                                            $status_class = 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100';
                                            if ($end_datetime && $end_datetime->isPast()){
                                                 $status_text = __('Passé');
                                                 $status_class = 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status_class }}">
                                        {{ $status_text }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    {{ $event->start_datetime ? $event->start_datetime->translatedFormat('d/m/y H:i') : __('N/A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                                     {{ Str::limit($event->getTranslation('location', $primaryLocale, false), 30) ?? __('N/A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm hidden md:table-cell">
                                    @if($event->is_featured)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-600 dark:text-amber-100" title="{{__('En vedette')}}">⭐</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    @can('view event_registrations')
                                    <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 mr-2 p-1 inline-block" title="{{__('Voir les Inscriptions')}} ({{ $event->registrations_count ?? $event->registrations()->count() }})">
                                        <x-heroicon-o-users class="h-5 w-5"/>
                                    </a>
                                    @endcan
                                    @can('view events')
                                    <a href="{{ route('admin.events.show', $event) }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 mr-2 p-1 inline-block" title="{{__('Voir')}}">
                                        <x-heroicon-o-eye class="h-5 w-5"/>
                                    </a>
                                    @endcan
                                    @can('manage events')
                                    <a href="{{ route('admin.events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2 p-1 inline-block" title="{{__('Modifier')}}">
                                        <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer l\'événement :name ?', ['name' => addslashes($event->getTranslation('title', $primaryLocale, false))]) }}');">
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
                {{ $events->links() }}
            </div>
        @else
            <div class="text-center py-8">
                 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucun événement trouvé.') }}</h3>
                @can('manage events')
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Commencez par en créer un nouveau.') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                        {{ __('Ajouter un Événement') }}
                    </a>
                </div>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
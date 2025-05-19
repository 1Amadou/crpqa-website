@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'Inscription') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Pour l'événement : <a href="{{ route('admin.events.show', $registration->event) }}" class="text-blue-600 hover:underline">{{ Str::limit($registration->event->title, 50) }}</a>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.events.registrations.index', $registration->event_id) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Retour aux Inscriptions') }}
            </a>
            {{-- @can('update', $registration) --}}
            <a href="{{ route('admin.event-registrations.edit', $registration) }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Modifier cette Inscription') }}
            </a>
            {{-- @endcan --}}
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Nom du Participant') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $registration->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Email') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $registration->email }}</p>
                    </div>
                    @if($registration->phone_number)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Téléphone') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $registration->phone_number }}</p>
                    </div>
                    @endif
                    @if($registration->organization)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Organisation') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $registration->organization }}</p>
                    </div>
                    @endif
                </div>
                <div class="space-y-4">
                     <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Statut Actuel') }}</h3>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'cancelled_by_user' => 'bg-gray-100 text-gray-700',
                                'attended' => 'bg-blue-100 text-blue-800',
                            ];
                            $statusDisplay = $statuses[$registration->status] ?? ucfirst(str_replace('_', ' ', $registration->status));
                        @endphp
                        <p class="mt-1"><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClasses[$registration->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $statusDisplay }}</span></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Date d\'Inscription') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $registration->registered_at->format('d/m/Y \à H:i') }}</p>
                    </div>
                    @if($registration->user)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Compte Utilisateur Lié') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">
                            <a href="{{ route('admin.users.show', $registration->user) }}" class="text-blue-600 hover:underline">
                                {{ $registration->user->name }} ({{ $registration->user->email }})
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            @if($registration->motivation)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Motivation (si fournie)') }}</h3>
                <div class="mt-1 prose max-w-none text-gray-700">
                    {!! nl2br(e($registration->motivation)) !!}
                </div>
            </div>
            @endif

            @if($registration->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Notes Administratives') }}</h3>
                <div class="mt-1 prose max-w-none text-gray-700 bg-yellow-50 p-3 rounded">
                    {!! nl2br(e($registration->notes)) !!}
                </div>
            </div>
            @endif

            {{-- @can('delete', $registration) --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <form action="{{ route('admin.event-registrations.destroy', $registration) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription pour \'{{ addslashes($registration->name) }}\' ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        {{ __('Supprimer cette Inscription') }}
                    </button>
                </form>
            </div>
            {{-- @endcan --}}
        </div>
    </div>
@endsection
@extends('layouts.admin')

@php
    $eventTitle = $registration->event ? Str::limit($registration->event->getTranslation('title', app()->getLocale(), false), 40) : __('Événement Inconnu');
@endphp

@section('title', __('Détails de l\'Inscription de') . ' ' . $registration->name . ' - ' . $eventTitle)

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails de l\'Inscription') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ __('Participant:') }} <strong class="font-medium text-gray-700 dark:text-gray-200">{{ $registration->name }}</strong><br>
                {{ __('Pour l\'événement:') }} 
                @if($registration->event)
                <a href="{{ route('admin.events.show', $registration->event) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                    {{ $registration->event->getTranslation('title', app()->getLocale(), false) }}
                </a>
                @else
                {{ __('Événement non spécifié') }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.events.registrations.index', $registration->event_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour aux Inscriptions') }}
            </a>
            @can('manage event_registrations')
            <a href="{{ route('admin.event-registrations.edit', $registration->id) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
            <div class="md:col-span-2 space-y-5">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nom du Participant') }}</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $registration->name }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Email') }}</h3>
                    <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">{{ $registration->email }}</p>
                </div>
                @if($registration->phone_number)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Téléphone') }}</h3>
                    <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">{{ $registration->phone_number }}</p>
                </div>
                @endif
                @if($registration->organization)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Organisation') }}</h3>
                    <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">{{ $registration->organization }}</p>
                </div>
                @endif
                 @if($registration->user_id && $registration->user)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Compte Utilisateur Associé') }}</h3>
                    <p class="mt-1 text-gray-700 dark:text-gray-200">
                        <a href="{{ route('admin.users.show', $registration->user->id) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                            {{ $registration->user->name }} ({{ $registration->user->email }})
                        </a>
                    </p>
                </div>
                @endif
            </div>

            <div class="md:col-span-1 space-y-5">
                 <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Statut') }}</h3>
                     @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
                            'cancelled_by_user' => 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                            'attended' => 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
                        ];
                        $statusDisplay = $statuses[$registration->status] ?? Str::title(str_replace('_', ' ', $registration->status));
                    @endphp
                    <p class="mt-1"><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClasses[$registration->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">{{ $statusDisplay }}</span></p>
                </div>
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Date d\'Inscription') }}</h3>
                    <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">{{ $registration->registered_at ? $registration->registered_at->translatedFormat('d F Y \à H:i') : __('N/A') }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID Inscription') }}</h3>
                    <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">#{{ $registration->id }}</p>
                </div>
            </div>
        </div>

        @if($registration->motivation)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Motivation du participant') }}</h3>
            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-200">{!! nl2br(e($registration->motivation)) !!}</div>
        </div>
        @endif

        @if($registration->notes)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Notes Administratives') }}</h3>
            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 bg-yellow-50 dark:bg-yellow-700/20 p-3 rounded-md">{!! nl2br(e($registration->notes)) !!}</div>
        </div>
        @endif

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap justify-start gap-3">
            @can('manage event_registrations')
            <form action="{{ route('admin.event-registrations.destroy', $registration->id) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette inscription pour :name ? Ceci est irréversible.', ['name' => addslashes($registration->name)]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer cette Inscription') }}
                </button>
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection
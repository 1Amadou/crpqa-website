@extends('layouts.admin')

@section('title', __('Modifier l\'Inscription de') . ' ' . $registration->name)

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Modifier l\'Inscription de :') }} 
                <span class="text-primary-600 dark:text-primary-400">{{ $registration->name }}</span>
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ __('Pour l\'événement :') }} 
                <a href="{{ route('admin.events.show', $registration->event_id) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                    {{ Str::limit($registration->event->getTranslation('title', app()->getLocale(), false), 50) }}
                </a>
            </p>
        </div>
        <a href="{{ route('admin.events.registrations.index', $registration->event_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 shadow-sm transition ease-in-out duration-150">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            {{ __('Annuler et Retourner aux Inscriptions') }}
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-700/50 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600 rounded-md">
                <strong class="font-bold">{{ __('Oups ! Il y a eu quelques problèmes.') }}</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.event-registrations.update', $registration->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Nom du Participant') }}</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white font-semibold">{{ $registration->name }}</p>
                    </div>
                    <div>
                        <p class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                        <p class="mt-1 text-lg text-gray-700 dark:text-gray-200">{{ $registration->email }}</p>
                    </div>
                    @if($registration->phone_number)
                    <div>
                        <p class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Téléphone') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-200">{{ $registration->phone_number }}</p>
                    </div>
                    @endif
                    @if($registration->organization)
                    <div>
                        <p class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Organisation') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-200">{{ $registration->organization }}</p>
                    </div>
                    @endif
                </div>

                @if($registration->motivation)
                <div class="pt-4">
                    <p class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Motivation') }}</p>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $registration->motivation }}</p>
                </div>
                @endif

                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Changer le Statut') }} <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md @error('status') border-red-500 @enderror">
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ (old('status', $registration->status) == $key) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lier à un Compte Utilisateur (Optionnel)') }}</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('user_id') border-red-500 @enderror">
                            <option value="">-- {{ __('Aucun') }} --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ old('user_id', $registration->user_id) == $id ? 'selected' : '' }}>{{ $name }} ({{ \App\Models\User::find($id)->email ?? '' }})</option>
                            @endforeach
                        </select>
                         @error('user_id') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div>
                    <label for="registered_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date d\'inscription') }}</label>
                    <input type="datetime-local" name="registered_at" id="registered_at" 
                           value="{{ old('registered_at', $registration->registered_at ? $registration->registered_at->format('Y-m-d\TH:i') : '') }}" 
                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('registered_at') border-red-500 @enderror">
                    @error('registered_at') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Notes Administratives (Optionnel)') }}</label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('notes') border-red-500 @enderror">{{ old('notes', $registration->notes) }}</textarea>
                    @error('notes') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <a href="{{ route('admin.events.registrations.index', $registration->event_id) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">{{ __('Annuler') }}</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Mettre à Jour l\'Inscription') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
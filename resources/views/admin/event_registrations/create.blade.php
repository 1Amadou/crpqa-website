@extends('layouts.admin')

@section('title', __('Ajouter une Inscription Manuelle') . ' - ' . Str::limit($event->getTranslation('title', app()->getLocale(), false), 30))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Ajouter une Inscription Manuelle') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ __('Pour l\'événement :') }} 
                <a href="{{ route('admin.events.show', $event->id) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                    {{ $event->getTranslation('title', app()->getLocale(), false) }}
                </a>
            </p>
        </div>
        <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 shadow-sm transition ease-in-out duration-150">
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
                <strong class="font-bold">{{ __('Oups ! Il y a eu quelques problèmes avec votre soumission.') }}</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.events.registrations.store', $event->id) }}">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom complet du Participant') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Adresse Email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Numéro de Téléphone') }}</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" 
                               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('phone_number') border-red-500 @enderror">
                        @error('phone_number') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Organisation') }}</label>
                        <input type="text" name="organization" id="organization" value="{{ old('organization') }}" 
                               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('organization') border-red-500 @enderror">
                        @error('organization') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div>
                    <label for="motivation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Motivation (Optionnel)') }}</label>
                    <textarea name="motivation" id="motivation" rows="3" 
                              class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('motivation') border-red-500 @enderror">{{ old('motivation') }}</textarea>
                    @error('motivation') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Statut Initial') }} <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md @error('status') border-red-500 @enderror">
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ (old('status', 'approved') == $key) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lier à un Compte Utilisateur (Optionnel)') }}</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('user_id') border-red-500 @enderror">
                            <option value="">-- {{ __('Aucun') }} --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }} ({{ \App\Models\User::find($id)->email ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div>
                    <label for="registered_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date d\'inscription (Optionnel, par défaut maintenant)') }}</label>
                    <input type="datetime-local" name="registered_at" id="registered_at" value="{{ old('registered_at') }}" 
                           class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('registered_at') border-red-500 @enderror">
                    @error('registered_at') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Notes Administratives (Optionnel)') }}</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4">{{ __('Annuler') }}</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-xs leading-tight uppercase rounded shadow-md hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{ __('Enregistrer l\'Inscription') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
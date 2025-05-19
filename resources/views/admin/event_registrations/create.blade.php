@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ajouter une Inscription Manuelle') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Pour l'événement : <a href="{{ route('admin.events.show', $event) }}" class="text-blue-600 hover:underline">{{ Str::limit($event->title, 50) }}</a>
            </p>
        </div>
        <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            {{ __('Annuler et Retourner aux Inscriptions') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md">
                    <strong class="font-bold">{{ __('Oups ! Il y a eu quelques problèmes.') }}</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.events.registrations.store', $event) }}">
                @csrf
                <div class="space-y-6">
                    {{-- Nom du Participant --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom du Participant') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">{{ __('Téléphone (Optionnel)') }}</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('phone_number') border-red-500 @enderror">
                        @error('phone_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Organisation --}}
                    <div>
                        <label for="organization" class="block text-sm font-medium text-gray-700">{{ __('Organisation (Optionnel)') }}</label>
                        <input type="text" name="organization" id="organization" value="{{ old('organization') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('organization') border-red-500 @enderror">
                        @error('organization') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Motivation --}}
                    <div>
                        <label for="motivation" class="block text-sm font-medium text-gray-700">{{ __('Motivation (Optionnel)') }}</label>
                        <textarea name="motivation" id="motivation" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('motivation') border-red-500 @enderror">{{ old('motivation') }}</textarea>
                        @error('motivation') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Statut --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Statut Initial') }} <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('status') border-red-500 @enderror">
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}" {{ (old('status', 'approved') == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Lier à un utilisateur (Optionnel) --}}
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">{{ __('Lier à un Compte Utilisateur (Optionnel)') }}</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('user_id') border-red-500 @enderror">
                                <option value="">-- Aucun --</option>
                                @foreach($users as $id => $name)
                                    <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                             @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                     {{-- Date d'inscription (Optionnel, par défaut maintenant) --}}
                    <div>
                        <label for="registered_at" class="block text-sm font-medium text-gray-700">{{ __('Date d\'inscription (Optionnel, par défaut maintenant)') }}</label>
                        <input type="datetime-local" name="registered_at" id="registered_at" value="{{ old('registered_at') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('registered_at') border-red-500 @enderror">
                        @error('registered_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>


                    {{-- Notes Administratives --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Notes Administratives (Optionnel)') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('Enregistrer l\'Inscription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier l\'Inscription de :') }} <span class="text-blue-600">{{ $registration->name }}</span>
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Pour l'événement : <a href="{{ route('admin.events.show', $registration->event) }}" class="text-blue-600 hover:underline">{{ Str::limit($registration->event->title, 50) }}</a>
            </p>
        </div>
         <a href="{{ route('admin.events.registrations.index', $registration->event_id) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
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

            <form method="POST" action="{{ route('admin.event-registrations.update', $registration) }}">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- Nom du Participant (non modifiable ici, généralement) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('Nom du Participant') }}</label>
                        <p class="mt-1 text-lg text-gray-900 font-semibold">{{ $registration->name }}</p>
                    </div>

                    {{-- Email (non modifiable ici, généralement) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('Email') }}</label>
                        <p class="mt-1 text-lg text-gray-900 font-semibold">{{ $registration->email }}</p>
                    </div>
                    
                    {{-- Informations de contact et organisation (peuvent être rendues modifiables si besoin) --}}
                     @if($registration->phone_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('Téléphone') }}</label>
                        <p class="mt-1 text-md text-gray-800">{{ $registration->phone_number }}</p>
                    </div>
                    @endif
                     @if($registration->organization)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('Organisation') }}</label>
                        <p class="mt-1 text-md text-gray-800">{{ $registration->organization }}</p>
                    </div>
                    @endif
                    @if($registration->motivation)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('Motivation') }}</label>
                        <p class="mt-1 text-md text-gray-800 whitespace-pre-line">{{ $registration->motivation }}</p>
                    </div>
                    @endif


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Statut --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Changer le Statut') }} <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('status') border-red-500 @enderror">
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}" {{ (old('status', $registration->status) == $key) ? 'selected' : '' }}>{{ $value }}</option>
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
                                    <option value="{{ $id }}" {{ old('user_id', $registration->user_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                             @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    {{-- Date d'inscription (peut être modifiable) --}}
                    <div>
                        <label for="registered_at" class="block text-sm font-medium text-gray-700">{{ __('Date d\'inscription') }}</label>
                        <input type="datetime-local" name="registered_at" id="registered_at" value="{{ old('registered_at', $registration->registered_at ? $registration->registered_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('registered_at') border-red-500 @enderror">
                        @error('registered_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>


                    {{-- Notes Administratives --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Notes Administratives (Optionnel)') }}</label>
                        <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('notes') border-red-500 @enderror">{{ old('notes', $registration->notes) }}</textarea>
                        @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('Mettre à Jour l\'Inscription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
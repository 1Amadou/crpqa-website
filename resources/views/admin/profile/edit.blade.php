@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Mon Profil') }}
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

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

            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PATCH') {{-- Ou PUT, PATCH est souvent utilisé pour les mises à jour partielles --}}

                <div class="space-y-6">
                    {{-- Nom --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom complet') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Adresse Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <hr class="my-4">
                    <p class="text-sm font-medium text-gray-700">{{ __('Changer le mot de passe (optionnel)') }}</p>

                    {{-- Nouveau Mot de passe --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Nouveau mot de passe') }}</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Laissez vide si vous ne souhaitez pas changer le mot de passe.') }}</p>
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirmation du Nouveau Mot de passe --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirmer le nouveau mot de passe') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Mettre à jour le Profil') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
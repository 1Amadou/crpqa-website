@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'Utilisateur') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ $user->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
            @can('manage users') {{-- Ou 'edit users' --}}
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Modifier cet utilisateur') }}
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200 space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Nom') }}</h3>
                <p class="mt-1 text-md text-gray-700">{{ $user->name }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Email') }}</h3>
                <p class="mt-1 text-md text-gray-700">{{ $user->email }}</p>
            </div>
             <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Rôles') }}</h3>
                <div class="mt-1 space-x-2">
                    @forelse ($user->getRoleNames() as $role)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-sky-100 text-sky-800">
                            {{ $role }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500">Aucun rôle assigné.</p>
                    @endforelse
                </div>
            </div>

            @if ($user->researcher)
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Profil Chercheur Associé') }}</h3>
                <p class="mt-1 text-md text-gray-700">
                    <a href="{{ route('admin.researchers.show', $user->researcher->id) }}" class="text-blue-600 hover:underline">
                        {{ $user->researcher->getFullNameAttribute() }} (ID: {{ $user->researcher->id }})
                    </a>
                </p>
            </div>
            @endif

            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Date de création du compte') }}</h3>
                <p class="mt-1 text-md text-gray-700">{{ $user->created_at->format('d/m/Y \à H:i:s') }}</p>
            </div>
             <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Dernière modification') }}</h3>
                <p class="mt-1 text-md text-gray-700">{{ $user->updated_at->format('d/m/Y \à H:i:s') }}</p>
            </div>

            @if(auth()->id() !== $user->id)
            <div class="pt-8 mt-2 border-t border-gray-200 flex flex-wrap justify-start gap-3">
                @can('manage users') {{-- Ou 'delete users' --}}
                 <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur : \'{{ addslashes($user->name) }}\' ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                        {{ __('Supprimer cet utilisateur') }}
                    </button>
                </form>
                @endcan
            </div>
            @endif
        </div>
    </div>
@endsection
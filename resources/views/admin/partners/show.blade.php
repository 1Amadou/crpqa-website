@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Partenaire') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($partner->name, 80) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.partners.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour à la liste') }}
            </a>
            <a href="{{ route('admin.partners.edit', $partner) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                {{ __('Modifier ce partenaire') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                {{-- Colonne Informations Principales --}}
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Nom du Partenaire') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $partner->name }}</p>
                    </div>

                    @if($partner->description)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Description') }}</h3>
                        <div class="mt-1 prose max-w-none text-gray-700">
                            {!! nl2br(e($partner->description)) !!}
                        </div>
                    </div>
                    @endif

                    @if($partner->type)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Type de Partenaire') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $partner->type }}</p>
                    </div>
                    @endif

                    @if($partner->website_url)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Site Web') }}</h3>
                        <p class="mt-1 text-md text-blue-600 hover:text-blue-800">
                            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer">{{ $partner->website_url }}</a>
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Colonne Logo et Informations Secondaires --}}
                <div class="md:col-span-1 space-y-6">
                    @if($partner->logo_path && Storage::disk('public')->exists($partner->logo_path))
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('Logo') }}</h3>
                        <img src="{{ Storage::url($partner->logo_path) }}" alt="Logo de {{ $partner->name }}" class="w-full h-auto object-contain rounded-md shadow-md border">
                    </div>
                    @else
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('Logo') }}</h3>
                        <p class="text-sm text-gray-500">Aucun logo fourni.</p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Statut') }}</h3>
                        <p class="mt-1">
                            @if($partner->is_active)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactif</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Ordre d\'affichage') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $partner->display_order }}</p>
                    </div>
                     <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Date de création') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $partner->created_at->format('d/m/Y \à H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-gray-200 flex flex-wrap justify-start gap-3">
                 <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire : \'{{ addslashes(Str::limit($partner->name, 30)) }}\' ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Supprimer ce partenaire') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
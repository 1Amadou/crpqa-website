@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Domaine de Recherche') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($researchAxis->name, 70) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.research-axes.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm">
                {{ __('Retour à la liste') }}
            </a>
            {{-- @can('update', $researchAxis) --}}
            <a href="{{ route('admin.research-axes.edit', $researchAxis) }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm">
                {{ __('Modifier') }}
            </a>
            {{-- @endcan --}}
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-4">
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Nom du Domaine') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $researchAxis->name }}</p>
                    </div>
                     @if($researchAxis->subtitle)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Sous-titre') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $researchAxis->subtitle }}</p>
                    </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Slug') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 bg-gray-100 p-2 rounded break-all">{{ $researchAxis->slug }}</p>
                    </div>
                     @if($researchAxis->icon_class)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Icône (Classe)') }}</h3>
                        <p class="mt-1 text-md text-gray-700"><ion-icon name="{{ $researchAxis->icon_class }}" class="text-2xl text-blue-600"></ion-icon> ({{ $researchAxis->icon_class }})</p>
                    </div>
                    @endif
                </div>
                <div class="space-y-4">
                     @if($researchAxis->cover_image_path && Storage::disk('public')->exists($researchAxis->cover_image_path))
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('Image de Couverture') }}</h3>
                        <img src="{{ Storage::url($researchAxis->cover_image_path) }}" alt="Image de couverture pour {{ $researchAxis->name }}" class="w-full h-auto object-cover rounded-md shadow-md border">
                    </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Statut') }}</h3>
                        <p class="mt-1">
                            @if($researchAxis->is_active)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactif</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Ordre d\'affichage') }}</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $researchAxis->display_order }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Description détaillée') }}</h3>
                <div class="mt-1 prose max-w-none text-gray-700">
                    {!! $researchAxis->description !!} {{-- Sera rendu par TinyMCE --}}
                </div>
            </div>

            @if($researchAxis->meta_title || $researchAxis->meta_description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 mb-2">{{ __('Informations SEO') }}</h3>
                @if($researchAxis->meta_title)
                <div>
                    <h4 class="text-sm font-medium text-gray-500">{{ __('Méta Titre') }}</h4>
                    <p class="mt-1 text-md text-gray-700">{{ $researchAxis->meta_title }}</p>
                </div>
                @endif
                @if($researchAxis->meta_description)
                <div class="mt-2">
                    <h4 class="text-sm font-medium text-gray-500">{{ __('Méta Description') }}</h4>
                    <p class="mt-1 text-md text-gray-700 whitespace-pre-line">{{ $researchAxis->meta_description }}</p>
                </div>
                @endif
            </div>
            @endif

             <div class="mt-8 pt-6 border-t border-gray-200">
                {{-- @can('delete', $researchAxis) --}}
                 <form action="{{ route('admin.research-axes.destroy', $researchAxis) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce domaine de recherche : \'{{ addslashes($researchAxis->name) }}\' ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        {{ __('Supprimer ce Domaine de Recherche') }}
                    </button>
                </form>
                {{-- @endcan --}}
            </div>
        </div>
    </div>
@endsection
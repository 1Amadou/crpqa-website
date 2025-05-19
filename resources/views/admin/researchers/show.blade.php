@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil du Chercheur :') }} <span class="italic">{{ $researcher->first_name }} {{ $researcher->last_name }}</span>
        </h2>
        <div>
            <a href="{{ route('admin.researchers.edit', $researcher) }}" class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700 text-sm font-medium mr-2 shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                {{ __('Modifier ce Profil') }}
            </a>
            <a href="{{ route('admin.researchers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                {{-- Section Photo --}}
                <div class="flex-shrink-0 w-full lg:w-1/3 text-center lg:text-left">
                    @if($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path))
                        <img src="{{ Storage::url($researcher->photo_path) }}" alt="Photo de {{ $researcher->first_name }} {{ $researcher->last_name }}" class="w-48 h-48 md:w-56 md:h-56 rounded-full object-cover shadow-lg mx-auto lg:mx-0 border-4 border-white">
                    @else
                        <div class="w-48 h-48 md:w-56 md:h-56 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 shadow-lg mx-auto lg:mx-0 border-4 border-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                    <div class="mt-4 text-center lg:text-left">
                         @if($researcher->is_active)
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">Profil Actif</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">Profil Inactif</span>
                        @endif
                    </div>
                </div>

                {{-- Section Informations Principales --}}
                <div class="flex-grow mt-6 lg:mt-0">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $researcher->title ? $researcher->title . ' ' : '' }}{{ $researcher->first_name }} {{ $researcher->last_name }}</h3>
                    @if($researcher->position)
                        <p class="mt-1 text-md text-indigo-600 font-semibold">{{ $researcher->position }}</p>
                    @endif

                    <dl class="mt-5 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        @if($researcher->email)
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900"><a href="mailto:{{ $researcher->email }}" class="hover:underline">{{ $researcher->email }}</a></dd>
                        </div>
                        @endif
                        @if($researcher->phone_number)
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $researcher->phone_number }}</dd>
                        </div>
                        @endif
                        @if($researcher->linkedin_url)
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Profil LinkedIn</dt>
                            <dd class="mt-1 text-sm text-gray-900"><a href="{{ $researcher->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">Voir le profil</a></dd>
                        </div>
                        @endif
                        @if($researcher->google_scholar_url)
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Profil Google Scholar</dt>
                            <dd class="mt-1 text-sm text-gray-900"><a href="{{ $researcher->google_scholar_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">Voir le profil</a></dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($researcher->research_areas)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-medium text-gray-900 mb-2">Domaines de Recherche</h4>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $researcher->research_areas }}</p>
            </div>
            @endif

            @if($researcher->biography)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-medium text-gray-900 mb-2">Biographie / Présentation</h4>
                {{-- nl2br pour les sauts de ligne, e() pour la sécurité si le contenu est du texte simple --}}
                {{-- Si la biographie peut contenir du HTML intentionnel (ex: depuis un éditeur riche), utiliser {!! $researcher->biography !!} avec prudence --}}
                <div class="text-sm text-gray-700 prose prose-sm max-w-none whitespace-pre-line">{!! nl2br(e($researcher->biography)) !!}</div>
            </div>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-200 text-xs text-gray-500">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <span>Ordre d'affichage : {{ $researcher->display_order }}</span>
                    <span>Profil créé le : {{ $researcher->created_at->format('d/m/Y à H:i') }}</span>
                    <span>Dernière mise à jour : {{ $researcher->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
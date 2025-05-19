@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Publication') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($publication->title, 80) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.publications.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour à la liste') }}
            </a>
            @can('update', $publication)
            <a href="{{ route('admin.publications.edit', $publication) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                {{ __('Modifier cette publication') }}
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            {{-- Titre --}}
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Titre') }}</h3>
                <p class="mt-1 text-md text-gray-700">{{ $publication->title }}</p>
            </div>

            {{-- Slug --}}
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Slug') }}</h3>
                <p class="mt-1 text-sm text-gray-500 bg-gray-100 p-2 rounded break-all">{{ $publication->slug }}</p>
            </div>
            
            {{-- Auteurs Internes --}}
            @if ($publication->researchers->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Auteurs Internes (CRPQA)') }}</h3>
                <div class="mt-1">
                    @foreach($publication->researchers as $researcher)
                        <span class="inline-block bg-indigo-100 text-indigo-700 text-sm font-medium mr-2 mb-2 px-2.5 py-0.5 rounded-full">{{ $researcher->getFullNameAttribute() }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Auteurs Externes --}}
            @if($publication->authors_external)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Auteurs Externes') }}</h3>
                <div class="mt-1 text-md text-gray-700 whitespace-pre-line">{{ $publication->authors_external }}</div>
            </div>
            @endif

            {{-- Résumé --}}
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Résumé (Abstract)') }}</h3>
                <div class="mt-1 prose max-w-none text-gray-700">
                    {!! nl2br(e($publication->abstract)) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Date de Publication --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Date de Publication') }}</h3>
                    <p class="mt-1 text-md text-gray-700">{{ $publication->publication_date ? \Carbon\Carbon::parse($publication->publication_date)->format('d F Y') : 'N/A' }}</p>
                </div>

                {{-- Type de Publication --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Type') }}</h3>
                    <p class="mt-1 text-md text-gray-700">{{ $publicationTypeDisplay ?? $publication->type }}</p>
                </div>
            </div>

            {{-- Informations additionnelles --}}
            <div class="mb-6 p-4 border border-dashed border-gray-300 rounded-md bg-slate-50">
                <p class="text-sm font-medium text-gray-700 mb-2">Informations Additionnelles :</p>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    @if($publication->journal_name)
                        <div class="py-1"><dt class="font-medium text-gray-500 inline">Revue/Journal : </dt><dd class="text-gray-700 inline">{{ $publication->journal_name }}</dd></div>
                    @endif
                    @if($publication->conference_name)
                         <div class="py-1"><dt class="font-medium text-gray-500 inline">Conférence : </dt><dd class="text-gray-700 inline">{{ $publication->conference_name }}</dd></div>
                    @endif
                    @if($publication->volume)
                         <div class="py-1"><dt class="font-medium text-gray-500 inline">Volume : </dt><dd class="text-gray-700 inline">{{ $publication->volume }}</dd></div>
                    @endif
                    @if($publication->issue)
                         <div class="py-1"><dt class="font-medium text-gray-500 inline">Numéro/Issue : </dt><dd class="text-gray-700 inline">{{ $publication->issue }}</dd></div>
                    @endif
                    @if($publication->pages)
                         <div class="py-1"><dt class="font-medium text-gray-500 inline">Pages : </dt><dd class="text-gray-700 inline">{{ $publication->pages }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Lien DOI --}}
                @if($publication->doi_url)
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Lien DOI') }}</h3>
                    <p class="mt-1 text-md text-blue-600 hover:text-blue-800 break-all">
                        <a href="{{ $publication->doi_url }}" target="_blank" rel="noopener noreferrer">{{ $publication->doi_url }}</a>
                    </p>
                </div>
                @endif

                {{-- Lien Externe --}}
                @if($publication->external_url)
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Lien Externe') }}</h3>
                    <p class="mt-1 text-md text-blue-600 hover:text-blue-800 break-all">
                        <a href="{{ $publication->external_url }}" target="_blank" rel="noopener noreferrer">{{ $publication->external_url }}</a>
                    </p>
                </div>
                @endif
            </div>
            
            {{-- Fichier PDF --}}
            @if($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path))
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Fichier PDF') }}</h3>
                <p class="mt-1">
                    <a href="{{ Storage::url($publication->pdf_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Télécharger / Voir le PDF ({{ Str::afterLast($publication->pdf_path, '/') }})
                    </a>
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 {{-- En Vedette --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Mise en Vedette') }}</h3>
                    <p class="mt-1 text-md text-gray-700">{{ $publication->is_featured ? 'Oui (⭐)' : 'Non' }}</p>
                </div>

                {{-- Créé par --}}
                @if($publication->creator)
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Entrée créée par') }}</h3>
                    <p class="mt-1 text-md text-gray-700">{{ $publication->creator->name }} ({{ $publication->created_at->format('d/m/Y H:i') }})</p>
                </div>
                @endif
            </div>


            @can('delete', $publication)
            <div class="pt-8 mt-2 border-t border-gray-200 flex flex-wrap justify-start gap-3">
                 <form action="{{ route('admin.publications.destroy', $publication) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette publication : \'{{ addslashes(Str::limit($publication->title, 30)) }}\' ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ __('Supprimer cette publication') }}
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>
@endsection
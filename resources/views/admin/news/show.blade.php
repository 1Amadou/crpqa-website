@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'Actualité') }}
        </h2>
        <div>
            <a href="{{ route('admin.news.edit', $newsItem) }}" class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700 text-sm font-medium mr-2 shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                {{ __('Modifier') }}
            </a>
            <a href="{{ route('admin.news.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 md:p-8">

            {{-- Image de couverture --}}
            @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ Storage::url($newsItem->cover_image_path) }}" alt="Image de couverture pour {{ $newsItem->title }}" class="w-full h-auto max-h-[400px] object-cover">
                </div>
            @endif

            {{-- Titre --}}
            <h3 class="text-3xl font-bold text-gray-900 mb-3 break-words leading-tight">{{ $newsItem->title }}</h3>

            {{-- Métadonnées : Auteur, Statut de publication, En vedette --}}
            <div class="mb-6 text-sm text-gray-500 border-b border-t py-3 space-y-1 md:space-y-0 md:flex md:items-center md:space-x-6">
                <div>
                    <span class="font-semibold">Auteur :</span>
                    <span class="text-gray-700">{{ $newsItem->user->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-semibold">Statut :</span>
                    @if($newsItem->published_at && \Carbon\Carbon::parse($newsItem->published_at)->isFuture())
                        <span class="font-medium text-blue-600">Planifiée pour le {{ \Carbon\Carbon::parse($newsItem->published_at)->isoFormat('LL à HH[h]mm') }}</span>
                    @elseif($newsItem->published_at)
                        <span class="font-medium text-green-600">Publiée le {{ \Carbon\Carbon::parse($newsItem->published_at)->isoFormat('LL') }}</span>
                    @else
                        <span class="font-medium text-gray-600">Brouillon</span>
                    @endif
                </div>
                @if($newsItem->is_featured)
                    <div>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 border border-amber-200">⭐ En Vedette</span>
                    </div>
                @endif
            </div>

            {{-- Résumé --}}
            @if($newsItem->summary)
                <div class="mb-6 p-4 bg-slate-50 rounded-md border border-slate-200 shadow-sm">
                    <h4 class="text-md font-semibold text-gray-700 mb-1">{{ __('Résumé') }}</h4>
                    <p class="text-sm text-gray-800 whitespace-pre-line">{!! nl2br(e($newsItem->summary)) !!}</p>
                </div>
            @endif

            {{-- Contenu Complet --}}
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-700 mb-2">{{ __('Contenu Complet') }}</h4>
                <div class="prose prose-sm lg:prose-base max-w-none text-gray-800 whitespace-pre-line">
                    {{-- Si le contenu est du HTML sûr (ex: d'un éditeur WYSIWYG), utiliser {!! $newsItem->content !!} --}}
                    {{-- Sinon, pour du texte simple avec des sauts de ligne : --}}
                    {!! nl2br(e($newsItem->content)) !!}
                </div>
            </div>

            {{-- Pied de page : Slug, Dates de création/modification --}}
            <div class="mt-8 pt-6 border-t border-gray-200 text-xs text-gray-500 space-y-1">
                <p><span class="font-semibold">Slug :</span> <span class="font-mono bg-gray-100 px-1 py-0.5 rounded">{{ $newsItem->slug }}</span></p>
                <p>Actualité créée le : {{ $newsItem->created_at->isoFormat('LLLL') }}</p>
                <p>Dernière mise à jour : {{ $newsItem->updated_at->isoFormat('LLLL') }}</p>
            </div>
        </div>
    </div>
@endsection
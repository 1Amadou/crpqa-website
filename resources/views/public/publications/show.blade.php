@extends('layouts.public')

@section('title', $metaTitle ?? $publication->title)
@section('meta_description', $metaDescription ?? Str::limit(strip_tags($publication->abstract), 160))

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="prose lg:prose-xl max-w-none mx-auto bg-white p-6 md:p-8 shadow-lg rounded-lg">
            <p class="text-sm text-gray-500">{{ $publicationTypeDisplay }} - Publié le : {{ \Carbon\Carbon::parse($publication->publication_date)->format('d F Y') }}</p>
            <h1 class="text-3xl md:text-4xl font-bold mb-4 mt-2">{{ $publication->title }}</h1>

            @if($publication->researchers->isNotEmpty() || $publication->authors_external)
            <div class="mb-6 text-sm text-gray-700">
                <strong class="font-semibold">Auteurs :</strong>
                @if($publication->researchers->isNotEmpty())
                    {{ $publication->researchers->pluck('full_name')->join(', ') }}
                    @if($publication->authors_external) ; @endif
                @endif
                @if($publication->authors_external)
                    {{ $publication->authors_external }}
                @endif
            </div>
            @endif

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Résumé</h2>
                {!! nl2br(e($publication->abstract)) !!}
            </div>

            <div class="text-sm space-y-1 mb-6">
                @if($publication->journal_name)<p><strong>Journal/Revue :</strong> {{ $publication->journal_name }}</p>@endif
                @if($publication->conference_name)<p><strong>Conférence :</strong> {{ $publication->conference_name }}</p>@endif
                @if($publication->volume)<p><strong>Volume :</strong> {{ $publication->volume }}</p>@endif
                @if($publication->issue)<p><strong>Numéro/Issue :</strong> {{ $publication->issue }}</p>@endif
                @if($publication->pages)<p><strong>Pages :</strong> {{ $publication->pages }}</p>@endif
            </div>

            @if($publication->doi_url)
                <p class="mb-2"><strong class="font-semibold">DOI :</strong> <a href="{{ $publication->doi_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $publication->doi_url }}</a></p>
            @endif
            @if($publication->external_url)
                <p class="mb-2"><strong class="font-semibold">Lien Externe :</strong> <a href="{{ $publication->external_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $publication->external_url }}</a></p>
            @endif
            @if($publication->pdf_path && Storage::disk('public')->exists($publication->pdf_path))
                <p class="mt-4 mb-6">
                    <a href="{{ Storage::url($publication->pdf_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Télécharger le PDF
                    </a>
                </p>
            @endif
        </article>
        <div class="mt-8 text-center">
            <a href="{{ route('public.publications.index') }}" class="text-blue-600 hover:underline">&larr; Retour à toutes les publications</a>
        </div>
    </div>
@endsection
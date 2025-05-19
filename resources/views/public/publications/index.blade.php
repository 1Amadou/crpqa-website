@extends('layouts.public')

@section('title', 'Publications - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', 'Découvrez les publications scientifiques du CRPQA.')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl md:text-4xl font-bold mb-8 text-center">Nos Publications</h1>

        @if($publications->count() > 0)
            <div class="space-y-8">
                @foreach($publications as $publicationItem)
                    <article class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <p class="text-xs text-gray-500 mb-1">{{ $publicationItem->type }} - {{ \Carbon\Carbon::parse($publicationItem->publication_date)->format('F Y') }}</p>
                        <h2 class="text-xl font-semibold mb-2 hover:text-cyan-600">
                            <a href="{{ route('public.publications.show', $publicationItem->slug) }}">{{ $publicationItem->title }}</a>
                        </h2>
                        <p class="text-sm text-gray-600 mb-3">
                            @if($publicationItem->researchers->isNotEmpty())
                                Auteurs : {{ $publicationItem->researchers->pluck('full_name')->join(', ') }}
                                @if($publicationItem->authors_external) ; {{ $publicationItem->authors_external }} @endif
                            @elseif($publicationItem->authors_external)
                                Auteurs : {{ $publicationItem->authors_external }}
                            @endif
                        </p>
                        <p class="text-gray-700 text-sm leading-relaxed mb-4">
                            {{ Str::limit(strip_tags($publicationItem->abstract), 250) }}
                        </p>
                        <a href="{{ route('public.publications.show', $publicationItem->slug) }}" class="text-cyan-600 hover:text-cyan-700 font-semibold self-start text-sm">Lire la suite &rarr;</a>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $publications->links() }} {{-- Pagination --}}
            </div>
        @else
            <p class="text-center text-gray-600 py-8">Aucune publication à afficher pour le moment.</p>
        @endif
    </div>
@endsection
@extends('layouts.public')

@section('title', 'Actualités - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', 'Suivez les dernières actualités et annonces du CRPQA.')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl md:text-4xl font-bold mb-8 text-center">Nos Actualités</h1>

        @if($newsItems->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($newsItems as $newsItem)
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                        @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                            <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                <img class="h-56 w-full object-cover" src="{{ Storage::url($newsItem->cover_image_path) }}" alt="{{ $newsItem->title }}">
                            </a>
                        @else
                            <a href="{{ route('public.news.show', $newsItem->slug) }}" class="h-56 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </a>
                        @endif
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-semibold mb-2 hover:text-cyan-600 transition duration-300">
                                <a href="{{ route('public.news.show', $newsItem->slug) }}">{{ Str::limit($newsItem->title, 65) }}</a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-1">{{ $newsItem->published_at->format('d F Y') }}</p>
                            <p class="text-gray-700 text-sm leading-relaxed mb-4 flex-grow">
                                {{ Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 120) }}
                            </p>
                            <a href="{{ route('public.news.show', $newsItem->slug) }}" class="text-cyan-600 hover:text-cyan-700 font-semibold self-start">Lire la suite &rarr;</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $newsItems->links() }} {{-- Pagination --}}
            </div>
        @else
            <p class="text-center text-gray-600 py-8">Aucune actualité à afficher pour le moment.</p>
        @endif
    </div>
@endsection
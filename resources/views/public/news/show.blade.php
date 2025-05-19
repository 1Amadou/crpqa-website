@extends('layouts.public')

@section('title', $metaTitle ?? $newsItem->title)
@section('meta_description', $metaDescription ?? Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 160))

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="prose lg:prose-xl max-w-none mx-auto bg-white p-6 md:p-8 shadow-lg rounded-lg">
            <h1>{{ $newsItem->title }}</h1>
            @if($newsItem->published_at)
                <p class="text-sm text-gray-500">Publié le : {{ $newsItem->published_at->format('d F Y') }}</p>
            @endif
            @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                <img class="my-6 rounded-lg shadow-md" src="{{ Storage::url($newsItem->cover_image_path) }}" alt="{{ $newsItem->title }}">
            @endif
            <div>
                {!! $newsItem->content !!} {{-- Si le contenu est du HTML sûr --}}
            </div>
             @if($newsItem->summary)
                <div class="mt-6 p-4 bg-gray-100 rounded-md">
                    <h3 class="font-semibold text-gray-700">Résumé :</h3>
                    <p class="text-gray-600">{!! nl2br(e($newsItem->summary)) !!}</p>
                </div>
            @endif
        </article>
        <div class="mt-8 text-center">
            <a href="{{ route('public.news.index') }}" class="text-blue-600 hover:underline">&larr; Retour à toutes les actualités</a>
        </div>
    </div>
@endsection
@extends('layouts.public')

@section('title', $metaTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'article')
@if($publication->publication_date)
    @section('article_published_time', $publication->publication_date->toIso8601String())
@endif

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
<style>
    .publication-show-hero { padding-top: calc(var(--header-height, 4rem) + 1rem); padding-bottom: 2rem; color: white; position: relative; background-size:cover; background-position: center; }
    .publication-show-hero__overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.1) 100%); z-index: 0;}
    .publication-show-hero .container { position: relative; z-index: 1; }
    .publication-show-hero__title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 700; line-height: 1.25; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.6); }
    .publication-show-hero__meta { font-size: 0.8rem; opacity: 0.95; }
    .publication-content-section { padding-top: 2.5rem; padding-bottom: 2.5rem; }
    @media(min-width: 768px){ .publication-content-section { padding-top: 3.5rem; padding-bottom: 3.5rem; } }
    .publication-sidebar dt { font-weight: 600; color: var(--title-color); }
    .dark .publication-sidebar dt { color: var(--dark-title-color); }
    .publication-sidebar dd { margin-bottom: 0.75rem; color: var(--text-color-light); }
    .dark .publication-sidebar dd { color: var(--dark-text-color-light); }
    .publication-sidebar .button { width: 100%; justify-content: center; }
    .authors-list li a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
    <main class="main">
        <section class="publication-show-hero section--bg"
                 style="background-image: url('{{ $publication->cover_image_url ?: ($siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/publications_hero_default.jpg')) }}');"
                 data-aos="fade-in">
            <div class="publication-show-hero__overlay"></div>
            <div class="container">
                <div class="publication-show-hero__data max-w-3xl mx-auto text-center py-8 md:py-10">
                     <p class="publication-show-hero__meta text-gray-300 mb-2" data-aos="fade-up" data-aos-delay="100">
                        <span>{{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }}</span>
                        @if($publication->publication_date)
                        <span class="mx-1">&bull;</span>
                        <time datetime="{{ $publication->publication_date->toIso8601String() }}">{{ $publication->publication_date->translatedFormat('d F Y') }}</time>
                        @endif
                    </p>
                    <h1 class="publication-show-hero__title" data-aos="fade-up" data-aos-delay="200">{{ $publication->title }}</h1>
                    <nav aria-label="breadcrumb" class="mt-4" data-aos="fade-up" data-aos-delay="300">
                        <ol class="breadcrumb text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('public.publications.index') }}">{{ __('Publications') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">/ {{ Str::limit($publication->title, 25) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        <section class="publication-content-section bg-white dark:bg-gray-800">
            <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">
                    <div class="lg:col-span-2">
                        @if($publication->abstract)
                        <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700" data-aos="fade-up">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{__('Résumé (Abstract)')}}</h2>
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-justify">
                                {!! nl2br(e($publication->abstract)) !!} {{-- nl2br si texte simple, sinon {!! ... !!} si HTML --}}
                            </div>
                        </div>
                        @endif
                        
                        {{-- Contenu principal si vous l'ajoutez au modèle Publication --}}
                        {{-- @if($publication->full_content)
                        <div class="prose prose-lg lg:prose-xl dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify" data-aos="fade-up" data-aos-delay="100">
                            {!! $publication->full_content !!}
                        </div>
                        @endif --}}

                        @if($publication->keywords)
                        <div class="mt-8" data-aos="fade-up">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{__('Mots-clés :')}}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $publication->keywords) as $keyword)
                                    <span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                         <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700" data-aos="fade-up">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{__('Partager cette publication :')}}</h3>
                            <div class="flex space-x-3">
                                {{-- Liens de partage --}}
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400" aria-label="Partager sur Facebook"><ion-icon name="logo-facebook" class="text-2xl"></ion-icon></a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($publication->title) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-sky-500 dark:text-gray-400 dark:hover:text-sky-400" aria-label="Partager sur Twitter"><ion-icon name="logo-twitter" class="text-2xl"></ion-icon></a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($publication->title) }}&summary={{ urlencode($publication->abstract) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-700 dark:text-gray-400 dark:hover:text-blue-500" aria-label="Partager sur LinkedIn"><ion-icon name="logo-linkedin" class="text-2xl"></ion-icon></a>
                            </div>
                        </div>

                    </div>

                    {{-- Sidebar --}}
                    <aside class="lg:col-span-1 space-y-6" data-aos="fade-left" data-aos-delay="200">
                        <div class="p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">{{__('Détails de Publication')}}</h3>
                            <dl class="space-y-3 text-sm">
                                @if($publication->researchers->isNotEmpty() || $publication->authors_external)
                                <div class="flex flex-col">
                                    <dt class="text-gray-500 dark:text-gray-400 mb-0.5">{{__('Auteurs')}}:</dt>
                                    <dd class="text-gray-700 dark:text-gray-200">
                                        @php
                                            $allAuthors = [];
                                            if ($publication->researchers->isNotEmpty()) { $allAuthors[] = $publication->researchers->pluck('full_name')->join(', '); }
                                            if ($publication->authors_external) { $allAuthors[] = e($publication->authors_external); }
                                        @endphp
                                        {{ implode('; ', $allAuthors) }}
                                    </dd>
                                </div>
                                @endif
                                @if($publication->journal_name)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Journal')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->journal_name }}</dd>
                                </div>
                                @endif
                                 @if($publication->conference_name)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Conférence')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->conference_name }}</dd>
                                </div>
                                @endif
                                @if($publication->volume)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Volume')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->volume }}</dd>
                                </div>
                                @endif
                                @if($publication->issue)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Numéro (Issue)')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->issue }}</dd>
                                </div>
                                @endif
                                @if($publication->pages)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Pages')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->pages }}</dd>
                                </div>
                                @endif
                                @if($publication->publisher)
                                <div class="flex">
                                    <dt class="w-2/5 text-gray-500 dark:text-gray-400 flex-shrink-0">{{__('Éditeur')}}:</dt>
                                    <dd class="w-3/5 text-gray-700 dark:text-gray-200">{{ $publication->publisher }}</dd>
                                </div>
                                @endif
                            </dl>

                            @if($publication->doi_url)
                                <a href="{{ $publication->doi_url }}" target="_blank" rel="noopener noreferrer" class="button button--primary mt-6 w-full text-center">
                                    {{__('Consulter sur DOI')}} <ion-icon name="open-outline" class="ml-2"></ion-icon>
                                </a>
                            @endif
                            @if($publication->getFirstMediaUrl('publication_pdf'))
                                <a href="{{ route('public.publications.download', $publication->slug) }}" {{-- Route à créer --}}
                                   class="button button--outline-primary mt-3 w-full text-center">
                                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2"/> {{__('Télécharger le PDF')}}
                                    @if($publication->getFirstMedia('publication_pdf')->size)
                                        ({{ $publication->getFirstMedia('publication_pdf')->human_readable_size }})
                                    @endif
                                </a>
                            @endif
                        </div>
                        
                        @if($publication->researchers->isNotEmpty())
                        <div class="p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">{{__('Auteurs du CRPQA')}}</h3>
                            <ul class="space-y-3 authors-list">
                                @foreach($publication->researchers as $researcher)
                                <li>
                                    <a href="{{ route('public.researchers.show', $researcher->slug) }}" class="flex items-center space-x-3 group">
                                        <img src="{{ $researcher->photo_thumbnail_url ?? asset('assets/images/placeholders/researcher_default_'.($loop->index%2).'.png') }}" 
                                             alt="{{ $researcher->full_name }}"
                                             class="h-10 w-10 rounded-full object-cover">
                                        <div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $researcher->full_name }}</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $researcher->title_position }}</p>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </aside>
                </div>
            </div>
        </section>

        {{-- Publications Similaires --}}
        @if(isset($relatedPublications) && $relatedPublications->count() > 0)
        <section class="section related-publications-section bg-slate-50 dark:bg-gray-800/50" id="related-publications">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section__title text-center" data-aos="fade-up">{{ __('Publications Similaires') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($relatedPublications as $index => $relatedItem)
                    <article class="section-card publication__card group" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                        <div class="section-card__content">
                           <p class="section-card__meta publication__meta">
                                <span>{{ $relatedItem->type_display ?? Str::title(str_replace('_', ' ', $relatedItem->type)) }}</span>
                                @if($relatedItem->publication_date)<span class="mx-1">&bull;</span> <time datetime="{{ $relatedItem->publication_date->toDateString() }}">{{ $relatedItem->publication_date->translatedFormat('M Y') }}</time>@endif
                            </p>
                            <h3 class="section-card__title text-md publication__title">
                                <a href="{{ route('public.publications.show', $relatedItem->slug) }}">
                                    {{ Str::limit($relatedItem->title, 55) }}
                                </a>
                            </h3>
                             <a href="{{ route('public.publications.show', $relatedItem->slug) }}" class="section-card__link publication__link text-sm mt-auto">
                                {{ __('Lire la suite') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection

@push('scripts')
{{-- Si vous avez des scripts spécifiques pour cette page (ex: lightbox pour images de publication) --}}
@endpush
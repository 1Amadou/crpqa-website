@extends('layouts.public')

@section('title', $metaTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'profile') {{-- Type Open Graph pour un profil --}}

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
<style>
    .researcher-show-hero { padding-top: calc(var(--header-height, 4rem) + 1rem); padding-bottom: 2rem; position: relative; background-color: var(--bg-color-light, #f9fafb); }
    .dark .researcher-show-hero { background-color: var(--dark-bg-color-alt, #1f2937); }
    .researcher-show-hero__photo { width: 10rem; height: 10rem; md:width: 12rem; md:height: 12rem; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: var(--shadow-lg); }
    .dark .researcher-show-hero__photo { border-color: var(--dark-card-bg-color, #374151); }
    .researcher-show-hero__name { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 700; line-height: 1.2; color: var(--title-color); margin-bottom: 0.25rem; }
    .dark .researcher-show-hero__name { color: var(--dark-title-color); }
    .researcher-show-hero__position { font-size: 1.125rem; color: rgb(var(--color-primary)); font-weight: 500; margin-bottom: 1rem; }
    .researcher-show-hero__social-links a { color: var(--text-color-light); hover:color: rgb(var(--color-primary)); font-size: 1.5rem; } /* text-2xl */
    .dark .researcher-show-hero__social-links a { color: var(--dark-text-color-light); hover:color: rgb(var(--color-primary-light)); }
    .content-section { padding-top: 2rem; padding-bottom: 2.5rem; }
    @media(min-width: 768px){ .content-section { padding-top: 3rem; padding-bottom: 3.5rem; } }
    .content-section__title { font-size: 1.5rem; font-weight: 600; color: var(--title-color); margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; }
    .dark .content-section__title { color: var(--dark-title-color); border-color: #4b5563; }
</style>
@endpush

@section('content')
    <main class="main">
        <section class="researcher-show-hero section--bg" data-aos="fade-in">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:gap-8 py-8">
                    <div class="flex-shrink-0 text-center md:text-left mb-6 md:mb-0" data-aos="fade-right">
                        <img src="{{ $researcher->photo_profile_url ?? asset('assets/images/placeholders/researcher_default.png') }}" 
                             alt="{{ $researcher->getTranslation('photo_alt_text', $currentLocale, false) ?: $researcher->full_name }}"
                             class="researcher-show-hero__photo mx-auto md:mx-0">
                    </div>
                    <div class="text-center md:text-left" data-aos="fade-left" data-aos-delay="100">
                        <h1 class="researcher-show-hero__name">{{ $researcher->full_name }}</h1>
                        <p class="researcher-show-hero__position">{{ $researcher->title_position }}</p>
                        @if($researcher->email)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><a href="mailto:{{ $researcher->email }}" class="hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center"><x-heroicon-s-envelope class="w-4 h-4 mr-1.5"/> {{ $researcher->email }}</a></p>
                        @endif
                        @if($researcher->phone)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3"><x-heroicon-s-phone class="w-4 h-4 mr-1.5 inline-block"/> {{ $researcher->phone }}</p>
                        @endif
                        <div class="researcher-show-hero__social-links flex justify-center md:justify-start space-x-4">
                            @if($researcher->website_url)<a href="{{ $researcher->website_url }}" target="_blank" rel="noopener noreferrer" title="{{__('Site Web')}}"><ion-icon name="globe-outline"></ion-icon></a>@endif
                            @if($researcher->linkedin_url)<a href="{{ $researcher->linkedin_url }}" target="_blank" rel="noopener noreferrer" title="LinkedIn"><ion-icon name="logo-linkedin"></ion-icon></a>@endif
                            @if($researcher->researchgate_url)<a href="{{ $researcher->researchgate_url }}" target="_blank" rel="noopener noreferrer" title="ResearchGate"><ion-icon name="navigate-circle-outline"></ion-icon></a>@endif
                            @if($researcher->google_scholar_url)<a href="{{ $researcher->google_scholar_url }}" target="_blank" rel="noopener noreferrer" title="Google Scholar"><ion-icon name="school-outline"></ion-icon></a>@endif
                            @if($researcher->orcid_id)<a href="https://orcid.org/{{ $researcher->orcid_id }}" target="_blank" rel="noopener noreferrer" title="ORCID iD"><ion-icon name="id-card-outline"></ion-icon></a>@endif
                        </div>
                    </div>
                </div>
                 <nav aria-label="breadcrumb" class="mt-4 md:mt-0 pb-4" data-aos="fade-up" data-aos-delay="200">
                    <ol class="breadcrumb !justify-start text-gray-600 dark:text-gray-400">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('public.researchers.index') }}">{{ __('Notre Équipe') }}</a></li>
                        <li class="breadcrumb-item active !text-gray-500 dark:!text-gray-500" aria-current="page">/ {{ Str::limit($researcher->full_name, 25) }}</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="content-section bg-white dark:bg-gray-800">
            <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($researcher->biography)
                <div class="mb-10" data-aos="fade-up">
                    <h2 class="content-section__title">{{__('Biographie')}}</h2>
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify">
                        {!! $researcher->biography !!}
                    </div>
                </div>
                @endif

                @if($researcher->research_interests)
                <div class="mb-10" data-aos="fade-up">
                    <h2 class="content-section__title">{{__('Domaines de Recherche')}}</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify">
                         {!! $researcher->research_interests !!}
                    </div>
                </div>
                @endif

                @if($researcher->publications->isNotEmpty())
                <div data-aos="fade-up">
                    <h2 class="content-section__title">{{__('Publications Récentes')}} ({{ $researcher->publications->count() }})</h2>
                    <div class="space-y-6">
                        @foreach($researcher->publications->take(5) as $publication) {{-- Afficher les 5 plus récentes --}}
                        <article class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow">
                            <h3 class="text-md font-semibold text-primary-600 dark:text-primary-400">
                                <a href="{{ route('public.publications.show', $publication->slug) }}">{{ $publication->title }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>{{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }}</span>
                                @if($publication->publication_date)<span class="mx-1">&bull;</span> {{ $publication->publication_date->translatedFormat('Y') }} @endif
                                @if($publication->journal_name) <span class="mx-1">&bull;</span> <em>{{ $publication->journal_name }}</em> @endif
                            </p>
                            @php
                                $pubShowAuthorsList = [];
                                if ($publication->researchers()->where('researchers.id', '!=', $researcher->id)->exists()) { // Autres auteurs du CRPQA
                                    $otherCrpqaAuthors = $publication->researchers()->where('researchers.id', '!=', $researcher->id)->take(2)->get()->pluck('full_name')->join(', ');
                                    if($otherCrpqaAuthors) $pubShowAuthorsList[] = $otherCrpqaAuthors;
                                    if($publication->researchers()->where('researchers.id', '!=', $researcher->id)->count() > 2) $pubShowAuthorsList[] = 'et al.';
                                }
                                if ($publication->authors_external) { $pubShowAuthorsList[] = e($publication->authors_external); }
                            @endphp
                            @if(!empty($pubShowAuthorsList))
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                               <span class="font-medium">{{__('Co-auteurs (extrait) :')}}</span> {{ Str::limit(implode('; ', $pubShowAuthorsList), 150) }}
                            </p>
                            @endif
                        </article>
                        @endforeach
                        @if($researcher->publications->count() > 5 && Route::has('public.publications.index'))
                            <div class="mt-4">
                                <a href="{{ route('public.publications.index', ['researcher' => $researcher->id]) }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ __('Voir toutes les publications de :name', ['name' => $researcher->first_name]) }} &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </section>
    </main>
@endsection
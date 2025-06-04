@extends('layouts.public')

{{--
    Variables attendues du PublicPageController@about():
    - $page: Instance de la StaticPage (slug 'a-propos'), chargée avec $page->load('media').
    - $siteSettings: Disponible globalement (contient les champs spécifiques pour les sections).
--}}

@php
    $currentLocale = app()->getLocale();
    // Titre spécifique pour le bandeau de cette page "À Propos", fallback sur le titre de la StaticPage
    $aboutPageHeroTitle = $siteSettings->getTranslation('about_page_hero_title', $currentLocale, false) ?: $page->getTranslation('title', $currentLocale, false);
    $aboutPageHeroSubtitle = $siteSettings->getTranslation('about_page_hero_subtitle', $currentLocale, false);
@endphp

@section('title', ($page->getTranslation('meta_title', $currentLocale, false) ?: $page->getTranslation('title', $currentLocale, false)) . ' - ' . ($siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name')))
@section('meta_description', $page->getTranslation('meta_description', $currentLocale, false) ?: Str::limit(strip_tags($siteSettings->getTranslation('about_introduction_content', $currentLocale, false) ?: $page->getTranslation('content', $currentLocale, false) ?: $siteSettings->getTranslation('site_description', $currentLocale, false)), 160))
@section('og_title', $page->getTranslation('meta_title', $currentLocale, false) ?: $page->getTranslation('title', $currentLocale, false))
@section('og_description', $page->getTranslation('meta_description', $currentLocale, false) ?: Str::limit(strip_tags($siteSettings->getTranslation('about_introduction_content', $currentLocale, false) ?: $page->getTranslation('content', $currentLocale, false) ?: $siteSettings->getTranslation('site_description', $currentLocale, false)), 160))
@if($page->cover_image_url)
    @section('og_image', $page->cover_image_url)
@elseif($siteSettings->default_og_image_url)
    @section('og_image', $siteSettings->default_og_image_url)
@endif

@push('styles')
{{-- Styles spécifiques à la page "À Propos" (repris de la proposition précédente) --}}
{{-- Il est fortement recommandé de déplacer ces styles dans vos fichiers CSS compilés --}}
<style>
    .about-hero-public { padding-top: calc(var(--header-height, 4rem) + 2rem); padding-bottom: 3rem; color: white; text-align: center; background-size: cover; background-position: center; }
    .about-hero-public__title { font-size: clamp(2.25rem, 5vw, 3.25rem); font-weight: 800; margin-bottom: 0.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
    .about-hero-public__subtitle { font-size: clamp(1.1rem, 3vw, 1.5rem); font-weight: 300; opacity: 0.9; margin-bottom: 1.5rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .breadcrumb { list-style: none; display: flex; gap: 0.5rem; justify-content: center; font-size:0.875rem; padding:0; }
    .breadcrumb-item a { color: rgba(255,255,255,0.8); text-decoration: none; } .breadcrumb-item a:hover { color: white; }
    .breadcrumb-item.active { color: rgba(255,255,255,0.6); }
    .page-section { padding-top: 3rem; padding-bottom: 3rem; }
    @media (min-width: 768px) { .page-section { padding-top: 4rem; padding-bottom: 4rem; } }
    .page-section__title { font-size: clamp(1.75rem, 4vw, 2.25rem); font-weight: 700; color: var(--title-color, #1f2937); margin-bottom: 2rem; text-align: center; }
    .dark .page-section__title { color: var(--dark-title-color, #f3f4f6); }
    .page-section__subtitle { display: block; text-align: center; font-size: 0.875rem; color: rgb(var(--color-primary, 19, 78, 138)); font-weight: 600; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .timeline { list-style: none; padding: 0; position: relative; max-width: 800px; margin-left:auto; margin-right:auto; }
    .timeline:before { content: ''; position: absolute; top: 0; bottom: 0; width: 4px; background: #e2e8f0; left: 1.5rem; margin-left: -2px; } 
    .dark .timeline:before { background: #4a5568; }
    .timeline-item { margin-bottom: 2.5rem; position: relative; } .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: 1.5rem; top:0; transform: translateX(-50%); background: rgb(var(--color-primary)); color: white; width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 4px var(--body-bg-color, white); z-index:1; }
    .dark .timeline-icon { box-shadow: 0 0 0 4px var(--dark-body-bg-color, #1f2937); }
    .timeline-icon ion-icon, .timeline-icon i { font-size: 1.5rem; }
    .timeline-content { margin-left: 4.5rem; padding: 1.25rem; background: var(--card-bg-color, white); border-radius: var(--radius-md, 0.375rem); border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); }
    .dark .timeline-content { background: var(--dark-card-bg-color, #374151); border-color: #4a5568; }
    .timeline-year { display:block; font-size:0.8rem; color: var(--text-color-light); margin-bottom: 0.25rem; font-weight:500; }
    .timeline-title { font-size:1.125rem; font-weight:600; margin-bottom:0.5rem; color: var(--title-color); }
    .dark .timeline-title { color: var(--dark-title-color); }
    .timeline-description { font-size:0.9rem; line-height:1.6; color: var(--text-color); }
    .dark .timeline-description { color: var(--dark-text-color-dark); }
    .commitment-block { text-align: center; padding: 1.5rem; }
    .commitment-block__icon { font-size: 2.5rem; color: rgb(var(--color-primary)); margin-bottom: 1rem; display: inline-block; }
    .commitment-block__title { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--title-color); }
    .dark .commitment-block__title { color: var(--dark-title-color); }
    .commitment-block__content { font-size: 0.95rem; line-height: 1.6; color: var(--text-color); }
    .dark .commitment-block__content { color: var(--dark-text-color-dark); }
    .commitment-block ul { list-style: none; padding-left: 0; text-align:left; /* pour que les puces soient alignées à gauche dans le bloc */ } .commitment-block ul li { margin-bottom: 0.5rem; }
    .director-section__card { display: flex; flex-direction: column; md:flex-direction: row; align-items: center; md:align-items:start; gap: 2rem; background-color: var(--bg-color-light, #f9fafb); dark:bg-gray-700; padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); }
    .director-section__photo { width: 10rem; height: 10rem; md:w-48 md:h-48; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 4px solid white; box-shadow: var(--shadow-md); }
    .director-section__name { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--title-color); }
    .dark .director-section__name { color: var(--dark-title-color); }
    .director-section__position { font-size: 1rem; color: rgb(var(--color-primary)); margin-bottom: 1rem; font-weight: 500; }
    .director-section__message .prose { max-width: none; }
</style>
@endpush

@section('content')
    <main class="main">
        {{-- 1. Section Hero de la Page "À Propos" --}}
        <section class="about-hero-public section--bg" data-aos="fade-in"
                 style="background-image: linear-gradient(rgba(var(--color-primary-rgb, 19, 78, 138), 0.8), rgba(var(--color-secondary-rgb, 55, 48, 163), 0.9)), url('{{ $page->cover_image_url ?: ($siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/about_hero_default.jpg')) }}');">
            <div class="about-hero__container container">
                <div class="about-hero__data py-10 md:py-16">
                    <h1 class="about-hero__title" data-aos="zoom-in-down">
                        {{ $aboutPageHeroTitle }}
                    </h1>
                    @if($aboutPageHeroSubtitle)
                    <p class="about-hero__subtitle max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                        {{ $aboutPageHeroSubtitle }}
                    </p>
                    @endif
                    <nav aria-label="breadcrumb" class="mt-4" data-aos="fade-up" data-aos-delay="200">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">/ {{ $page->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        {{-- 2. Introduction (depuis SiteSettings) --}}
        @if($siteSettings->getTranslation('about_introduction_title', $currentLocale, false) || $siteSettings->getTranslation('about_introduction_content', $currentLocale, false))
        <section class="page-section bg-white dark:bg-gray-800" id="introduction-about" aria-labelledby="introduction-about-title">
            <div class="container max-w-3xl mx-auto">
                @if($siteSettings->getTranslation('about_introduction_title', $currentLocale, false))
                    <h2 class="page-section__title" id="introduction-about-title" data-aos="fade-up">
                        {{ $siteSettings->getTranslation('about_introduction_title', $currentLocale, false) }}
                    </h2>
                @endif
                @if($siteSettings->getTranslation('about_introduction_content', $currentLocale, false))
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify" data-aos="fade-up" data-aos-delay="100">
                    {!! $siteSettings->getTranslation('about_introduction_content', $currentLocale, false) !!}
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- 3. Contenu principal et plus libre de la StaticPage 'a-propos' --}}
        @if($page->getTranslation('content', $currentLocale, false))
        <section class="page-section-alt container @if(!($siteSettings->getTranslation('about_introduction_title', $currentLocale, false) || $siteSettings->getTranslation('about_introduction_content', $currentLocale, false))) bg-white dark:bg-gray-800 @endif" id="page-main-content-static">
            <div class="prose prose-lg lg:prose-xl dark:prose-invert max-w-3xl mx-auto text-gray-800 dark:text-gray-200 content-styles text-justify">
                {!! $page->getTranslation('content', $currentLocale, false) !!}
            </div>
        </section>
        @endif
        
        {{-- 4. Historique avec Timeline (depuis SiteSettings JSON) --}}
        @php
            $timelineData = $siteSettings->about_history_timeline_json;
            if (is_string($timelineData)) {
                $decodedJsonTimeline = json_decode($timelineData, true);
                $timelineEvents = (json_last_error() === JSON_ERROR_NONE && is_array($decodedJsonTimeline)) ? $decodedJsonTimeline : [];
            } elseif (is_array($timelineData)) {
                $timelineEvents = $timelineData;
            } else { $timelineEvents = []; }
        @endphp
        @if($siteSettings->getTranslation('about_history_title', $currentLocale, false) || !empty($timelineEvents))
        <section class="page-section bg-slate-50 dark:bg-gray-800/50" id="historique-about" aria-labelledby="historique-about-title">
            <div class="container">
                @if($siteSettings->getTranslation('about_history_title', $currentLocale, false))
                    <span class="page-section__subtitle" data-aos="fade-up">{{ __('Notre Parcours') }}</span>
                    <h2 class="page-section__title" id="historique-about-title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->getTranslation('about_history_title', $currentLocale, false) }}</h2>
                @endif
                
                @if(!empty($timelineEvents))
                    <div class="timeline-wrapper mt-8" data-aos="fade-up" data-aos-delay="200">
                        <ul class="timeline">
                            @foreach($timelineEvents as $index => $eventData)
                                @if(is_array($eventData))
                                <li class="timeline-item">
                                    <div class="timeline-icon" data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 100) }}">
                                        <ion-icon name="{{ $eventData['icon'] ?? 'calendar-clear-outline' }}"></ion-icon>
                                    </div>
                                    <div class="timeline-content" data-aos="{{ $index % 2 == 0 ? 'fade-left' : 'fade-right' }}" data-aos-delay="{{ 150 + ($index * 100) }}">
                                        @if(!empty($eventData['year']))<span class="timeline-year">{{ $eventData['year'] }}</span>@endif
                                        <h4 class="timeline-title">{{ $eventData['title_'.$currentLocale] ?? ($eventData['title_fr'] ?? ($eventData['title'] ?? __('Événement Important'))) }}</h4>
                                        <p class="timeline-description">{{ $eventData['description_'.$currentLocale] ?? ($eventData['description_fr'] ?? ($eventData['description'] ?? '')) }}</p>
                                    </div>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @elseif($siteSettings->getTranslation('about_history_title', $currentLocale, false))
                    <p class="text-center text-gray-600 dark:text-gray-400">{{__('Les détails de notre historique seront bientôt disponibles.')}}</p>
                @endif
            </div>
        </section>
        @endif

        {{-- 5. Mission, Vision, Valeurs (depuis SiteSettings) --}}
        @if($siteSettings->getTranslation('about_mission_title', $currentLocale, false) || $siteSettings->getTranslation('about_vision_title', $currentLocale, false) || $siteSettings->getTranslation('about_values_title', $currentLocale, false))
        <section class="page-section container" id="mission-vision-valeurs-about" aria-labelledby="mvv-about-title">
            <span class="page-section__subtitle" data-aos="fade-up">{{ __('Nos Fondations') }}</span>
            <h2 class="page-section__title" id="mvv-about-title" data-aos="fade-up" data-aos-delay="100">{{ __('Nos Engagements Fondamentaux') }}</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @if($siteSettings->getTranslation('about_mission_title', $currentLocale, false) || $siteSettings->getTranslation('about_mission_content', $currentLocale, false))
                <div class="commitment-block" data-aos="fade-up" data-aos-delay="200">
                    @if($siteSettings->about_mission_icon_class)<ion-icon name="{{ $siteSettings->about_mission_icon_class }}" class="commitment-block__icon"></ion-icon>@endif
                    <h3 class="commitment-block__title">{{ $siteSettings->getTranslation('about_mission_title', $currentLocale, false) }}</h3>
                    <div class="commitment-block__content prose prose-sm dark:prose-invert max-w-none text-justify">{!! $siteSettings->getTranslation('about_mission_content', $currentLocale, false) !!}</div>
                </div>
                @endif
                @if($siteSettings->getTranslation('about_vision_title', $currentLocale, false) || $siteSettings->getTranslation('about_vision_content', $currentLocale, false))
                <div class="commitment-block" data-aos="fade-up" data-aos-delay="300">
                     @if($siteSettings->about_vision_icon_class)<ion-icon name="{{ $siteSettings->about_vision_icon_class }}" class="commitment-block__icon"></ion-icon>@endif
                    <h3 class="commitment-block__title">{{ $siteSettings->getTranslation('about_vision_title', $currentLocale, false) }}</h3>
                    <div class="commitment-block__content prose prose-sm dark:prose-invert max-w-none text-justify">{!! $siteSettings->getTranslation('about_vision_content', $currentLocale, false) !!}</div>
                </div>
                @endif
                @php 
                    $valuesListData = $siteSettings->about_values_list_json;
                    if (is_string($valuesListData)) {
                        $decodedJsonVL = json_decode($valuesListData, true);
                        $valuesListToDisplay = (json_last_error() === JSON_ERROR_NONE && is_array($decodedJsonVL)) ? $decodedJsonVL : [];
                    } elseif (is_array($valuesListData)) {
                        $valuesListToDisplay = $valuesListData;
                    } else { $valuesListToDisplay = []; }
                @endphp
                @if($siteSettings->getTranslation('about_values_title', $currentLocale, false) || !empty($valuesListToDisplay) )
                <div class="commitment-block" data-aos="fade-up" data-aos-delay="400">
                    @if($siteSettings->about_values_icon_class)<ion-icon name="{{ $siteSettings->about_values_icon_class }}" class="commitment-block__icon"></ion-icon>@endif
                    <h3 class="commitment-block__title">{{ $siteSettings->getTranslation('about_values_title', $currentLocale, false) }}</h3>
                    @if(!empty($valuesListToDisplay))
                    <ul class="commitment-block__content space-y-1">
                        @foreach ($valuesListToDisplay as $valueItem)
                            @if(is_array($valueItem))
                            <li class="flex items-start">
                                <x-heroicon-s-check-circle class="w-5 h-5 text-primary-500 dark:text-primary-400 mr-2 flex-shrink-0 mt-0.5"/>
                                <span>{{ $valueItem['text_'.$currentLocale] ?? ($valueItem['text_fr'] ?? ($valueItem['text'] ?? __('Valeur'))) }}</span>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- 6. Message du Directeur (depuis SiteSettings) --}}
        @if($siteSettings->getTranslation('about_director_name', $currentLocale, false) || $siteSettings->getTranslation('about_director_message_content', $currentLocale, false))
        <section class="page-section bg-slate-50 dark:bg-gray-800/50" id="mot-directeur-about" aria-labelledby="directeur-about-title">
            <div class="container">
                @if($siteSettings->getTranslation('about_director_message_title', $currentLocale, false))
                    <span class="page-section__subtitle" data-aos="fade-up">{{ __('La Parole à Notre Direction') }}</span>
                    <h2 class="page-section__title" id="directeur-about-title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->getTranslation('about_director_message_title', $currentLocale, false) }}</h2>
                @endif
                <div class="director-section__card max-w-4xl mx-auto" data-aos="zoom-in-up" data-aos-delay="150">
                    @if($siteSettings->about_director_photo_url)
                    <img src="{{ $siteSettings->about_director_photo_url }}" 
                         alt="{{ __('Photo de') }} {{ $siteSettings->getTranslation('about_director_name', $currentLocale, false) }}" class="director-section__photo">
                    @endif
                    <div class="flex-grow text-center md:text-left">
                        <h3 class="director-section__name">{{ $siteSettings->getTranslation('about_director_name', $currentLocale, false) }}</h3>
                        <p class="director-section__position">{{ $siteSettings->getTranslation('about_director_position', $currentLocale, false) }}</p>
                        <div class="director-section__message prose dark:prose-invert mt-4 text-gray-700 dark:text-gray-300 text-justify">
                            {!! $siteSettings->getTranslation('about_director_message_content', $currentLocale, false) !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        
        {{-- 7. Section FST / USTTB (depuis SiteSettings) --}}
        @if($siteSettings->getTranslation('about_fst_title', $currentLocale, false) || $siteSettings->getTranslation('about_fst_content', $currentLocale, false))
        <section class="page-section container" id="fst-usttb-about" aria-labelledby="fst-about-title">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="alternating-layout__text order-last md:order-1" data-aos="fade-right">
                    @if($siteSettings->getTranslation('about_fst_title', $currentLocale, false))
                    <span class="page-section__subtitle !text-left">{{__('Notre Ancrage Académique')}}</span>
                    <h2 class="page-section__title !text-left" id="fst-about-title">{{ $siteSettings->getTranslation('about_fst_title', $currentLocale, false) }}</h2>
                    @endif
                    @if($siteSettings->getTranslation('about_fst_content', $currentLocale, false))
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-justify">
                        {!! $siteSettings->getTranslation('about_fst_content', $currentLocale, false) !!}
                    </div>
                    @endif

                    @php 
                        $fstStatsData = $siteSettings->about_fst_statistics_json;
                        if (is_string($fstStatsData)) {
                            $decodedJsonFst = json_decode($fstStatsData, true);
                            $fstStatsToDisplay = (json_last_error() === JSON_ERROR_NONE && is_array($decodedJsonFst)) ? $decodedJsonFst : [];
                        } elseif (is_array($fstStatsData)) {
                            $fstStatsToDisplay = $fstStatsData;
                        } else { $fstStatsToDisplay = []; }
                    @endphp
                    @if(!empty($fstStatsToDisplay))
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($fstStatsToDisplay as $stat)
                            @if(is_array($stat))
                            <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow text-center">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $stat['value'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $stat['label_'.$currentLocale] ?? ($stat['label_fr'] ?? ($stat['label'] ?? __('Statistique'))) }}</div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                @if($siteSettings->about_fst_logo_url)
                <div class="alternating-layout__image order-first md:order-last flex justify-center md:justify-end" data-aos="fade-left" data-aos-delay="150">
                    <img src="{{ $siteSettings->about_fst_logo_url }}" alt="{{ __('Logo FST-USTTB') }}" class="max-h-40 md:max-h-56 w-auto object-contain">
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- 8. Section Décret de Création (depuis SiteSettings) --}}
        @if($siteSettings->getTranslation('about_decree_title', $currentLocale, false) || $siteSettings->about_decree_pdf_url)
        <section class="page-section bg-slate-50 dark:bg-gray-800/50" id="decret-creation-about" aria-labelledby="decret-about-title">
             <div class="container text-center">
                @if($siteSettings->getTranslation('about_decree_title', $currentLocale, false))
                    <span class="page-section__subtitle" data-aos="fade-up">{{ __('Fondement Légal') }}</span>
                    <h2 class="page-section__title" id="decret-about-title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->getTranslation('about_decree_title', $currentLocale, false) }}</h2>
                @endif
                @if($siteSettings->getTranslation('about_decree_intro_text', $currentLocale, false))
                <div class="prose dark:prose-invert max-w-2xl mx-auto text-gray-700 dark:text-gray-300 mb-6 text-justify" data-aos="fade-up" data-aos-delay="150">
                    {!! $siteSettings->getTranslation('about_decree_intro_text', $currentLocale, false) !!}
                </div>
                @endif
                @if($siteSettings->about_decree_pdf_url)
                <div data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ $siteSettings->about_decree_pdf_url }}" target="_blank" rel="noopener noreferrer"
                       class="button button--primary button--lg inline-flex items-center text-base px-8 py-3">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                        {{ __('Consulter le Décret') }}
                    </a>
                </div>
                @endif
            </div>
        </section>
        @endif
    </main>
@endsection

@push('scripts')
{{-- Scripts spécifiques si besoin --}}
@endpush
@extends('layouts.public')

{{--
    Variables globales attendues (injectées par le middleware ShareSiteSettings) : $siteSettings.
    Variables spécifiques passées par PublicPageController@home :
    $latestNews, $upcomingEvents, $featuredResearchAxes, $featuredPublications,
    $keyTeamMembers, $activePartners, $aboutSectionPage, $testimonials, $keyFigures
--}}

@php
    $currentLocale = app()->getLocale();
@endphp

@section('title', ($siteSettings->getTranslation('hero_main_title', $currentLocale, false) ?: __('Accueil')) . ' - ' . ($siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name')))
@section('meta_description', $siteSettings->getTranslation('site_description', $currentLocale, false) ?: __('Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.'))
@section('og_title', ($siteSettings->getTranslation('hero_main_title', $currentLocale, false) ?: __('Accueil')) . ' - ' . ($siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name')))
@section('og_description', $siteSettings->getTranslation('site_description', $currentLocale, false) ?: __('Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.'))
@if($siteSettings->default_og_image_url)
    @section('og_image', $siteSettings->default_og_image_url)
@endif

@push('styles')
{{-- Il est fortement recommandé de déplacer ces styles dans vos fichiers CSS compilés --}}
<style>
    :root {
        /* Définissez vos variables de couleur ici si elles ne sont pas déjà dans votre CSS principal */
        /* Exemple (adaptez avec vos vraies couleurs Tailwind ou CSS) */
        --color-primary-rgb: 37, 99, 235; /* Exemple: blue-600 */
        --color-primary-dark-rgb: 30, 64, 175; /* Exemple: blue-800 */
        --color-secondary-rgb: 109, 40, 217; /* Exemple: purple-700 */
        --color-secondary-dark-rgb: 76, 29, 149; /* Exemple: purple-900 */
        --color-accent-cyan: #06b6d4; /* text-cyan-500 */
        --color-primary: rgb(var(--color-primary-rgb));
        --color-primary-light: rgba(var(--color-primary-rgb), 0.1);
        --color-primary-dark: rgb(var(--color-primary-dark-rgb));
        --text-color: #374151; /* gray-700 */
        --text-color-light: #6b7280; /* gray-500 */
        --title-color: #1f2937; /* gray-800 */
        --body-bg-color: white;
        --card-bg-color: white;
        --dark-body-bg-color: #1f2937; /* gray-800 */
        --dark-card-bg-color: #374151; /* gray-700 */
        --dark-title-color: #f3f4f6; /* gray-100 */
        --dark-text-color: #d1d5db; /* gray-300 */
        --dark-text-color-light: #9ca3af; /* gray-400 */
        --radius-sm: 0.25rem;
        --radius-md: 0.375rem;
        --radius-lg: 0.5rem;
        --radius-xl: 0.75rem;
        --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        --shadow-2xl: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    /* Styles de base (certains repris de votre version) */
    .hero { color: #fff; padding-top: calc(var(--header-height, 4rem) + 2rem); padding-bottom: 2rem; position: relative; min-height: 75vh; display: flex; flex-direction: column; justify-content: center; background-size: cover; background-position: center; }
    @media (min-width: 768px) { .hero { min-height: 85vh; padding-top: calc(var(--header-height, 4rem) + 4rem); padding-bottom: 4rem; } }
    .hero-slider { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden; }
    .hero-slider__slide { position: absolute; inset: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.2s ease-in-out; }
    .hero-slider__slide.active-slide { opacity: 1; }
    .hero__container { position: relative; z-index: 1; text-align: center;}
    .hero__data { max-width: 48rem; margin-left: auto; margin-right: auto; }
    .hero__title { font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.15; font-weight: 800; margin-bottom: 1rem; text-shadow: 0 2px 5px rgba(0,0,0,0.6); }
    .hero__title-highlight { color: rgb(var(--color-accent-cyan, 0, 191, 255)); }
    .hero__description { font-size: clamp(1rem, 2.5vw, 1.25rem); line-height: 1.75; margin-bottom: 2rem; max-width: 42rem; margin-left: auto; margin-right: auto; text-shadow: 0 1px 3px rgba(0,0,0,0.6); opacity: 0.95; }
    .hero__buttons { display: flex; flex-direction: column; sm:flex-direction: row; justify-content: center; gap: 1rem; }
    .hero__scroll { position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); z-index: 1; }
    .hero__scroll-link { display: inline-flex; align-items: center; color: #fff; text-decoration: none; font-size: 0.875rem; opacity: 0.8; transition: opacity 0.3s ease; }
    .hero__scroll-link:hover { opacity: 1; }
    .hero__scroll-link ion-icon { margin-right: 0.5rem; font-size: 1.25rem; vertical-align: middle; }
    .section { padding-top: 3rem; padding-bottom: 3rem; }
    @media (min-width: 768px) { .section { padding-top: 4rem; padding-bottom: 4rem; } }
    .section__subtitle { display: block; text-align: center; font-size: 0.875rem; color: rgb(var(--color-primary)); font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .section__title { text-align: center; font-size: clamp(1.75rem, 4vw, 2.25rem); font-weight: 700; color: var(--title-color); margin-bottom: 2.5rem; line-height: 1.3; }
    .dark .section__title { color: var(--dark-title-color); }
    .section__action { text-align: center; margin-top: 2.5rem; }
    .about__img { border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); }
    .about__details { list-style: none; padding-left: 0; margin-top: 1.5rem; space-y: 0.75rem; }
    .about__details li { display: flex; align-items: flex-start; font-size: 0.95rem; }
    .about__details-icon { color: rgb(var(--color-primary)); margin-right: 0.75rem; font-size: 1.5rem; flex-shrink: 0; line-height: 1.5; }
    .section-card { background-color: var(--card-bg-color, white); color: var(--card-text-color, #374151); border-radius: var(--radius-xl, 0.75rem); box-shadow: var(--shadow-lg); transition: all 0.3s ease-in-out; overflow: hidden; display: flex; flex-direction: column; }
    .dark .section-card { background-color: var(--dark-card-bg-color, #374151); color: var(--dark-text-color, #d1d5db); }
    .section-card:hover { box-shadow: var(--shadow-2xl); transform: translateY(-6px); }
    .section-card__image-link { display: block; aspect-ratio: 16 / 10; overflow: hidden; }
    .section-card__image { width: 100%; height: 100%; object-fit: cover; }
    .home-news-card__image { /* hérité de votre code, s'assurer qu'il est cohérent */ }
    .section-card__content { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
    .section-card__meta { font-size: 0.75rem; color: var(--text-color-light, #6b7280); margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap-x: 0.75rem; gap-y: 0.25rem; align-items: center;}
    .dark .section-card__meta { color: var(--dark-text-color-light, #9ca3af); }
    .news__category { font-weight: 500; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.7rem; }
    .section-card__title { font-size: 1.125rem; font-semibold; line-height: 1.4; margin-bottom: 0.5rem; }
    .section-card__title a { color: var(--title-color, #1f2937); text-decoration: none; }
    .dark .section-card__title a { color: var(--dark-title-color, #f3f4f6); }
    .section-card__title a:hover { color: rgb(var(--color-primary)); }
    .section-card__description { font-size: 0.875rem; line-height: 1.625; margin-bottom: 1rem; flex-grow: 1; }
    .section-card__link { margin-top: auto; display: inline-flex; align-items: center; font-size: 0.875rem; font-semibold; color: rgb(var(--color-primary)); text-decoration:none; }
    .section-card__link ion-icon, .section-card__link svg { margin-left: 0.25rem; width: 1em; height: 1em; }
    .event__item--list-home .event__date { background-color: var(--color-primary-light, #eff6ff); color: rgb(var(--color-primary)); border: 1px solid var(--color-primary-light, #dbeafe);}
    .dark .event__item--list-home .event__date { background-color: rgba(var(--color-primary-rgb),0.2); border-color: rgba(var(--color-primary-rgb),0.3); }
    .event__item--list-home .event__day { color: rgb(var(--color-primary-dark, #1e40af)); }
    .dark .event__item--list-home .event__day { color: rgb(var(--color-primary-light, #93c5fd));}
    .research__card { text-align: center; padding: 1.5rem; } /* Le parent est un <a> donc il hérite déjà du style card */
    .research__card-icon-wrapper { display: inline-flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem; box-shadow: var(--shadow-sm); }
    .research__card-icon-wrapper > svg, .research__card-icon-wrapper > i { width: 100%; height: 100%; }
    .research__card-title { font-size: 1.125rem; font-semibold; margin-bottom: 0.25rem; }
    .research__card-description { font-size: 0.875rem; color: var(--text-color-light); }
    .dark .research__card-description { color: var(--dark-text-color-light); }
    .key-figures__container { margin-bottom: 3rem; }
    .key-figure__item { text-align: center; }
    .key-figure__number { font-size: clamp(2.25rem, 5vw, 3rem); font-weight: 800; color: rgb(var(--color-accent-cyan)); line-height: 1.1; }
    .key-figure__label { font-size: 0.95rem; color: var(--text-color-light); margin-top: 0.25rem; }
    .dark .key-figure__label { color: var(--dark-text-color-light); }
    .testimonials-section { background-color: var(--bg-color-light, #f9fafb); }
    .dark .testimonials-section { background-color: var(--dark-bg-color-alt, #1f2937); }
    .testimonials__container { margin-top: 2rem; }
    .testimonial__card { background-color: var(--body-color, white); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); text-align: center; }
    .dark .testimonial__card { background-color: var(--dark-card-bg-color, #374151); }
    .testimonial__quote { font-style: italic; color: var(--text-color); margin-bottom: 1rem; font-size: 1rem; line-height: 1.6; }
    .dark .testimonial__quote { color: var(--dark-text-color); }
    .testimonial__author { font-weight: 600; color: var(--title-color); }
    .dark .testimonial__author { color: var(--dark-title-color); }
    .testimonial__author-title { font-size: 0.875rem; color: var(--text-color-light); }
    .dark .testimonial__author-title { color: var(--dark-text-color-light); }
    .join { background-color: var(--dark-color, #111827); position: relative; border-radius: var(--radius-xl, 0.75rem); padding: 0; }
    .join__container { padding: 4rem 1.5rem; position: relative; z-index: 1; text-align: center; color: white;}
    .join__bg { position: absolute; inset: 0; overflow: hidden; border-radius: var(--radius-xl, 0.75rem); }
    .join__bg-img { width: 100%; height: 100%; object-fit: cover; }
    .join__overlay { position: absolute; inset: 0; background-color: rgba(var(--color-primary-dark-rgb, 10, 42, 77), 0.85); border-radius: var(--radius-xl, 0.75rem); }
    .join__title { font-size: clamp(1.75rem, 3.5vw, 2.25rem); font-weight: 700; margin-bottom: 1rem; }
    .join__description { margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto; opacity: 0.9; font-size: 0.95rem; }
    .join__buttons { display: flex; flex-direction: column; sm:flex-direction: row; justify-content: center; gap: 1rem; }
    .partners-scroll-container { overflow: hidden; position: relative; width: 100%; padding: 2rem 0; }
    .partners-track { display: flex; width: fit-content; animation: scrollPartners 60s linear infinite; }
    .partners-track:hover { animation-play-state: paused; }
    .partner-logo-item { height: 60px; md:height: 70px; margin: 0 2rem; md:margin: 0 2.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .partner-logo-item img { max-height: 100%; max-width: 150px; filter: grayscale(100%); opacity: 0.6; transition: all 0.3s ease; }
    .partner-logo-item img:hover { filter: grayscale(0%); opacity: 1; transform: scale(1.05); }
    @keyframes scrollPartners { 0% { transform: translateX(0%); } 100% { transform: translateX(-50%); } }
</style>
@endpush

@section('content')
    {{-- 1. Section Héros --}}
    <section class="hero section" id="accueil-hero"
             @if($siteSettings->hero_background_image_url && $siteSettings->hero_background_image_url !== asset('assets/images/placeholders/default_hero_bg.jpg'))
                 style="background-image: linear-gradient(135deg, rgba(var(--color-primary-rgb, 19, 78, 138), 0.9) 0%, rgba(var(--color-secondary-rgb, 67, 56, 202), 0.85) 100%), url('{{ $siteSettings->hero_background_image_url }}');"
             @else
                 style="background-image: linear-gradient(135deg, rgba(var(--color-primary-rgb, 19, 78, 138), 0.95) 0%, rgba(var(--color-secondary-rgb, 67, 56, 202), 0.9) 100%);"
             @endif
             data-aos="fade-in" data-aos-duration="800">
        
        @if($siteSettings->hero_banner_images->isNotEmpty())
            <div class="hero-slider">
                @foreach($siteSettings->hero_banner_images as $index => $imageMedia)
                    <div class="hero-slider__slide @if($index === 0) active-slide @endif"
                         style="background-image: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.4)), url('{{ $imageMedia->getUrl() }}');">
                         {{-- Le texte alternatif est géré via $siteSettings->hero_banner_image_alt pour la section ou par image si défini dans l'admin --}}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="hero__container container grid relative z-10 py-16 md:py-20 lg:py-24">
            <div class="hero__data" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                <h1 class="hero__title">
                    {{ $siteSettings->hero_main_title }}
                    @if($siteSettings->hero_highlight_word)
                        <span class="hero__title-highlight">{{ $siteSettings->hero_highlight_word }}</span>
                    @endif.
                    @if($siteSettings->hero_subtitle_line2)
                        <span class="block text-xl md:text-2xl lg:text-3xl font-semibold mt-2 opacity-90">{{ $siteSettings->hero_subtitle_line2 }}</span>
                    @endif
                </h1>
                <p class="hero__description mt-6">
                    {{ $siteSettings->hero_description }}
                </p>
                <div class="hero__buttons mt-8" data-aos="fade-up" data-aos-delay="500" data-aos-duration="1000">
                    <a href="{{ $siteSettings->hero_button1_url ?: (Route::has('public.research_axes.index') ? route('public.research_axes.index') : '#recherche-accueil-section') }}" 
                       class="button button--white hero__button text-base px-8 py-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                        {{ $siteSettings->hero_button1_text ?: __('Découvrir nos Axes') }}
                        @if($siteSettings->hero_button1_icon)<ion-icon name="{{ $siteSettings->hero_button1_icon }}" class="button__icon ml-2"></ion-icon>@endif
                    </a>
                    <a href="{{ $siteSettings->hero_button2_url ?: (Route::has('public.publications.index') ? route('public.publications.index') : '#publications-accueil-section') }}" 
                       class="button button--outline-white hero__button text-base px-8 py-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                        {{ $siteSettings->hero_button2_text ?: __('Nos Publications') }}
                        @if($siteSettings->hero_button2_icon)<ion-icon name="{{ $siteSettings->hero_button2_icon }}" class="button__icon ml-2"></ion-icon>@endif
                    </a>
                </div>
            </div>
        </div>
        <div class="hero__scroll" data-aos="fade-up" data-aos-delay="900" data-aos-anchor=".hero__buttons">
            <a href="#apropos-section-home" class="hero__scroll-link">
                <ion-icon name="arrow-down-outline"></ion-icon> {{ __('Explorer') }}
            </a>
        </div>
    </section>

    {{-- 2. Brève Présentation du CRPQA (Section "À Propos" de l'Accueil) --}}
    @if($siteSettings->about_home_title || $siteSettings->about_home_short_description || ($aboutSectionPage && $aboutSectionPage->content))
    <section class="section about bg-white dark:bg-gray-800" id="apropos-section-home">
        <div class="about__container container grid md:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
            @if($siteSettings->about_home_image_url && $siteSettings->about_home_image_url !== asset('assets/images/placeholders/default_about_home.jpg'))
            <div class="about__image-wrapper order-last md:order-first" data-aos="fade-right" data-aos-duration="1000">
                <img src="{{ $siteSettings->about_home_image_url }}"
                     alt="{{ $siteSettings->getTranslation('about_home_image_alt', $currentLocale, false) ?: ($siteSettings->about_home_title ?: __('Image de présentation CRPQA')) }}"
                     class="about__img w-full h-auto max-h-[450px] object-contain md:object-cover rounded-lg shadow-xl mx-auto">
            </div>
            @endif
            <div class="about__data @if(empty($siteSettings->about_home_image_url) || $siteSettings->about_home_image_url === asset('assets/images/placeholders/default_about_home.jpg')) md:col-span-2 text-center md:text-left @else order-first md:order-none @endif" 
                 data-aos="fade-left" data-aos-duration="1000" data-aos-delay="150">
                
                @if($siteSettings->about_home_subtitle)
                <span class="section__subtitle @if(empty($siteSettings->about_home_image_url) || $siteSettings->about_home_image_url === asset('assets/images/placeholders/default_about_home.jpg')) !text-center @else !text-left @endif">
                    {{ $siteSettings->about_home_subtitle }}
                </span>
                @endif

                <h2 class="section__title about__title @if(empty($siteSettings->about_home_image_url) || $siteSettings->about_home_image_url === asset('assets/images/placeholders/default_about_home.jpg')) !text-center @else !text-left @endif">
                    {{ $siteSettings->about_home_title }}
                </h2>
                
                @if($siteSettings->about_home_short_description)
                <div class="prose dark:prose-invert max-w-none about__description text-gray-700 dark:text-gray-300 mb-6 text-justify">
                    {!! $siteSettings->about_home_short_description !!}
                </div>
                @elseif($aboutSectionPage && $aboutSectionPage->content) {{-- Fallback sur le contenu de la page statique 'a-propos-accueil' --}}
                 <div class="prose dark:prose-invert max-w-none about__description text-gray-700 dark:text-gray-300 mb-6 text-justify">
                    {!! Str::limit(strip_tags($aboutSectionPage->content), 400) !!}
                </div>
                @endif
                
                @php
                    $aboutHomePointsToDisplay = is_array($siteSettings->about_home_points) ? $siteSettings->about_home_points : [];
                    if (empty($aboutHomePointsToDisplay) && (empty($siteSettings->about_home_image_url) || $siteSettings->about_home_image_url === asset('assets/images/placeholders/default_about_home.jpg'))) {
                        $aboutHomePointsToDisplay = [
                            ['icon' => 'rocket-outline', 'text_fr' => 'Recherche de pointe', 'text_en' => 'Cutting-edge research'],
                            ['icon' => 'school-outline', 'text_fr' => 'Formation d\'excellence', 'text_en' => 'Excellence in training'],
                            ['icon' => 'earth-outline', 'text_fr' => 'Impact international', 'text_en' => 'International impact'],
                        ];
                    }
                @endphp

                @if(!empty($aboutHomePointsToDisplay))
                <ul class="about__details mt-6 space-y-3">
                    @foreach($aboutHomePointsToDisplay as $i => $point)
                    <li class="flex items-start" data-aos="fade-left" data-aos-delay="{{ 200 + ($i * 100) }}">
                        <ion-icon name="{{ $point['icon'] ?? 'checkmark-circle-outline' }}" class="about__details-icon"></ion-icon>
                        <span>{{ $point['text_'.$currentLocale] ?? ($point['text_fr'] ?? ($point['text'] ?? 'Point clé')) }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif

                @php $aboutPageDedicatedSlug = $siteSettings->about_page_slug; @endphp
                @if($aboutPageDedicatedSlug && Route::has('public.page'))
                <div class="section__action mt-8 @if(empty($siteSettings->about_home_image_url) || $siteSettings->about_home_image_url === asset('assets/images/placeholders/default_about_home.jpg')) !text-center @else !text-left @endif" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ route('public.page', ['staticPage' => $aboutPageDedicatedSlug]) }}" class="button button--primary">
                        {{ __('En savoir plus sur nous') }} <ion-icon name="arrow-forward-outline" class="button__icon ml-2"></ion-icon>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- 3. Dernières Actualités --}}
    @if(isset($latestNews) && $latestNews->count() > 0)
    <section class="section news bg-slate-50 dark:bg-gray-800/50" id="actualites-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings->home_news_subtitle ?? __('Restez Informé') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_news_title ?? __('Nos Dernières Actualités') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach($latestNews as $index => $newsItem)
                <article class="section-card news__card group" data-aos="fade-up" data-aos-delay="{{ ($index * 150) }}">
                    <a href="{{ route('public.news.show', $newsItem->slug) }}" class="section-card__image-link" aria-label="{{ __('Lire l\'actualité:') }} {{ $newsItem->title }}">
                        <img src="{{ $newsItem->cover_image_card_url ?? ($newsItem->cover_image_thumbnail_url ?? asset('assets/images/placeholders/news_default_'.($index%2+1).'.jpg')) }}"
                             alt="{{ $newsItem->cover_image_alt ?? $newsItem->title }}"
                             class="section-card__image group-hover:scale-105 transition-transform duration-300">
                    </a>
                    <div class="section-card__content">
                        <p class="section-card__meta news__meta">
                            @if($newsItem->published_at)
                            <time datetime="{{ $newsItem->published_at->toDateString() }}">{{ $newsItem->published_at->translatedFormat('d M Y') }}</time>
                            @endif
                            @if($newsItem->category)
                                <span class="news__category-separator mx-1">&bull;</span>
                                <span class="news__category font-medium text-xs px-2 py-0.5 rounded-full" 
                                      style="background-color: {{ $newsItem->category->color ?: 'rgba(var(--color-primary-rgb),0.1)' }}; color: {{ $newsItem->category->text_color ?: 'rgb(var(--color-primary))' }};">
                                    {{ $newsItem->category->name }}
                                </span>
                            @endif
                        </p>
                        <h3 class="section-card__title text-lg news__title">
                            <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                {{ Str::limit($newsItem->title, 70) }}
                            </a>
                        </h3>
                        <p class="section-card__description news__description">
                            {{ Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 120) }}
                        </p>
                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="section-card__link news__link">
                            {{ __('Lire la suite') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            @if(Route::has('public.news.index') && $latestNews->count() >= 3)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.news.index') }}" class="button button--primary">{{ __('Toutes les Actualités') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 4. Événements à Venir --}}
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
    <section class="section events bg-white dark:bg-gray-800" id="evenements-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings->home_events_subtitle ?? __('Notre Agenda') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_events_title ?? __('Prochains Événements') }}</h2>
            <div class="space-y-8">
                @foreach($upcomingEvents as $index => $event)
                <article class="event__item--list-home bg-white dark:bg-gray-800/50 rounded-xl shadow-lg p-5 md:p-6 md:flex gap-5 md:gap-6 items-center group transition-all duration-300 hover:shadow-2xl" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    @if($event->start_datetime)
                    <div class="event__date text-center md:text-left mb-4 md:mb-0 md:pr-5 lg:pr-6 flex-shrink-0 w-full md:w-24 lg:w-28">
                        <span class="event__day block text-3xl lg:text-4xl font-bold text-primary-600 dark:text-primary-400">{{ $event->start_datetime->format('d') }}</span>
                        <span class="event__month block text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $event->start_datetime->translatedFormat('MMM') }}</span>
                        <span class="event__year block text-xs text-gray-400 dark:text-gray-500">{{ $event->start_datetime->format('Y') }}</span>
                    </div>
                    @endif
                    <div class="event__info flex-grow min-w-0 overflow-hidden md:border-l md:border-gray-200 dark:md:border-gray-700 md:pl-5 lg:pl-6">
                        <h3 class="event__title text-lg font-semibold text-gray-800 dark:text-white mb-1 break-words">
                            <a href="{{ route('public.events.show', $event->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $event->title }}
                            </a>
                        </h3>
                        <p class="event__time-location text-xs text-gray-500 dark:text-gray-400 mb-2 flex flex-col sm:flex-row sm:flex-wrap gap-x-3 gap-y-1">
                            @if($event->start_datetime)
                            <span class="inline-flex items-center"><x-heroicon-o-clock class="w-3.5 h-3.5 mr-1 flex-shrink-0"/> {{ $event->start_datetime->translatedFormat('H:i') }}
                                @if($event->end_datetime && $event->end_datetime->format('H:i') !== $event->start_datetime->format('H:i'))
                                 - {{ $event->end_datetime->translatedFormat('H:i') }}
                                @endif
                            </span>
                            @endif
                            @if($event->location)
                            <span class="inline-flex items-center"><x-heroicon-o-map-pin class="w-3.5 h-3.5 mr-1 flex-shrink-0"/>{{ Str::limit($event->location, 35) }}</span>
                            @endif
                        </p>
                        <p class="event__description text-sm text-gray-700 dark:text-gray-300 mb-3 break-words">
                            {{ Str::limit(strip_tags($event->description), 110) }}
                        </p>
                        <a href="{{ route('public.events.show', $event->slug) }}" class="section-card__link event__link text-sm font-medium">
                            {{ __('Détails de l\'événement') }} <ion-icon name="arrow-forward-outline" class="ml-1"></ion-icon>
                        </a>
                    </div>
                    @if($event->cover_image_thumbnail_url)
                        <a href="{{ route('public.events.show', $event->slug) }}" class="event__image-link md:ml-auto mt-4 md:mt-0 flex-shrink-0 w-full md:w-40 lg:w-48 h-32 md:h-auto md:max-h-36 rounded-md overflow-hidden block">
                            <img src="{{ $event->cover_image_thumbnail_url }}" alt="{{ $event->getTranslation('cover_image_alt_text', app()->getLocale(), false) ?: $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                    @endif
                </article>
                @endforeach
            </div>
            @if(Route::has('public.events.index') && $upcomingEvents->count() >= 3)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.events.index') }}" class="button button--primary">{{ __('Tous les Événements') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 5. Domaines de Recherche Clés --}}
    @if(isset($featuredResearchAxes) && $featuredResearchAxes->count() > 0)
    <section class="section research bg-slate-50 dark:bg-gray-800/50" id="recherche-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings->home_research_axes_subtitle ?? __('Nos Pôles d\'Excellence') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_research_axes_title ?? __('Domaines de Recherche Clés') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                @foreach($featuredResearchAxes as $index => $axis)
                <a href="{{ route('public.research_axes.show', $axis->slug) }}" 
                   class="research__card block p-6 group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 ease-in-out text-center" 
                   data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <div class="research__card-icon-wrapper inline-flex items-center justify-center w-16 h-16 p-3 mb-4 rounded-lg shadow-md mx-auto transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg"
                         style="background-color: {{ $axis->color_hex ? \App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.15) : 'rgba(var(--color-primary-rgb), 0.1)' }}; 
                                color: {{ $axis->color_hex ?: 'rgb(var(--color-primary))' }};">
                        @if($axis->getTranslation('icon_svg', $currentLocale, false))
                            {!! $axis->getTranslation('icon_svg', $currentLocale, false) !!}
                        @elseif($axis->icon_class)
                            <i class="{{ $axis->icon_class }} text-3xl"></i>
                        @else
                             <x-heroicon-o-academic-cap class="w-8 h-8"/>
                        @endif
                    </div>
                    <h3 class="research__card-title text-gray-800 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                        {{ $axis->name }}
                    </h3>
                    <p class="research__card-description text-sm text-gray-600 dark:text-gray-400">
                        {{ Str::limit(strip_tags($axis->subtitle ?: $axis->description), 75) }}
                    </p>
                </a>
                @endforeach
            </div>
             @if(Route::has('public.research_axes.index') && $featuredResearchAxes->count() >= 3)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.research_axes.index') }}" class="button button--outline-primary">
                    {{ __('Explorer tous les domaines') }}
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 6. Publications en Vedette --}}
    @if(isset($featuredPublications) && $featuredPublications->count() > 0)
    <section class="section publications-home bg-white dark:bg-gray-800" id="publications-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings->home_publications_subtitle ?? __('Nos Contributions Scientifiques') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_publications_title ?? __('Publications Récentes et en Vedette') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach($featuredPublications as $index => $publication)
                <article class="section-card publication__card_home" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                     <div class="section-card__content">
                        <div class="research__card-icon-wrapper w-12 h-12 !mb-3" style="background-color: rgba(var(--color-secondary-rgb, 109, 40, 217), 0.1);">
                            <ion-icon name="document-text-outline" class="research__card-icon text-2xl" style="color: rgb(var(--color-secondary, 109, 40, 217));"></ion-icon>
                        </div>
                        <h3 class="section-card__title text-lg publication__title_home">
                            <a href="{{ route('public.publications.show', $publication->slug) }}">
                                {{ Str::limit($publication->title, 65) }}
                            </a>
                        </h3>
                        @php
                            $pubAuthorsList = [];
                            if ($publication->researchers->isNotEmpty()) { $pubAuthorsList[] = $publication->researchers->take(3)->pluck('full_name')->join(', '); if($publication->researchers->count() > 3) $pubAuthorsList[] = 'et al.';}
                            if ($publication->authors_external) { $pubAuthorsList[] = e(Str::limit($publication->authors_external, 50)); }
                        @endphp
                        @if(!empty($pubAuthorsList))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                           <span class="font-medium">{{ __('Auteurs:') }}</span> {{ Str::limit(implode('; ', $pubAuthorsList), 100) }}
                        </p>
                        @endif
                        <p class="section-card__meta publication__meta_home">
                            <span>{{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }}</span>
                            @if($publication->publication_date)
                            <span class="mx-1">&bull;</span>
                            <time datetime="{{ $publication->publication_date->toDateString() }}">{{ $publication->publication_date->translatedFormat('Y') }}</time>
                            @endif
                            @if($publication->journal_name) <span class="hidden sm:inline">| {{ Str::limit($publication->journal_name, 25) }}</span> @endif
                        </p>
                         <div class="mt-auto pt-3">
                            <a href="{{ $publication->doi_url ?: route('public.publications.show', $publication->slug) }}"
                               target="{{ $publication->doi_url ? '_blank' : '_self' }}" rel="noopener noreferrer"
                               class="section-card__link publication__link_home">
                                {{ $publication->doi_url ? __('Consulter sur DOI') : __('Lire la suite') }} <ion-icon name="open-outline"></ion-icon>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @if(Route::has('public.publications.index') && $featuredPublications->count() >= 3)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.publications.index') }}" class="button button--primary">{{ __('Toutes les Publications') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 7. Aperçu de l'Équipe --}}
    @if(isset($keyTeamMembers) && $keyTeamMembers->count() > 0)
    <section class="section team-preview bg-slate-50 dark:bg-gray-800/50" id="equipe-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings->home_team_subtitle ?? __('Notre Équipe') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_team_title ?? __('Nos Chercheurs Experts') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                @foreach($keyTeamMembers as $index => $researcher)
                <div class="text-center group" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <a href="{{ route('public.researchers.show', $researcher->slug) }}" class="block p-4">
                        <img src="{{ $researcher->photo_thumbnail_url ?? asset('assets/images/placeholders/researcher_default_'.($index%2+1).'.png') }}" 
                             alt="{{ $researcher->getTranslation('photo_alt_text', $currentLocale, false) ?: $researcher->full_name }}"
                             class="w-32 h-32 rounded-full object-cover mx-auto mb-4 shadow-lg border-4 border-white dark:border-gray-700 transform transition-all duration-300 group-hover:scale-105 group-hover:shadow-primary-500/40">
                        <h3 class="text-md font-semibold text-gray-800 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 mb-0.5">{{ $researcher->full_name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $researcher->title_position }}</p>
                    </a>
                </div>
                @endforeach
            </div>
            @if(Route::has('public.researchers.index') && $keyTeamMembers->count() >= 3)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.researchers.index') }}" class="button button--outline-primary">{{ __('Découvrir toute l\'Équipe') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 8. Présentation des Partenaires (Défilement) --}}
    @if(isset($activePartners) && $activePartners->count() > 0)
    <section class="section partners-section bg-white dark:bg-gray-800" id="partenaires-accueil-section">
        <div class="container">
            <span class="section__subtitle text-center" data-aos="fade-up">{{ $siteSettings->home_partners_subtitle ?? __('Ils nous font confiance') }}</span>
            <h2 class="section__title text-center" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_partners_title ?? __('Nos Partenaires Stratégiques') }}</h2>
            
            <div class="partners-scroll-container mt-8" data-aos="fade-up" data-aos-delay="200">
                <div class="partners-track">
                    @php $doubledPartners = collect($activePartners)->merge($activePartners); @endphp
                    @foreach($doubledPartners as $partner)
                        <div class="partner-logo-item">
                            <a href="{{ $partner->website_url ?: '#' }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}">
                                <img src="{{ $partner->logo_thumbnail_url ?? asset('assets/images/placeholders/partner_default.png') }}" 
                                     alt="{{ $partner->getTranslation('logo_alt_text', $currentLocale, false) ?: $partner->name }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
             @if(Route::has('public.partners.index') && $activePartners->count() > 3) {{-- Afficher si plus de 3 partenaires par exemple --}}
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.partners.index') }}" class="button button--outline-primary">{{ __('Voir tous nos Partenaires') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif
    
    {{-- 9. Témoignages et Chiffres Clés --}}
    @if((isset($testimonials) && !empty($testimonials)) || (isset($keyFigures) && !empty($keyFigures)))
    <section class="section testimonials-section bg-slate-50 dark:bg-gray-800/50" id="impact-chiffres-accueil-section">
        <div class="container">
            @if(is_array($keyFigures) && !empty($keyFigures))
            <div class="key-figures__container grid grid-cols-2 md:grid-cols-{{ count($keyFigures) > 4 ? 4 : max(1, count($keyFigures)) }} gap-6 mb-12" data-aos="fade-up">
                @foreach($keyFigures as $figure)
                <div class="key-figure__item p-4 bg-white dark:bg-gray-700/50 rounded-lg shadow">
                    <div class="key-figure__number">{{ $figure['number'] ?? '0' }}</div>
                    <div class="key-figure__label">{{ $figure['label_'.$currentLocale] ?? ($figure['label_fr'] ?? ($figure['label'] ?? __('Statistique'))) }}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if(is_array($testimonials) && !empty($testimonials))
            <span class="section__subtitle text-center" data-aos="fade-up">{{ $siteSettings->home_testimonials_subtitle ?? __('Ce qu\'ils disent de nous') }}</span>
            <h2 class="section__title text-center" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings->home_testimonials_title ?? __('Témoignages') }}</h2>
            <div class="testimonials__container grid md:grid-cols-{{ count($testimonials) > 1 ? '2' : '1' }} lg:grid-cols-{{ count($testimonials) > 2 ? '3' : max(1, count($testimonials)) }} gap-6" data-aos="fade-up" data-aos-delay="200">
                @foreach($testimonials as $testimonial)
                <div class="testimonial__card bg-white dark:bg-gray-700">
                    <p class="testimonial__quote">"{{ $testimonial['quote_'.$currentLocale] ?? ($testimonial['quote_fr'] ?? ($testimonial['quote'] ?? '')) }}"</p>
                    <p class="testimonial__author mt-4">{{ $testimonial['author_'.$currentLocale] ?? ($testimonial['author_fr'] ?? ($testimonial['author'] ?? '')) }}</p>
                    @if(!empty($testimonial['author_title_'.$currentLocale]) || !empty($testimonial['author_title_fr']) || !empty($testimonial['author_title']))
                    <p class="testimonial__author-title">{{ $testimonial['author_title_'.$currentLocale] ?? ($testimonial['author_title_fr'] ?? ($testimonial['author_title'] ?? '')) }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 10. Appel à la Collaboration/Partenariat --}}
    <section class="section join !py-0" id="collaboration-accueil-section">
        <div class="join__container container" data-aos="zoom-in" data-aos-duration="800">
            <div class="join__bg">
                <img src="{{ $siteSettings->home_cta_bg_image_url ?? asset('assets/images/backgrounds/join_us_bg_default.jpg') }}"
                     alt="{{ __('Collaborer avec le CRPQA') }}" class="join__bg-img">
            </div>
            <div class="join__overlay"></div>
            <div class="join__data">
                <h2 class="join__title">{{ $siteSettings->home_cta_title }}</h2>
                <p class="join__description">
                    {{ $siteSettings->home_cta_text }}
                </p>
                <div class="join__buttons">
                    <a href="{{ $siteSettings->home_cta_button1_url ?: (Route::has('public.contact.form') ? route('public.contact.form') : '#') }}" class="button button--white text-base px-6 py-3">
                        {{ $siteSettings->home_cta_button1_text ?: __('Devenir Partenaire') }}
                         @if($siteSettings->home_cta_button1_icon)<ion-icon name="{{ $siteSettings->home_cta_button1_icon }}" class="button__icon ml-2"></ion-icon>@endif
                    </a>
                    <a href="{{ $siteSettings->home_cta_button2_url ?: (Route::has('public.page') && $siteSettings->careers_page_slug ? route('public.page', ['staticPage' => $siteSettings->careers_page_slug]) : '#') }}" class="button button--outline-white text-base px-6 py-3">
                        {{ $siteSettings->home_cta_button2_text ?: __('Nous Rejoindre') }}
                        @if($siteSettings->home_cta_button2_icon)<ion-icon name="{{ $siteSettings->home_cta_button2_icon }}" class="button__icon ml-2"></ion-icon>@endif
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
@if(isset($siteSettings) && $siteSettings->hero_banner_images->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const slides = Array.from(heroSlider.querySelectorAll('.hero-slider__slide'));
        if (slides.length > 1) {
            let currentIndex = 0;
            slides.forEach((slide, index) => {
                slide.style.transition = 'opacity 1.2s ease-in-out';
                if (index !== 0) slide.style.opacity = '0';
                else slide.style.opacity = '1';
            });

            setInterval(() => {
                slides[currentIndex].style.opacity = '0';
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].style.opacity = '1';
            }, 7000); 
        } else if (slides.length === 1) {
             slides[0].style.opacity = '1';
        }
    }
});
</script>
@endif

@if(isset($activePartners) && $activePartners->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.partners-track');
    if (track) {
        const logos = Array.from(track.querySelectorAll('.partner-logo-item'));
        if (logos.length > 0) {
            logos.forEach(logo => {
                const clone = logo.cloneNode(true);
                track.appendChild(clone);
            });
        }
    }
});
</script>
@endif
@endpush
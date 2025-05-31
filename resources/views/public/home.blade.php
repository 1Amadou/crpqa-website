@extends('layouts.public')

@section('title', __('Accueil') . ' - ' . ($siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', $siteSettings['site_description'] ?? __('Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.'))
@section('og_title', __('Accueil') . ' - ' . ($siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name')))
@section('og_description', $siteSettings['site_description'] ?? __('Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.'))
{{-- @section('og_image', $siteSettings['og_image_url'] ?? asset('assets/images/default_og_image.jpg')) --}}

@push('styles')
<style>
    .home-news-card__image {
        height: 220px;
        object-fit: cover;
        width: 100%;
    }
    .research__card-icon-wrapper > svg {
        width: 3rem;
        height: 3rem;
    }
    .research__card-icon-wrapper--default {
        background-color: rgba(var(--color-primary-rgb), 0.1);
    }
    .research__card-icon--default,
    .research__card-icon-wrapper svg.default-axis-icon {
        color: rgb(var(--color-primary));
    }
    .hero__scroll-link ion-icon {
        margin-right: 0.25rem; /* Ajustement pour l'icône explorer */
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    {{-- 1. Section Héros --}}
    <section class="hero section" id="accueil-hero"
             @if(!empty($siteSettings['hero_bg_image_url']))
                 style="background-image: linear-gradient(135deg, rgba(var(--color-primary-dark-rgb, 10, 42, 77), 0.88) 0%, rgba(var(--color-secondary-dark-rgb, 29, 44, 90), 0.92) 100%), url('{{ asset($siteSettings['hero_bg_image_url']) }}');"
             @endif
             data-aos="fade-in" data-aos-duration="800">
        <div class="hero__container container grid">
            <div class="hero__data" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                <h1 class="hero__title">
                    {{ $siteSettings['hero_main_title'] ?? __('L\'Avenir est') }}
                    <span class="hero__title-highlight">{{ $siteSettings['hero_highlight_word'] ?? __('Quantique') }}</span>.
                    <br class="hidden md:block">
                    {{ $siteSettings['hero_subtitle_line2'] ?? __('Façonnez-le avec le CRPQA.') }}
                </h1>
                <p class="hero__description">
                    {{ $siteSettings['hero_description'] ?? __('Au cœur de la révolution scientifique, le CRPQA est le fer de lance de la recherche en physique quantique au Mali, ouvrant la voie à des innovations qui transformeront notre monde.') }}
                </p>
                <div class="hero__buttons" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                    <a href="{{ $siteSettings['hero_button1_url'] ?? (Route::has('public.research_axes.index') ? route('public.research_axes.index') : '#recherche-accueil-section') }}" class="button button--white hero__button">
                        {{ $siteSettings['hero_button1_text'] ?? __('Découvrir nos Axes') }}
                        <ion-icon name="{{ $siteSettings['hero_button1_icon'] ?? 'arrow-forward-outline' }}" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ $siteSettings['hero_button2_url'] ?? (Route::has('public.publications.index') ? route('public.publications.index') : '#publications-accueil-section') }}" class="button button--outline-white hero__button">
                        {{ $siteSettings['hero_button2_text'] ?? __('Nos Publications') }}
                        <ion-icon name="{{ $siteSettings['hero_button2_icon'] ?? 'book-outline' }}" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>

            @if(!empty($siteSettings['hero_banner_image_url']))
            <div class="hero__img-bg" data-aos="zoom-in-left" data-aos-delay="500" data-aos-duration="1200">
                <img src="{{ asset($siteSettings['hero_banner_image_url']) }}"
                     alt="{{ $siteSettings['hero_banner_image_alt'] ?? __('Illustration de la physique quantique pour CRPQA') }}"
                     class="hero__img">
            </div>
            @endif
        </div>
        <div class="hero__scroll" data-aos="fade-up" data-aos-delay="800" data-aos-anchor=".hero__buttons">
            <a href="#apropos-section-home" class="hero__scroll-link">
                <ion-icon name="arrow-down-outline"></ion-icon> {{ __('Explorer') }}
            </a>
        </div>
    </section>

    {{-- 2. Brève Présentation du CRPQA --}}
    <section class="section about" id="apropos-section-home">
        <div class="about__container container grid">
            @if(!empty($siteSettings['about_home_image_url']))
            <div class="about__img-bg order-last md:order-first" data-aos="fade-right" data-aos-duration="1000">
                <img src="{{ asset($siteSettings['about_home_image_url']) }}"
                     alt="{{ $siteSettings['about_home_image_alt'] ?? __('Équipe et installations du CRPQA') }}"
                     class="about__img">
            </div>
            @endif
            <div class="about__data" data-aos="fade-left" data-aos-duration="1000">
                <span class="section__subtitle">{{ $siteSettings['about_home_subtitle'] ?? __('Notre Centre') }}</span>
                <h2 class="section__title about__title">
                    {{ $siteSettings['about_home_title'] ?? __('Au Cœur de la Révolution Quantique') }}
                </h2>
                <p class="about__description">
                    {{ $siteSettings['about_home_short_description'] ?? __('Fondé sur un héritage de plus de 55 ans d\'enseignement de la physique quantique, le CRPQA se positionne comme un pôle d\'excellence pour façonner l\'avenir.') }}
                </p>
                @php
                    $aboutHomePoints = isset($siteSettings['about_home_points']) && is_string($siteSettings['about_home_points']) ? json_decode($siteSettings['about_home_points'], true) : ($siteSettings['about_home_points'] ?? []);
                    if (empty($aboutHomePoints) || !is_array($aboutHomePoints)) {
                        $aboutHomePoints = [
                            ['icon' => 'rocket-outline', 'text' => __('Recherche de pointe')],
                            ['icon' => 'school-outline', 'text' => __('Formation d\'excellence')],
                            ['icon' => 'earth-outline', 'text' => __('Impact international')],
                        ];
                    }
                @endphp
                @if(!empty($aboutHomePoints))
                <ul class="about__details">
                    @foreach($aboutHomePoints as $i => $point)
                    <li data-aos="fade-left" data-aos-delay="{{ 200 + ($i * 100) }}">
                        <ion-icon name="{{ $point['icon'] ?? 'checkmark-circle-outline' }}" class="about__details-icon"></ion-icon>
                        <span>{{ $point['text'] ?? '' }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                <div class="section__action mt-6" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ Route::has('public.page') && !empty($siteSettings['about_page_slug']) ? route('public.page', ['staticPage' => $siteSettings['about_page_slug']]) : (Route::has('public.about') ? route('public.about') : '#') }}" class="button button--outline">
                        {{ __('En savoir plus sur nous') }} <ion-icon name="arrow-forward-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Dernières Actualités --}}
    @if(isset($latestNews) && $latestNews->count() > 0)
    <section class="section news bg-slate-100 dark:bg-slate-800" id="actualites-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ __('Restez Informé') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ __('Nos Dernières Actualités') }}</h2>
            <div class="news__container grid">
                @foreach($latestNews->take(3) as $index => $newsItem)
                <article class="news__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__img-link" aria-label="{{ __('Lire l\'actualité:') }} {{ $newsItem->title }}">
                        <img src="{{ $newsItem->cover_image_url ?? asset('assets/images/placeholders/news_default.jpg') }}"
                             alt="{{ $newsItem->cover_image_alt ?? __('Image pour') . ' ' . $newsItem->title }}"
                             class="news__img home-news-card__image">
                    </a>
                    <div class="news__data">
                        <p class="news__meta">
                            @if($newsItem->published_at)
                            <time datetime="{{ $newsItem->published_at->toDateString() }}">{{ $newsItem->published_at->translatedFormat('D MMM YYYY') }}</time>
                            @endif
                            @if($newsItem->category)
                                <span class="news__category-separator">|</span>
                                <span class="news__category" style="{{ $newsItem->category->text_color ? 'color: '.$newsItem->category->text_color.';' : '' }} {{ $newsItem->category->bg_color ? 'background-color: '.$newsItem->category->bg_color.'; padding: 0.1rem 0.4rem; border-radius: 0.25rem;' : '' }}">
                                    {{ $newsItem->category->name }}
                                </span>
                            @endif
                        </p>
                        <h3 class="news__title">
                            <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                {{ Str::limit($newsItem->title, 65) }}
                            </a>
                        </h3>
                        <p class="news__description">
                            {{ Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 120) }}
                        </p>
                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__link">
                            {{ __('Lire la suite') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            @if(Route::has('public.news.index'))
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.news.index') }}" class="button">{{ __('Toutes les Actualités') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 4. Événements à Venir --}}
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
    <section class="section events" id="evenements-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ __('Agenda') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ __('Nos Prochains Événements') }}</h2>
            <div class="events__container grid">
                @foreach($upcomingEvents->take(3) as $index => $event)
                <article class="event__item" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    @if($event->start_datetime)
                    <div class="event__date">
                        <span class="event__day">{{ $event->start_datetime->format('d') }}</span>
                        <span class="event__month">{{ $event->start_datetime->translatedFormat('MMM') }}</span>
                        <span class="event__year">{{ $event->start_datetime->format('Y') }}</span>
                    </div>
                    @endif
                    <div class="event__info">
                        <h3 class="event__title">
                            <a href="{{ route('public.events.show', $event->slug) }}">{{ Str::limit($event->title, 70) }}</a>
                        </h3>
                        <p class="event__time-location">
                            @if($event->start_datetime)
                            <span><ion-icon name="time-outline"></ion-icon> {{ $event->start_datetime->translatedFormat('H:i') }}
                                @if($event->end_datetime && $event->end_datetime->format('H:i') !== $event->start_datetime->format('H:i'))
                                 - {{ $event->end_datetime->translatedFormat('H:i') }}
                                @endif
                            </span>
                            @endif
                            @if($event->location)
                            <span><ion-icon name="location-outline"></ion-icon> {{ Str::limit($event->location, 30) }}</span>
                            @endif
                        </p>
                        <p class="event__description">
                            {{ Str::limit(strip_tags($event->summary ?: $event->description), 90) }}
                        </p>
                        <a href="{{ route('public.events.show', $event->slug) }}" class="event__link">
                            {{ __('Détails de l\'événement') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            @if(Route::has('public.events.index'))
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.events.index') }}" class="button">{{ __('Tous les Événements') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 5. Domaines de Recherche Clés --}}
    @if(isset($keyResearchAxes) && $keyResearchAxes->count() > 0)
    <section class="section research bg-slate-100 dark:bg-slate-800" id="recherche-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ __('Nos Pôles d\'Excellence') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ __('Domaines de Recherche Clés') }}</h2>
            <div class="research__container grid">
                @foreach($keyResearchAxes->take(4) as $index => $axis)
                <div class="research__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <div class="research__card-icon-wrapper {{ (!$axis->icon_svg && !$axis->color_hex) ? 'research__card-icon-wrapper--default' : '' }}"
                         style="{{ $axis->color_hex ? 'background-color: '.\App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.1).';' : '' }}">
                        @if($axis->icon_svg)
                            {!! $axis->icon_svg !!}
                        @else
                            @php $defaultIcons = ['flask-outline', 'hardware-chip-outline', 'magnet-outline', 'color-filter-outline', 'analytics-outline', 'rocket-outline']; @endphp
                            <ion-icon name="{{ $defaultIcons[$loop->index % count($defaultIcons)] }}"
                                      class="research__card-icon {{ !$axis->color_hex ? 'research__card-icon--default' : '' }}"
                                      style="{{ $axis->color_hex ? 'color: '.$axis->color_hex.';' : '' }}"></ion-icon>
                        @endif
                    </div>
                    <h3 class="research__card-title">{{ $axis->name }}</h3>
                    <p class="research__card-description">
                        {{ Str::limit(strip_tags($axis->short_description ?: $axis->description), 100) }}
                    </p>
                    @if(Route::has('public.research_axes.show'))
                    <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="research__card-link">
                        {{ __('Explorer ce domaine') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @if(Route::has('public.research_axes.index'))
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.research_axes.index') }}" class="button button--outline">
                    {{ __('Voir tous les domaines') }}
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 6. Publications en Vedette --}}
    @if(isset($featuredPublications) && $featuredPublications->count() > 0)
    <section class="section publications-home" id="publications-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ __('Nos Contributions') }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ __('Publications en Vedette') }}</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredPublications->take(3) as $index => $publication)
                <article class="publication__card_home research__card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <div class="research__card-icon-wrapper" style="background-color: rgba(var(--color-primary-rgb), 0.1);">
                        <ion-icon name="document-text-outline" class="research__card-icon" style="color: rgb(var(--color-primary));"></ion-icon>
                    </div>
                    <h3 class="research__card-title publication__title_home text-lg font-semibold mt-4 mb-2 text-gray-800 dark:text-white">
                        <a href="{{ route('public.publications.show', $publication->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                            {{ Str::limit($publication->title, 60) }} {{-- Géré par HasLocalizedFields --}}
                        </a>
                    </h3>
                    @php
                        $authorsList = [];
                        if ($publication->researchers->isNotEmpty()) {
                            $authorsList[] = $publication->researchers->pluck('full_name')->join(', ');
                        }
                        if ($publication->authors_external) {
                            $authorsList[] = $publication->authors_external;
                        }
                    @endphp
                    @if(!empty($authorsList))
                    <p class="publication__authors_home text-xs text-gray-500 dark:text-gray-400 mb-2">
                       {{ Str::limit(implode('; ', $authorsList), 100) }}
                    </p>
                    @endif
                    <p class="research__card-description publication__meta_home text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span>{{ $publication->type_display ?? Str::title(str_replace('_', ' ', $publication->type)) }}</span>
                        <span class="mx-1">&bull;</span>
                        <time datetime="{{ $publication->publication_date->toDateString() }}">{{ $publication->publication_date->translatedFormat('Y') }}</time>
                        @if($publication->journal_name) | {{ Str::limit($publication->journal_name, 30) }} @endif
                        @if($publication->conference_name) | {{ Str::limit($publication->conference_name, 30) }} @endif
                    </p>
                    <a href="{{ $publication->doi_url ?? route('public.publications.show', $publication->slug) }}"
                       target="{{ $publication->doi_url ? '_blank' : '_self' }}" rel="noopener noreferrer"
                       class="research__card-link publication__link_home text-primary-600 dark:text-primary-400 hover:underline">
                        {{ $publication->doi_url ? __('Voir sur DOI') : __('Lire la publication') }} <ion-icon name="open-outline"></ion-icon>
                    </a>
                </article>
                @endforeach
            </div>
            @if(Route::has('public.publications.index'))
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.publications.index') }}" class="button">{{ __('Toutes les Publications') }}</a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 7. Témoignages ou Chiffres Clés --}}
    @php
        $testimonials = isset($siteSettings['home_testimonials']) && is_string($siteSettings['home_testimonials']) ? json_decode($siteSettings['home_testimonials'], true) : ($siteSettings['home_testimonials'] ?? []);
        $keyFigures = isset($siteSettings['home_key_figures']) && is_string($siteSettings['home_key_figures']) ? json_decode($siteSettings['home_key_figures'], true) : ($siteSettings['home_key_figures'] ?? []);
    @endphp
    @if((is_array($testimonials) && !empty($testimonials)) || (is_array($keyFigures) && !empty($keyFigures)))
    <section class="section testimonials-section bg-slate-100 dark:bg-slate-800" id="impact-chiffres-accueil-section">
        <div class="container">
            @if(is_array($keyFigures) && !empty($keyFigures))
            <div class="key-figures__container grid grid-cols-2 md:grid-cols-{{ count($keyFigures) > 4 ? 4 : (count($keyFigures) ?: 1) }} gap-6 mb-12" data-aos="fade-up">
                @foreach($keyFigures as $figure)
                <div class="key-figure__item text-center">
                    <div class="key-figure__number text-4xl font-extrabold text-primary-600 dark:text-primary-400">{{ $figure['number'] ?? '0' }}</div>
                    <div class="key-figure__label text-gray-600 dark:text-gray-400 mt-1">{{ $figure['label'] ?? __('Statistique') }}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if(is_array($testimonials) && !empty($testimonials))
            <span class="section__subtitle text-center" data-aos="fade-up">{{ __('Ce qu\'ils disent de nous') }}</span>
            <h2 class="section__title text-center" data-aos="fade-up" data-aos-delay="100">{{ __('Témoignages') }}</h2>
            <div class="testimonials__container grid md:grid-cols-{{ count($testimonials) > 1 ? '2' : '1' }} lg:grid-cols-{{ count($testimonials) > 2 ? '3' : (count($testimonials) ?: 1) }} gap-6" data-aos="fade-up" data-aos-delay="200">
                @foreach($testimonials as $testimonial)
                <div class="testimonial__card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-lg text-center">
                    <p class="testimonial__quote text-gray-600 dark:text-gray-300 italic mb-4">"{{ $testimonial['quote'] ?? '' }}"</p>
                    <p class="testimonial__author font-semibold text-gray-800 dark:text-white">{{ $testimonial['author'] ?? '' }}</p>
                    @if(!empty($testimonial['author_title']))
                    <p class="testimonial__author-title text-sm text-gray-500 dark:text-gray-400">{{ $testimonial['author_title'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 8. Appel à la Collaboration/Partenariat --}}
    <section class="section join" id="collaboration-accueil-section">
        <div class="join__container container" data-aos="zoom-in" data-aos-duration="800">
            <div class="join__bg">
                <img src="{{ $siteSettings['home_cta_bg_image_url'] ?? asset('assets/images/backgrounds/join_us_bg_default.jpg') }}"
                     alt="{{ __('Collaborer avec le CRPQA') }}" class="join__bg-img">
            </div>
            <div class="join__overlay"></div>
            <div class="join__data">
                <h2 class="join__title">{{ $siteSettings['home_cta_title'] ?? __('Façonnons l\'Avenir Ensemble') }}</h2>
                <p class="join__description">
                    {{ $siteSettings['home_cta_text'] ?? __('Le CRPQA est ouvert aux collaborations avec des institutions académiques, des entreprises et des chercheurs du monde entier. Contactez-nous pour explorer des opportunités de partenariat.') }}
                </p>
                <div class="join__buttons">
                    <a href="{{ $siteSettings['home_cta_button1_url'] ?? (Route::has('public.contact.form') ? route('public.contact.form') : '#') }}" class="button button--white">
                        {{ $siteSettings['home_cta_button1_text'] ?? __('Devenir Partenaire') }}
                        <ion-icon name="{{ $siteSettings['home_cta_button1_icon'] ?? 'people-circle-outline' }}" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ $siteSettings['home_cta_button2_url'] ?? (Route::has('public.page') && !empty($siteSettings['careers_page_slug']) ? route('public.page', ['staticPage' => $siteSettings['careers_page_slug']]) : '#') }}" class="button button--outline-white">
                        {{ $siteSettings['home_cta_button2_text'] ?? __('Nous Rejoindre') }}
                        <ion-icon name="{{ $siteSettings['home_cta_button2_icon'] ?? 'school-outline' }}" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script type="module">
    // Pas de scripts spécifiques ici, public-main.js gère les initialisations globales.
    // Assurez-vous que AOS est initialisé dans public-main.js ou votre layout principal.
</script>
@endpush
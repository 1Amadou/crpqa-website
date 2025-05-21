@extends('layouts.public')

@section('title', 'Accueil - ' . ($siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA'))
@section('meta_description', $siteSettings['site_description'] ?? 'Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.')
@section('og_title', 'Accueil - ' . ($siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA'))
@section('og_description', $siteSettings['site_description'] ?? 'Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.')
{{-- Vous pouvez définir une image OG spécifique pour l'accueil si différent du défaut --}}
{{-- @section('og_image', asset('assets/images/og_accueil.jpg')) --}}


@push('styles')
<style>
    /* Styles MINIMAUX spécifiques à la page d'accueil si absolument nécessaire. */
    /* Essayez de mettre tous les styles réutilisables dans style.css */

    /* Exemple : si la hauteur des images d'actualités sur l'accueil est spécifique */
    .home-news-card__image {
        height: 220px; /* Hauteur fixe comme dans votre style.css pour .news__img */
        object-fit: cover;
        width: 100%;
    }

    /* Fallback pour l'icône d'axe de recherche si $axis->icon_svg et $axis->color_hex ne sont pas définis */
    .research__card-icon-wrapper--default {
        background-color: rgba(0, 191, 255, 0.1); /* Fallback basé sur --accent-color-cyan */
    }
    .research__card-icon--default {
        color: var(--accent-color-cyan); /* Fallback */
    }
    .research__card-icon-wrapper svg.default-axis-icon { /* Si vous utilisez un SVG placeholder avec cette classe */
        width: 3rem;
        height: 3rem;
        color: var(--accent-color-cyan);
    }

    /* Assurer que les SVG injectés pour les axes de recherche ont une taille par défaut s'ils n'en ont pas une intrinsèque */
    .research__card-icon-wrapper > svg {
        width: 3rem; /* Correspond à .research__card-icon de style.css */
        height: 3rem;
        /* La couleur est gérée inline via style ou par le SVG lui-même */
    }

    /* La section Témoignages/Chiffres Clés pourrait avoir besoin de styles ici si non définie globalement */
    .testimonials-section {
        background-color: var(--container-color); /* Ou une autre couleur/image de fond */
    }
    .testimonial__card {
        background-color: var(--body-color);
        padding: var(--sp-2);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-light);
        text-align: center;
    }
    .testimonial__quote {
        font-style: italic;
        color: var(--text-color-light);
        margin-bottom: var(--sp-1);
    }
    .testimonial__author {
        font-weight: var(--font-semi-bold);
        color: var(--title-color);
    }
    .testimonial__author-title {
        font-size: var(--smaller-font-size);
        color: var(--text-color-light);
    }
    .key-figure__item {
        text-align: center;
    }
    .key-figure__number {
        font-size: var(--h1-font-size); /* Ou une taille de chiffre impactante */
        font-weight: var(--font-extra-bold);
        color: var(--accent-color-cyan);
        line-height: 1.1;
    }
    .key-figure__label {
        font-size: var(--normal-font-size);
        color: var(--text-color-light);
        margin-top: var(--sp-0-5);
    }

</style>
@endpush

@section('content')
    {{-- Variables du contrôleur attendues (rappel) :
         $latestNews (Collection, ~3), $upcomingEvents (Collection, ~2-3),
         $keyResearchAxes (Collection, ~3), $featuredPublications (Collection, ~3),
         $siteSettings (globale)
    --}}

    {{-- 1. Section Héros --}}
    <section class="hero section" id="accueil-hero" {{-- Changé l'ID pour éviter conflit avec un éventuel #accueil sur le nav --}}
             @if(isset($siteSettings['hero_bg_image_url']) && !empty($siteSettings['hero_bg_image_url']))
                 style="background-image: linear-gradient(135deg, rgba(10, 42, 77, 0.88) 0%, rgba(29, 44, 90, 0.92) 100%), url('{{ asset($siteSettings['hero_bg_image_url']) }}'); background-size: cover; background-position: center; background-attachment: fixed;"
             @else
                 {{-- Le style par défaut du .hero (dégradé bleu) de style.css s'appliquera --}}
             @endif
             data-aos="fade-in" data-aos-duration="800">
        <div class="hero__container container grid">
            <div class="hero__data" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                <h1 class="hero__title">
                    {{ $siteSettings['hero_main_title'] ?? 'L\'Avenir est' }}
                    <span class="hero__title-highlight">{{ $siteSettings['hero_highlight_word'] ?? 'Quantique' }}</span>.
                    @if(!empty($siteSettings['hero_subtitle_line2']))
                        <br class="hidden md:block"> {{ $siteSettings['hero_subtitle_line2'] }}
                    @else
                        <br class="hidden md:block"> Façonnez-le avec le CRPQA.
                    @endif
                </h1>
                <p class="hero__description">
                    {{ $siteSettings['hero_description'] ?? 'Au cœur de la révolution scientifique, le CRPQA est le fer de lance de la recherche en physique quantique au Mali, ouvrant la voie à des innovations qui transformeront notre monde.' }}
                </p>
                <div class="hero__buttons" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                    <a href="{{ $siteSettings['hero_button1_url'] ?? route('public.research_axes.index') }}" class="button button--white hero__button">
                        {{ $siteSettings['hero_button1_text'] ?? 'Découvrir nos Axes' }}
                        <ion-icon name="{{ $siteSettings['hero_button1_icon'] ?? 'arrow-forward-outline' }}" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ $siteSettings['hero_button2_url'] ?? route('public.publications.index') }}" class="button button--outline-white hero__button">
                        {{ $siteSettings['hero_button2_text'] ?? 'Nos Publications' }}
                        <ion-icon name="{{ $siteSettings['hero_button2_icon'] ?? 'book-outline' }}" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>

            @if(isset($siteSettings['hero_banner_image_url']) && !empty($siteSettings['hero_banner_image_url']))
            <div class="hero__img-bg" data-aos="zoom-in-left" data-aos-delay="500" data-aos-duration="1200">
                <img src="{{ asset($siteSettings['hero_banner_image_url']) }}"
                     alt="{{ $siteSettings['hero_banner_image_alt'] ?? 'Illustration de la physique quantique pour CRPQA' }}"
                     class="hero__img">
            </div>
            @endif
        </div>
        <div class="hero__scroll" data-aos="fade-up" data-aos-delay="800" data-aos-anchor=".hero__buttons">
            <a href="#apropos-section-home" class="hero__scroll-link">
                <ion-icon name="arrow-down-outline"></ion-icon> Explorer
            </a>
        </div>
    </section>

    {{-- 2. Brève Présentation du CRPQA --}}
    <section class="section about" id="apropos-section-home">
        <div class="about__container container grid">
            @if(!empty($siteSettings['about_home_image_url']))
            <div class="about__img-bg order-last md:order-first" data-aos="fade-right" data-aos-duration="1000">
                <img src="{{ asset($siteSettings['about_home_image_url']) }}"
                     alt="{{ $siteSettings['about_home_image_alt'] ?? 'Équipe et installations du CRPQA' }}"
                     class="about__img">
            </div>
            @endif
            <div class="about__data" data-aos="fade-left" data-aos-duration="1000">
                <span class="section__subtitle">{{ $siteSettings['about_home_subtitle'] ?? 'Notre Centre' }}</span>
                <h2 class="section__title about__title">
                    {{ $siteSettings['about_home_title'] ?? 'Au Cœur de la Révolution Quantique' }}
                </h2>
                <p class="about__description">
                    {{ $siteSettings['about_home_short_description'] ?? 'Fondé sur un héritage de plus de 55 ans d\'enseignement de la physique quantique, le CRPQA se positionne comme un pôle d\'excellence pour façonner l\'avenir.' }}
                </p>
                {{-- Si vous avez des points clés spécifiques pour l'accueil --}}
                @php
                    // Exemple de récupération de points clés depuis les settings, à adapter
                    // $aboutHomePoints = json_decode($siteSettings['about_home_points'] ?? '[]', true);
                    // if (empty($aboutHomePoints)) {
                        $aboutHomePoints = [
                            ['icon' => 'rocket-outline', 'text' => 'Recherche de pointe'],
                            ['icon' => 'school-outline', 'text' => 'Formation d\'excellence'],
                            ['icon' => 'earth-outline', 'text' => 'Impact international'],
                        ];
                    // }
                @endphp
                @if(!empty($aboutHomePoints))
                <ul class="about__details">
                    @foreach($aboutHomePoints as $i => $point)
                    <li data-aos="fade-left" data-aos-delay="{{ 200 + ($i * 100) }}">
                        <ion-icon name="{{ $point['icon'] ?? 'checkmark-circle-outline' }}" class="about__details-icon"></ion-icon>
                        <span>{{ $point['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                <div class="section__action mt-sp-2" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ route('public.page', ['staticPage' => $siteSettings['about_page_slug'] ?? 'a-propos']) }}" class="button button--outline">
                        En savoir plus sur nous <ion-icon name="arrow-forward-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Dernières Actualités --}}
    @if(isset($latestNews) && $latestNews->count() > 0)
    <section class="section news" id="actualites-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Restez Informé</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Dernières Actualités</h2>
            <div class="news__container grid" data-aos="fade-up" data-aos-delay="200">
                @foreach($latestNews->take(3) as $index => $newsItem) {{-- Limiter à 3 sur l'accueil --}}
                <article class="news__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__img-link" aria-label="Lire l'actualité : {{ $newsItem->title }}">
                        <img src="{{ $newsItem->image_url ? Storage::url($newsItem->image_url) : asset('assets/images/placeholders/news_default.jpg') }}"
                             alt="{{ $newsItem->image_alt ?? 'Image pour ' . $newsItem->title }}"
                             class="news__img home-news-card__image">
                    </a>
                    <div class="news__data">
                        <p class="news__meta">
                            <time datetime="{{ $newsItem->published_at->toDateString() }}">{{ $newsItem->published_at->isoFormat('D MMM YY') }}</time>
                            @if($newsItem->category)
                                <span class="news__category-separator">|</span>
                                <span class="news__category" style="color: {{ $newsItem->category->color ?? 'var(--accent-color-cyan)' }};">
                                    {{ $newsItem->category->name }}
                                </span>
                            @endif
                        </p>
                        <h3 class="news__title">
                            <a href="{{ route('public.news.show', $newsItem->slug) }}">
                                {{ Str::limit($newsItem->title, 60) }}
                            </a>
                        </h3>
                        <p class="news__description">
                            {{ Str::limit(strip_tags($newsItem->short_content ?? $newsItem->content), 100) }}
                        </p>
                        <a href="{{ route('public.news.show', $newsItem->slug) }}" class="news__link">
                            Lire la suite <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.news.index') }}" class="button">Toutes les Actualités</a>
            </div>
        </div>
    </section>
    @endif

    {{-- 4. Événements à Venir --}}
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
    <section class="section events" id="evenements-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Agenda</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Prochains Événements</h2>
            <div class="events__container grid" data-aos="fade-up" data-aos-delay="200">
                @foreach($upcomingEvents->take(3) as $index => $event) {{-- Limiter à 3 sur l'accueil --}}
                <article class="event__item" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    @if($event->start_date)
                    <div class="event__date">
                        <span class="event__day">{{ $event->start_date->format('d') }}</span>
                        <span class="event__month">{{ $event->start_date->isoFormat('MMM') }}</span>
                        <span class="event__year">{{ $event->start_date->format('Y') }}</span>
                    </div>
                    @endif
                    <div class="event__info">
                        <h3 class="event__title">
                             <a href="{{ route('public.events.show', $event->slug) }}">{{ Str::limit($event->title, 70) }}</a>
                        </h3>
                        <p class="event__time-location">
                            @if($event->start_date)
                            <span><ion-icon name="time-outline"></ion-icon> {{ $event->start_date->isoFormat('HH:mm') }}</span>
                            @endif
                            @if($event->location)
                            <span><ion-icon name="location-outline"></ion-icon> {{ Str::limit($event->location, 30) }}</span>
                            @endif
                        </p>
                        <p class="event__description">
                            {{ Str::limit(strip_tags($event->short_description ?? $event->description), 90) }}
                        </p>
                        <a href="{{ route('public.events.show', $event->slug) }}" class="event__link">
                            Détails de l'événement <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.events.index') }}" class="button">Tous les Événements</a>
            </div>
        </div>
    </section>
    @endif

    {{-- 5. Domaines de Recherche Clés --}}
    @if(isset($keyResearchAxes) && $keyResearchAxes->count() > 0)
    <section class="section research" id="recherche-accueil-section">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Nos Pôles d'Excellence</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Domaines de Recherche Clés</h2>
            <div class="research__container grid" data-aos="fade-up" data-aos-delay="200">
                @foreach($keyResearchAxes->take(4) as $index => $axis) {{-- Limiter à 3-4 sur l'accueil --}}
                <div class="research__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <div class="research__card-icon-wrapper {{ (!$axis->icon_svg && !$axis->color_hex) ? 'research__card-icon-wrapper--default' : '' }}"
                         style="{{ $axis->color_hex ? 'background-color: '.\App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.1).';' : '' }}">
                        @if($axis->icon_svg)
                            {!! $axis->icon_svg !!} {{-- Assurez-vous que le SVG a une taille (ex: class="w-12 h-12") ou via CSS --}}
                        @else
                            @php $defaultIcons = ['flask-outline', 'hardware-chip-outline', 'magnet-outline', 'color-filter-outline', 'analytics-outline', 'rocket-outline']; @endphp
                            <ion-icon name="{{ $defaultIcons[$index % count($defaultIcons)] }}"
                                      class="research__card-icon {{ !$axis->color_hex ? 'research__card-icon--default' : '' }}"
                                      style="{{ $axis->color_hex ? 'color: '.$axis->color_hex.';' : '' }}"></ion-icon>
                        @endif
                    </div>
                    <h3 class="research__card-title">{{ $axis->name }}</h3>
                    <p class="research__card-description">
                        {{ Str::limit(strip_tags($axis->short_description ?? $axis->description), 100) }}
                    </p>
                    <a href="{{ route('public.research_axes.show', $axis->slug) }}" class="research__card-link">
                        Explorer ce domaine <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.research_axes.index') }}" class="button button--outline">
                    Voir tous les domaines
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- 6. Publications Récentes/En Vedette --}}
    @if(isset($featuredPublications) && $featuredPublications->count() > 0)
    <section class="section publications-home" id="publications-accueil-section"> {{-- Nouvelle classe pour ciblage --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Nos Contributions</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Publications en Vedette</h2>
            {{-- Ici, un style de carte différent peut être utilisé, plus compact que .news__card --}}
            {{-- S'inspirer de .research__card ou créer .publication__card --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-sp-2" data-aos="fade-up" data-aos-delay="200">
                @foreach($featuredPublications->take(3) as $index => $publication)
                <article class="publication__card_home research__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}"> {{-- Réutilisation de research__card pour la structure --}}
                    <div class="research__card-icon-wrapper">
                        <ion-icon name="document-text-outline" class="research__card-icon"></ion-icon>
                    </div>
                    <h3 class="research__card-title publication__title_home">{{ Str::limit($publication->title, 70) }}</h3>
                    <p class="publication__authors_home text-sm text-slate-600 mb-2">
                        {{ Str::limit(collect($publication->authors_list)->pluck('name')->join(', '), 100) }}
                    </p>
                    <p class="research__card-description publication__meta_home text-xs text-slate-500">
                        {{ $publication->type }} - {{ $publication->publication_date->format('Y') }}
                        @if($publication->journal_conference_name) | {{ Str::limit($publication->journal_conference_name, 40) }} @endif
                    </p>
                    <a href="{{ $publication->doi_url ?? route('public.publications.show', $publication->slug) }}"
                       target="{{ $publication->doi_url ? '_blank' : '_self' }}"
                       class="research__card-link publication__link_home">
                        Lire la publication <ion-icon name="open-outline"></ion-icon>
                    </a>
                </article>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.publications.index') }}" class="button">Toutes les Publications</a>
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Témoignages ou Chiffres Clés (Optionnel) --}}
    @php
        $testimonials = isset($siteSettings['home_testimonials']) ? json_decode($siteSettings['home_testimonials'], true) : [];
        $keyFigures = isset($siteSettings['home_key_figures']) ? json_decode($siteSettings['home_key_figures'], true) : [];
    @endphp
    @if(!empty($testimonials) || !empty($keyFigures))
    <section class="section testimonials-section" id="impact- chiffres-accueil-section">
        <div class="container">
            @if(!empty($keyFigures))
            <div class="key-figures__container grid md:grid-cols-{{ count($keyFigures) > 4 ? 4 : count($keyFigures) }} gap-sp-2 mb-sp-3" data-aos="fade-up">
                @foreach($keyFigures as $figure)
                <div class="key-figure__item">
                    <div class="key-figure__number">{{ $figure['number'] ?? '0' }}</div>
                    <div class="key-figure__label">{{ $figure['label'] ?? 'Statistique' }}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($testimonials))
            <span class="section__subtitle" data-aos="fade-up">Ce qu'ils disent de nous</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Témoignages</h2>
            <div class="testimonials__container grid md:grid-cols-{{ count($testimonials) > 1 ? '2' : '1' }} lg:grid-cols-{{ count($testimonials) > 2 ? '3' : count($testimonials) }} gap-sp-2" data-aos="fade-up" data-aos-delay="200">
                @foreach($testimonials as $testimonial)
                <div class="testimonial__card">
                    <p class="testimonial__quote">"{{ $testimonial['quote'] ?? '' }}"</p>
                    <p class="testimonial__author">{{ $testimonial['author'] ?? '' }}</p>
                    @if(!empty($testimonial['author_title']))
                    <p class="testimonial__author-title">{{ $testimonial['author_title'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif


    {{-- 8. Appel à la Collaboration/Partenariat --}}
    {{-- La section .join de votre style.css est parfaite pour cela --}}
    <section class="section join" id="collaboration-accueil-section">
        <div class="join__container container" data-aos="zoom-in" data-aos-duration="800">
            <div class="join__bg">
                <img src="{{ $siteSettings['home_cta_bg_image_url'] ?? asset('assets/images/backgrounds/join_us_bg.jpg') }}"
                     alt="Collaborer avec le CRPQA" class="join__bg-img">
            </div>
            <div class="join__overlay"></div>
            <div class="join__data">
                <h2 class="join__title">{{ $siteSettings['home_cta_title'] ?? 'Façonnons l\'Avenir Ensemble' }}</h2>
                <p class="join__description">
                    {{ $siteSettings['home_cta_text'] ?? 'Le CRPQA est ouvert aux collaborations avec des institutions académiques, des entreprises et des chercheurs du monde entier. Contactez-nous pour explorer des opportunités de partenariat.' }}
                </p>
                <div class="join__buttons">
                    <a href="{{ $siteSettings['home_cta_button1_url'] ?? route('public.contact.form') }}" class="button button--white">
                       {{ $siteSettings['home_cta_button1_text'] ?? 'Devenir Partenaire' }}
                       <ion-icon name="{{ $siteSettings['home_cta_button1_icon'] ?? 'people-circle-outline' }}" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ $siteSettings['home_cta_button2_url'] ?? route('public.page', ['staticPage' => $siteSettings['careers_page_slug'] ?? 'carrieres']) }}" class="button button--outline-white">
                        {{ $siteSettings['home_cta_button2_text'] ?? 'Nous Rejoindre' }}
                        <ion-icon name="{{ $siteSettings['home_cta_button2_icon'] ?? 'school-outline' }}" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script type="module">
    // Aucun script spécifique à la page d'accueil pour le moment,
    // public-main.js gère les initialisations globales.
</script>
@endpush
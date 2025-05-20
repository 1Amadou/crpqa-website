{{-- resources/views/public/home.blade.php --}}
@extends('layouts.public')

@section('title', 'Accueil - ' . ($siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA'))
@section('meta_description', $siteSettings['site_description'] ?? 'Bienvenue au CRPQA. Nous explorons les frontières de la physique quantique pour façonner l\'avenir.')

@push('styles')
{{-- 
    Très peu de styles additionnels ici. 
    L'essentiel des styles provient de resources/css/style.css (importé via app.css)
    et des classes Tailwind CSS.
    Les styles en ligne dynamiques (images de fond, couleurs spécifiques) sont conservés.
--}}
<style>
    /* La variable --header-height est définie dans layouts.public ou style.css */
    /* Le style pour .hero-section est maintenant principalement dans .hero de style.css */
    /* L'animation bounceVertical est renommée bounceArrow dans votre style.css et appliquée aux ion-icon */

    /* Ajustement pour les images dans les cartes (si .news__img ou équivalent n'est pas suffisant) */
    /* Par exemple, si vous voulez une hauteur spécifique pour les cartes d'actualités sur la page d'accueil uniquement */
    .home-news-card__image { /* Classe spécifique si besoin d'override */
        height: 200px; /* Ou la valeur souhaitée, ex: 12.5rem */
        object-fit: cover;
    }

    /* Les styles spécifiques pour les icônes des axes de recherche (research__card-icon)
       sont maintenant gérés dans le composant .research__card de style.css */
</style>
@endpush

@section('content')
    {{-- Variables du contrôleur attendues :
         $latestNews (Collection, ~3), $upcomingEvents (Collection, ~2-3),
         $keyResearchAxes (Collection, ~3),
         $partners (Collection, ~5),
         $siteSettings (globale)
    --}}

    {{-- 1. Section Héros --}}
    <section class="hero section" id="accueil"
             @if(isset($siteSettings['hero_bg_image_url']) && !empty($siteSettings['hero_bg_image_url']))
                 style="background-image: linear-gradient(135deg, rgba(10, 42, 77, 0.85) 0%, rgba(29, 44, 90, 0.85) 100%), url('{{ $siteSettings['hero_bg_image_url'] }}'); background-size: cover; background-position: center; background-attachment: fixed;"
             @else
                 {{-- Le style par défaut du .hero (dégradé) de style.css s'appliquera --}}
             @endif
             data-aos="fade-in" data-aos-duration="800">
        <div class="hero__container container grid">
            <div class="hero__data" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                <h1 class="hero__title">
                    {{ $siteSettings['hero_main_title'] ?? 'L\'Avenir est' }}
                    <span class="hero__title-highlight">{{ $siteSettings['hero_highlight_word'] ?? 'Quantique' }}</span>.
                    <br class="hidden md:block"> {{ $siteSettings['hero_subtitle_line2'] ?? 'Façonnez-le avec le CRPQA.' }}
                </h1>
                <p class="hero__description">
                    {{ $siteSettings['hero_description'] ?? 'Au cœur de la révolution scientifique, le CRPQA est le fer de lance de la recherche en physique quantique au Mali, ouvrant la voie à des innovations qui transformeront notre monde.' }}
                </p>
                <div class="hero__buttons" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                    <a href="{{ route('public.research_axes.index') }}" class="button button--white hero__button">
                        Découvrir nos Axes <ion-icon name="arrow-forward-outline" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ route('public.publications.index') }}" class="button button--outline-white hero__button">
                        Nos Publications <ion-icon name="book-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>

            <div class="hero__img-bg" data-aos="zoom-in-left" data-aos-delay="500" data-aos-duration="1200">
                <img src="{{ $siteSettings['hero_banner_image_url'] ?? asset('images/placeholders/hero_crpqa_inspired.png') }}"
                     alt="Illustration de la physique quantique pour CRPQA"
                     class="hero__img">
            </div>
        </div>
        <div class="hero__scroll" data-aos="fade-up" data-aos-delay="800" data-aos-anchor=".hero__buttons">
            <a href="#apropos-section-home" class="hero__scroll-link">
                <ion-icon name="arrow-down-outline"></ion-icon> Explorer
            </a>
        </div>
    </section>

    {{-- 2. Section "À Propos" (Brève Présentation) --}}
    <section class="section about" id="apropos-section-home"> {{-- Ajout de la classe .about --}}
        <div class="about__container container grid">
            <div class="about__img-bg order-last md:order-first" data-aos="fade-right" data-aos-duration="1000">
                <img src="{{ $siteSettings['about_image_url'] ?? asset('images/placeholders/crpqa_about_section.jpg') }}"
                     alt="Équipe et installations du CRPQA"
                     class="about__img">
            </div>
            <div class="about__data" data-aos="fade-left" data-aos-duration="1000">
                <span class="section__subtitle">Notre Centre</span>
                <h2 class="section__title about__title">
                    {{ $siteSettings['about_home_title'] ?? 'Au Cœur de la Révolution Quantique' }}
                </h2>
                <p class="about__description">
                    {{ $siteSettings['about_home_text'] ?? 'Fondé sur un héritage de plus de 55 ans d\'enseignement de la physique quantique à l\'Université malienne, le CRPQA se positionne comme un pôle d\'excellence. Notre mission est de mener une recherche de haut niveau, de former la prochaine génération de scientifiques et de traduire les découvertes fondamentales en applications technologiques révolutionnaires.' }}
                </p>
                <ul class="about__details">
                    @php
                        $aboutPoints = $siteSettings['about_points'] ?? [
                            ['icon' => 'rocket-outline', 'text' => 'Recherche Fondamentale et Appliquée'],
                            ['icon' => 'school-outline', 'text' => 'Formation d\'Excellence et Mentorat'],
                            ['icon' => 'earth-outline', 'text' => 'Collaborations Nationales et Internationales'],
                            ['icon' => 'bulb-outline', 'text' => 'Innovation et Transfert Technologique'],
                        ];
                    @endphp
                    @foreach($aboutPoints as $i => $point)
                    <li data-aos="fade-left" data-aos-delay="{{ 200 + ($i * 100) }}">
                        <ion-icon name="{{ $point['icon'] ?? 'checkmark-circle-outline' }}" class="about__details-icon"></ion-icon>
                        <span>{{ $point['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="section__action" data-aos="fade-up" data-aos-delay="400">
                    <a href="{{ route('public.page', ['staticPage' => 'a-propos']) }}" class="button button--outline">
                        En savoir plus sur le CRPQA <ion-icon name="arrow-forward-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Section Domaines de Recherche --}}
    @if(isset($keyResearchAxes) && $keyResearchAxes->count() > 0)
    <section class="section research" id="recherche-section-home"> {{-- Classe .research --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Nos Domaines d'Expertise</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Explorer les Frontières de la Science</h2>
            <p class="home-section-description text-center max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                Découvrez les axes de recherche prioritaires qui animent notre centre et façonnent l'avenir de la physique quantique.
            </p>
            <div class="research__container grid" data-aos="fade-up" data-aos-delay="300">
                @foreach($keyResearchAxes as $index => $axis)
                <div class="research__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <div class="research__card-icon-wrapper" style="{{-- Style pour couleur de fond de l'icône si dynamique --}}
                         @if($axis->color_hex) background-color: {{ \App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.1) }}; @endif">
                        @if($axis->icon_svg)
                            {!! $axis->icon_svg !!} {{-- S'assurer que le SVG a les bonnes classes/taille --}}
                        @else
                            @php $defaultIcons = ['flask-outline', 'hardware-chip-outline', 'magnet-outline', 'color-filter-outline']; @endphp
                            <ion-icon name="{{ $defaultIcons[$index % count($defaultIcons)] }}" class="research__card-icon"
                                      style="{{-- Style pour couleur de l'icône si dynamique --}}
                                           @if($axis->color_hex) color: {{ $axis->color_hex }}; @endif"></ion-icon>
                        @endif
                    </div>
                    <h3 class="research__card-title">{{ $axis->name }}</h3>
                    <p class="research__card-description">
                        {{ Str::limit(strip_tags($axis->short_description ?? $axis->description), 120) }}
                    </p>
                    <a href="{{ route('public.research_axes.show', $axis) }}" class="research__card-link">
                        Détails <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('public.research_axes.index') }}" class="button">
                    Tous nos Axes de Recherche
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- 4. Section Impact (Vision/Pourquoi le Quantique) --}}
    <section class="section impact" id="impact-section-home">
        <div class="impact__container container grid">
            <div class="impact__data" data-aos="fade-right" data-aos-duration="1000">
                <span class="section__subtitle">La Promesse Quantique</span>
                <h2 class="section__title">
                    {{ $siteSettings['impact_title'] ?? 'Transformer le Futur, Aujourd\'hui' }}
                </h2>
                <p class="impact__description">
                    {{ $siteSettings['impact_text'] ?? 'La physique quantique n\'est pas seulement une théorie fascinante ; elle est le moteur d\'une révolution technologique qui redéfinit déjà tous les secteurs, de l\'informatique à la médecine, en passant par la communication et l\'énergie. Au CRPQA, nous sommes au premier plan de cette transformation.' }}
                </p>
                @if(isset($siteSettings['impact_quote']) && isset($siteSettings['impact_quote_author']))
                <blockquote class="impact__quote">
                    <p>"{{ $siteSettings['impact_quote'] }}"</p>
                    <cite>- {{ $siteSettings['impact_quote_author'] }}</cite>
                </blockquote>
                @endif
                <div class="section__action text-left mt-sp-2" data-aos="fade-up" data-aos-delay="200">
                     <a href="{{ route('public.page', ['staticPage' => $siteSettings['vision_page_slug'] ?? 'notre-vision']) }}" class="button button--white">
                         Notre Vision Stratégique <ion-icon name="trending-up-outline" class="button__icon"></ion-icon>
                     </a>
                </div>
            </div>
            <div class="impact__visual" data-aos="fade-left" data-aos-delay="200" data-aos-duration="1000">
                <img src="{{ $siteSettings['impact_image_url'] ?? asset('images/placeholders/quantum_visualization.jpg') }}"
                     alt="Visualisation de l'impact quantique"
                     class="impact__img">
            </div>
        </div>
    </section>

    {{-- 5. Section Actualités --}}
    @if(isset($latestNews) && $latestNews->count() > 0)
    <section class="section news" id="actualites-section-home"> {{-- Classe .news --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Restez Informé</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Dernières Actualités</h2>
            <p class="home-section-description text-center max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                Plongez au cœur de l'actualité du CRPQA : découvertes, publications, événements et collaborations.
            </p>
            <div class="news__container grid" data-aos="fade-up" data-aos-delay="300">
                @foreach($latestNews as $index => $newsItem)
                <article class="news__card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    <a href="{{ route('public.news.show', $newsItem) }}" class="news__img-link">
                        <img src="{{ $newsItem->image_url ?? asset('images/placeholders/news_list_'.($index % 3 + 1).'.jpg') }}"
                             alt="Image pour {{ $newsItem->title }}"
                             class="news__img home-news-card__image"> {{-- home-news-card__image pour hauteur spécifique si besoin --}}
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
                            <a href="{{ route('public.news.show', $newsItem) }}">
                                {{ Str::limit($newsItem->title, 65) }}
                            </a>
                        </h3>
                        <p class="news__description">
                            {{ Str::limit(strip_tags($newsItem->short_content ?? $newsItem->content), 120) }}
                        </p>
                        <a href="{{ route('public.news.show', $newsItem) }}" class="news__link">
                            Lire la suite <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('public.news.index') }}" class="button">Toutes les Actualités</a>
            </div>
        </div>
    </section>
    @endif

    {{-- 6. Section Événements --}}
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
    <section class="section events" id="evenements-section-home"> {{-- Classe .events --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Agenda</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Prochains Événements</h2>
            <p class="home-section-description text-center max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                Participez à nos conférences, séminaires et ateliers pour échanger avec des experts et découvrir les dernières tendances.
            </p>
            <div class="events__container grid" data-aos="fade-up" data-aos-delay="300">
                @foreach($upcomingEvents as $index => $event)
                <article class="event__item" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                    @if($event->start_date)
                    <div class="event__date">
                        <span class="event__day">{{ $event->start_date->format('d') }}</span>
                        <span class="event__month">{{ $event->start_date->isoFormat('MMM') }}</span>
                    </div>
                    @endif
                    <div class="event__info">
                        <h3 class="event__title">
                             <a href="{{ route('public.events.show', $event) }}">{{ $event->title }}</a>
                        </h3>
                        <p class="event__time-location">
                            @if($event->start_date)
                            <ion-icon name="time-outline"></ion-icon> {{ $event->start_date->isoFormat('HH:mm') }}
                            @endif
                            @if($event->location)
                            <span class="mx-1.5">|</span>
                            <ion-icon name="location-outline"></ion-icon> {{ Str::limit($event->location, 35) }}
                            @endif
                        </p>
                        <p class="event__description">
                            {{ Str::limit(strip_tags($event->description), 100) }}
                        </p>
                        <a href="{{ route('public.events.show', $event) }}" class="event__link">
                            Détails de l'événement <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="section__action" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('public.events.index') }}" class="button">Tous les Événements</a>
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Section Rejoindre/Collaboration (CTA) --}}
    <section class="section join" id="rejoindre-section-home"> {{-- La classe .join de votre style.css sera utilisée --}}
        <div class="join__container container" {{-- Pas de 'grid' ici, .join__container gère son contenu --}}
             data-aos="zoom-in" data-aos-duration="800">
            {{-- Image de fond et overlay gérés par .join__bg et .join__overlay dans style.css --}}
            <div class="join__bg">
                <img src="{{ $siteSettings['join_bg_image_url'] ?? asset('images/placeholders/join_collaborate_dark_bg.jpg') }}"
                     alt="Collaboration et recherche quantique" class="join__bg-img">
            </div>
            <div class="join__overlay"></div>

            <div class="join__data">
                <h2 class="join__title">
                    {{ $siteSettings['join_title'] ?? 'Rejoignez l\'Avant-Garde de la Recherche Quantique' }}
                </h2>
                <p class="join__description">
                    {{ $siteSettings['join_text'] ?? 'Que vous soyez chercheur, étudiant, ou une institution désireuse de collaborer, le CRPQA offre un environnement stimulant pour repousser les limites de la connaissance et bâtir le futur.' }}
                </p>
                <div class="join__buttons">
                    <a href="{{ route('public.contact.form') }}" class="button button--white"> {{-- ou button--secondary s'il existe et convient --}}
                        <ion-icon name="school-outline" class="button__icon"></ion-icon> Opportunités
                    </a>
                    <a href="{{ route('public.partners.index') }}" class="button button--outline-white">
                        <ion-icon name="people-outline" class="button__icon"></ion-icon> Devenir Partenaire
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Section Partenaires --}}
    @if(isset($partners) && $partners->count() > 0)
    <section class="section partners" id="partenaires-section-home"> {{-- Classe .partners --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Nos Collaborateurs</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Un Réseau d'Excellence</h2>
            <div class="partners__container" data-aos="fade-up" data-aos-delay="200">
                @foreach($partners->take(5) as $partner) {{-- Limité à 5 comme dans votre code original --}}
                <a href="{{ $partner->website_url ?? '#' }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="partner__link">
                    <img src="{{ $partner->logo_url ?? asset('images/placeholders/logo_partenaire_'.($loop->index % 2 + 1).'.png') }}"
                         alt="Logo {{ $partner->name }}" class="partner__logo">
                </a>
                @endforeach
            </div>
            @if($partners->count() > 5)
            <div class="section__action" data-aos="fade-up">
                <a href="{{ route('public.partners.index') }}" class="button button--outline">
                    Voir tous nos partenaires
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

@endsection

@push('scripts')
<script type="module">
    // Si vous avez besoin de JS spécifique à la page d'accueil
    // Par exemple, pour des animations plus complexes non gérées par AOS ou CSS pur.
    // console.log("Page d'accueil chargée et scripts spécifiques initialisés.");
</script>
@endpush
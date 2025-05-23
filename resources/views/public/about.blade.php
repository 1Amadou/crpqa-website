@extends('layouts.public')

@section('title', $pageTitle ?? "À Propos du CRPQA")


@push('page-styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
@endpush


@section('content')
    <main class="main">
        {{-- Section Hero --}}
        <section class="about-hero section--bg" data-aos="fade-in">
            <div class="about-hero__container container grid">
                <div class="about-hero__data">
                    <h1 class="about-hero__title" data-aos="zoom-in-down">{{ $pageTitle }}</h1>
                    <nav aria-label="breadcrumb" style="display: flex; justify-content: center;" data-aos="fade-up" data-aos-delay="200">
                        <ol class="breadcrumb" style="background-color: transparent; padding: 0; margin: 0; list-style: none; display: flex; gap: 0.3rem;">
                            <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Accueil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">/ {{ $pageTitle }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        <div class="about-content">

            {{-- Introduction --}}
            <section class="section container" id="introduction" aria-labelledby="introduction-title">
                <span class="section__supertitle" data-aos="fade-up">Découvrir le {{ $siteSettings['site_acronym'] ?? 'CRPQA' }}</span>
                <h2 class="section__title--main" id="introduction-title" data-aos="fade-up" data-aos-delay="100">Qui Sommes-Nous ?</h2>

                <div class="alternating-layout">
                    <div class="text-block" data-aos="fade-right">
                        <span class="section__subtitle">{{ $siteSettings['site_name'] ?? 'CRPQA' }}</span>
                        <h3 class="section__title">{{ $crpqaInfo['preambule_titre'] ?? 'Notre Centre' }}</h3>
                        <p class="text-content">{{ $crpqaInfo['preambule_contenu'] ?? 'Plus d\'informations sur notre préambule seront bientôt disponibles.' }}</p>
                    </div>
                    <div class="image-block" data-aos="fade-left" data-aos-delay="150">
                        <img src="{{ asset($crpqaInfo['preambule_image'] ?? 'img/placeholders/crpqa_lab_concept.jpg') }}" alt="Illustration conceptuelle du laboratoire CRPQA">
                    </div>
                </div>
            </section>

            {{-- Historique avec Timeline --}}
            <section class="section container" id="historique" aria-labelledby="historique-title">
                <span class="section__supertitle" data-aos="fade-up">Notre Parcours</span>
                <h2 class="section__title--main" id="historique-title" data-aos="fade-up" data-aos-delay="100">{{ $crpqaInfo['historique_titre_section'] ?? 'Notre Histoire en Quelques Dates' }}</h2>
                
                {{-- $crpqaInfo['historique_evenements'] devrait être un tableau d'objets/tableaux
                     chaque élément ayant au moins 'annee', 'titre', 'description'.
                     Exemple de structure attendue pour chaque événement :
                     [
                         'annee' => '2010',
                         'titre' => 'Fondation du Centre',
                         'description' => 'Le CRPQA a été officiellement créé pour répondre aux besoins croissants...',
                         'icone' => 'uil-award' // Optionnel
                     ]
                --}}
                @if(isset($crpqaInfo['historique_evenements']) && !empty($crpqaInfo['historique_evenements']) && is_array($crpqaInfo['historique_evenements']))
                    <div class="timeline-wrapper" data-aos="fade-up" data-aos-delay="200">
                        <ul class="timeline">
                            @foreach($crpqaInfo['historique_evenements'] as $index => $event)
                            <li class="timeline-item">
                                <div class="timeline-icon" data-aos="zoom-in" data-aos-delay="{{ 200 + ($index * 100) }}">
                                    <i class="uil {{ $event['icone'] ?? 'uil-calendar-alt' }}"></i>
                                </div>
                                <div class="timeline-content {{ $index % 2 == 0 ? 'right' : 'left' }}" data-aos="{{ $index % 2 == 0 ? 'fade-left' : 'fade-right' }}" data-aos-delay="{{ 250 + ($index * 100) }}">
                                    <span class="timeline-year">{{ $event['annee'] ?? 'Date Inconnue' }}</span>
                                    <h4 class="timeline-title">{{ $event['titre'] ?? 'Événement Important' }}</h4>
                                    <p class="timeline-description">{{ $event['description'] ?? 'Description de l\'événement à venir.' }}</p>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center" data-aos="fade-up">
                        <p class="text-muted">{{ $crpqaInfo['historique_contenu_fallback'] ?? 'Les détails de notre historique seront partagés ici prochainement.' }}</p>
                         @if(isset($crpqaInfo['historique_image_fallback']))
                        <img src="{{ asset($crpqaInfo['historique_image_fallback'] ?? 'img/placeholders/timeline_innovation.jpg') }}" alt="Illustration de l'innovation et de l'histoire" style="max-width:400px; margin-top:20px; border-radius: var(--radius-medium);">
                        @endif
                    </div>
                @endif
            </section>

            {{-- Mission, Vision, Valeurs --}}
            <section class="section mission-vision-values" id="mission-vision-valeurs" aria-labelledby="mvv-title" data-aos="fade-up">
                <div class="container">
                    <h2 class="section__title--main" id="mvv-title" data-aos="fade-up" data-aos-delay="100">Nos Engagements Fondamentaux</h2>
                    <div class="row">
                        <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="200">
                            <div class="icon-box">
                                <i class="uil {{ $crpqaInfo['mission_icon'] ?? 'uil-rocket' }} icon-box__icon"></i>
                                <h3 class="icon-box__title">{{ $crpqaInfo['mission_titre'] ?? 'Mission' }}</h3>
                                <p class="text-content">{{ $crpqaInfo['mission_contenu'] ?? 'Notre mission sera détaillée ici.' }}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="350">
                            <div class="icon-box">
                                <i class="uil {{ $crpqaInfo['vision_icon'] ?? 'uil-eye' }} icon-box__icon"></i>
                                <h3 class="icon-box__title">{{ $crpqaInfo['vision_titre'] ?? 'Vision' }}</h3>
                                <p class="text-content">{{ $crpqaInfo['vision_contenu'] ?? 'Notre vision pour l\'avenir sera bientôt disponible.' }}</p>
                            </div>
                        </div>
                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                             <div class="icon-box">
                                <i class="uil {{ $crpqaInfo['valeurs_icon'] ?? 'uil-diamond' }} icon-box__icon"></i>
                                <h3 class="icon-box__title">{{ $crpqaInfo['valeurs_titre'] ?? 'Valeurs' }}</h3>
                                @if(!empty($crpqaInfo['valeurs_liste']) && is_array($crpqaInfo['valeurs_liste']))
                                <ul class="values-list-styled">
                                    @foreach ($crpqaInfo['valeurs_liste'] as $valeur)
                                        <li><i class="uil uil-check-circle"></i>{{ $valeur }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-content">{{ $crpqaInfo['valeurs_contenu_alternatif'] ?? 'Nos valeurs fondamentales seront listées ici.' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            {{-- Message du Directeur --}}
            @if(isset($crpqaInfo['directeur_nom']) && $crpqaInfo['directeur_nom'])
            <section class="section director-section container" id="mot-directeur" aria-labelledby="directeur-title-main" data-aos="fade-up">
                <span class="section__supertitle director-card__supertitle text-center" data-aos="fade-up">La Parole à</span>
                <h2 class="section__title--main director-card__main-title" id="directeur-title-main" data-aos="fade-up" data-aos-delay="100">{{ $crpqaInfo['directeur_titre_section'] ?? 'Message de la Direction' }}</h2>
                <div class="director-card" data-aos="zoom-in-up" data-aos-delay="200">
                    <img src="{{ asset( $siteSettings['director_photo_path'] ?? $crpqaInfo['directeur_photo_exemple'] ?? 'img/placeholders/director_avatar.png' ) }}" 
                         alt="Photo de {{ $crpqaInfo['directeur_nom'] }}" class="director-card__photo">
                    <div class="director-card__content">
                        <h3 class="director-card__name">{{ $crpqaInfo['directeur_nom'] }}</h3>
                        <p class="director-card__position">{{ $crpqaInfo['directeur_position'] ?? 'Directeur du CRPQA' }}</p>
                        <p class="text-content">
                            {{ $crpqaInfo['directeur_message'] ?? 'Le message du directeur sera affiché ici prochainement.' }}
                        </p>
                    </div>
                </div>
            </section>
            @endif

            {{-- Section USTTB / FST --}}
            @if(!empty($crpqaInfo['usttb_fst_titre']))
            <section class="section usttb-highlight" id="usttb-fst" aria-labelledby="usttb-title" data-aos="fade-up">
                <div class="container">
                    <div class="alternating-layout">
                        <div class="text-block" data-aos="fade-right">
                            <span class="section__subtitle">Notre Fondation Académique</span>
                            <h3 class="section__title" id="usttb-title">{{ $crpqaInfo['usttb_fst_titre'] }}</h3>
                            <p class="text-content">{{ $crpqaInfo['usttb_fst_contenu'] ?? 'Informations sur notre lien avec l\'USTTB / FST à venir.' }}</p>
                        </div>
                        <div class="image-block" data-aos="fade-left" data-aos-delay="150">
                            <img src="{{ asset( $siteSettings['usttb_logo_path'] ?? $crpqaInfo['usttb_logo_placeholder'] ?? 'img/placeholders/usttb_logo.png' ) }}" alt="Logo USTTB">
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- Section Notre Équipe (Aperçu des Chercheurs) --}}
            @if(isset($featuredResearchers) && $featuredResearchers->count() > 0)
            <section class="section container" id="notre-equipe" aria-labelledby="equipe-title" data-aos="fade-up">
                <span class="section__supertitle" data-aos="fade-up">Nos Experts</span>
                <h2 class="section__title--main" id="equipe-title" data-aos="fade-up" data-aos-delay="100">Une Équipe d'Excellence</h2>
                <div class="team-grid">
                    @foreach ($featuredResearchers as $researcher)
                        <div class="researcher-card" data-aos="zoom-in-up" data-aos-delay="{{ ($loop->iteration % 3 + 1) * 100 }}">
                            <img src="{{ $researcher->photo_path ? asset('storage/' . $researcher->photo_path) : asset('img/placeholders/user_avatar.png') }}" 
                                 alt="Photo de {{ $researcher->name_fr ?? $researcher->full_name }}" class="researcher-card__image">
                            <h4 class="researcher-card__name">{{ $researcher->name_fr ?? $researcher->full_name }}</h4>
                            <p class="researcher-card__position">{{ $researcher->position_fr ?? 'Chercheur' }}</p>
                        </div>
                    @endforeach
                </div>
                @if(Route::has('public.team.index')) {{-- Adaptez le nom de la route si besoin --}}
                <div class="text-center mt-4 pt-2" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ route('public.team.index') }}" class="button button--flex">
                        Découvrir Toute l'Équipe <i class="uil uil-arrow-right button__icon"></i>
                    </a>
                </div>
                @endif
            </section>
            @elseif(config('app.env') === 'local' || config('app.env') === 'development')
            <section class="section container" id="notre-equipe-fallback" data-aos="fade-up">
                 <span class="section__supertitle">Nos Experts</span>
                <h2 class="section__title--main">Une Équipe d'Excellence</h2>
                <p class="text-center text-muted">Aucun chercheur à afficher pour le moment. Veuillez vérifier la configuration ou ajouter des chercheurs.</p>
            </section>
            @endif

            {{-- Section Dernières Publications (Chargement AJAX) --}}
            <section class="section publications-section" id="latest-publications" aria-labelledby="publications-title" data-aos="fade-up">
                <div class="container">
                    <span class="section__supertitle" data-aos="fade-up">Recherche & Innovation</span>
                    <h2 class="section__title--main" id="publications-title" data-aos="fade-up" data-aos-delay="100">Nos Dernières Publications</h2>
                    <div id="latest-publications-content" class="latest-publications-container text-center" data-aos="fade-up" data-aos-delay="200">
                        {{-- Le contenu sera chargé ici par JavaScript --}}
                        <div class="loading-spinner"></div> {{-- Sera stylé en CSS --}}
                        <p class="text-muted loading-message">Chargement des publications...</p>
                    </div>
                    @if(Route::has('public.publications.index')) {{-- Adaptez le nom de la route --}}
                    <div class="text-center mt-4 pt-2" data-aos="fade-up" data-aos-delay="300">
                        <a href="{{ route('public.publications.index') }}" class="button button--flex">
                            Voir Toutes les Publications <i class="uil uil-arrow-right button__icon"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Section Nos Partenaires (Carrousel) --}}
            @if(isset($featuredPartners) && $featuredPartners->count() > 0)
            <section class="section partners-section" id="nos-partenaires" aria-labelledby="partenaires-title" data-aos="fade-up">
                 <div class="container">
                    <span class="section__supertitle" data-aos="fade-up">Nos Collaborations</span>
                    <h2 class="section__title--main" id="partenaires-title" data-aos="fade-up" data-aos-delay="100">Partenaires Stratégiques</h2>
                    
                    {{-- Structure pour SwiperJS --}}
                    <div class="swiper partners-carousel" data-aos="fade-up" data-aos-delay="200">
                        <div class="swiper-wrapper">
                            @foreach($featuredPartners as $partner)
                            <div class="swiper-slide">
                                <div class="partner-logo-item"> {{-- Garder cette classe pour le style individuel du logo --}}
                                    <a href="{{ $partner->website_url ?? '#' }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name_fr ?? $partner->name }}">
                                        <img src="{{ $partner->logo_path ? asset('storage/' . $partner->logo_path) : asset('img/placeholders/partner_logo.png') }}" alt="Logo {{ $partner->name_fr ?? $partner->name }}">
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        {{-- Pagination (optionnel) --}}
                        <div class="swiper-pagination partners-carousel__pagination"></div>
                        {{-- Flèches de navigation (optionnel) --}}
                        <div class="swiper-button-prev partners-carousel__nav-prev"></div>
                        <div class="swiper-button-next partners-carousel__nav-next"></div>
                    </div>
                 </div>
                 @if(Route::has('public.partners.index')) {{-- Adaptez le nom de la route --}}
                 <div class="text-center mt-4 pt-2" data-aos="fade-up" data-aos-delay="300">
                     <a href="{{ route('public.partners.index') }}" class="button button--flex">
                         Voir Tous Nos Partenaires <i class="uil uil-arrow-right button__icon"></i>
                     </a>
                 </div>
                 @endif
            </section>
            @endif

            {{-- Sections Additionnelles (Structure, Gouvernance, Infrastructure) --}}
            @if(!empty($crpqaInfo['structure_titre']) || !empty($crpqaInfo['gouvernance_titre']) || !empty($crpqaInfo['infrastructure_titre']))
            <section class="section container" id="organisation-details" aria-labelledby="organisation-title" data-aos="fade-up">
                <span class="section__supertitle" data-aos="fade-up">Fonctionnement Interne</span>
                <h2 class="section__title--main" id="organisation-title" data-aos="fade-up" data-aos-delay="100">Organisation et Capacités</h2>
                <div class="row">
                    @if(!empty($crpqaInfo['structure_titre']))
                    <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="200">
                        <article class="organisation-details__item" aria-labelledby="structure-subtitle">
                            <h3 class="section__title" id="structure-subtitle">{{ $crpqaInfo['structure_titre'] }}</h3>
                            <p class="text-content">{{ $crpqaInfo['structure_contenu'] ?? 'Les informations sur notre structure seront bientôt disponibles.' }}</p>
                        </article>
                    </div>
                    @endif
                    @if(!empty($crpqaInfo['gouvernance_titre']))
                    <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="350">
                         <article class="organisation-details__item" aria-labelledby="gouvernance-subtitle">
                            <h3 class="section__title" id="gouvernance-subtitle">{{ $crpqaInfo['gouvernance_titre'] }}</h3>
                            <p class="text-content">{{ $crpqaInfo['gouvernance_contenu'] ?? 'Les détails sur notre gouvernance seront publiés prochainement.' }}</p>
                        </article>
                    </div>
                    @endif
                    @if(!empty($crpqaInfo['infrastructure_titre']))
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                        <article class="organisation-details__item" aria-labelledby="infrastructure-subtitle">
                            <h3 class="section__title" id="infrastructure-subtitle">{{ $crpqaInfo['infrastructure_titre'] }}</h3>
                            <p class="text-content">{{ $crpqaInfo['infrastructure_contenu'] ?? 'Un aperçu de notre infrastructure sera bientôt fourni.' }}</p>
                        </article>
                    </div>
                    @endif
                </div>
            </section>
            @endif

        </div> {{-- Fin de .about-content --}}

    </main>
@endsection

@push('page-scripts')
{{-- Rappel: Si vous utilisez SwiperJS pour le carrousel, assurez-vous d'inclure son JS.
     Soit globalement dans layouts.public, soit ici si ce n'est que pour cette page.
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
--}}
{{-- Votre script public-main.js sera chargé globalement, mais si vous avez des initialisations
     spécifiques à cette page qui doivent être exécutées après le script principal,
     vous pourriez les mettre ici ou les inclure conditionnellement dans public-main.js --}}
@endpush
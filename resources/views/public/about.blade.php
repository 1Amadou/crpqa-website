@extends('layouts.public')

{{--
    Pour cette page, vous pourriez vouloir passer des données spécifiques depuis votre PublicPageController
    ou récupérer des champs spécifiques de $siteSettings si vous les avez structurés pour la page "À Propos".
    Exemple: $pageData = $staticPage->contentAsJson() ?? []; // Si le contenu est en JSON
    Exemple: $aboutSettings = $siteSettings['about_page_content'] ?? []; // Si les settings contiennent une section "À Propos"
--}}
@php
    // Tentative de récupération des données pour la page "À propos"
    // Ceci est un exemple, adaptez-le à la manière dont vous stockez/récupérez le contenu de cette page
    $aboutPageData = [];
    if (isset($staticPage) && is_object($staticPage) && method_exists($staticPage, 'getMeta')) {
        // Si $staticPage est un modèle avec des métadonnées ou du contenu structuré
        // $aboutPageData['title'] = $staticPage->getLocalizedField('title');
        // $aboutPageData['preamble'] = $staticPage->getLocalizedField('preamble_content'); // champ personnalisé
        // $aboutPageData['history_intro'] = $staticPage->getLocalizedField('history_intro');
        // $aboutPageData['timeline_events'] = json_decode($staticPage->getLocalizedField('history_timeline_json') ?? '[]', true);
        // $aboutPageData['mission_text'] = $staticPage->getLocalizedField('mission_text');
        // ... etc.
    }

    // Fallbacks génériques si les données spécifiques ne sont pas là (tirés de $siteSettings ou du doc Word)
    $pageTitle = $aboutPageData['title'] ?? $siteSettings['about_page_title'] ?? "À Propos du CRPQA";
    $metaDescription = $aboutPageData['meta_description'] ?? $siteSettings['about_page_meta_description'] ?? "Découvrez l'histoire, la mission, la vision et les valeurs du Centre de Recherche en Physique Quantique et ses Applications.";
    $ogImage = $aboutPageData['og_image'] ?? $siteSettings['about_page_og_image'] ?? ($siteSettings['og_image_url'] ?? null);
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', Str::startsWith($ogImage, ['http://', 'https://']) ? $ogImage : Storage::url($ogImage))
@endif

@push('styles')
<style>
    /* Styles spécifiques MINIMAUX pour la page "À Propos" si nécessaire. */
    /* La plupart des styles doivent provenir de style.css (ex: .page-hero, .timeline, etc.) */

    .director-message__img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        float: left; /* ou utiliser flexbox pour le layout */
        margin-right: var(--sp-2);
        margin-bottom: var(--sp-1);
        border: 4px solid var(--border-color);
        box-shadow: var(--shadow-light);
    }
    @media screen and (max-width: 767px) {
        .director-message__img {
            float: none;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: var(--sp-1-5);
        }
        .director-message__content {
            clear: both; /* S'assurer que le texte ne flotte pas si l'image est en float none */
        }
    }
    .governance-member {
        text-align: center;
        margin-bottom: var(--sp-1-5);
    }
    .governance-member img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto var(--sp-0-75);
        border: 3px solid var(--container-color);
        box-shadow: var(--shadow-sm);
    }
    .governance-member strong {
        display: block;
        color: var(--title-color);
    }
    .governance-member span {
        font-size: var(--small-font-size);
        color: var(--text-color-light);
    }
    .infrastructure__item ion-icon {
        font-size: 2rem;
        color: var(--accent-color-cyan);
        margin-bottom: var(--sp-0-5);
    }
</style>
@endpush

@section('content')

    {{-- 1. Bannière de Page (Page Hero) --}}
    {{-- Utilisation de la classe .page-hero de votre style.css --}}
    <section class="page-hero"
             style="background-image: linear-gradient(rgba(10, 42, 77, 0.8), rgba(29, 44, 90, 0.75)), url('{{ $aboutPageData['hero_bg_image'] ?? $siteSettings['about_hero_bg_image_url'] ?? asset('assets/images/backgrounds/about_hero_default.jpg') }}');">
        <div class="container" data-aos="fade-up">
            <h1 class="page-hero__title">
                {{ $pageTitle }}
            </h1>
            @if(!empty($siteSettings['about_hero_subtitle']))
                <p class="page-hero__subtitle">
                    {{ $siteSettings['about_hero_subtitle'] }}
                </p>
            @endif
        </div>
    </section>

    {{-- 2. Préambule / Introduction --}}
    {{-- Utilisation de .about-detailed__container de votre style.css --}}
    <section class="section about-detailed" id="introduction">
        <div class="about-detailed__container container grid items-center">
            <div class="about-detailed__data" data-aos="fade-right">
                <span class="section__subtitle">{{ $siteSettings['about_intro_subtitle'] ?? 'Notre Essence' }}</span>
                <h2 class="section__title about-detailed__title-alt">
                    {{ $siteSettings['about_intro_title'] ?? 'Le CRPQA : Aux Frontières de la Science' }}
                </h2>
                <div class="prose max-w-none lg:prose-lg text-gray-700 about-detailed__text">
                    {{-- Le contenu ici viendra de $siteSettings['about_intro_content'] ou d'un champ de $staticPage --}}
                    {!! $aboutPageData['preamble_html'] ?? $siteSettings['about_intro_content_html'] ?? '<p>La physique quantique, née des paradoxes de la physique classique à l\'aube du XXe siècle, a révolutionné notre compréhension de la matière et de la lumière à l\'échelle atomique et subatomique. Face aux défis et aux opportunités immenses qu\'offre cette discipline, l\'Université du Mali a initié la création du Consortium de Recherche en Physique Quantique et de ses Applications (CRPQA). Ce centre vise à devenir un pôle d\'excellence régional, catalysant la recherche fondamentale, la formation de haut niveau et le développement d\'applications novatrices issues des technologies quantiques.</p><p>Le CRPQA s\'inscrit dans une démarche visionnaire pour doter le Mali et la sous-région des compétences et des infrastructures nécessaires pour participer activement à la seconde révolution quantique.</p>' !!}
                </div>
            </div>
            @if(!empty($siteSettings['about_intro_image_url']))
            <div class="about-detailed__image" data-aos="fade-left" data-aos-delay="200">
                <img src="{{ asset($siteSettings['about_intro_image_url']) }}" alt="{{ $siteSettings['about_intro_image_alt'] ?? 'Illustration du CRPQA' }}">
            </div>
            @endif
        </div>
    </section>

    {{-- 3. Historique (Timeline) --}}
    {{-- Utilisation de .history et .timeline de votre style.css --}}
    @php
        // $timelineEvents = $aboutPageData['timeline_events'] ?? json_decode($siteSettings['about_history_timeline_json'] ?? '[]', true);
        // Pour l'exemple, données en dur. Remplacez par vos données dynamiques.
        $timelineEvents = json_decode($siteSettings['about_history_timeline_json'] ?? '[
            {"year":"1960s-2000s", "title":"Fondations et Enseignement Initial", "description":"Plus de 55 ans d\'enseignement de la physique quantique à l\'Université malienne, posant les bases de l\'expertise locale.", "icon":"school-outline"},
            {"year":"2010s", "title":"Prise de Conscience et Premières Initiatives", "description":"Reconnaissance croissante de l\'importance stratégique des technologies quantiques et premières discussions pour un centre dédié.", "icon":"bulb-outline"},
            {"year":"2023", "title":"Conceptualisation du CRPQA", "description":"Développement du concept et des objectifs du Consortium de Recherche en Physique Quantique et de ses Applications.", "icon":"document-text-outline"},
            {"year":"2024+", "title":"Lancement et Développement", "description":"Mise en place opérationnelle du CRPQA, recrutement des équipes, établissement des premiers partenariats et lancement des projets de recherche.", "icon":"rocket-outline"}
        ]', true);
         if (json_last_error() !== JSON_ERROR_NONE) $timelineEvents = [];
    @endphp
    @if(!empty($timelineEvents))
    <section class="section history" id="historique">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Notre Parcours</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Historique du Centre</h2>
            @if(!empty($siteSettings['about_history_intro_text']))
                <p class="text-center max-w-3xl mx-auto mb-sp-3 text-gray-600" data-aos="fade-up" data-aos-delay="150">
                    {{ $siteSettings['about_history_intro_text'] }}
                </p>
            @endif
            <div class="timeline timeline-ultime" data-aos="fade-up" data-aos-delay="200"> {{-- Utilisation de .timeline-ultime de style.css --}}
                @foreach($timelineEvents as $index => $event)
                <div class="timeline-ultime__item {{ $index % 2 == 0 ? 'timeline-ultime__item--left' : 'timeline-ultime__item--right' }}">
                    <div class="timeline-ultime__icon">
                        <ion-icon name="{{ $event['icon'] ?? 'ellipse-outline' }}"></ion-icon>
                    </div>
                    <div class="timeline-ultime__content">
                        <h3 class="timeline-ultime__title">{{ $event['title'] }}</h3>
                        @if(!empty($event['year'])) <span class="timeline-ultime__year">{{ $event['year'] }}</span> @endif
                        <p>{{ $event['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 4. Mission & 5. Vision (peut être combiné ou séparé) --}}
    {{-- Utilisation de .mission-vision__container de votre style.css --}}
    @if(!empty($siteSettings['about_mission_text']) || !empty($siteSettings['about_vision_text']))
    <section class="section mission-vision" id="mission-vision">
        <div class="container">
            <div class="mission-vision__container grid">
                @if(!empty($siteSettings['about_mission_text']))
                <div class="mission-vision__card mission" data-aos="fade-right">
                    <ion-icon name="{{ $siteSettings['about_mission_icon'] ?? 'flag-outline' }}" class="mission-vision__icon"></ion-icon>
                    <h3 class="mission-vision__title">{{ $siteSettings['about_mission_title'] ?? 'Notre Mission' }}</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! $siteSettings['about_mission_text'] !!}
                    </div>
                </div>
                @endif

                @if(!empty($siteSettings['about_vision_text']))
                <div class="mission-vision__card vision" data-aos="fade-left" data-aos-delay="100">
                    <ion-icon name="{{ $siteSettings['about_vision_icon'] ?? 'eye-outline' }}" class="mission-vision__icon"></ion-icon>
                    <h3 class="mission-vision__title">{{ $siteSettings['about_vision_title'] ?? 'Notre Vision' }}</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! $siteSettings['about_vision_text'] !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- 6. Valeurs --}}
    {{-- Utilisation de .values et .value__card de votre style.css --}}
    @php
        // $values = $aboutPageData['values_json'] ?? json_decode($siteSettings['about_values_json'] ?? '[]', true);
        $values = json_decode($siteSettings['about_values_json'] ?? '[
            {"icon":"bulb-outline", "title":"Innovation", "description":"Repousser les frontières de la connaissance par une recherche créative et audacieuse."},
            {"icon":"ribbon-outline", "title":"Excellence", "description":"Viser les plus hauts standards dans toutes nos activités de recherche, de formation et de service."},
            {"icon":"people-outline", "title":"Collaboration", "description":"Favoriser un environnement ouvert et collaboratif, tant en interne qu\'avec nos partenaires nationaux et internationaux."},
            {"icon":"shield-checkmark-outline", "title":"Intégrité", "description":"Conduire nos recherches avec rigueur éthique, transparence et responsabilité."},
            {"icon":"trending-up-outline", "title":"Impact", "description":"Traduire nos découvertes en applications concrètes bénéficiant à la société."}
        ]', true);
         if (json_last_error() !== JSON_ERROR_NONE) $values = [];
    @endphp
    @if(!empty($values))
    <section class="section values" id="valeurs">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">Nos Principes</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Valeurs Fondamentales</h2>
            <div class="values__container grid" data-aos="fade-up" data-aos-delay="200">
                @foreach($values as $value)
                <div class="value__card">
                    @if(!empty($value['icon'])) <ion-icon name="{{ $value['icon'] }}" class="value__icon"></ion-icon> @endif
                    <h3 class="value__title">{{ $value['title'] }}</h3>
                    <p class="value__description">{{ $value['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Structure / Organisation (Exemple simple) --}}
    @if(!empty($siteSettings['about_organisation_title']) && !empty($siteSettings['about_organisation_content_html']))
    <section class="section organization" id="organisation">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings['about_organisation_subtitle'] ?? 'Notre Fonctionnement' }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings['about_organisation_title'] }}</h2>
            <div class="prose max-w-none lg:prose-lg mx-auto text-gray-700 text-center" data-aos="fade-up" data-aos-delay="200">
                {!! $siteSettings['about_organisation_content_html'] !!}
                {{-- Si vous avez une image d'organigramme :
                @if(!empty($siteSettings['about_organisation_chart_image_url']))
                    <img src="{{ asset($siteSettings['about_organisation_chart_image_url']) }}"
                         alt="{{ $siteSettings['about_organisation_chart_image_alt'] ?? 'Organigramme du CRPQA' }}"
                         class="mt-sp-2 rounded-lg shadow-md mx-auto">
                @endif
                --}}
            </div>
        </div>
    </section>
    @endif

    {{-- 8. Message du Directeur --}}
    @if(!empty($siteSettings['about_director_message_html']) && !empty($siteSettings['about_director_name']))
    <section class="section director-message bg-gray-50" id="message-directeur"> {{-- bg-gray-50 pour alternance si section paire/impaire ne s'applique pas --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings['about_director_section_subtitle'] ?? 'Un Mot de Notre Leadership' }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings['about_director_section_title'] ?? 'Message du Directeur' }}</h2>
            <div class="grid md:grid-cols-12 gap-sp-2 items-center" data-aos="fade-up" data-aos-delay="200">
                @if(!empty($siteSettings['about_director_image_url']))
                <div class="md:col-span-3 lg:col-span-2 text-center md:text-left">
                    <img src="{{ asset($siteSettings['about_director_image_url']) }}"
                         alt="{{ $siteSettings['about_director_name'] }}"
                         class="director-message__img inline-block md:float-none">
                </div>
                @endif
                <div class="{{ !empty($siteSettings['about_director_image_url']) ? 'md:col-span-9 lg:col-span-10' : 'md:col-span-12' }} director-message__content">
                    <div class="prose max-w-none lg:prose-lg text-gray-700">
                        {!! $siteSettings['about_director_message_html'] !!}
                    </div>
                    <p class="mt-sp-1-5 font-semibold text-gray-800">
                        {{ $siteSettings['about_director_name'] }}<br>
                        <span class="text-sm text-gray-600 font-normal">{{ $siteSettings['about_director_title_role'] ?? 'Directeur du CRPQA' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- 9. Gouvernance (Exemple simple) --}}
    @php
        // $governanceMembers = json_decode($siteSettings['about_governance_members_json'] ?? '[]', true);
        // if (json_last_error() !== JSON_ERROR_NONE) $governanceMembers = [];
    @endphp
    @if(!empty($siteSettings['about_governance_title']) && !empty($siteSettings['about_governance_intro_html']))
    <section class="section governance" id="gouvernance">
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings['about_governance_subtitle'] ?? 'Notre Cadre' }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings['about_governance_title'] }}</h2>
            <div class="prose max-w-3xl mx-auto text-gray-700 text-center" data-aos="fade-up" data-aos-delay="200">
                {!! $siteSettings['about_governance_intro_html'] !!}
            </div>

            {{-- @if(!empty($governanceMembers))
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-sp-2 mt-sp-3" data-aos="fade-up" data-aos-delay="300">
                @foreach($governanceMembers as $member)
                <div class="governance-member">
                    @if(!empty($member['image_url']))
                    <img src="{{ asset($member['image_url']) }}" alt="{{ $member['name'] }}">
                    @else
                    <img src="{{ asset('assets/images/placeholders/avatar_default.png') }}" alt="{{ $member['name'] }}">
                    @endif
                    <strong>{{ $member['name'] }}</strong>
                    <span>{{ $member['role'] }}</span>
                </div>
                @endforeach
            </div>
            @endif --}}
        </div>
    </section>
    @endif

    {{-- 10. Infrastructure et Moyens (Exemple simple) --}}
    @php
        // $infrastructures = json_decode($siteSettings['about_infrastructure_items_json'] ?? '[]', true);
        // if (json_last_error() !== JSON_ERROR_NONE) $infrastructures = [];
    @endphp
    @if(!empty($siteSettings['about_infrastructure_title']))
    <section class="section infrastructure bg-gray-50" id="infrastructure"> {{-- bg-gray-50 pour alternance --}}
        <div class="container">
            <span class="section__subtitle" data-aos="fade-up">{{ $siteSettings['about_infrastructure_subtitle'] ?? 'Nos Atouts' }}</span>
            <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">{{ $siteSettings['about_infrastructure_title'] }}</h2>
             @if(!empty($siteSettings['about_infrastructure_intro_html']))
            <div class="prose max-w-3xl mx-auto text-gray-700 text-center mb-sp-3" data-aos="fade-up" data-aos-delay="200">
                {!! $siteSettings['about_infrastructure_intro_html'] !!}
            </div>
            @endif

            {{-- @if(!empty($infrastructures))
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-sp-2" data-aos="fade-up" data-aos-delay="300">
                @foreach($infrastructures as $item)
                <div class="infrastructure__item text-center p-sp-1-5 border border-gray-200 rounded-lg bg-white shadow-sm hover:shadow-md transition-shadow">
                    @if(!empty($item['icon'])) <ion-icon name="{{ $item['icon'] }}"></ion-icon> @endif
                    <h4 class="font-semibold text-lg text-first-color mb-sp-0-5">{{ $item['name'] }}</h4>
                    <p class="text-sm text-gray-600">{{ $item['description'] }}</p>
                </div>
                @endforeach
            </div>
            @endif --}}
        </div>
    </section>
    @endif

@endsection
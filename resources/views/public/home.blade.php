@extends('layouts.public')

{{-- Titre et Méta-description spécifiques à la page d'accueil --}}
@section('title', ($siteSettings->site_name ?? config('app.name')) . ' - Façonnons l\'Avenir Quantique du Mali')
@section('meta_description', $siteSettings->homepage_meta_description ?? 'Découvrez le CRPQA, pôle d\'excellence en physique quantique au Mali. Recherche de pointe, formation innovante et collaborations internationales pour un impact technologique majeur.')

@section('content')

    {{-- 1. SECTION HÉROS "ODYSSÉE QUANTIQUE" --}}
    <section class="hero section relative overflow-hidden" id="accueil" style="background-image: url('{{ $siteSettings->homepage_hero_bg_image_path ?? asset('/assets/placeholders/hero-bg-nebula.jpg') }}'); background-size: cover; background-position: center center;">
        <div class="hero__overlay absolute inset-0" style="background-color: rgba(10, 42, 77, 0.7);"></div>
        <div class="hero__container container grid lg:grid-cols-2 gap-8 items-center relative z-10 min-h-[calc(100vh-var(--header-height))] py-12 md:py-20">
            <div class="hero__data text-white" data-aos="fade-right" data-aos-delay="200">
                <h1 class="hero__title text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    {{ $siteSettings->homepage_hero_main_title ?? 'CRPQA : L\'Avenir Quantique' }}<br>
                    <span class="hero__title-highlight">{{ $siteSettings->homepage_hero_highlight_title ?? 'Se Dessine au Mali.' }}</span>
                </h1>
                <p class="hero__description text-lg sm:text-xl text-crpqa-second mb-8 max-w-xl">
                    {{ $siteSettings->homepage_hero_subtitle ?? 'Pionnier de la recherche et de l\'innovation, le CRPQA est dédié à l\'avancement scientifique, à la formation d\'une nouvelle génération de leaders et au développement technologique souverain du Mali et de l\'Afrique.' }}
                </p>
                <div class="hero__buttons space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ $siteSettings->homepage_hero_cta1_link ?? route('public.static.page', $siteSettings->about_page_slug ?? 'a-propos-crpqa') }}" class="button button--flex hero__button button--white text-base sm:text-lg">
                        {{ $siteSettings->homepage_hero_cta1_text ?? 'Découvrir Notre Vision' }} <ion-icon name="rocket-outline" class="button__icon"></ion-icon>
                    </a>
                    <a href="{{ $siteSettings->homepage_hero_cta2_link ?? '#recherche-accueil' }}" class="button button--flex button--outline-white hero__button text-base sm:text-lg">
                        {{ $siteSettings->homepage_hero_cta2_text ?? 'Nos Axes de Recherche' }} <ion-icon name="flask-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>

            <div class="hero__visual lg:flex items-center justify-center hidden" data-aos="fade-left" data-aos-delay="500">
                {{-- CARROUSEL D'IMAGES POUR LA SECTION HÉROS --}}
                {{-- Ce carrousel nécessite du JavaScript (ex: SwiperJS ou AlpineJS) --}}
                {{-- En attendant, nous affichons une image statique ou la vôtre si fournie --}}
                {{-- TODO: Implémenter le carrousel d'images animées pour le hero --}}
                @if(isset($siteSettings->homepage_hero_main_image_path) && $siteSettings->homepage_hero_main_image_path)
                    <img src="{{ Storage::url($siteSettings->homepage_hero_main_image_path) }}" alt="CRPQA - Recherche Quantique au Mali" class="rounded-xl shadow-2xl object-cover max-h-[500px] w-full">
                @else
                    {{-- Placeholder si vous avez plusieurs images pour un carrousel --}}
                    <div class="relative w-full max-w-md h-80 md:h-96 bg-crpqa-first-alt rounded-xl shadow-2xl overflow-hidden">
                        <img src="{{ asset('/assets/placeholders/quantum-visual-1.jpg') }}" alt="Physique Quantique Visualisation 1" class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000 ease-in-out hero-carousel-image">
                        {{-- <img src="{{ asset('/assets/placeholders/quantum-visual-2.jpg') }}" alt="Physique Quantique Visualisation 2" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000 ease-in-out hero-carousel-image"> --}}
                        {{-- <img src="{{ asset('/assets/placeholders/quantum-visual-3.jpg') }}" alt="Physique Quantique Visualisation 3" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000 ease-in-out hero-carousel-image"> --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-crpqa-first via-transparent to-transparent opacity-50"></div>
                    </div>
                @endif
            </div>
        </div>
        <div class="hero__scroll hidden md:block absolute bottom-12 left-1/2 transform -translate-x-1/2 z-10">
            <a href="#essence-crpqa" class="hero__scroll-link text-crpqa-second hover:text-crpqa-accent-cyan">
                <ion-icon name="arrow-down-circle-outline" class="text-3xl"></ion-icon> <span class="ml-2">Explorer le CRPQA</span>
            </a>
        </div>
    </section>

    {{-- 2. NOTRE ESSENCE : LA FLAMME QUANTIQUE AU CŒUR DU MALI --}}
    <section class="essence-accueil section" id="essence-crpqa">
        <div class="about__container container grid">
            <div class="about__img-bg" data-aos="fade-right" data-aos-delay="100">
                <img src="{{ $siteSettings->homepage_essence_image_path ?? asset('/assets/placeholders/mali-science-future.jpg') }}" alt="La science quantique et l'avenir du Mali" class="about__img">
            </div>
            <div class="about__data" data-aos="fade-left" data-aos-delay="200">
                <span class="section__subtitle">{{ $siteSettings->homepage_essence_tagline ?? 'Notre Identité, Notre Engagement' }}</span>
                <h2 class="section__title about__title">{{ $siteSettings->homepage_essence_title ?? 'CRPQA : La Science Quantique, Moteur de Progrès pour le Mali' }}</h2>
                <p class="about__description">
                    {{ $siteSettings->homepage_essence_text1 ?? 'Le Centre de Recherche en Physique Quantique et de ses Applications (CRPQA) incarne l\'ambition du Mali de se positionner à l\'avant-garde de la révolution scientifique mondiale. Nous sommes convaincus que la maîtrise des sciences quantiques est un impératif stratégique pour un développement technologique souverain et un rayonnement international accru.' }}
                </p>
                <p class="about__description">
                    {{ $siteSettings->homepage_essence_text2 ?? 'Profondément ancré dans le paysage académique national grâce à notre symbiose avec la Faculté des Sciences et Techniques (FST) de Bamako, nous formons les esprits brillants qui construiront demain, tout en tissant des alliances internationales pour catalyser l\'innovation et le transfert de savoir.' }}
                </p>
                <a href="{{ route('public.static.page', $siteSettings->about_page_slug ?? 'a-propos-crpqa') }}" class="button button--flex mt-6">
                    Plonger au cœur du CRPQA <ion-icon name="chevron-forward-outline" class="button__icon"></ion-icon>
                </a>
            </div>
        </div>
    </section>

    {{-- 3. NOS CHANTIERS D'EXCELLENCE (AXES DE RECHERCHE - Dynamique) --}}
    <section class="research-home section bg-crpqa-container" id="recherche-accueil">
        <span class="section__subtitle" data-aos="fade-up">Domaines Stratégiques</span>
        <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Nos Chantiers d'Excellence Quantique</h2>
        <p class="text-center text-crpqa-text-light max-w-3xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="200">
            {{ $siteSettings->homepage_research_intro_text ?? 'Le CRPQA concentre son expertise sur des axes de recherche porteurs, alliant la quête de savoir fondamental à la recherche de solutions innovantes pour les défis de demain.' }}
        </p>
        <div class="research__container container grid mt-12">
            @php
                // Données Placeholder pour les axes de recherche - À dynamiser via le module Admin "Domaines de Recherche"
                // Le contrôleur devra passer $researchAxes (une collection de modèles DomainedeRecherche)
                $researchAxes = $researchAxes ?? collect([
                    ['icon' => 'planet-outline', 'color' => 'var(--first-color)', 'bg_color' => 'rgba(10, 42, 77, 0.05)', 'title' => 'Physique Théorique & Cosmologie', 'description' => 'Décrypter les lois fondamentales de l\'univers, de l\'infiniment petit aux grandes structures cosmiques.', 'slug' => 'theorie-cosmologie'],
                    ['icon' => 'hardware-chip-outline', 'color' => 'var(--accent-color-cyan)', 'bg_color' => 'rgba(0, 191, 255, 0.05)', 'title' => 'Technologies & Matériaux Quantiques', 'description' => 'Concevoir les composants de la prochaine révolution technologique : ordinateurs, capteurs et matériaux innovants.', 'slug' => 'technologies-materiaux'],
                    ['icon' => 'analytics-outline', 'color' => 'var(--accent-color-gold)', 'bg_color' => 'rgba(212, 175, 55, 0.05)', 'title' => 'Calcul & Information Quantiques', 'description' => 'Développer de nouveaux algorithmes et protocoles pour le traitement de l\'information et la communication sécurisée du futur.', 'slug' => 'calcul-information'],
                ]);
            @endphp
            @forelse($researchAxes as $index => $axis)
            <article class="research__card" data-aos="fade-up" data-aos-delay="{{ $index * 100 + 200 }}">
                <div class="research__card-icon" style="background-color: {{ $axis['bg_color'] }};"><ion-icon name="{{ $axis['icon'] }}" style="color:{{ $axis['color'] }}"></ion-icon></div>
                <h3 class="research__card-title">{{ $axis['title'] }}</h3>
                <p class="research__card-description">{{ $axis['description'] }}</p>
                {{-- TODO: Définir la route pour les détails d'un axe de recherche --}}
                <a href="{{-- route('public.research.axis', $axis['slug']) --}}" class="research__card-link">Explorer cet axe <ion-icon name="arrow-forward-outline"></ion-icon></a>
            </article>
            @empty
                <p class="col-span-full text-center text-crpqa-text-light">Les informations sur nos axes de recherche seront bientôt disponibles.</p>
            @endforelse
        </div>
        <div class="section__action" data-aos="fade-up">
            <a href="{{-- route('public.research.index') --}}" class="button button--outline">Toute notre Recherche</a>
        </div>
    </section>

    {{-- 4. FORMATION & TALENTS : LA SYMBIOSE CRPQA-FST --}}
    <section class="formation-fst-home section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
             <div class="formation-fst__image-content order-last md:order-first" data-aos="fade-right" data-aos-delay="100">
                <img src="{{ $siteSettings->homepage_fst_image_path ?? asset('/assets/placeholders/students-learning.jpg') }}" alt="Étudiants et chercheurs du CRPQA et de la FST" class="rounded-lg shadow-xl object-cover h-full max-h-[450px] w-full">
            </div>
            <div class="formation-fst__data" data-aos="fade-left" data-aos-delay="200">
                <span class="section__subtitle">{{ $siteSettings->homepage_fst_tagline ?? 'Forger les Esprits de Demain' }}</span>
                <h2 class="section__title about__title">{{ $siteSettings->homepage_fst_title ?? 'Notre Engagement pour la Formation Quantique' }}</h2>
                <p class="about__description">
                    {{ $siteSettings->homepage_fst_text_main ?? 'Le CRPQA est intrinsèquement lié à la Faculté des Sciences et Techniques (FST) de Bamako. Nos chercheurs y sont des enseignants passionnés, transmettant le flambeau de la connaissance quantique et inspirant la nouvelle génération à relever les défis scientifiques et technologiques du Mali.' }}
                </p>
                <ul class="about__details text-sm mt-4 space-y-2">
                    <li><ion-icon name="school-outline" class="about__details-icon text-crpqa-accent-cyan"></ion-icon> Co-direction de thèses et masters en physique quantique.</li>
                    <li><ion-icon name="flask-outline" class="about__details-icon text-crpqa-accent-cyan"></ion-icon> Ateliers pratiques et séminaires de recherche ouverts aux étudiants.</li>
                    <li><ion-icon name="sparkles-outline" class="about__details-icon text-crpqa-accent-cyan"></ion-icon> Programmes de mentorat pour susciter des vocations scientifiques.</li>
                </ul>
                <div class="mt-8">
                     {{-- TODO: Définir la route vers la page "Espace Étudiants" ou "Formation" --}}
                    <a href="{{-- route('public.education') --}}" class="button button--flex">
                        Opportunités pour Étudiants <ion-icon name="id-card-outline" class="button__icon"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. VISAGES DE L'EXCELLENCE (TEASER ÉQUIPE - Dynamique) --}}
    @if(isset($featuredResearchers) && $featuredResearchers->count() > 0)
    <section class="team-teaser section bg-crpqa-container" id="equipe-accueil">
        <span class="section__subtitle" data-aos="fade-up">Notre Force Vive</span>
        <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Rencontrez Nos Architectes du Quantique</h2>
        <div class="chercheurs-ultime__grid container grid mt-12">
            @foreach($featuredResearchers as $researcher)
            <div class="chercheur-ultime__card" data-aos="fade-up" data-aos-delay="{{ ($loop->index + 1) * 100 }}">
                <div class="chercheur-ultime__image-wrapper">
                     {{-- TODO: S'assurer que $researcher->photo_path est géré et existe --}}
                    <img src="{{ $researcher->photo_path ? Storage::url($researcher->photo_path) : asset('/assets/placeholders/avatar-'.($loop->index+1).'.png') }}" alt="{{ $researcher->getFullNameAttribute() }}" class="chercheur-ultime__image">
                </div>
                <div class="chercheur-ultime__info">
                    <h3 class="chercheur-ultime__nom">{{ $researcher->title ? $researcher->title.' ' : '' }}{{ $researcher->getFullNameAttribute() }}</h3>
                    <p class="chercheur-ultime__titre">{{ $researcher->position }}</p>
                    <p class="chercheur-ultime__bio text-xs italic">
                        "{{ Str::limit($researcher->homepage_quote ?? $researcher->biography, 120) }}" {{-- Ajouter un champ 'homepage_quote' au modèle Researcher ? --}}
                    </p>
                    @if($researcher->research_areas)
                        <span class="chercheur-ultime__expertise mt-2"><ion-icon name="bulb-outline"></ion-icon> {{ Str::limit($researcher->research_areas, 40) }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="section__action" data-aos="fade-up">
            {{-- TODO: Définir la route vers la page "Équipe" complète --}}
            <a href="{{-- route('public.team.index') --}}" class="button button--outline">Toute notre Équipe d'Excellence</a>
        </div>
    </section>
    @endif

    {{-- 6. ACTUALITÉS RÉCENTES & 7. PROCHAINS ÉVÉNEMENTS (Combinés pour dynamisme) --}}
    @if((isset($latestNews) && $latestNews->count() > 0) || (isset($upcomingEvents) && $upcomingEvents->count() > 0))
    <section class="news-events-home section" id="updates-accueil">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                {{-- Colonne Actualités --}}
                @if(isset($latestNews) && $latestNews->count() > 0)
                <div data-aos="fade-right">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-bold">Dernières Actualités</h2>
                        <a href="{{ route('public.news.index') }}" class="text-sm text-crpqa-accent-cyan hover:underline font-semibold">Voir tout &rarr;</a>
                    </div>
                    <div class="space-y-6">
                        @foreach($latestNews->take(3) as $newsItem)
                        <article class="news__card--condensed flex items-start gap-4 group">
                            <a href="{{ route('public.news.show', $newsItem->slug) }}" class="block shrink-0">
                                @if($newsItem->cover_image_path && Storage::disk('public')->exists($newsItem->cover_image_path))
                                    <img src="{{ Storage::url($newsItem->cover_image_path) }}" alt="{{ $newsItem->title }}" class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-md shadow-sm group-hover:shadow-lg transition-shadow">
                                @else
                                    <div class="w-24 h-24 sm:w-32 sm:h-32 bg-crpqa-second flex items-center justify-center text-crpqa-text-light rounded-md group-hover:shadow-lg transition-shadow"> <ion-icon name="image-outline" style="font-size: 2.5rem;"></ion-icon> </div>
                                @endif
                            </a>
                            <div class="news__data--condensed">
                                <span class="news__meta text-xs">{{ $newsItem->published_at->isoFormat('LL') }}</span>
                                <h3 class="news__title text-md sm:text-lg font-semibold leading-tight mt-1 group-hover:text-crpqa-accent-cyan">
                                    <a href="{{ route('public.news.show', $newsItem->slug) }}">{{ Str::limit($newsItem->title, 70) }}</a>
                                </h3>
                                <p class="news__description text-xs sm:text-sm hidden sm:block mt-1">{{ Str::limit(strip_tags($newsItem->summary ?: $newsItem->content), 80) }}</p>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Colonne Événements --}}
                @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
                <div data-aos="fade-left" data-aos-delay="100">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-bold">Prochains Événements</h2>
                         <a href="{{ route('public.events.index') }}" class="text-sm text-crpqa-accent-cyan hover:underline font-semibold">Voir tout &rarr;</a>
                    </div>
                    <div class="space-y-6">
                        @foreach($upcomingEvents->take(3) as $eventItem)
                        <article class="event__item--condensed flex items-start gap-4 group">
                            <div class="event__date--condensed text-center bg-crpqa-accent-cyan text-white rounded-md p-2 shadow-sm min-w-[60px]">
                                <span class="block text-xl sm:text-2xl font-bold leading-none">{{ $eventItem->start_datetime->format('d') }}</span>
                                <span class="block text-xs sm:text-sm uppercase">{{ Str::upper($eventItem->start_datetime->translatedFormat('MMM')) }}</span>
                            </div>
                            <div class="event__info--condensed">
                                <h3 class="event__title text-md sm:text-lg font-semibold leading-tight group-hover:text-crpqa-accent-cyan">
                                    <a href="{{ route('public.events.show', $eventItem->slug) }}">{{ Str::limit($eventItem->title, 70) }}</a>
                                </h3>
                                <p class="event__time-location text-xs sm:text-sm text-crpqa-text-light mt-1">
                                    <ion-icon name="time-outline"></ion-icon> {{ $eventItem->start_datetime->format('H:i') }}
                                    @if($eventItem->location) | <ion-icon name="location-outline"></ion-icon> {{ Str::limit($eventItem->location, 25) }} @endif
                                </p>
                                <a href="{{ $eventItem->registration_url ?: route('public.events.registration.create', $eventItem->slug) }}" 
                                   class="text-xs text-crpqa-accent-cyan-alt hover:underline mt-2 inline-block font-semibold"
                                   @if($eventItem->registration_url) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $eventItem->registration_url ? 'S\'inscrire (Externe)' : 'Détails / Inscription' }}
                                </a>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif


    {{-- 8. NOS ALLIANCES STRATÉGIQUES (APERÇU PARTENAIRES - Dynamique) --}}
    @if(isset($activePartners) && $activePartners->count() > 0)
    <section class="partners-home section bg-crpqa-container" id="partenaires-accueil">
        <span class="section__subtitle" data-aos="fade-up">Collaborations d'Impact</span>
        <h2 class="section__title" data-aos="fade-up" data-aos-delay="100">Notre Réseau de Partenaires Stratégiques</h2>
        <p class="text-center text-crpqa-text-light max-w-3xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="200">
            {{ $siteSettings->homepage_partners_intro_text ?? 'Le CRPQA valorise et cultive des collaborations solides avec des institutions nationales et internationales de premier plan pour amplifier notre impact et accélérer l\'innovation.' }}
        </p>
        <div class="partners__container container mt-12" data-aos="fade-up" data-aos-delay="300">
            @foreach($activePartners as $partner)
                <div class="partner__logo-item flex justify-center items-center p-4">
                    <a href="{{ $partner->website_url ?? '#' }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="block transition-opacity duration-300 hover:opacity-75">
                        @if($partner->logo_path && Storage::disk('public')->exists($partner->logo_path))
                            <img src="{{ Storage::url($partner->logo_path) }}" alt="Logo {{ $partner->name }}" class="partner__logo h-16 md:h-20 object-contain">
                        @else
                            <span class="partner__logo_textual text-center text-crpqa-text-light hover:text-crpqa-first text-sm font-semibold">
                                {{ $partner->name }}
                            </span>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
        @if(\App\Models\Partner::where('is_active', true)->count() > $activePartners->count())
            <div class="section__action" data-aos="fade-up">
                 {{-- TODO: Définir la route vers la page des partenaires --}}
                <a href="{{-- route('public.partners.index') --}}" class="button button--outline">Découvrir tous nos partenaires</a>
            </div>
        @endif
    </section>
    @endif

    {{-- 9. L'APPEL DU QUANTIQUE (CTA GLOBAL) --}}
    <section class="appel-quantique section join" id="appel-quantique-accueil">
         <div class="join__container container" data-aos="zoom-in">
             <div class="join__bg">
                <img src="{{ $siteSettings->homepage_cta_image_path ?? asset('/assets/placeholders/global-cta-bg.jpg') }}" alt="Rejoignez l'aventure quantique avec le CRPQA" class="join__bg-img">
                <div class="join__overlay" style="background-color: rgba(10, 42, 77, 0.85);"></div>
            </div>
            <div class="join__data">
                <h2 class="join__title text-3xl md:text-4xl">{{ $siteSettings->homepage_cta_title ?? 'L\'Avenir Quantique se Construit Maintenant. Ensemble.' }}</h2>
                <p class="join__description text-lg">
                    {{ $siteSettings->homepage_cta_text ?? 'Le CRPQA est plus qu\'un centre de recherche : c\'est une communauté d\'esprits passionnés, une plateforme d\'innovation et un catalyseur de changement. Rejoignez-nous dans cette épopée scientifique et technologique pour le Mali et pour le monde.' }}
                </p>
                <div class="join__buttons appel-quantique__buttons mt-8">
                    {{-- TODO: Mettre à jour avec les bonnes routes une fois les pages créées --}}
                    <a href="{{ $siteSettings->homepage_cta_link1_url ?? '#' }}" class="button button--white"><ion-icon name="{{ $siteSettings->homepage_cta_link1_icon ?? 'school-outline' }}"></ion-icon> {{ $siteSettings->homepage_cta_link1_text ?? 'Espace Étudiants' }}</a>
                    <a href="{{ $siteSettings->homepage_cta_link2_url ?? '#' }}" class="button button--outline-white"><ion-icon name="{{ $siteSettings->homepage_cta_link2_icon ?? 'git-network-outline' }}"></ion-icon> {{ $siteSettings->homepage_cta_link2_text ?? 'Nos Collaborations' }}</a>
                    <a href="{{ $siteSettings->homepage_cta_link3_url ?? '#contact-footer' }}" class="button button--white"><ion-icon name="{{ $siteSettings->homepage_cta_link3_icon ?? 'mail-open-outline' }}"></ion-icon> {{ $siteSettings->homepage_cta_link3_text ?? 'Nous Contacter' }}</a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Logique pour le carrousel d'images du Hero (si implémenté)
    const heroImages = document.querySelectorAll('.hero-carousel-image');
    if (heroImages.length > 1) {
        let currentHeroImage = 0;
        setInterval(() => {
            heroImages[currentHeroImage].style.opacity = 0;
            currentHeroImage = (currentHeroImage + 1) % heroImages.length;
            heroImages[currentHeroImage].style.opacity = 1;
        }, 5000); // Change d'image toutes les 5 secondes
    }

    // AOS est déjà initialisé dans public-main.js
    // Si vous avez besoin de rafraîchir AOS après un chargement de contenu dynamique sur cette page :
    // AOS.refresh();
});
</script>
@endpush
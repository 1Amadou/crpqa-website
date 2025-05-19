@extends('layouts.public')

{{-- Le titre et la méta-description viendront de l'objet $page (StaticPage) --}}
{{-- Assurez-vous que $page est bien passée par le contrôleur --}}
@section('title', $page->meta_title ?: $page->title . ' - ' . ($siteSettings->site_name ?? config('app.name')))
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->content ?? 'Découvrez le Centre de Recherche en Physique Quantique et ses Applications : notre histoire, mission, vision, équipe et collaborations.'), 160))

@section('content')

    {{-- 1. BANNIÈRE DE PAGE (PAGE HERO) - Comme défini dans votre apropos.html --}}
    {{-- Ce contenu pourrait être le début du champ $page->content ou des champs personnalisés pour le hero --}}
    {{-- Si vous voulez que la bannière soit distincte du $page->content principal : --}}
    <section class="page-hero ultime-hero section" style="background-image: url('{{ $page->hero_background_image_path ?? ($siteSettings->about_hero_bg_image_path ?? asset('/assets/placeholders/about-hero-bg-default.jpg')) }}');">
        <div class="page-hero__overlay absolute inset-0" style="background-color: rgba(10, 42, 77, 0.75);"></div>
        <div class="page-hero__container container relative z-10" data-aos="fade-in">
            <h1 class="page-hero__title text-white">
                {{-- Ce titre peut venir d'un champ 'hero_title' de l'objet $page, ou être le $page->title --}}
                {{ $page->hero_title ?? $page->title ?? 'CRPQA : Au Cœur de la Révolution Quantique pour le Mali et le Monde' }}
            </h1>
            <p class="page-hero__subtitle text-crpqa-second">
                {{ $page->hero_subtitle ?? 'Une exploration à 360° de notre centre : notre histoire inspirante, notre mission audacieuse, notre vision pour un avenir quantique et l\'écosystème qui nous propulse.' }}
            </p>
        </div>
    </section>

    {{-- 2. CONTENU PRINCIPAL DE LA PAGE "À PROPOS ULTIME" --}}
    {{-- Ici, nous allons afficher le champ 'content' de la page statique.
         Ce champ 'content' contiendra le HTML structuré de TOUTES les sections
         de votre apropos.html (Essence, Odyssée, Mission, Vision, Valeurs, etc., EXCLUANT le hero ci-dessus).
         L'administrateur gérera ce contenu via l'éditeur de texte riche en backend.
    --}}
    <article class="static-page-content-about">
        {{-- La classe .prose de Tailwind Typography est excellente pour styler du HTML brut.
             Nous la personnaliserons pour qu'elle corresponde à votre style.css.
             Si votre style.css gère déjà parfaitement le formatage du contenu HTML,
             vous n'aurez peut-être pas besoin de la classe .prose ici,
             juste d'un conteneur si nécessaire.
        --}}
        @if(isset($page) && $page->content)
            {{-- Enveloppez chaque section de votre apropos.html (après le hero) avec sa classe de section d'origine
                 et placez-les ici, ou mettez tout le bloc HTML dans $page->content --}}

            {{-- Option 1: Tout le contenu HTML des sections est dans $page->content --}}
            <div class="prose prose-lg lg:prose-xl max-w-none mx-auto px-4 sm:px-6 lg:px-8 prose-headings:font-display prose-headings:text-crpqa-first prose-a:text-crpqa-accent-cyan hover:prose-a:text-crpqa-accent-cyan-alt">
                {!! $page->content !!}
            </div>

            {{-- Option 2: Si vous voulez plus de contrôle et potentiellement injecter des données dynamiques
                 dans certaines sections de la page "À Propos" (plus complexe mais plus flexible) :
                 Vous auriez des @includes de partiels ici, et le contenu textuel viendrait de
                 champs personnalisés sur le modèle StaticPage ou de $siteSettings.
                 Pour l'instant, Option 1 est plus simple si votre HTML est déjà bien structuré.

            <section class="essence section" id="essence">
                <div class="essence__container container grid">
                    Contenu de la section Essence... (peut utiliser des champs de $page ou $siteSettings)
                </div>
            </section>

            <section class="history-ultime section" id="histoire">
                Contenu de la section Histoire...
            </section>
            
            ... et ainsi de suite pour toutes les sections de votre apropos.html ...
            (Mission, Vision, Valeurs, Écosystème, Alliances, Chercheurs Teaser, Axes de Recherche Teaser, Appel Quantique)
            Pour les sections comme "Nos Chercheurs" ou "Nos Alliances", on pourrait imaginer
            inclure un partiel qui fait une boucle sur des données dynamiques passées par le contrôleur.
            --}}

        @else
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <p class="text-center text-gray-600">Le contenu de la page "À Propos" sera bientôt disponible.</p>
            </div>
        @endif
    </article>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Assurer le bon fonctionnement des ancres internes avec le header fixe
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            const href = anchor.getAttribute('href');
            if (href.length > 1 && href.startsWith('#')) {
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const header = document.getElementById('header');
                        const headerOffset = header ? header.offsetHeight : 0;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset - 20; // 20px de marge

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });

                        // Fermer le menu mobile si ouvert
                        const navMenu = document.getElementById('nav-menu');
                        if (navMenu && navMenu.classList.contains('show-menu')) {
                            navMenu.classList.remove('show-menu');
                            document.body.classList.remove('no-scroll');
                        }
                    });
                }
            }
        });
        // AOS est déjà initialisé globalement dans public-main.js
        // Si des éléments sont ajoutés dynamiquement et nécessitent une réinitialisation d'AOS :
        // AOS.refresh();
    });
</script>
@endpush
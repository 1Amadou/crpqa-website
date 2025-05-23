<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Titre et Description --}}
    <title>@yield('title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))</title>
    <meta name="description" content="@yield('meta_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">

    {{-- Meta Tags pour le SEO et le Partage Social (Open Graph & Twitter Cards) --}}
    <meta property="og:title" content="@yield('og_title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))">
    <meta property="og:description" content="@yield('og_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- Assurez-vous que $siteSettings['og_image_url'] contient un chemin accessible publiquement ou utilisez Storage::url() si stocké dans storage/app/public --}}
    <meta property="og:image" content="@yield('og_image', !empty($siteSettings['og_image_url']) ? asset($siteSettings['og_image_url']) : asset('assets/images/crpqa_og_default.jpg'))">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? config('app.name', 'CRPQA') }}">
    {{-- <meta property="fb:app_id" content="YOUR_FACEBOOK_APP_ID" /> --}} {{-- Si vous avez un App ID Facebook --}}

    <meta name="twitter:card" content="summary_large_image">
    {{-- <meta name="twitter:site" content="@VotreCompteTwitter"> --}} {{-- Si vous avez un compte Twitter pour le site --}}
    <meta name="twitter:title" content="@yield('og_title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))">
    <meta name="twitter:description" content="@yield('og_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">
    <meta name="twitter:image" content="@yield('og_image', !empty($siteSettings['og_image_url']) ? asset($siteSettings['og_image_url']) : asset('assets/images/crpqa_og_default.jpg'))">

    {{-- Favicons --}}
    {{-- Utilisez un générateur de favicons pour obtenir toutes les tailles nécessaires et le manifest --}}
    <link rel="icon" href="{{ !empty($siteSettings['favicon_url']) ? asset($siteSettings['favicon_url']) : asset('assets/icons/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/icons/favicon-16x16.png') }}">
    {{-- <link rel="manifest" href="{{ asset('assets/icons/site.webmanifest') }}"> --}}
    {{-- <meta name="msapplication-TileColor" content="#0A2A4D"> --}} {{-- Couleur pour tuile Windows (var(--first-color)) --}}
    <meta name="theme-color" content="#ffffff"> {{-- Couleur du thème pour la barre d'adresse mobile --}}

    {{-- Polices Google (Preconnect) --}}
    {{-- Le <link href="..." pour charger les polices est dans votre style.css, ce qui est bien. --}}
    {{-- Si ce n'est pas le cas, vous devez le remettre ici ou l'importer dans style.css --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Assurez-vous que le lien suivant est bien présent DANS votre style.css ou ici, mais pas aux deux endroits.
         Si dans style.css, ces <link> sont suffisants.
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    {{-- Styles principaux et JS via Vite --}}
    {{-- app.css devrait importer votre style.css ET le CSS d'AOS (si installé via npm) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/public-main.js'])

    {{-- Dans la section <head> de layouts.public.blade.php --}}
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

{{-- Avant la balise </body> de layouts.public.blade.php (et AVANT votre public-main.js) --}}
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    {{-- Styles spécifiques à une page --}}
    @stack('styles')

    {{-- La variable --header-height est définie dans :root de style.css --}}
    {{-- Le script JS pour la définir ici a été supprimé car redondant et moins flexible --}}
</head>
<body class="antialiased">
    {{--
        La classe `antialiased` est une classe utilitaire Tailwind pour le lissage des polices.
        Les styles de base du body (font-family, background-color, color, line-height)
        sont définis dans votre `style.css` (`body { ... }`).
        Les styles pour la sélection de texte (::selection) sont également dans `style.css`.
    --}}

    <div id="crpqa-app" class="flex flex-col min-h-screen">
        {{-- Header --}}
        {{-- Le header est "sticky" grâce à la classe .header et position:fixed dans style.css --}}
        @include('layouts.partials.public-header')

        {{-- Contenu Principal --}}
        {{-- La classe `main-content` est ajoutée pour des styles potentiels spécifiques au conteneur principal du contenu.
             Le padding-top pour compenser le header fixe est géré par les sections elles-mêmes
             (ex: .hero, .page-hero dans votre style.css ont `padding-top: calc(var(--header-height) + Xrem);`)
             ou par un wrapper autour de @yield('content') si une page n'a pas de telle section en haut.
        --}}
        <main class="main-content flex-grow">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.partials.public-footer')
    </div>

    {{-- Bouton Scroll-up (markup selon votre style.css) --}}
    <a href="#" class="scrollup" id="scroll-up" title="Remonter en haut">
        <ion-icon name="arrow-up-outline" class="scrollup__icon"></ion-icon>
        <span class="sr-only">Remonter en haut</span>
    </a>

    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800, 
    once: true    
  });
</script>
    @stack('scripts')
</body>
</html>
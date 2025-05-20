<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))</title>
    <meta name="description" content="@yield('meta_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">

    {{-- Meta tags Open Graph & Twitter (essentiels pour un look pro sur les réseaux) --}}
    <meta property="og:title" content="@yield('og_title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))">
    <meta property="og:description" content="@yield('og_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', !empty($siteSettings['og_image_url']) ? Storage::url($siteSettings['og_image_url']) : asset('assets/crpqa_og_default.jpg'))">
    {{-- Assurez-vous que 'og_image_url' est stocké correctement (ex: chemin relatif depuis public/storage) et que crpqa_og_default.jpg existe --}}

    <meta name="twitter:card" content="summary_large_image">
    {{-- <meta name="twitter:site" content="@VotreCompteTwitter"> --}}
    <meta name="twitter:title" content="@yield('og_title', $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? config('app.name', 'CRPQA'))">
    <meta name="twitter:description" content="@yield('og_description', $siteSettings['site_tagline'] ?? 'Centre de Recherche en Physique Quantique et ses Applications.')">
    <meta name="twitter:image" content="@yield('og_image', !empty($siteSettings['og_image_url']) ? Storage::url($siteSettings['og_image_url']) : asset('assets/crpqa_og_default.jpg'))">

    {{-- Favicons (simplifié, mais vous pouvez étendre avec realfavicongenerator.net) --}}
    <link rel="icon" href="{{ !empty($siteSettings['favicon_url']) ? Storage::url($siteSettings['favicon_url']) : asset('assets/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ !empty($siteSettings['apple_touch_icon_url']) ? Storage::url($siteSettings['apple_touch_icon_url']) : asset('assets/apple-touch-icon.png') }}">

    {{-- Polices Google (déjà dans votre style.css, mais le preconnect est utile ici) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Le lien <link href="https://fonts.googleapis.com/css2?..." est déjà dans votre style.css fourni (au début)
        ou vous pouvez le laisser ici si style.css ne l'importe pas directement. Pour éviter la duplication,
        il est mieux de le charger une seule fois. Si votre style.css le fait, supprimez le lien <link href=...> d'ici.
        Je suppose que votre style.css actuel ne l'importe PAS et que vous le gardez ici.
    --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    {{-- CSS & JS via Vite --}}
    {{-- app.css importera votre style.css principal ET le CSS d'AOS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/public-main.js'])

    @stack('styles')
</head>
<body class="antialiased"> {{-- Les styles de body (police, couleur, fond) viennent de votre style.css --}}
    {{-- La variable --header-height doit être définie dans :root de votre style.css --}}

    <div id="crpqa-app" class="flex flex-col min-h-screen">
        @include('layouts.partials.public-header')

        <main class="main-content flex-grow">
            {{-- Le padding-top pour le header fixe est géré par les sections .hero, .page-hero
                 ou un wrapper spécifique si une page commence sans ces sections.
                 Si une page standard a besoin d'un offset pour le header :
                 <div style="padding-top: var(--header-height);">@yield('content')</div>
                 Ou mieux, une classe CSS pour ce wrapper.
                 Pour l'instant, on se fie aux sections pour gérer leur propre padding.
            --}}
            @yield('content')
        </main>

        @include('layouts.partials.public-footer')
    </div>

    {{-- Bouton Scroll-up (Markup de votre style.css) --}}
    <a href="#" class="scrollup" id="scroll-up">
        <ion-icon name="arrow-up-outline" class="scrollup__icon"></ion-icon>
        <span class="sr-only">Remonter en haut</span>
    </a>

    {{-- Les scripts globaux comme AOS init sont dans public-main.js --}}
    @stack('scripts')
</body>
</html>
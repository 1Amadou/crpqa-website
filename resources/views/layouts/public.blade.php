<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        // Assurer que $siteSettings est un objet et non un tableau pour utiliser les accesseurs et le trait de localisation.
        // Ce code est une sécurité ; idéalement, le middleware ShareSiteSettings fournit déjà l'objet.
        $currentSiteSettings = ($siteSettings instanceof \App\Models\SiteSetting) ? $siteSettings : new \App\Models\SiteSetting((array) $siteSettings);
        $currentLocale = app()->getLocale();
    @endphp

    {{-- Titre et Description --}}
    <title>@yield('title', $currentSiteSettings->getTranslation('site_name_short', $currentLocale, false) ?: ($currentSiteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name', 'CRPQA')))</title>
    <meta name="description" content="@yield('meta_description', $currentSiteSettings->getTranslation('site_description', $currentLocale, false) ?: __('Centre de Recherche en Physique Quantique et ses Applications.'))">

    {{-- Meta Tags pour le SEO et le Partage Social --}}
    <meta property="og:title" content="@yield('og_title', $currentSiteSettings->getTranslation('site_name_short', $currentLocale, false) ?: ($currentSiteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name', 'CRPQA')))">
    <meta property="og:description" content="@yield('og_description', $currentSiteSettings->getTranslation('site_description', $currentLocale, false) ?: __('Centre de Recherche en Physique Quantique et ses Applications.'))">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', $currentSiteSettings->default_og_image_url ?: asset('assets/images/placeholders/default_og_image.jpg'))">
    <meta property="og:site_name" content="{{ $currentSiteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name', 'CRPQA') }}">
    {{-- <meta property="fb:app_id" content="YOUR_FACEBOOK_APP_ID" /> --}}

    <meta name="twitter:card" content="summary_large_image">
    {{-- <meta name="twitter:site" content="{{ $currentSiteSettings->twitter_url ? '@'.basename($currentSiteSettings->twitter_url) : '' }}"> --}}
    <meta name="twitter:title" content="@yield('og_title', $currentSiteSettings->getTranslation('site_name_short', $currentLocale, false) ?: ($currentSiteSettings->getTranslation('site_name', $currentLocale, false) ?: config('app.name', 'CRPQA')))">
    <meta name="twitter:description" content="@yield('og_description', $currentSiteSettings->getTranslation('site_description', $currentLocale, false) ?: __('Centre de Recherche en Physique Quantique et ses Applications.'))">
    <meta name="twitter:image" content="@yield('og_image', $currentSiteSettings->default_og_image_url ?: asset('assets/images/placeholders/default_og_image.jpg'))">

    {{-- Favicons --}}
    <link rel="icon" href="{{ $currentSiteSettings->favicon_url }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/icons/apple-touch-icon.png') }}"> {{-- Gardez si vous avez ce fichier --}}
    {{-- <link rel="manifest" href="{{ asset('assets/icons/site.webmanifest') }}"> --}}
    {{-- <meta name="msapplication-TileColor" content="{{ $currentSiteSettings->theme_color_primary ?? '#0A2A4D' }}"> --}}
    <meta name="theme-color" content="{{ $currentSiteSettings->theme_color_browser_bar ?? '#ffffff' }}">

    {{-- Polices --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Assurez-vous que le lien effectif des polices est dans votre style.css/app.css --}}
    
    {{-- Ionicons --}}
    <script type="module" src="https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.js"></script>

    {{-- AOS (Animate On Scroll) --}}
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    {{-- SwiperJS CSS (si utilisé) --}}
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/public-main.js'])

    @stack('styles')
</head>
<body class="antialiased bg-body-color text-text-color dark:bg-dark-body-color dark:text-dark-text-color font-sans text-base leading-relaxed">
    <div id="crpqa-app" class="flex flex-col min-h-screen">
        @include('layouts.partials.public-header', ['siteSettings' => $currentSiteSettings])

        <main class="main-content flex-grow" id="main-content">
            @yield('content')
        </main>

        @include('layouts.partials.public-footer', ['siteSettings' => $currentSiteSettings])
    </div>

    <a href="#" class="scrollup" id="scroll-up" title="{{ __('Remonter en haut') }}" aria-label="{{ __('Remonter en haut') }}">
        <ion-icon name="arrow-up-outline" class="scrollup__icon"></ion-icon>
    </a>
    
    {{-- Scripts globaux --}}
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800, 
        once: true    
      });
    </script>
    
    @stack('scripts')
</body>
</html>
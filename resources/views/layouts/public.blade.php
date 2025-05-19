<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Utile si vous faites des appels AJAX POST plus tard --}}

    {{-- Le titre et la méta-description seront définis par chaque vue enfant ou utiliseront des valeurs par défaut --}}
    <title>@yield('title', $siteSettings->site_name ?? config('app.name', 'CRPQA'))</title>
    <meta name="description" content="@yield('meta_description', $siteSettings->meta_description_default ?? 'Centre de Recherche en Physique Quantique et ses Applications - CRPQA')">
    {{-- Vous pourrez ajouter une section @stack('meta_tags') pour d'autres méta spécifiques à la page --}}

    {{-- Favicon (depuis les paramètres du site ou un placeholder) --}}
    @if(isset($siteSettings) && $siteSettings->favicon_path && Storage::disk('public')->exists($siteSettings->favicon_path))
        <link rel="icon" href="{{ Storage::url($siteSettings->favicon_path) }}" type="image/png"> {{-- Adaptez le type si votre favicon est .ico ou .svg --}}
    @else
        <link rel="icon" href="{{ asset('assets/favicon.png') }}" type="image/png"> {{-- Placeholder basé sur votre HTML (suppose un dossier public/assets) --}}
    @endif

    {{-- Polices Google Fonts (celles de votre HTML) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    
    {{-- Ionicons via CDN (comme dans votre HTML - simple et efficace pour commencer) --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" defer></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js" defer></script>

    {{-- AOS CSS sera importé via app.js (grâce à l'import dans public-main.js), donc le lien CDN n'est plus nécessaire ici --}}
    {{-- <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" /> --}}

    {{-- Notre CSS principal (incluant Tailwind et votre style.css) et JS principal (incluant votre script.js et AOS) via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Permet aux vues enfants d'ajouter des styles CSS spécifiques si nécessaire --}}
    @stack('styles')
</head>
<body class="font-body text-crpqa-text bg-crpqa-body antialiased"> {{-- Utilisation des classes de la charte (via Tailwind config ou votre style.css) --}}
    
    {{-- Wrapper principal de l'application --}}
    <div id="app-public" class="flex flex-col min-h-screen"> {{-- ID changé pour éviter conflit avec #app de Vue/React si un jour utilisé --}}

        {{-- Inclusion du Header --}}
        @include('layouts.partials.public-header')

        {{-- Contenu principal de la page spécifique --}}
        <main class="main flex-grow" id="main-content"> {{-- L'ID 'main' était dans votre HTML pour script.js --}}
            @yield('content')
        </main>

        {{-- Inclusion du Footer --}}
        @include('layouts.partials.public-footer')

        {{-- Bouton Scroll Up (de votre HTML) --}}
        <a href="#" class="scrollup" id="scroll-up">
            <ion-icon name="arrow-up-outline" class="scrollup__icon"></ion-icon>
        </a>

    </div>{{-- Fin de #app-public --}}

    {{-- AOS JS est initialisé dans notre public-main.js, donc le script CDN et l'init ici ne sont plus nécessaires --}}
    {{-- <script src="https://unpkg.com/aos@next/dist/aos.js"></script> --}}
    {{-- <script> AOS.init(...); </script> --}}

    {{-- Permet aux vues enfants d'ajouter des scripts JS spécifiques à la fin du body --}}
    @stack('scripts')
</body>
</html>
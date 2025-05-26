<!DOCTYPE html>
{{-- On ajoute une classe 'dark' ici si vous gérez le mode sombre via une classe sur <html>
     Sinon, Tailwind peut le gérer via la media query ( prefers-color-scheme: dark )
     Je vais supposer pour l'instant que vous gérez cela via la configuration de Tailwind (media query ou classe) --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - Administration CRPQA</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"> {{-- Ajout du poids 700 pour les titres --}}

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <meta name="tinymce-lang-url" content="{{ asset('assets/tinymce/langs/fr_FR.js') }}">
<meta name="tinymce-skin-url" content="{{ asset('assets/tinymce/skins/ui/oxide') }}">
<meta name="tinymce-content-css-dark" content="{{ asset('assets/tinymce/skins/content/dark/content.css') }}">
<meta name="tinymce-content-css-default" content="{{ asset('assets/tinymce/skins/content/default/content.css') }}">
    
    {{-- Assurez-vous que le chemin est correct par rapport à votre dossier public --}}
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script> {{-- Chemin commun si installé via package --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Styles spécifiques pour l'admin si nécessaire (ou importés dans app.css) --}}
    {{-- @stack('admin-styles') --}}
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        {{-- Barre Latérale Améliorée --}}
        <aside class="w-64 bg-slate-800 dark:bg-gray-800 text-slate-200 dark:text-gray-300 p-4 space-y-6 hidden sm:flex sm:flex-col shadow-xl z-20">
            <div class="text-center py-3 border-b border-slate-700 dark:border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center">
                    {{-- Vous pourriez ajouter un petit logo SVG ici si vous en avez un --}}
                    {{-- <img src="{{ asset('logo-admin.svg') }}" alt="Logo" class="h-8 w-auto mr-2"> --}}
                    <span class="text-xl font-bold text-white group-hover:text-slate-300 dark:group-hover:text-gray-200 transition-colors">
                        CRPQA Admin
                    </span>
                </a>
            </div>
            <nav class="flex-grow space-y-1">
                {{-- Fonction pour générer les liens de navigation (optionnel, pour la clarté) --}}
                @php
                    function navLink($route, $iconName, $label, $activeRoutes = []) {
                        $isActive = request()->routeIs($route) || collect($activeRoutes)->contains(fn($pattern) => request()->routeIs($pattern));
                        $linkClasses = 'flex items-center space-x-3 py-2.5 px-4 rounded-lg transition-all duration-200 ease-in-out text-sm font-medium ';
                        $activeClasses = 'bg-sky-600 dark:bg-sky-500 text-white shadow-md';
                        $inactiveClasses = 'hover:bg-slate-700 dark:hover:bg-gray-700 hover:text-white dark:hover:text-gray-100';
                        return '<a href="'.route($route).'" class="'.$linkClasses.($isActive ? $activeClasses : $inactiveClasses).'">
                                    <ion-icon name="'.$iconName.'" class="text-xl '.($isActive ? 'text-white' : 'text-slate-400 dark:text-gray-400 group-hover:text-slate-200 dark:group-hover:text-gray-200').'"></ion-icon>
                                    <span>'.$label.'</span>
                                </a>';
                    }
                @endphp

                {!! navLink('admin.dashboard', 'grid-outline', 'Tableau de Bord') !!}
                {!! navLink('admin.static-pages.index', 'document-text-outline', 'Pages Statiques', ['admin.static-pages.*']) !!}
                {!! navLink('admin.researchers.index', 'people-outline', 'Chercheurs', ['admin.researchers.*']) !!}
                {!! navLink('admin.publications.index', 'book-outline', 'Publications', ['admin.publications.*']) !!}
                {!! navLink('admin.news.index', 'newspaper-outline', 'Actualités', ['admin.news.*']) !!}
                {!! navLink('admin.events.index', 'calendar-outline', 'Événements', ['admin.events.*']) !!}
                {!! navLink('admin.partners.index', 'business-outline', 'Partenaires', ['admin.partners.*']) !!}
                {!! navLink('admin.research-axes.index', 'flask-outline', 'Axes de Recherche', ['admin.research-axes.*']) !!}
                
                {{-- Section Utilisateurs et Paramètres (peut-être séparée par un titre ou une ligne) --}}
                <div class="pt-4 mt-4 border-t border-slate-700 dark:border-gray-700 space-y-1">
                    <span class="px-4 text-xs font-semibold text-slate-500 dark:text-gray-500 uppercase tracking-wider">Gestion</span>
                    {!! navLink('admin.users.index', 'person-circle-outline', 'Utilisateurs', ['admin.users.*']) !!}
                    {!! navLink('admin.settings.edit', 'settings-outline', 'Paramètres du Site', ['admin.settings.edit']) !!}
                </div>
            </nav>
            {{-- Pied de la barre latérale (optionnel) --}}
            <div class="mt-auto pt-4 border-t border-slate-700 dark:border-gray-700">
                <p class="text-xs text-slate-500 dark:text-gray-500 text-center">&copy; {{ date('Y') }} {{ config('app.name') }}.</p>
            </div>
        </aside>

        {{-- Contenu Principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Barre de Navigation Supérieure (mobile & desktop) --}}
            {{-- layouts.navigation est responsable du menu burger sur mobile pour afficher la sidebar --}}
            @include('layouts.navigation') 

            {{-- En-tête de Page (si un @section('header') est défini) --}}
            @hasSection('header')
                <header class="bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-700/50">
                    <div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        @yield('header') {{-- Ex: <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Titre</h2> --}}
                    </div>
                </header>
            @endif

            {{-- Contenu de la Page --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                {{-- Le @yield('content') est souvent enveloppé d'un container pour le padding dans la page elle-même.
                     Sinon, ajoutez le padding ici ou dans la div de la page spécifique.
                     J'ai ajouté un padding par défaut dans la page dashboard, ce qui est une bonne pratique.
                --}}
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals') {{-- Pour les modales globalement accessibles --}}
    @stack('scripts') {{-- Pour les scripts spécifiques à la page --}}
</body>
</html>
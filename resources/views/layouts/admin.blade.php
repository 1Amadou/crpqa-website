<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - Administration CRPQA</title>

    <!-- Préchargement des polices -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <!-- Charger TinyMCE globalement AVANT les scripts de l'application -->
    <script src="{{ asset('assets/tinymce/tinymce.min.js') }}"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
 
    {{-- Assurez-vous que le fichier existe dans le bon chemin --}}

    <!-- Scripts et styles de l'application -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <aside class="w-64 bg-slate-800 text-slate-100 p-4 space-y-6 hidden sm:block shadow-lg">
            <div class="text-center py-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold hover:text-slate-300">
                    CRPQA - Admin
                </a>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Tableau de Bord</span>
                </a>

                <a href="{{ route('admin.static-pages.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.static-pages.*') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Pages Statiques</span>
                </a>

                <a href="{{ route('admin.researchers.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.researchers.*') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Chercheurs</span>
                </a>

                 <a href="{{ route('admin.publications.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.publications.*') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m0 0a7.5 7.5 0 100-11.494A7.5 7.5 0 0012 17.747zM12 6.253V2.25m0 15.494V21.75M12 6.253h.008v.008H12V6.253zm0 15.494h.008v.008H12v-.008zM12 6.253H7.5m4.5 15.494H7.5M12 6.253H16.5m-4.5 15.494H16.5"></path></svg> {{-- Icône exemple pour publications --}}
                    <span>Publications</span>
                </a>

                <a href="{{ route('admin.news.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.news.*') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg> {{-- Icône exemple pour actualités --}}
                    <span>Actualités</span>
                </a>

                <a href="{{ route('admin.events.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.events.*') ? 'bg-slate-700' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{-- Icône exemple pour événements --}}
                    <span>Événements</span>
                </a>

                <a href="{{ route('admin.partners.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.partners.*') ? 'bg-slate-700' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> {{-- Icône exemple pour Partenaires --}}
                            <span>Partenaires</span>
                </a>

                <a href="{{ route('admin.research-axes.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.research-axes.*') ? 'bg-slate-700' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> {{-- Icône exemple pour Domaines de Recherche (Explosion/Idées) --}}
                            <span>Domaines de Recherche</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.users.*') ? 'bg-slate-700' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> {{-- Même icône que partenaires, à changer si souhaité --}}
                            <span>Utilisateurs</span>
                </a>

                <a href="{{ route('admin.settings.edit') }}" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 {{ request()->routeIs('admin.settings.edit') ? 'bg-slate-700' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Paramètres du Site</span>
                </a>
                {{-- Les futurs liens de navigation admin viendront ici : --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Chercheurs</a> --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Publications</a> --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Actualités</a> --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Événements</a> --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Pages Statiques</a> --}}
                {{-- <a href="#" class="flex items-center space-x-2 py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Paramètres</a> --}}
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            @hasSection('header')
                <header class="bg-white shadow-md">
                    <div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    {{-- Vous pourriez ajouter des scripts JS spécifiques pour l'admin ici --}}
    {{-- <script src="{{ asset('js/admin-custom.js') }}"></script> --}}
</body>
</html>
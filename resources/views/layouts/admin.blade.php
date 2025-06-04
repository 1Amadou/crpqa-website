<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('darkMode') === 'true',
          sidebarOpen: false,
          initAlpine() {
              this.darkMode = localStorage.getItem('darkMode') === 'true';
              this.$watch('darkMode', val => localStorage.setItem('darkMode', val));

              // Logique pour la sidebar mobile si Alpine la gère aussi
              // Ou si c'est géré par layouts.navigation et le JS de Flowbite/autre
          }
      }"
      x-init="initAlpine()"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- $siteSettings est globalement disponible grâce au middleware ShareSiteSettings --}}
    <title>@yield('title', 'Tableau de Bord') - {{ $siteSettings->getTranslation('site_name_short', app()->getLocale(), false) ?: $siteSettings->getTranslation('site_name', app()->getLocale(), false) ?: config('app.name', 'CRPQA') }} Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    {{-- Ionicons --}}
    <script type="module" src="https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.js"></script>

    {{-- Meta tags pour TinyMCE (Vite gérera le versioning des assets) --}}
   {{-- <meta name="tinymce-lang-url" content="{{ Vite::asset('resources/tinymce/langs/fr_FR.js') }}">
    <meta name="tinymce-skin-url" content="{{ Vite::asset('resources/tinymce/skins/ui/oxide') }}">
    <meta name="tinymce-content-css-dark" content="{{ Vite::asset('resources/tinymce/skins/content/dark/content.min.css') }}">
    <meta name="tinymce-content-css-default" content="{{ Vite::asset('resources/tinymce/skins/content/default/content.min.css') }}">
    
    {{-- TinyMCE JS est maintenant importé dans resources/js/admin/app-admin.js --}}

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/app-admin.js']) {{-- app-admin.js pour le JS spécifique à l'admin --}}

    @stack('styles') {{-- Renommé pour cohérence Laravel --}}
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        {{-- Barre Latérale --}}
        {{-- La visibilité sur mobile ('hidden sm:flex') est gérée par Tailwind. --}}
        {{-- L'ouverture/fermeture sur mobile peut être gérée par Alpine.js via $sidebarOpen ou par le JS de navigation.blade.php --}}
        <aside class="w-64 bg-slate-800 dark:bg-gray-800 text-slate-200 dark:text-gray-300 p-4 space-y-4 hidden sm:flex sm:flex-col shadow-xl z-30"
               id="admin-sidebar">
            <div class="text-center py-3 border-b border-slate-700 dark:border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center">
                    @if($siteSettings->logo_header_url && $siteSettings->logo_header_url !== asset('assets/images/placeholders/default_logo_header.png'))
                        <img src="{{ $siteSettings->logo_header_url }}" alt="{{ $siteSettings->getTranslation('site_name_short', app()->getLocale(), false) }} Logo" class="h-8 w-auto mr-2">
                    @endif
                    <span class="text-xl font-bold text-white group-hover:text-slate-300 dark:group-hover:text-gray-200 transition-colors">
                        {{ $siteSettings->getTranslation('site_name_short', app()->getLocale(), false) ?: 'Admin' }}
                    </span>
                </a>
            </div>
            <nav class="flex-grow space-y-1 overflow-y-auto">
                @php
                    $currentRouteName = Route::currentRouteName();

                    // Fonction pour générer les liens de navigation simples
                    function navLink($route, $iconName, $label, $activeRoutes = [], $isSubItem = false) {
                        global $currentRouteName; // Rendre la variable globale accessible
                        $url = Route::has($route) ? route($route) : '#';
                        $isActive = $currentRouteName === $route || collect($activeRoutes)->contains(fn($pattern) => Str::is($pattern, $currentRouteName));
                        
                        $linkClasses = 'flex items-center space-x-3 py-2.5 px-3 rounded-md transition-all duration-200 ease-in-out text-sm font-medium ';
                        $subItemIndentClass = $isSubItem ? 'pl-10 ' : ' '; // Indentation pour les sous-éléments

                        $activeClasses = 'bg-primary-600 dark:bg-primary-500 text-white shadow-sm';
                        $inactiveClasses = 'text-slate-300 dark:text-gray-400 hover:bg-slate-700 dark:hover:bg-gray-700 hover:text-white dark:hover:text-gray-100';
                        
                        $iconColorClass = $isActive ? 'text-white' : 'text-slate-400 dark:text-gray-400 group-hover:text-slate-200 dark:group-hover:text-gray-100';
                        $iconSizeClass = $isSubItem ? 'text-lg' : 'text-xl';
                        $iconHtml = '';
                        if (!empty($iconName)) {
                             $iconHtml = '<ion-icon name="'.$iconName.'" class="'.$iconSizeClass.' '.$iconColorClass.' flex-shrink-0"></ion-icon>';
                        } elseif ($isSubItem) { // Espace pour aligner si pas d'icône pour sous-item
                            $iconHtml = '<span class="w-5 h-5 mr-3 flex-shrink-0"></span>'; 
                        }

                        return '<a href="'.$url.'" class="group '.$linkClasses.$subItemIndentClass.($isActive ? $activeClasses : $inactiveClasses).'">
                                    '.$iconHtml.'
                                    <span class="truncate">'.__($label).'</span>
                                </a>';
                    }

                    // Fonction pour un groupe de menu déroulant
                    function navDropdownGroup($groupId, $groupIcon, $groupLabel, $mainRouteActivePatterns = [], $subItems = []) {
                        global $currentRouteName;
                        $isGroupActive = false;
                        if (!empty($mainRouteActivePatterns)) {
                             $isGroupActive = collect($mainRouteActivePatterns)->contains(fn($pattern) => Str::is($pattern, $currentRouteName));
                        }
                        // Si le groupe n'est pas actif par sa route principale, vérifier les sous-éléments
                        if (!$isGroupActive) {
                             $isGroupActive = collect($subItems)->contains(function ($item) use ($currentRouteName) {
                                return $currentRouteName === $item['route'] || collect($item['activeRoutes'] ?? [])->contains(fn($pattern) => Str::is($pattern, $currentRouteName));
                            });
                        }

                        $buttonClasses = 'flex items-center w-full py-2.5 px-3 text-slate-200 dark:text-gray-300 transition duration-75 rounded-md group hover:bg-slate-700 dark:hover:bg-gray-700 ';
                        $activeGroupClasses = 'bg-slate-700 dark:bg-gray-700 text-white'; // Style pour le bouton parent si un sous-menu est actif

                        // Utiliser x-show et x-collapse pour le dropdown Alpine.js
                        $output = '<li x-data="{ open: ' . ($isGroupActive ? 'true' : 'false') . ' }">
                                    <button type="button" @click="open = !open" 
                                            class="'.$buttonClasses.($isGroupActive ? $activeGroupClasses : '').'" 
                                            aria-controls="dropdown-'.$groupId.'" :aria-expanded="open.toString()">
                                        <ion-icon name="'.$groupIcon.'" class="flex-shrink-0 w-5 h-5 text-slate-400 dark:text-gray-400 transition duration-75 group-hover:text-slate-200 dark:group-hover:text-gray-100 '.($isGroupActive ? 'text-white' : '').'"></ion-icon>
                                        <span class="flex-1 ml-3 text-left whitespace-nowrap text-sm font-medium">'.__($groupLabel).'</span>
                                        <ion-icon name="chevron-down-outline" class="w-4 h-4 transition-transform duration-200" :class="{\'rotate-180\': open}"></ion-icon>
                                    </button>
                                    <ul id="dropdown-'.$groupId.'" x-show="open" x-collapse class="py-1 space-y-1 mt-1">';
                        
                        foreach ($subItems as $item) {
                            if (isset($item['permission']) && !(Auth::check() && Auth::user()->can($item['permission']))) {
                                continue;
                            }
                            $output .= '<li>'.navLink($item['route'], $item['icon'] ?? '', __($item['label']), $item['activeRoutes'] ?? [], true).'</li>';
                        }
                        $output .= '</ul></li>';
                        return $output;
                    }
                @endphp

                {!! navLink('admin.dashboard', 'grid-outline', 'Tableau de Bord', ['admin.dashboard']) !!}
                
                {{-- Contenus Principaux --}}
                <div class="mt-3 pt-3 border-t border-slate-700 dark:border-gray-700">
                    <span class="px-3 text-xs font-semibold text-slate-500 dark:text-gray-500 uppercase tracking-wider">{{__('Contenus')}}</span>
                </div>
                {!! navLink('admin.static-pages.index', 'document-text-outline', 'Pages Statiques', ['admin.static-pages.*']) !!}
                
                {!! navDropdownGroup('news_management', 'newspaper-outline', 'Actualités', ['admin.news.*', 'admin.news-categories.*'], [
                    ['route' => 'admin.news.index', 'label' => 'Toutes les actualités', 'icon' => 'list-outline', 'activeRoutes' => ['admin.news.create', 'admin.news.edit', 'admin.news.show']],
                    ['route' => 'admin.news-categories.index', 'label' => 'Catégories', 'icon' => 'pricetags-outline', 'activeRoutes' => ['admin.news-categories.*'], 'permission' => 'manage news_categories']
                ]) !!}

                {!! navDropdownGroup('events_management', 'calendar-outline', 'Événements', ['admin.events.*', 'admin.event-registrations.*'], [
                    ['route' => 'admin.events.index', 'label' => 'Tous les événements', 'icon' => 'list-outline', 'activeRoutes' => ['admin.events.create', 'admin.events.edit', 'admin.events.show', 'admin.events.registrations.index']],
                    // Lien global vers les inscriptions si vous en avez un. Sinon, les inscriptions sont par événement.
                    // ['route' => 'admin.event-registrations.index', 'label' => 'Toutes les Inscriptions', 'icon' => 'person-add-outline', 'activeRoutes' => ['admin.event-registrations.*'], 'permission' => 'view event_registrations']
                ]) !!}

                {!! navLink('admin.publications.index', 'book-outline', 'Publications', ['admin.publications.*']) !!}

                {{-- Organisation & Recherche --}}
                <div class="mt-3 pt-3 border-t border-slate-700 dark:border-gray-700">
                    <span class="px-3 text-xs font-semibold text-slate-500 dark:text-gray-500 uppercase tracking-wider">{{__('Organisation')}}</span>
                </div>
                {!! navLink('admin.researchers.index', 'people-outline', 'Chercheurs', ['admin.researchers.*']) !!}
                {!! navLink('admin.research-axes.index', 'flask-outline', 'Axes de Recherche', ['admin.research-axes.*']) !!}
                {!! navLink('admin.partners.index', 'business-outline', 'Partenaires', ['admin.partners.*']) !!}
                
                {{-- Administration --}}
                <div class="mt-3 pt-3 border-t border-slate-700 dark:border-gray-700">
                    <span class="px-3 text-xs font-semibold text-slate-500 dark:text-gray-500 uppercase tracking-wider">{{__('Administration')}}</span>
                </div>
                @can('manage users')
                    {!! navLink('admin.users.index', 'people-circle-outline', 'Utilisateurs', ['admin.users.*']) !!}
                @endcan
                {{-- @can('manage roles')
                    {!! navLink('admin.roles.index', 'shield-checkmark-outline', __('Rôles'), ['admin.roles.*']) !!}
                @endcan --}}
                @can('manage site settings')
                    {!! navLink('admin.settings.edit', 'settings-outline', 'Paramètres du Site', ['admin.settings.edit']) !!}
                @endcan
            </nav>
            <div class="mt-auto pt-4 border-t border-slate-700 dark:border-gray-700">
                <p class="text-xs text-slate-500 dark:text-gray-400 text-center">&copy; {{ date('Y') }} {{ $siteSettings->getTranslation('site_name_short', app()->getLocale(), false) ?: config('app.name') }}.</p>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Barre de navigation supérieure - gère le menu burger pour mobile --}}
            @include('layouts.navigation', ['siteSettings' => $siteSettings, 'darkMode' => '$darkMode', 'sidebarOpen' => '$sidebarOpen']) 

            @hasSection('header')
                <header class="bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-700/50">
                    <div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
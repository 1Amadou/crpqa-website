<nav x-data="{ open: false }" class="bg-slate-800 dark:bg-gray-800 border-b border-slate-700 dark:border-gray-700 shadow-md">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8"> {{-- Changé à max-w-full pour s'étendre --}}
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    {{-- Bouton pour afficher la sidebar sur mobile, si la sidebar est cachée par défaut et non gérée par ce menu --}}
                    {{-- Normalement, le bouton hamburger ci-dessous gère le menu responsive. --}}
                    {{-- Si la sidebar principale doit être togglée, il faudrait un autre mécanisme ici. --}}
                    {{-- Pour l'instant, on garde le logo/titre simple --}}
                    <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center">
                        {{-- Optionnel: Votre logo SVG/Image ici --}}
                        {{-- <img src="{{ asset('logo-admin-sm.svg') }}" alt="Logo" class="block h-9 w-auto mr-2"> --}}
                        <span class="font-semibold text-lg text-white dark:text-gray-100 group-hover:text-slate-300 dark:group-hover:text-gray-200 transition-colors">
                            {{ $siteSettings->site_name_short ?? 'CRPQA' }}
                        </span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="text-slate-300 dark:text-gray-300 hover:text-white dark:hover:text-gray-100 focus:text-white dark:focus:text-gray-100">
                        {{ __('Tableau de Bord') }}
                    </x-nav-link>
                    {{-- Si vous avez 1 ou 2 autres liens TRES importants pour le desktop topbar --}}
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-200 dark:text-gray-300 bg-slate-700 dark:bg-gray-700 hover:text-white dark:hover:text-gray-100 hover:bg-slate-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 dark:focus:ring-offset-gray-800 focus:ring-sky-500 transition ease-in-out duration-150">
                            {{-- Optionnel: Avatar utilisateur --}}
                            {{-- <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ Auth::user()->profile_photo_url ?? asset('img/placeholders/user_avatar_sm.png') }}" alt="{{ Auth::user()->name ?? 'Utilisateur' }}" /> --}}
                            <div>
                                @if(Auth::check() && Auth::user())
                                    {{ Auth::user()->name }}
                                @else
                                    Utilisateur
                                @endif
                            </div>

                            <div class="ms-1">
                                <ion-icon name="chevron-down-outline" class="text-base"></ion-icon>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-1 rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5">
                            <x-dropdown-link :href="route('admin.profile.edit')" class="text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <ion-icon name="person-circle-outline" class="mr-2 text-lg"></ion-icon>
                                {{ __('Mon Profil') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <ion-icon name="log-out-outline" class="mr-2 text-lg"></ion-icon>
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-300 dark:text-gray-400 hover:text-white dark:hover:text-gray-100 hover:bg-slate-700 dark:hover:bg-gray-700 focus:outline-none focus:bg-slate-700 dark:focus:bg-gray-700 focus:text-white dark:focus:text-gray-100 transition duration-150 ease-in-out">
                    <span class="sr-only">Ouvrir le menu principal</span>
                    <ion-icon :name="open ? 'close-outline' : 'menu-outline'" class="h-6 w-6 text-2xl"></ion-icon>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-700 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Liens de navigation principaux pour mobile (doivent refléter la sidebar) --}}
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="grid-outline">
                {{ __('Tableau de Bord') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.static-pages.index')" :active="request()->routeIs('admin.static-pages.*')" icon="document-text-outline">
                {{ __('Pages Statiques') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.researchers.index')" :active="request()->routeIs('admin.researchers.*')" icon="people-outline">
                {{ __('Chercheurs') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')" icon="book-outline">
                {{ __('Publications') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.news.index')" :active="request()->routeIs('admin.news.*')" icon="newspaper-outline">
                {{ __('Actualités') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')" icon="calendar-outline">
                {{ __('Événements') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.partners.index')" :active="request()->routeIs('admin.partners.*')" icon="business-outline">
                {{ __('Partenaires') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.research-axes.index')" :active="request()->routeIs('admin.research-axes.*')" icon="flask-outline">
                {{ __('Axes de Recherche') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-slate-600 dark:border-gray-600">
            <div class="px-4">
                @if(Auth::check() && Auth::user())
                    <div class="font-medium text-base text-white dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-400 dark:text-gray-400">{{ Auth::user()->email }}</div>
                @else
                    <div class="font-medium text-base text-white dark:text-gray-200">{{ __('Visiteur') }}</div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.profile.edit')" icon="person-circle-outline">
                    {{ __('Mon Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            icon="log-out-outline">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
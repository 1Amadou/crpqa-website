{{-- resources/views/layouts/partials/public-header.blade.php --}}
{{-- $siteSettings est passé depuis layouts.public.blade.php --}}
@php
    $currentLocale = app()->getLocale();
@endphp
<header class="header fixed top-0 left-0 w-full z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-sm" id="header" 
        x-data="{ mobileMenuOpen: false, isScrolled: false }" 
        @scroll.window="isScrolled = (window.pageYOffset > 50) ? true : false"
        :class="{ 'header--scrolled shadow-lg': isScrolled }">
    <nav class="nav container mx-auto px-4 sm:px-6 lg:px-8 h-header-height flex justify-between items-center">
        <a href="{{ route('public.home') }}" class="nav__logo inline-flex items-center space-x-2 rtl:space-x-reverse" aria-label="{{ __('Page d\'accueil de') }} {{ $siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) }}">
            @if($siteSettings->logo_header_url)
                <img src="{{ $siteSettings->logo_header_url }}" 
                     class="h-10 md:h-12 object-contain" {{-- Ajustez la hauteur ici --}}
                     alt="{{ $siteSettings->getTranslation('site_name', $currentLocale, false) }} {{ __('Logo') }}">
            @else
                <span class="self-center text-xl md:text-2xl font-semibold whitespace-nowrap text-title-color dark:text-dark-title-color">
                    {{ $siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) }}
                </span>
            @endif
        </a>

        <div class="nav__menu bg-body-color dark:bg-dark-body-color sm:!bg-transparent sm:dark:!bg-transparent" id="nav-menu" :class="{ 'show-menu': mobileMenuOpen }">
            <ul class="nav__list flex flex-col sm:flex-row sm:space-x-1 lg:space-x-2">
                <li class="nav__item">
                    <a href="{{ route('public.home') }}" class="nav__link {{ request()->routeIs('public.home') ? 'active-link' : '' }}">
                        <ion-icon name="home-outline" class="nav__icon"></ion-icon> {{ __('Accueil') }}
                    </a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('public.page', ['staticPage' => $siteSettings->about_page_slug ?: 'a-propos']) }}" class="nav__link {{ request()->is('page/'.($siteSettings->about_page_slug ?: 'a-propos')) || request()->routeIs('public.about') ? 'active-link' : '' }}">
                        <ion-icon name="information-circle-outline" class="nav__icon"></ion-icon> {{ __('À Propos') }}
                    </a>
                </li>

                {{-- Menu déroulant Vie du Centre --}}
                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false" @focusout.window="if (!event.currentTarget.contains(event.relatedTarget)) open = false">
                    <button type="button" @mouseover="open = true" @click.prevent="open = !open" aria-expanded="open" aria-controls="dropdown-vie-centre"
                            class="nav__link nav__link--button {{ (request()->routeIs('public.news.*') || request()->routeIs('public.events.*') ) ? 'active-link' : '' }}">
                        <ion-icon name="leaf-outline" class="nav__icon"></ion-icon> {{ __('Vie du Centre') }} <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
                    </button>
                    <ul x-show="open" id="dropdown-vie-centre" x-cloak
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="nav__dropdown">
                        <li><a href="{{ route('public.news.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.news.*') ? 'active-dropdown-link' : '' }}">{{ __('Actualités') }}</a></li>
                        <li><a href="{{ route('public.events.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.events.*') ? 'active-dropdown-link' : '' }}">{{ __('Événements') }}</a></li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.publications.index') }}" class="nav__link {{ request()->routeIs('public.publications.*') ? 'active-link' : '' }}">
                         <ion-icon name="book-outline" class="nav__icon"></ion-icon> {{ __('Publications') }}
                    </a>
                </li>

                {{-- Menu déroulant Recherche & Équipe --}}
                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false" @focusout.window="if (!event.currentTarget.contains(event.relatedTarget)) open = false">
                    <button type="button" @mouseover="open = true" @click.prevent="open = !open" aria-expanded="open" aria-controls="dropdown-recherche"
                            class="nav__link nav__link--button {{ (request()->routeIs('public.research_axes.*') || request()->routeIs('public.researchers.*')) ? 'active-link' : '' }}">
                        <ion-icon name="flask-outline" class="nav__icon"></ion-icon> {{ __('Recherche & Équipe') }} <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
                    </button>
                    <ul x-show="open" id="dropdown-recherche" x-cloak @click.away="open = false" x-transition class="nav__dropdown">
                        <li><a href="{{ route('public.research_axes.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.research_axes.*') ? 'active-dropdown-link' : '' }}">{{ __('Axes de Recherche') }}</a></li>
                        <li><a href="{{ route('public.researchers.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.researchers.*') ? 'active-dropdown-link' : '' }}">{{ __('Notre Équipe') }}</a></li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.partners.index') }}" class="nav__link {{ request()->routeIs('public.partners.index') ? 'active-link' : '' }}">
                         <ion-icon name="business-outline" class="nav__icon"></ion-icon> {{ __('Partenaires') }}
                    </a>
                </li>
                
                {{-- Liens supplémentaires (ex: Carrières) si configuré --}}
                @if($siteSettings->careers_page_slug && Route::has('public.page'))
                <li class="nav__item">
                    <a href="{{ route('public.page', ['staticPage' => $siteSettings->careers_page_slug]) }}" class="nav__link {{ request()->is('page/'.$siteSettings->careers_page_slug) ? 'active-link' : '' }}">
                        <ion-icon name="school-outline" class="nav__icon"></ion-icon> {{ __('Carrières') }}
                    </a>
                </li>
                @endif

                <li class="nav__item nav__item--mobile-only mt-4">
                    <a href="{{ route('public.contact.form') }}" class="button button--primary button--small w-full">
                        {{ __('Contactez-nous') }}
                    </a>
                </li>
            </ul>

            <button type="button" class="nav__close" id="nav-close" @click="mobileMenuOpen = false" title="Fermer le menu" aria-label="Fermer le menu">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <div class="nav__buttons flex items-center">
            {{-- Bouton de contact pour desktop --}}
            <a href="{{ route('public.contact.form') }}" class="button button--primary button--small nav__contact-button hidden sm:inline-flex">
                {{ __('Contactez-nous') }}
            </a>
            {{-- Bouton Dark Mode Toggle --}}
            <button @click="darkMode = !darkMode" class="nav__theme-button ml-3 text-xl text-text-color dark:text-dark-text-color hover:text-primary-500 dark:hover:text-primary-400" aria-label="{{__('Changer le thème')}}">
                <ion-icon name="moon-outline" x-show="!darkMode"></ion-icon>
                <ion-icon name="sunny-outline" x-show="darkMode" x-cloak></ion-icon>
            </button>
            {{-- Toggle pour menu mobile --}}
            <button type="button" class="nav__toggle sm:hidden ml-3 text-2xl text-title-color dark:text-dark-title-color" id="nav-toggle" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Ouvrir le menu" aria-expanded="mobileMenuOpen" aria-controls="nav-menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
        </div>
    </nav>
</header>
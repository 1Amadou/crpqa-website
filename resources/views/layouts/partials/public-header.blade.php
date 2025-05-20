{{-- resources/views/layouts/partials/public-header.blade.php --}}
<header class="header" id="header"> {{-- Classe .header de style.css --}}
    <nav class="nav container">
        <a href="{{ route('public.home') }}" class="nav__logo">
            {{-- <span class="nav__logo-text">{{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}</span> --}}
        </a>

        {{-- Le x-data pour Alpine est déplacé ici pour englober le menu et le toggle si Alpine gère le 'show-menu' --}}
        {{-- Si votre JS natif dans public-main.js gère 'show-menu', x-data n'est pas nécessaire ici pour cela --}}
        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="{{ route('public.home') }}" class="nav__link {{ request()->routeIs('public.home') ? 'active-link' : '' }}">Accueil</a>
                </li>
                <li class="nav__item">
                    <a href="{{ route('public.page', ['staticPage' => 'a-propos']) }}" class="nav__link {{ (request()->routeIs('public.page') && request()->route('staticPage') && request()->route('staticPage')->slug == 'a-propos') ? 'active-link' : '' }}">À Propos</a>
                </li>

                {{-- Menu déroulant Vie du Centre - Utilisation d'Alpine pour le dropdown desktop --}}
                {{-- Pour mobile, le CSS devra peut-être transformer cela en "accordéon" ou liens directs --}}
                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false">
                    <button @mouseover="open = true" @click.prevent="open = !open" type="button" class="nav__link nav__link--button {{ (request()->routeIs('public.news.index') || request()->routeIs('public.news.show') || request()->routeIs('public.events.index') || request()->routeIs('public.events.show') ) ? 'active-link' : '' }}">
                        Vie du Centre <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
                    </button>
                    <ul x-show="open" x-cloak x-transition class="nav__dropdown">
                        <li><a href="{{ route('public.news.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.news.index') || request()->routeIs('public.news.show') ? 'active-dropdown-link' : '' }}">Actualités</a></li>
                        <li><a href="{{ route('public.events.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.events.index') || request()->routeIs('public.events.show') ? 'active-dropdown-link' : '' }}">Événements</a></li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.publications.index') }}" class="nav__link {{ (request()->routeIs('public.publications.index') || request()->routeIs('public.publications.show')) ? 'active-link' : '' }}">Publications</a>
                </li>

                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false">
                    <button @mouseover="open = true" @click.prevent="open = !open" type="button" class="nav__link nav__link--button {{ (request()->routeIs('public.research_axes.index') || request()->routeIs('public.research_axes.show') || request()->routeIs('public.researchers.index') || request()->routeIs('public.researchers.show')) ? 'active-link' : '' }}">
                        Recherche & Équipe <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
                    </button>
                    <ul x-show="open" x-cloak x-transition class="nav__dropdown">
                        <li><a href="{{ route('public.research_axes.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.research_axes.index') || request()->routeIs('public.research_axes.show') ? 'active-dropdown-link' : '' }}">Domaines de Recherche</a></li>
                        <li><a href="{{ route('public.researchers.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.researchers.index') || request()->routeIs('public.researchers.show') ? 'active-dropdown-link' : '' }}">Notre Équipe</a></li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.partners.index') }}" class="nav__link {{ request()->routeIs('public.partners.index') ? 'active-link' : '' }}">Partenaires</a>
                </li>
                {{-- Le bouton Contact est maintenant dans .nav__buttons pour desktop, et peut être répété ici pour mobile si besoin --}}
                 <li class="nav__item sm:hidden"> {{-- Uniquement pour le menu mobile si .nav__contact-button est caché sur sm --}}
                    <a href="{{ route('public.contact.form') }}" class="nav__link button button--small button--outline">
                        Contactez-nous
                    </a>
                </li>
            </ul>
            <div class="nav__close" id="nav-close">
                <ion-icon name="close-outline"></ion-icon>
            </div>
        </div>

        <div class="nav__buttons">
            {{-- Bouton Contact principal pour Desktop, votre CSS le cache sur mobile --}}
            <a href="{{ route('public.contact.form') }}" class="button button--small nav__contact-button">
                Contactez-nous
            </a>
            <div class="nav__toggle" id="nav-toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
    </nav>
</header>
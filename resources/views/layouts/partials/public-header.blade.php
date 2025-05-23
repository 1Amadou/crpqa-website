{{-- resources/views/layouts/partials/public-header.blade.php --}}
<header class="header" id="header" x-data="{ mobileMenuOpen: false }">
    <nav class="nav container">
        <a href="{{ route('public.home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            {{-- Utilise 'logo_path' ici --}}
            @if(isset($siteSettings['logo_path']) && $siteSettings['logo_path'])
                <img src="{{ asset('storage/' . $siteSettings['logo_path']) }}" class="h-12" alt="{{ $siteSettings['site_name'] ?? config('app.name', 'CRPQA') }} Logo">
            @else
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">{{ $siteSettings['site_name'] ?? config('app.name', 'CRPQA') }}</span>
            @endif
        </a>

        {{-- Menu de navigation --}}

        <div class="nav__menu" id="nav-menu" :class="{ 'show-menu': mobileMenuOpen }">
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="{{ route('public.home') }}" class="nav__link {{ request()->routeIs('public.home') ? 'active-link' : '' }}">
                        Accueil
                    </a>
                </li>
                <li class="nav__item">
                            <a href="{{ route('public.about') }}" class="nav__link {{ request()->routeIs('public.about') ? 'active-link' : '' }}">
                                <i class="uil uil-info-circle nav__icon"></i> À Propos
                            </a>
                </li>

                {{-- Menu déroulant Vie du Centre --}}
                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false">
                    <button type="button" @mouseover="open = true" @click.prevent="open = !open" aria-expanded="open" aria-controls="dropdown-vie-centre"
                            class="nav__link nav__link--button {{ (request()->routeIs('public.news.*') || request()->routeIs('public.events.*') ) ? 'active-link' : '' }}">
                        Vie du Centre <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
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
                        <li>
                            <a href="{{ route('public.news.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.news.*') ? 'active-dropdown-link' : '' }}">
                                Actualités
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.events.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.events.*') ? 'active-dropdown-link' : '' }}">
                                Événements
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.publications.index') }}" class="nav__link {{ request()->routeIs('public.publications.*') ? 'active-link' : '' }}">
                        Publications
                    </a>
                </li>

                {{-- Menu déroulant Recherche & Équipe --}}
                <li class="nav__item nav__item--dropdown" x-data="{ open: false }" @mouseleave="open = false">
                    <button type="button" @mouseover="open = true" @click.prevent="open = !open" aria-expanded="open" aria-controls="dropdown-recherche"
                            class="nav__link nav__link--button {{ (request()->routeIs('public.research_axes.*') || request()->routeIs('public.researchers.*')) ? 'active-link' : '' }}">
                        Recherche & Équipe <ion-icon name="chevron-down-outline" class="nav__link-arrow" :class="{'rotate-180': open}"></ion-icon>
                    </button>
                    <ul x-show="open" id="dropdown-recherche" x-cloak
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="nav__dropdown">
                        <li>
                            <a href="{{ route('public.research_axes.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.research_axes.*') ? 'active-dropdown-link' : '' }}">
                                Domaines de Recherche
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.researchers.index') }}" class="nav__dropdown-link {{ request()->routeIs('public.researchers.*') ? 'active-dropdown-link' : '' }}">
                                Notre Équipe
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav__item">
                    <a href="{{ route('public.partners.index') }}" class="nav__link {{ request()->routeIs('public.partners.index') ? 'active-link' : '' }}">
                        Partenaires
                    </a>
                </li>

                {{-- Lien Contact pour le menu mobile (si le bouton principal est caché) --}}
                <li class="nav__item nav__item--mobile-only"> {{-- Classe à ajouter pour afficher seulement en mode menu mobile --}}
                    <a href="{{ route('public.contact.form') }}" class="nav__link button button--outline button--small" style="width: max-content; margin-top: var(--sp-1); margin-left: var(--sp-1-5);">
                         Contactez-nous
                    </a>
                </li>
            </ul>

            {{-- Bouton de fermeture pour le menu mobile --}}
            <div class="nav__close" id="nav-close" @click="mobileMenuOpen = false" title="Fermer le menu">
                <ion-icon name="close-outline"></ion-icon>
            </div>
        </div>

        {{-- Boutons d'action du header (ex: Contact) et Toggle pour mobile --}}
        <div class="nav__buttons">
            {{-- Le bouton Contact principal est stylé par .nav__contact-button dans votre style.css,
                 qui le cache sur les écrans plus petits (<= 992px) --}}
            <a href="{{ route('public.contact.form') }}" class="button button--small nav__contact-button">
                Contactez-nous
            </a>
            {{-- Toggle pour menu mobile --}}
            <button type="button" class="nav__toggle" id="nav-toggle" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Ouvrir le menu" aria-expanded="mobileMenuOpen" aria-controls="nav-menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
        </div>
    </nav>
</header>
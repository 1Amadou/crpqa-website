{{-- resources/views/layouts/partials/public-header.blade.php --}}
<header class="header fixed top-0 left-0 w-full z-fixed transition-all duration-400 ease-in-out" id="header">
    <nav class="nav container mx-auto h-[var(--header-height)] flex justify-between items-center">
        <a href="{{ route('public.home') }}" class="nav__logo">
            {{-- Si vous avez un logo image dans $siteSettings->logo_path --}}
            @if(isset($siteSettings) && $siteSettings->logo_path && Storage::disk('public')->exists($siteSettings->logo_path))
                <img src="{{ Storage::url($siteSettings->logo_path) }}" alt="{{ $siteSettings->site_name ?? 'CRPQA Logo' }}" class="h-10 md:h-12"> {{-- Ajustez la hauteur au besoin --}}
            @else
            {{-- Sinon, affichez le nom du site ou l'acronyme --}}
                <span class="nav__logo-text text-2xl md:text-3xl font-extrabold tracking-tight" style="color: var(--first-color);">
                    {{ $siteSettings->site_name_short ?? 'CRPQA' }} {{-- $siteSettings->site_name_short est une variable que vous pourriez ajouter à vos paramètres si le nom complet est trop long pour le logo texte --}}
                </span>
            @endif
        </a>

        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                {{-- Les href seront mis à jour avec des routes Laravel nommées au fur et à mesure
                     que nous créerons les pages publiques correspondantes.
                     Pour l'instant, on peut utiliser des ancres si ces sections sont sur la page d'accueil,
                     ou des liens temporaires.
                --}}
                <li class="nav__item"><a href="{{ route('public.home') }}#accueil" class="nav__link active-link">Accueil</a></li>
                <li class="nav__item"><a href="{{-- route('public.about') --}}#apropos" class="nav__link">À Propos</a></li> {{-- Supposant une section #apropos sur l'accueil --}}
                <li class="nav__item"><a href="{{-- route('public.research') --}}#recherche" class="nav__link">Recherche</a></li>
                <li class="nav__item"><a href="{{-- route('public.publications.index') --}}#publications" class="nav__link">Publications</a></li>
                <li class="nav__item"><a href="{{-- route('public.news.index') --}}#actualites" class="nav__link">Actualités</a></li>
                <li class="nav__item"><a href="{{-- route('public.contact') --}}#contact-footer" class="nav__link">Contact</a></li> {{-- Lien vers l'ancre du footer --}}
            </ul>
            <div class="nav__close" id="nav-close">
                <ion-icon name="close-outline"></ion-icon>
            </div>
        </div>

        <div class="nav__buttons flex items-center">
            {{-- Le bouton "Nous Contacter" peut aussi pointer vers l'ancre du footer ou une page contact dédiée --}}
            <a href="{{-- route('public.contact') --}}#contact-footer" class="button button--ghost button--small nav__contact-button hidden md:inline-flex">Nous Contacter</a>
            <div class="nav__toggle" id="nav-toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
    </nav>
</header>
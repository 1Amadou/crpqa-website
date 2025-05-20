{{-- resources/views/layouts/partials/public-footer.blade.php --}}
<footer class="footer section">
    <div class="footer__container container grid">
        {{-- Colonne 1: Logo, Description, Réseaux Sociaux --}}
        <div class="footer__content footer__brand"> {{-- Classe spécifique pour branding --}}
            <a href="{{ route('public.home') }}" class="footer__logo">
                <span class="footer__logo-text">{{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}</span>
            </a>
            @if(!empty($siteSettings['site_tagline']))
            <p class="footer__description">
                {{ $siteSettings['site_tagline'] }}
            </p>
            @endif

            @php
                $socials = [];
                if (!empty($siteSettings['social_facebook'])) $socials['facebook'] = ['url' => $siteSettings['social_facebook'], 'icon' => 'logo-facebook', 'title' => 'Facebook'];
                if (!empty($siteSettings['social_twitter'])) $socials['twitter'] = ['url' => $siteSettings['social_twitter'], 'icon' => 'logo-twitter', 'title' => 'Twitter'];
                if (!empty($siteSettings['social_linkedin'])) $socials['linkedin'] = ['url' => $siteSettings['social_linkedin'], 'icon' => 'logo-linkedin', 'title' => 'LinkedIn'];
                // Ajoutez d'autres réseaux ici si gérés par le tableau de bord
                // if (!empty($siteSettings['social_youtube'])) $socials['youtube'] = ['url' => $siteSettings['social_youtube'], 'icon' => 'logo-youtube', 'title' => 'YouTube'];
            @endphp

            @if(count($socials) > 0)
            <div class="footer__social">
                @foreach($socials as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" title="{{ $social['title'] }}">
                        <ion-icon name="{{ $social['icon'] }}"></ion-icon>
                    </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Colonnes de Liens (dynamiques si possible) --}}
        {{-- Exemple si vous avez des groupes de liens configurables depuis le tableau de bord --}}
        {{-- Pour l'instant, on garde les colonnes statiques comme précédemment, mais la structure doit être flexible --}}

        <div class="footer__content">
            <h3 class="footer__title">Navigation</h3>
            <ul class="footer__links">
                <li><a href="{{ route('public.home') }}" class="footer__link">Accueil</a></li>
                <li><a href="{{ route('public.page', ['staticPage' => 'a-propos']) }}" class="footer__link">À Propos</a></li>
                {{-- Ajoutez d'autres liens de navigation principaux ici --}}
                <li><a href="{{ route('public.news.index') }}" class="footer__link">Actualités</a></li>
                <li><a href="{{ route('public.events.index') }}" class="footer__link">Événements</a></li>
            </ul>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Ressources Clés</h3>
            <ul class="footer__links">
                <li><a href="{{ route('public.publications.index') }}" class="footer__link">Publications</a></li>
                <li><a href="{{ route('public.research_axes.index') }}" class="footer__link">Domaines de Recherche</a></li>
                <li><a href="{{ route('public.researchers.index') }}" class="footer__link">Notre Équipe</a></li>
                <li><a href="{{ route('public.partners.index') }}" class="footer__link">Partenaires</a></li>
            </ul>
        </div>

        <div class="footer__content footer__contact"> {{-- Classe spécifique pour contact --}}
            <h3 class="footer__title">Contactez-Nous</h3>
            <ul class="footer__contact-details">
                @if(!empty($siteSettings['contact_address']))
                <li>
                    <ion-icon name="location-outline"></ion-icon>
                    <span>{!! nl2br(e($siteSettings['contact_address'])) !!}</span> {{-- nl2br pour les retours à la ligne dans l'adresse --}}
                </li>
                @endif
                @if(!empty($siteSettings['contact_email']))
                <li>
                    <ion-icon name="mail-outline"></ion-icon>
                    <a href="mailto:{{ $siteSettings['contact_email'] }}" class="footer__link">{{ $siteSettings['contact_email'] }}</a>
                </li>
                @endif
                @if(!empty($siteSettings['contact_phone']))
                <li>
                    <ion-icon name="call-outline"></ion-icon>
                    <a href="tel:{{ str_replace([' ', '.', '-'], '', $siteSettings['contact_phone']) }}" class="footer__link">{{ $siteSettings['contact_phone'] }}</a>
                </li>
                @endif
            </ul>
            @if(Route::has('public.contact.form')) {{-- Afficher seulement si la route existe --}}
            <div class="mt-4">
                 <a href="{{ route('public.contact.form') }}" class="button button--small button--outline footer__contact-button">
                     Envoyer un message
                 </a>
            </div>
            @endif
        </div>
    </div>

    <div class="footer__bottom container">
        <p class="footer__copy">
            &copy; <span id="currentYear">{{ date('Y') }}</span> {{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}. Tous droits réservés.
        </p>
        <nav class="footer__sublinks-nav" aria-label="Liens légaux">
            @if(!empty($siteSettings['legal_notice_slug']))
                <a href="{{ route('public.page', ['staticPage' => $siteSettings['legal_notice_slug']]) }}" class="footer__sublink">Mentions Légales</a>
            @else
                <a href="{{ route('public.page', ['staticPage' => 'mentions-legales']) }}" class="footer__sublink">Mentions Légales</a>
            @endif

            @if(!empty($siteSettings['privacy_policy_slug']))
                <a href="{{ route('public.page', ['staticPage' => $siteSettings['privacy_policy_slug']]) }}" class="footer__sublink">Politique de Confidentialité</a>
            @else
                <a href="{{ route('public.page', ['staticPage' => 'politique-de-confidentialite']) }}" class="footer__sublink">Politique de Confidentialité</a>
            @endif
            {{-- Ajoutez d'autres liens comme Plan du site si nécessaire --}}
        </nav>
        @if(!empty($siteSettings['site_credits']))
            <p class="footer__credits text-xs text-gray-500 mt-1">
                {!! $siteSettings['site_credits'] !!}
            </p>
        @endif
    </div>
</footer>
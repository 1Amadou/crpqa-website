{{-- resources/views/layouts/partials/public-footer.blade.php --}}
<footer class="footer section"> {{-- Utilisation de .footer et .section de style.css --}}
    <div class="footer__container container grid">
        {{-- Colonne 1: Logo, Description, Réseaux Sociaux --}}
        <div class="footer__content footer__brand"> {{-- Classe spécifique pour cette colonne si besoin de styles additionnels --}}
            <a href="{{ route('public.home') }}" class="footer__logo" title="Accueil {{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}">
                <span class="footer__logo-text">{{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}</span>
            </a>

            @if(!empty($siteSettings['site_tagline']))
            <p class="footer__description">
                {{ $siteSettings['site_tagline'] }}
            </p>
            @endif

            @php
                // Préparation des données pour les réseaux sociaux pour un code plus propre
                $socialLinks = [];
                if (!empty($siteSettings['social_facebook'])) {
                    $socialLinks[] = ['url' => $siteSettings['social_facebook'], 'icon' => 'logo-facebook', 'title' => 'Facebook'];
                }
                if (!empty($siteSettings['social_twitter'])) {
                    $socialLinks[] = ['url' => $siteSettings['social_twitter'], 'icon' => 'logo-twitter', 'title' => 'Twitter'];
                }
                if (!empty($siteSettings['social_linkedin'])) {
                    $socialLinks[] = ['url' => $siteSettings['social_linkedin'], 'icon' => 'logo-linkedin', 'title' => 'LinkedIn'];
                }
                // Ajoutez d'autres réseaux sociaux ici, ex:
                // if (!empty($siteSettings['social_youtube'])) {
                //     $socialLinks[] = ['url' => $siteSettings['social_youtube'], 'icon' => 'logo-youtube', 'title' => 'YouTube'];
                // }
            @endphp

            @if(count($socialLinks) > 0)
            <div class="footer__social">
                @foreach($socialLinks as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" title="{{ $social['title'] }}">
                        <ion-icon name="{{ $social['icon'] }}"></ion-icon>
                    </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Colonne 2: Liens de Navigation Principaux --}}
        {{-- Ces liens pourraient aussi être gérés dynamiquement si nécessaire --}}
        <div class="footer__content">
            <h3 class="footer__title">Navigation</h3>
            <ul class="footer__links">
                <li><a href="{{ route('public.home') }}" class="footer__link">Accueil</a></li>
                <li><a href="{{ route('public.page', ['staticPage' => $siteSettings['about_page_slug'] ?? 'a-propos']) }}" class="footer__link">À Propos</a></li>
                <li><a href="{{ route('public.news.index') }}" class="footer__link">Actualités</a></li>
                <li><a href="{{ route('public.events.index') }}" class="footer__link">Événements</a></li>
                {{-- Ajoutez d'autres liens de navigation importants ici --}}
            </ul>
        </div>

        {{-- Colonne 3: Ressources / Autres Liens --}}
        <div class="footer__content">
            <h3 class="footer__title">Ressources</h3>
            <ul class="footer__links">
                <li><a href="{{ route('public.publications.index') }}" class="footer__link">Publications</a></li>
                <li><a href="{{ route('public.research_axes.index') }}" class="footer__link">Domaines de Recherche</a></li>
                <li><a href="{{ route('public.researchers.index') }}" class="footer__link">Notre Équipe</a></li>
                <li><a href="{{ route('public.partners.index') }}" class="footer__link">Partenaires</a></li>
                {{-- Pour les pages statiques comme "Plan du site", ajoutez-les si elles existent --}}
                {{-- <li><a href="{{ route('public.page', ['staticPage' => 'plan-du-site']) }}" class="footer__link">Plan du Site</a></li> --}}
            </ul>
        </div>

        {{-- Colonne 4: Informations de Contact --}}
        <div class="footer__content footer__contact">
            <h3 class="footer__title">Contactez-Nous</h3>
            <ul class="footer__contact-details">
                @if(!empty($siteSettings['contact_address']))
                <li>
                    <ion-icon name="location-outline" class="footer__contact-icon"></ion-icon>
                    <span>{!! nl2br(e($siteSettings['contact_address'])) !!}</span>
                </li>
                @endif
                @if(!empty($siteSettings['contact_email']))
                <li>
                    <ion-icon name="mail-outline" class="footer__contact-icon"></ion-icon>
                    <a href="mailto:{{ $siteSettings['contact_email'] }}" class="footer__link">{{ $siteSettings['contact_email'] }}</a>
                </li>
                @endif
                @if(!empty($siteSettings['contact_phone']))
                <li>
                    <ion-icon name="call-outline" class="footer__contact-icon"></ion-icon>
                    <a href="tel:{{ str_replace([' ', '.', '-', '(', ')'], '', $siteSettings['contact_phone']) }}" class="footer__link">{{ $siteSettings['contact_phone'] }}</a>
                </li>
                @endif
            </ul>
            @if(Route::has('public.contact.form'))
            <div class="footer__contact-action mt-4"> {{-- Nouvelle classe pour ce conteneur de bouton --}}
                 <a href="{{ route('public.contact.form') }}" class="button button--small button--outline footer__button--contact">
                     Envoyer un message
                 </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Section Copyright et liens légaux en bas du footer --}}
    <div class="footer__bottom container">
        <p class="footer__copy">
            &copy; <span id="currentYear">{{ date('Y') }}</span> {{ $siteSettings['site_name_short'] ?? $siteSettings['site_name'] ?? 'CRPQA' }}. Tous droits réservés.
        </p>
        <nav class="footer__legal-links" aria-label="Liens légaux et administratifs">
            @if(!empty($siteSettings['legal_notice_slug']))
                <a href="{{ route('public.page', ['staticPage' => $siteSettings['legal_notice_slug']]) }}" class="footer__sublink">Mentions Légales</a>
            @elseif(Route::has('public.page') && \App\Models\StaticPage::where('slug', 'mentions-legales')->exists())
                <a href="{{ route('public.page', ['staticPage' => 'mentions-legales']) }}" class="footer__sublink">Mentions Légales</a>
            @endif

            @if(!empty($siteSettings['privacy_policy_slug']))
                <a href="{{ route('public.page', ['staticPage' => $siteSettings['privacy_policy_slug']]) }}" class="footer__sublink">Politique de Confidentialité</a>
            @elseif(Route::has('public.page') && \App\Models\StaticPage::where('slug', 'politique-de-confidentialite')->exists())
                <a href="{{ route('public.page', ['staticPage' => 'politique-de-confidentialite']) }}" class="footer__sublink">Politique de Confidentialité</a>
            @endif
            {{-- Ajoutez d'autres liens comme 'Plan du site' ou 'Accessibilité' ici --}}
        </nav>
        @if(!empty($siteSettings['site_credits']))
            <p class="footer__credits">
                {!! $siteSettings['site_credits'] !!} {{-- Prudence avec {!! !!} - assurez-vous que le contenu est sûr --}}
            </p>
        @endif
    </div>
</footer>
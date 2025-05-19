{{-- resources/views/layouts/partials/public-footer.blade.php --}}
<footer class="footer section" id="contact-footer">
    <div class="footer__container container grid">
        <div class="footer__content">
            <a href="{{ route('public.home') }}" class="footer__logo">
                 @if(isset($siteSettings) && $siteSettings->logo_path && Storage::disk('public')->exists($siteSettings->logo_path))
                    <img src="{{ Storage::url($siteSettings->logo_path) }}" alt="{{ $siteSettings->site_name ?? 'CRPQA Logo' }}" class="h-10 opacity-80"> {{-- Logo plus petit dans le footer --}}
                @else
                    <span class="footer__logo-text">{{ $siteSettings->site_name_short ?? 'CRPQA' }}</span>
                @endif
            </a>
            <p class="footer__description">
                {{ $siteSettings->footer_tagline ?? 'Centre de Recherche en Physique Quantique et de ses Applications. Pionnier de la science quantique au service du progrès.' }}
                {{-- Vous pourriez ajouter un champ 'footer_tagline' dans site_settings --}}
            </p>
            <div class="footer__social">
                @if(isset($siteSettings) && $siteSettings->linkedin_url)<a href="{{ $siteSettings->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link"><ion-icon name="logo-linkedin"></ion-icon></a>@endif
                @if(isset($siteSettings) && $siteSettings->twitter_url)<a href="{{ $siteSettings->twitter_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link"><ion-icon name="logo-twitter"></ion-icon></a>@endif
                @if(isset($siteSettings) && $siteSettings->youtube_url)<a href="{{ $siteSettings->youtube_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link"><ion-icon name="logo-youtube"></ion-icon></a>@endif
                {{-- Ajoutez d'autres réseaux sociaux ici si vous les avez dans $siteSettings (ex: facebook_url) --}}
            </div>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Navigation</h3>
            <ul class="footer__links">
                {{-- Ces liens devront être mis à jour avec des routes Laravel une fois les pages créées --}}
                <li><a href="{{-- route('public.about') --}}#apropos" class="footer__link">À Propos de Nous</a></li>
                <li><a href="{{-- route('public.research') --}}#recherche" class="footer__link">Nos Recherches</a></li>
                <li><a href="{{-- route('public.publications.index') --}}#publications" class="footer__link">Publications</a></li>
                <li><a href="{{-- route('public.team') --}}" class="footer__link">Notre Équipe</a></li>
                <li><a href="{{-- route('public.news.index') --}}#actualites" class="footer__link">Actualités</a></li>
            </ul>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Ressources</h3>
            <ul class="footer__links">
                {{-- Ces liens sont des exemples, à remplacer par vos vraies pages/routes --}}
                <li><a href="#" class="footer__link">Séminaires & Événements</a></li> {{-- Devrait pointer vers la page des événements --}}
                <li><a href="#" class="footer__link">Carrières</a></li> {{-- Si vous avez une page Carrières --}}
                <li><a href="#" class="footer__link">Espace Presse</a></li>
                <li><a href="{{-- route('public.contact') --}}#contact-footer" class="footer__link">Nous Contacter</a></li>
            </ul>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Contactez-Nous</h3>
            <ul class="footer__contact-details">
                @if(isset($siteSettings) && $siteSettings->address)<li><ion-icon name="location-outline"></ion-icon> {{ $siteSettings->address }}</li>@endif
                @if(isset($siteSettings) && $siteSettings->contact_phone)<li><ion-icon name="call-outline"></ion-icon> {{ $siteSettings->contact_phone }}</li>@endif
                @if(isset($siteSettings) && $siteSettings->contact_email)<li><ion-icon name="mail-outline"></ion-icon> <a href="mailto:{{ $siteSettings->contact_email }}" class="text-[var(--second-color)] hover:text-[var(--accent-color-cyan)]">{{ $siteSettings->contact_email }}</a></li>@endif
            </ul>
        </div>
    </div>

    <p class="footer__copy">&#169; {{ date('Y') }} {{ $siteSettings->site_name ?? 'CRPQA' }}. {{ $siteSettings->footer_text ?? 'Tous droits réservés. Conçu avec passion.' }}</p>
    <p class="footer__sublinks">
        @if(isset($siteSettings) && $siteSettings->terms_of_service_url)
            <a href="{{ Str::startsWith($siteSettings->terms_of_service_url, ['http', 'https']) ? $siteSettings->terms_of_service_url : route('public.static.page', $siteSettings->terms_of_service_url) }}" class="footer__sublink" target="{{ Str::startsWith($siteSettings->terms_of_service_url, ['http', 'https']) ? '_blank' : '_self' }}">Mentions Légales</a>
        @endif
        @if(isset($siteSettings) && $siteSettings->privacy_policy_url)
            @if(isset($siteSettings) && $siteSettings->terms_of_service_url) | @endif
            <a href="{{ Str::startsWith($siteSettings->privacy_policy_url, ['http', 'https://']) ? $siteSettings->privacy_policy_url : route('public.static.page', $siteSettings->privacy_policy_url) }}" class="footer__sublink" target="{{ Str::startsWith($siteSettings->privacy_policy_url, ['http', 'https']) ? '_blank' : '_self' }}">Politique de Confidentialité</a>
        @endif
        @if(isset($siteSettings) && $siteSettings->cookie_policy_url)
            @if((isset($siteSettings) && $siteSettings->terms_of_service_url) || (isset($siteSettings) && $siteSettings->privacy_policy_url)) | @endif
            <a href="{{ Str::startsWith($siteSettings->cookie_policy_url, ['http', 'https://']) ? $siteSettings->cookie_policy_url : route('public.static.page', $siteSettings->cookie_policy_url) }}" class="footer__sublink" target="{{ Str::startsWith($siteSettings->cookie_policy_url, ['http', 'https']) ? '_blank' : '_self' }}">Politique des Cookies</a>
        @endif
        {{-- Exemple de lien statique si besoin : | <a href="/plan-du-site" class="footer__sublink">Plan du Site</a> --}}
    </p>
</footer>
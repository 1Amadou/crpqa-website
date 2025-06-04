{{-- resources/views/layouts/partials/public-footer.blade.php --}}
{{-- $siteSettings est passé depuis layouts.public.blade.php --}}
@php
    $currentLocale = app()->getLocale();
@endphp
<footer class="footer bg-gray-800 dark:bg-gray-900 text-gray-300 pt-12 pb-8 px-4">
    <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-8">
        
        <div class="footer__section">
            @if($siteSettings->logo_footer_light_url) {{-- Logo pour fond sombre (footer) --}}
                <a href="{{ route('public.home') }}" class="block mb-4">
                    <img src="{{ $siteSettings->logo_footer_light_url }}" alt="{{ $siteSettings->getTranslation('site_name', $currentLocale, false) }} Logo" class="h-10 md:h-12 object-contain">
                </a>
            @else
                <a href="{{ route('public.home') }}" class="block text-xl font-semibold text-white mb-2">
                    {{ $siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false) }}
                </a>
            @endif
            <p class="text-sm text-gray-400 leading-relaxed">
                {{ $siteSettings->getTranslation('site_description', $currentLocale, false) ? Str::limit($siteSettings->getTranslation('site_description', $currentLocale, false), 150) : __('Votre centre d\'excellence en physique quantique et ses applications.') }}
            </p>
        </div>

        <div>
            <h5 class="footer__title text-lg font-semibold text-white mb-4">{{ __('Navigation') }}</h5>
            <ul class="footer__list space-y-2 text-sm">
                <li><a href="{{ route('public.home') }}" class="footer__link">{{ __('Accueil') }}</a></li>
                <li><a href="{{ route('public.page', ['staticPage' => $siteSettings->about_page_slug ?: 'a-propos']) }}" class="footer__link">{{ __('À Propos') }}</a></li>
                <li><a href="{{ route('public.news.index') }}" class="footer__link">{{ __('Actualités') }}</a></li>
                <!-- <li><a href="{{ route('public.events.index') }}" class="footer__link">{{ __('Événements') }}</a></li>
                <li><a href="{{ route('public.publications.index') }}" class="footer__link">{{ __('Publications') }}</a></li>
                <li><a href="{{ route('public.research_axes.index') }}" class="footer__link">{{ __('Axes de Recherche') }}</a></li>
                <li><a href="{{ route('public.researchers.index') }}" class="footer__link">{{ __('Notre Équipe') }}</a></li>
                <li><a href="{{ route('public.partners.index') }}" class="footer__link">{{ __('Partenaires') }}</a></li> -->
                <li><a href="{{ route('public.contact.form') }}" class="footer__link">{{ __('Contact') }}</a></li>
            </ul>
        </div>

        <div>
            <h5 class="footer__title text-lg font-semibold text-white mb-4">{{ __('Contactez-nous') }}</h5>
            <ul class="footer__list space-y-3 text-sm">
                @if($siteSettings->getTranslation('address', $currentLocale, false))
                    <li class="flex items-start">
                        <ion-icon name="location-outline" class="footer__contact-icon"></ion-icon>
                        <span>{!! nl2br(e($siteSettings->getTranslation('address', $currentLocale, false))) !!}</span>
                    </li>
                @endif
                @if($siteSettings->contact_email)
                    <li class="flex items-center">
                        <ion-icon name="mail-outline" class="footer__contact-icon"></ion-icon>
                        <a href="mailto:{{ $siteSettings->contact_email }}" class="footer__link">{{ $siteSettings->contact_email }}</a>
                    </li>
                @endif
                @if($siteSettings->contact_phone)
                    <li class="flex items-center">
                        <ion-icon name="call-outline" class="footer__contact-icon"></ion-icon>
                        <span>{{ $siteSettings->contact_phone }}</span>
                    </li>
                @endif
            </ul>
        </div>

        <div>
            <h5 class="footer__title text-lg font-semibold text-white mb-4">{{ __('Suivez-nous') }}</h5>
            <div class="flex space-x-4">
                @if($siteSettings->facebook_url)<a href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="Facebook"><ion-icon name="logo-facebook" class="text-2xl"></ion-icon></a>@endif
                @if($siteSettings->twitter_url)<a href="{{ $siteSettings->twitter_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="Twitter"><ion-icon name="logo-twitter" class="text-2xl"></ion-icon></a>@endif
                @if($siteSettings->linkedin_url)<a href="{{ $siteSettings->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="LinkedIn"><ion-icon name="logo-linkedin" class="text-2xl"></ion-icon></a>@endif
                @if($siteSettings->youtube_url)<a href="{{ $siteSettings->youtube_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="YouTube"><ion-icon name="logo-youtube" class="text-2xl"></ion-icon></a>@endif
                {{-- @if($siteSettings->instagram_url)<a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="Instagram"><ion-icon name="logo-instagram" class="text-2xl"></ion-icon></a>@endif --}}
            </div>
        </div>
    </div>

    <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm">
        <p class="text-gray-400">
            {{ $siteSettings->getTranslation('copyright_text', $currentLocale, false) ?: $siteSettings->getTranslation('footer_text', $currentLocale, false) ?: ('© ' . date('Y') . ' ' . ($siteSettings->getTranslation('site_name_short', $currentLocale, false) ?: $siteSettings->getTranslation('site_name', $currentLocale, false)) . '. ' . __('Tous droits réservés.')) }}
        </p>
        <div class="mt-2 space-x-3 sm:space-x-4">
            @if($siteSettings->privacy_policy_page_slug && Route::has('public.page'))
                <a href="{{ route('public.page', ['staticPage' => $siteSettings->privacy_policy_page_slug]) }}" class="footer__link text-xs">{{ __('Politique de Confidentialité') }}</a>
            @endif
            @if($siteSettings->terms_of_service_page_slug && Route::has('public.page'))
                <span class="text-gray-500 text-xs">|</span>
                <a href="{{ route('public.page', ['staticPage' => $siteSettings->terms_of_service_page_slug]) }}" class="footer__link text-xs">{{ __('Conditions d\'Utilisation') }}</a>
            @endif
            @if($siteSettings->cookie_policy_page_slug && Route::has('public.page'))
                <span class="text-gray-500 text-xs">|</span>
                <a href="{{ route('public.page', ['staticPage' => $siteSettings->cookie_policy_page_slug]) }}" class="footer__link text-xs">{{ __('Politique de Cookies') }}</a>
            @endif
        </div>
    </div>
</footer>

<style>
    /* Styles spécifiques pour le footer (peuvent aller dans votre CSS principal) */
    .footer__title { /* ... */ }
    .footer__list li a, .footer__social-link { color: #cbd5e1; /* gray-400 */ transition: color 0.3s ease; }
    .footer__list li a:hover, .footer__social-link:hover { color: white; }
    .dark .footer__list li a, .dark .footer__social-link { color: #9ca3af; /* dark:gray-400 */ }
    .dark .footer__list li a:hover, .dark .footer__social-link:hover { color: #e5e7eb; /* dark:gray-200 */ }
    .footer__contact-icon { margin-right: 0.5rem; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.125rem; }
</style>
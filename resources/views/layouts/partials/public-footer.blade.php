<footer class="bg-gray-800 text-white py-12 px-4">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
        
        {{-- Section Nom du site et petite description/tagline --}}
        <div class="lg:col-span-1">
            <h5 class="text-xl font-bold mb-4">{{ $siteSettings['site_name'] ?? config('app.name', 'CRPQA') }}</h5>
            {{-- Si tu as une tagline ou une courte description dans tes settings, tu peux l'ajouter ici --}}
            {{-- Exemple : <p class="text-gray-400 text-sm">{{ $siteSettings['site_tagline'] ?? '' }}</p> --}}
            <p class="text-gray-400 text-sm">
                Votre centre d'excellence en physique quantique et ses applications.
            </p>
        </div>

        {{-- Section Liens Utiles (à adapter) --}}
        <div>
            <h5 class="text-lg font-semibold mb-4">Liens Rapides</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('public.home') }}" class="text-gray-400 hover:text-white">Accueil</a></li>
                <li><a href="{{-- route('public.about') --}}" class="text-gray-400 hover:text-white">À Propos</a></li> {{-- Décommente et ajuste le nom de la route --}}
                <li><a href="{{-- route('public.news.index') --}}" class="text-gray-400 hover:text-white">Actualités</a></li> {{-- Décommente et ajuste --}}
                <li><a href="{{-- route('public.events.index') --}}" class="text-gray-400 hover:text-white">Événements</a></li> {{-- Décommente et ajuste --}}
                <li><a href="{{-- route('public.contact') --}}" class="text-gray-400 hover:text-white">Contact</a></li> {{-- Décommente et ajuste --}}
            </ul>
        </div>

        {{-- Section Contact --}}
        <div>
            <h5 class="text-lg font-semibold mb-4">Contactez-nous</h5>
            <ul class="space-y-3 text-sm">
                @if(isset($siteSettings['address']) && $siteSettings['address'])
                    <li class="text-gray-400 flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        <span>{{ $siteSettings['address'] }}</span>
                    </li>
                @endif
                @if(isset($siteSettings['contact_email']) && $siteSettings['contact_email'])
                    <li class="text-gray-400 flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                        <a href="mailto:{{ $siteSettings['contact_email'] }}" class="hover:text-white">{{ $siteSettings['contact_email'] }}</a>
                    </li>
                @endif
                @if(isset($siteSettings['contact_phone']) && $siteSettings['contact_phone'])
                    <li class="text-gray-400 flex items-center">
                         <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                        <span>{{ $siteSettings['contact_phone'] }}</span>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Section Réseaux Sociaux --}}
        <div class="md:col-span-3 lg:col-span-1"> {{-- Pour que cette section prenne toute la largeur sur mobile/tablette et soit dans la grille sur desktop --}}
            <h5 class="text-lg font-semibold mb-4">Suivez-nous</h5>
            <div class="flex space-x-4">
                @if(isset($siteSettings['facebook_url']) && $siteSettings['facebook_url'])
                    <a href="{{ $siteSettings['facebook_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Facebook">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                @endif
                @if(isset($siteSettings['twitter_url']) && $siteSettings['twitter_url'])
                    <a href="{{ $siteSettings['twitter_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Twitter">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                    </a>
                @endif
                @if(isset($siteSettings['linkedin_url']) && $siteSettings['linkedin_url'])
                    <a href="{{ $siteSettings['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="LinkedIn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" /></svg>
                    </a>
                @endif
                @if(isset($siteSettings['youtube_url']) && $siteSettings['youtube_url'])
                     <a href="{{ $siteSettings['youtube_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="YouTube">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.78 22 12 22 12s0 3.22-.42 4.814a2.503 2.503 0 0 1-1.768 1.768c-1.594.42-6.812.42-6.812.42s-5.218 0-6.812-.42a2.503 2.503 0 0 1-1.768-1.768C2.002 15.22 2 12 2 12s0-3.22.42-4.814a2.503 2.503 0 0 1 1.768-1.768C5.782 5 11 5 11 5s5.218 0 6.812.418zM9.75 15.5V8.5l6 3.5-6 3.5z" clip-rule="evenodd" /></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Barre de Copyright en bas --}}
    <div class="mt-10 pt-8 border-t border-gray-700 text-center">
        <p class="text-gray-500 text-sm">
            {{ $siteSettings['footer_text'] ?? ('© ' . date('Y') . ' ' . ($siteSettings['site_name'] ?? config('app.name', 'CRPQA')) . '. Tous droits réservés.') }}
        </p>
        {{-- Liens vers les politiques si définis --}}
        <div class="mt-2 space-x-4 text-xs">
            @if(isset($siteSettings['privacy_policy_url']) && $siteSettings['privacy_policy_url'])
                <a href="{{ url($siteSettings['privacy_policy_url']) }}" class="text-gray-500 hover:text-white">Politique de Confidentialité</a>
            @endif
            @if(isset($siteSettings['terms_of_service_url']) && $siteSettings['terms_of_service_url'])
                <a href="{{ url($siteSettings['terms_of_service_url']) }}" class="text-gray-500 hover:text-white">Conditions d'Utilisation</a>
            @endif
             @if(isset($siteSettings['cookie_policy_url']) && $siteSettings['cookie_policy_url'])
                <a href="{{ url($siteSettings['cookie_policy_url']) }}" class="text-gray-400 hover:text-white">Politique de Cookies</a>
            @endif
        </div>
    </div>
</footer>
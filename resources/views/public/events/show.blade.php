@extends('layouts.public')

{{-- $event, $metaTitle, $metaDescription, $ogImage, $relatedEvents sont passés par le contrôleur --}}

@section('title', $metaTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'event') {{-- Type Open Graph spécifique pour un événement --}}
@if($event->start_datetime)
    @section('event_start_time', $event->start_datetime->toIso8601String())
@endif
@if($event->end_datetime)
    @section('event_end_time', $event->end_datetime->toIso8601String())
@endif
{{-- @section('event_location', $event->location) --}}


@push('styles')
<style>
    /* Styles pour la page de détail de l'événement */
    .event-show-hero { padding-top: calc(var(--header-height, 4rem) + 1rem); padding-bottom: 3rem; color: white; position: relative; background-size:cover; background-position: center; }
    .event-show-hero__overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.1) 100%); z-index: 0;}
    .event-show-hero .container { position: relative; z-index: 1; }
    .event-show-hero__title { font-size: clamp(2rem, 5vw, 3rem); font-weight: 700; line-height: 1.2; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.6); }
    .event-show-hero__meta { font-size: 0.9rem; opacity: 0.95; }
    .event-show-hero__meta ion-icon, .event-show-hero__meta svg { vertical-align: middle; margin-right: 0.3rem; font-size:1.1em; }
    
    .event-content-section { padding-top: 2.5rem; padding-bottom: 2.5rem; }
    @media(min-width: 768px){ .event-content-section { padding-top: 3.5rem; padding-bottom: 3.5rem; } }
    .event-cover-image-main { width:100%; max-height: 500px; object-fit: cover; border-radius: var(--radius-lg); margin-bottom: 2rem; box-shadow: var(--shadow-xl); }
    .event-sidebar dt { font-weight: 600; color: var(--title-color); }
    .dark .event-sidebar dt { color: var(--dark-title-color); }
    .event-sidebar dd { margin-bottom: 0.75rem; color: var(--text-color-light); }
    .dark .event-sidebar dd { color: var(--dark-text-color-light); }
    .event-sidebar .button { width: 100%; justify-content: center; }

    /* Style pour le modal d'inscription (simple, peut être amélioré avec Alpine.js ou une librairie de modal) */
    .registration-modal { position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 1rem; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
    .registration-modal.is-open { opacity: 1; visibility: visible; }
    .registration-modal__content { background-color: var(--card-bg-color, white); color: var(--card-text-color); border-radius: var(--radius-lg); padding: 2rem; max-width: 500px; width: 100%; box-shadow: var(--shadow-2xl); position: relative; }
    .dark .registration-modal__content { background-color: var(--dark-card-bg-color, #374151); }
    .registration-modal__close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-color-light); }
    .dark .registration-modal__close { color: var(--dark-text-color-light); }
</style>
@endpush

@section('content')
    <main class="main">
        {{-- Section Hero de l'Événement --}}
        <section class="event-show-hero section--bg"
                 style="background-image: url('{{ $event->cover_image_url ?: ($siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/event_hero_default.jpg')) }}');"
                 data-aos="fade-in">
            <div class="event-show-hero__overlay"></div>
            <div class="container">
                <div class="event-show-hero__data max-w-3xl mx-auto text-center py-8 md:py-12">
                    <h1 class="event-show-hero__title" data-aos="fade-up" data-aos-delay="100">{{ $event->title }}</h1>
                    <div class="event-show-hero__meta text-gray-200 space-y-1 md:space-y-0 md:flex md:justify-center md:gap-x-4 md:gap-y-1" data-aos="fade-up" data-aos-delay="200">
                        @if($event->start_datetime)
                        <span class="inline-flex items-center">
                            <ion-icon name="calendar-outline"></ion-icon>
                            {{ $event->start_datetime->translatedFormat('l d F Y') }}
                        </span>
                        @endif
                        @if($event->start_datetime)
                        <span class="hidden md:inline-flex items-center">&bull;</span>
                        <span class="inline-flex items-center">
                            <ion-icon name="time-outline"></ion-icon>
                            {{ $event->start_datetime->translatedFormat('H:i') }}
                            @if($event->end_datetime && $event->end_datetime->format('H:i') !== $event->start_datetime->format('H:i'))
                                - {{ $event->end_datetime->translatedFormat('H:i') }}
                            @endif
                             ({{ $event->start_datetime->diffForHumans() }})
                        </span>
                        @endif
                        @if($event->location)
                        <span class="hidden md:inline-flex items-center">&bull;</span>
                        <span class="inline-flex items-center">
                            <ion-icon name="location-outline"></ion-icon> {{ $event->location }}
                        </span>
                        @endif
                    </div>
                     <nav aria-label="breadcrumb" class="mt-6" data-aos="fade-up" data-aos-delay="300">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('public.events.index') }}">{{ __('Événements') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">/ {{ Str::limit($event->title, 30) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        {{-- Contenu de l'Événement --}}
        <section class="event-content-section bg-white dark:bg-gray-800">
            <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">
                    <div class="lg:col-span-2">
                        {{-- Image de couverture principale ici si vous ne l'utilisez pas dans le hero ou voulez la répéter --}}
                        {{-- @if($event->cover_image_url)
                            <img src="{{ $event->cover_image_url }}" alt="{{ $event->cover_image_alt ?: $event->title }}" class="event-cover-image-main">
                        @endif --}}

                        @if($event->description)
                        <div class="prose prose-lg lg:prose-xl dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify" data-aos="fade-up">
                            {!! $event->description !!}
                        </div>
                        @endif

                        @if($event->target_audience)
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700" data-aos="fade-up">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{__('Public Cible')}}</h3>
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">{!! nl2br(e($event->target_audience)) !!}</div>
                        </div>
                        @endif

                        {{-- Partage sur les réseaux sociaux --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700" data-aos="fade-up">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{__('Partager cet événement :')}}</h3>
                            <div class="flex space-x-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400" aria-label="Partager sur Facebook"><ion-icon name="logo-facebook" class="text-2xl"></ion-icon></a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($event->title) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-sky-500 dark:text-gray-400 dark:hover:text-sky-400" aria-label="Partager sur Twitter"><ion-icon name="logo-twitter" class="text-2xl"></ion-icon></a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($event->title) }}&summary={{ urlencode(Str::limit(strip_tags($event->description),100)) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-blue-700 dark:text-gray-400 dark:hover:text-blue-500" aria-label="Partager sur LinkedIn"><ion-icon name="logo-linkedin" class="text-2xl"></ion-icon></a>
                                <a href="mailto:?subject={{ urlencode($event->title) }}&body={{ urlencode(__('J\'ai trouvé cet événement intéressant: ') . url()->current()) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" aria-label="Partager par email"><ion-icon name="mail-outline" class="text-2xl"></ion-icon></a>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <aside class="lg:col-span-1 space-y-6" data-aos="fade-left" data-aos-delay="200">
                        <div class="p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{__('Informations Clés')}}</h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex">
                                    <dt class="w-1/3 text-gray-500 dark:text-gray-400">{{__('Date(s)')}}:</dt>
                                    <dd class="w-2/3 text-gray-700 dark:text-gray-200">
                                        {{ $event->start_datetime->translatedFormat('d M Y') }}
                                        @if($event->end_datetime && !$event->start_datetime->isSameDay($event->end_datetime))
                                            - {{ $event->end_datetime->translatedFormat('d M Y') }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-gray-500 dark:text-gray-400">{{__('Heure')}}:</dt>
                                    <dd class="w-2/3 text-gray-700 dark:text-gray-200">
                                        {{ $event->start_datetime->translatedFormat('H:i') }}
                                        @if($event->end_datetime && ($event->end_datetime->format('H:i') !== $event->start_datetime->format('H:i') || !$event->start_datetime->isSameDay($event->end_datetime) ))
                                         à {{ $event->end_datetime->translatedFormat('H:i') }}
                                        @endif
                                    </dd>
                                </div>
                                @if($event->location)
                                <div class="flex">
                                    <dt class="w-1/3 text-gray-500 dark:text-gray-400">{{__('Lieu')}}:</dt>
                                    <dd class="w-2/3 text-gray-700 dark:text-gray-200">{{ $event->location }}</dd>
                                </div>
                                @endif
                                {{-- Ajoutez d'autres infos si nécessaire: organisateur, type d'événement etc. --}}
                            </dl>

                            @if($event->registration_url)
                                <a href="{{ $event->registration_url }}" target="_blank" rel="noopener noreferrer" class="button button--primary mt-6 w-full text-center">
                                    {{__('S\'inscrire via lien externe')}} <ion-icon name="open-outline" class="ml-2"></ion-icon>
                                </a>
                            @elseif($event->start_datetime >= now()) {{-- Afficher le bouton d'inscription interne si l'événement n'est pas passé --}}
                                <button type="button" @click="$dispatch('open-modal', 'register-event-{{$event->id}}')" class="button button--primary mt-6 w-full text-center">
                                   <ion-icon name="ticket-outline" class="mr-2"></ion-icon> {{__('S\'inscrire à cet Événement')}}
                                </button>
                            @else
                                <p class="mt-6 text-sm text-center text-gray-500 dark:text-gray-400">{{__('Les inscriptions sont terminées ou cet événement est passé.')}}</p>
                            @endif
                        </div>

                        @if($event->partners->isNotEmpty())
                        <div class="p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{__('Partenaires de l\'Événement')}}</h3>
                            <div class="flex flex-wrap gap-4">
                                @foreach($event->partners as $partner)
                                <a href="{{ $partner->website_url ?: '#' }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="block">
                                    @if($partner->logo_thumbnail_url)
                                        <img src="{{ $partner->logo_thumbnail_url }}" alt="{{ $partner->name }}" class="h-12 max-w-[100px] object-contain grayscale hover:grayscale-0 transition-all duration-300">
                                    @else
                                        <span class="text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600">{{ $partner->name }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        {{-- Galerie d'images pour l'événement (si vous avez une collection 'event_gallery') --}}
                        @if($event->getMedia('event_gallery')->count() > 0)
                        <div class="p-6 bg-slate-50 dark:bg-gray-700/50 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{__('Galerie Photos')}}</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($event->getMedia('event_gallery') as $image)
                                    <a href="{{ $image->getUrl() }}" data-fslightbox="event-gallery-{{$event->id}}" class="block rounded overflow-hidden aspect-square">
                                        <img src="{{ $image->getUrl('thumbnail') }}" alt="{{ $image->name ?: __('Image de la galerie')}}" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </aside>
                </div>
            </div>
        </section>

        {{-- Modal d'Inscription (Utilisant Alpine.js pour un exemple simple) --}}
        @if($event->start_datetime >= now() && empty($event->registration_url))
        <div x-data="{ open: false, eventId: null }" 
             @open-modal.window="if ($event.detail === 'register-event-{{$event->id}}') open = true"
             x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="registration-modal"
             x-cloak
             aria-labelledby="modal-title-{{$event->id}}" role="dialog" aria-modal="true">
            <div class="registration-modal__content" @click.away="open = false" x-show="open" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <button @click="open = false" class="registration-modal__close" aria-label="{{__('Fermer')}}">
                    <ion-icon name="close-outline"></ion-icon>
                </button>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1" id="modal-title-{{$event->id}}">{{__('Inscription à l\'événement')}}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $event->title }}</p>

                @if(session('success_modal_event_id') == $event->id)
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error_modal_event_id') == $event->id)
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                @endif
                 @if(session('warning') && session('error_modal_event_id') == $event->id) {{-- Spécifique pour l'avertissement d'inscription existante --}}
                    <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800" role="alert">
                        {{ session('warning') }}
                    </div>
                @endif

                <form action="{{ route('public.events.register', $event->slug) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name-{{$event->id}}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Nom complet')}} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name-{{$event->id}}" value="{{ old('name', auth()->user()?->name) }}" required class="mt-1 block w-full form-input">
                        @if(session('error_modal_event_id') == $event->id && $errors->has('name')) <p class="text-red-500 text-xs mt-1">{{ $errors->first('name') }}</p> @endif
                    </div>
                    <div>
                        <label for="email-{{$event->id}}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Adresse Email')}} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email-{{$event->id}}" value="{{ old('email', auth()->user()?->email) }}" required class="mt-1 block w-full form-input">
                        @if(session('error_modal_event_id') == $event->id && $errors->has('email')) <p class="text-red-500 text-xs mt-1">{{ $errors->first('email') }}</p> @endif
                    </div>
                    <div>
                        <label for="phone_number-{{$event->id}}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Numéro de téléphone (Optionnel)')}}</label>
                        <input type="tel" name="phone_number" id="phone_number-{{$event->id}}" value="{{ old('phone_number') }}" class="mt-1 block w-full form-input">
                        @if(session('error_modal_event_id') == $event->id && $errors->has('phone_number')) <p class="text-red-500 text-xs mt-1">{{ $errors->first('phone_number') }}</p> @endif
                    </div>
                    <div>
                        <label for="organization-{{$event->id}}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Organisation (Optionnel)')}}</label>
                        <input type="text" name="organization" id="organization-{{$event->id}}" value="{{ old('organization') }}" class="mt-1 block w-full form-input">
                        @if(session('error_modal_event_id') == $event->id && $errors->has('organization')) <p class="text-red-500 text-xs mt-1">{{ $errors->first('organization') }}</p> @endif
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" @click="open = false" class="button button--outline-gray mr-2">{{__('Annuler')}}</button>
                        <button type="submit" class="button button--primary">{{__('Confirmer l\'inscription')}}</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Actualités Similaires --}}
        @if(isset($relatedEvents) && $relatedEvents->count() > 0)
        <section class="section related-news-section bg-slate-50 dark:bg-gray-800/50" id="related-events">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section__title text-center" data-aos="fade-up">{{ __('Autres Événements à Venir') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($relatedEvents as $index => $relatedItem)
                    <article class="section-card event__card group" data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                         <a href="{{ route('public.events.show', $relatedItem->slug) }}" class="section-card__image-link block h-48 overflow-hidden rounded-t-lg" aria-label="{{ __('Voir l\'événement:') }} {{ $relatedItem->title }}">
                            <img src="{{ $relatedItem->cover_image_thumbnail_url ?? asset('assets/images/placeholders/event_default_'.($index%2+1).'.jpg') }}"
                                 alt="{{ $relatedItem->cover_image_alt_text ?? $relatedItem->title }}"
                                 class="section-card__image group-hover:scale-105 transition-transform duration-300">
                        </a>
                        <div class="section-card__content">
                            <p class="section-card__meta event__meta">
                                @if($relatedItem->start_datetime)
                                <time datetime="{{ $relatedItem->start_datetime->toDateString() }}">{{ $relatedItem->start_datetime->translatedFormat('d M Y') }}</time>
                                @endif
                            </p>
                            <h3 class="section-card__title text-md event__title">
                                <a href="{{ route('public.events.show', $relatedItem->slug) }}">
                                    {{ Str::limit($relatedItem->title, 55) }}
                                </a>
                            </h3>
                             <a href="{{ route('public.events.show', $relatedItem->slug) }}" class="section-card__link event__link text-sm mt-auto">
                                {{ __('Voir les détails') }} <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fslightbox@3.4.1/index.min.js"></script>
{{-- Script pour ouvrir le modal d'inscription si une erreur s'est produite ou après un succès --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error_modal_event_id') == $event->id || session('success_modal_event_id') == $event->id)
        const eventId = {{ $event->id }};
        const modalEvent = new CustomEvent('open-modal', { detail: 'register-event-' + eventId });
        window.dispatchEvent(modalEvent);
    @endif
});
</script>
@endpush
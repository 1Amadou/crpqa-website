@extends('layouts.public')

@section('title', __('Contactez-Nous') . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Contactez le CRPQA pour toute question, collaboration ou information. Nous sommes à votre écoute.'))
@section('og_title', __('Contactez-Nous') . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('og_description', __('Contactez le CRPQA pour toute question, collaboration ou information.'))
@if($siteSettings->default_og_image_url)
    @section('og_image', $siteSettings->default_og_image_url)
@endif

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
<style>
    .contact-hero { padding-top: calc(var(--header-height, 4rem) + 2rem); padding-bottom: 3rem; text-align: center; background-color: var(--bg-color-light, #f9fafb); }
    .dark .contact-hero { background-color: var(--dark-bg-color-alt, #1f2937); }
    .contact-hero__title { font-size: clamp(2rem, 5vw, 3rem); font-weight: 700; margin-bottom: 0.5rem; color: var(--title-color); }
    .dark .contact-hero__title { color: var(--dark-title-color); }
    .contact-hero__subtitle { font-size: clamp(1rem, 2.5vw, 1.125rem); color: var(--text-color-light); margin-bottom: 1.5rem; }
    .dark .contact-hero__subtitle { color: var(--dark-text-color-light); }

    .contact-info-item { display: flex; align-items: flex-start; margin-bottom: 1rem; }
    .contact-info-item ion-icon, .contact-info-item svg { font-size: 1.5rem; color: rgb(var(--color-primary)); margin-right: 1rem; flex-shrink: 0; margin-top:0.125rem; }
    .contact-form-section { padding-bottom: 4rem; } /* Plus de padding en bas */
    .map-container { border-radius: var(--radius-lg); overflow:hidden; box-shadow: var(--shadow-lg); aspect-ratio: 16/9; max-height: 450px;}
    .map-container iframe { border:0; width:100%; height:100%; }
</style>
{{-- Si vous utilisez reCAPTCHA v2 invisible, vous aurez besoin de JS. Pour reCAPTCHA v3, c'est différent. --}}
{{-- Pour reCAPTCHA v2 checkbox, le script est chargé par le package. --}}
@if(config('services.recaptcha.key'))
    {!! RecaptchaV3::initJs() !!} {{-- Ou RecaptchaV2::initJs() si vous utilisez v2 --}}
@endif
@endpush

@section('content')
    <main class="main">
        <section class="contact-hero section--bg" data-aos="fade-in">
            <div class="container">
                <h1 class="contact-hero__title" data-aos="fade-up">{{ __('Contactez-Nous') }}</h1>
                <p class="contact-hero__subtitle" data-aos="fade-up" data-aos-delay="100">
                    {{ __('Nous sommes à votre écoute. N\'hésitez pas à nous contacter pour toute question ou collaboration.') }}
                </p>
                <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="200">
                    <ol class="breadcrumb !text-gray-600 dark:!text-gray-400">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="!text-gray-600 dark:!text-gray-400 hover:!text-primary-500">{{ __('Accueil') }}</a></li>
                        <li class="breadcrumb-item active !text-gray-500 dark:!text-gray-500" aria-current="page">/ {{ __('Contact') }}</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="contact-form-section page-section">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-start">
                    {{-- Colonne Formulaire --}}
                    <div data-aos="fade-right">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">{{ __('Envoyez-nous un message') }}</h2>
                        
                        @if(session('success'))
                            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                             <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($errors->any() && !session('success'))
                            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                                <p class="font-medium">{{ __('Veuillez corriger les erreurs ci-dessous :') }}</p>
                                <ul class="mt-1.5 list-disc list-inside">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('public.contact.submit') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Nom complet')}} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Adresse Email')}} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Sujet')}} <span class="text-red-500">*</span></label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Votre Message')}} <span class="text-red-500">*</span></label>
                                <textarea name="message" id="message" rows="5" required class="mt-1 block w-full form-textarea">{{ old('message') }}</textarea>
                            </div>
                            @if(config('services.recaptcha.key'))
                                <div class="form-group">
                                    {!! RecaptchaV3::field('contact') !!} {{-- Ou RecaptchaV2::display() si v2 checkbox --}}
                                     @error('g-recaptcha-response') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                            <div>
                                <button type="submit" class="button button--primary button--lg w-full sm:w-auto">
                                    <ion-icon name="send-outline" class="mr-2"></ion-icon> {{__('Envoyer le Message')}}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Colonne Informations de Contact et Carte --}}
                    <div data-aos="fade-left" data-aos-delay="100">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">{{ __('Nos Coordonnées') }}</h2>
                        <div class="space-y-4 text-gray-700 dark:text-gray-300">
                            @if($siteSettings->getTranslation('address', $currentLocale, false))
                            <div class="contact-info-item">
                                <ion-icon name="location-outline"></ion-icon>
                                <p>{!! nl2br(e($siteSettings->getTranslation('address', $currentLocale, false))) !!}</p>
                            </div>
                            @endif
                            @if($siteSettings->contact_phone)
                            <div class="contact-info-item">
                                <ion-icon name="call-outline"></ion-icon>
                                <p><a href="tel:{{ str_replace(' ', '', $siteSettings->contact_phone) }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $siteSettings->contact_phone }}</a></p>
                            </div>
                            @endif
                            @if($siteSettings->contact_email)
                            <div class="contact-info-item">
                                <ion-icon name="mail-outline"></ion-icon>
                                <p><a href="mailto:{{ $siteSettings->contact_email }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $siteSettings->contact_email }}</a></p>
                            </div>
                            @endif
                        </div>

                        @if($siteSettings->maps_url)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-3">{{__('Retrouvez-nous sur la carte')}}</h3>
                            <div class="map-container">
                                <iframe src="{{ $siteSettings->maps_url }}" 
                                        style="border:0;" allowfullscreen="" loading="lazy" 
                                        referrerpolicy="no-referrer-when-downgrade"
                                        title="{{__('Carte de localisation du CRPQA')}}"></iframe>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
{{-- Scripts spécifiques pour la page contact, si besoin --}}
@endpush
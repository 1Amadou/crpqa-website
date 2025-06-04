@extends('layouts.public')

@section('title', $pageTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', __('Découvrez les organisations et institutions qui collaborent avec le CRPQA.'))

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
<style>
    .partner-card { background-color: var(--card-bg-color, white); color: var(--card-text-color); border-radius: var(--radius-lg, 0.75rem); box-shadow: var(--shadow-md); transition: all 0.3s ease-in-out; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; aspect-ratio: 4 / 3; /* Pour un look plus uniforme */ }
    .dark .partner-card { background-color: var(--dark-card-bg-color, #374151); }
    .partner-card:hover { box-shadow: var(--shadow-xl); transform: translateY(-5px); }
    .partner-card__logo-link { display: block; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .partner-card__logo { max-height: 80px; /* Ajustez selon la taille souhaitée */ max-width: 180px; object-fit: contain; transition: transform 0.3s ease; }
    .partner-card:hover .partner-card__logo { transform: scale(1.05); }
    .partner-card__name { margin-top: 0.75rem; font-size: 0.875rem; font-weight: 500; color: var(--title-color); text-align: center; }
    .dark .partner-card__name { color: var(--dark-title-color); }
</style>
@endpush

@section('content')
<div class="bg-slate-50 dark:bg-gray-900">
    <section class="page-hero-section section--bg py-12 md:py-20 text-center" 
             style="background-image: linear-gradient(rgba(var(--color-primary-dark-rgb,10,42,77),0.75), rgba(var(--color-secondary-dark-rgb,29,44,90),0.85)), url({{ $siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/partners_hero_default.jpg') }});">
        <div class="container">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight" data-aos="fade-up">
                {{ $pageTitle }}
            </h1>
             <nav aria-label="breadcrumb" class="mt-3" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">/ {{ __('Partenaires') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section partners-index-section">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('public.partners.index') }}" method="GET" class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md" data-aos="fade-up">
                <div class="grid sm:grid-cols-2 md:grid-cols-[1fr,1fr,auto] gap-4 items-end">
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Rechercher un partenaire')}}</label>
                        <input type="text" name="search" id="search" value="{{ $searchTerm ?? '' }}" placeholder="{{__('Nom, type...')}}" class="mt-1 block w-full form-input-sm">
                    </div>
                    @if(isset($partnerTypes) && $partnerTypes->count() > 0)
                    <div>
                        <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{__('Type de partenaire')}}</label>
                        <select name="type" id="type" class="mt-1 block w-full form-select-sm">
                            <option value="">{{__('Tous les types')}}</option>
                            @foreach($partnerTypes as $typeKey => $typeName)
                                <option value="{{ $typeKey }}" {{ ($typeFilter == $typeKey) ? 'selected' : '' }}>
                                    {{ $typeName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex space-x-2">
                        <button type="submit" class="button button--primary w-full sm:w-auto justify-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2"/> {{__('Filtrer')}}
                        </button>
                         @if($searchTerm || $typeFilter)
                            <a href="{{ route('public.partners.index') }}" class="button button--outline w-full sm:w-auto justify-center" title="{{__('Réinitialiser')}}">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($partners->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                    @foreach($partners as $index => $partnerItem)
                    <article class="partner-card" data-aos="fade-up" data-aos-delay="{{ ($index % 5 * 50) }}">
                        <a href="{{ $partnerItem->website_url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="partner-card__logo-link" title="{{ $partnerItem->name }}">
                            @if($partnerItem->logo_thumbnail_url)
                                <img src="{{ $partnerItem->logo_thumbnail_url }}" 
                                     alt="{{ $partnerItem->getTranslation('logo_alt_text', $currentLocale, false) ?: $partnerItem->name }}"
                                     class="partner-card__logo">
                            @else
                                {{-- Fallback si pas de logo, afficher le nom --}}
                                <span class="partner-card__name">{{ $partnerItem->name }}</span>
                            @endif
                        </a>
                        {{-- Optionnel: afficher le nom sous le logo même s'il y a une image --}}
                        {{-- @if($partnerItem->logo_thumbnail_url) 
                        <h3 class="partner-card__name">
                            <a href="{{ $partnerItem->website_url ?: '#' }}" target="_blank" rel="noopener noreferrer">{{ $partnerItem->name }}</a>
                        </h3>
                        @endif --}}
                    </article>
                    @endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $partners->links() }}
                </div>
            @else
                <div class="text-center py-12" data-aos="fade-up">
                    <x-heroicon-o-building-office-2 class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">
                         @if($searchTerm || $typeFilter)
                            {{ __('Aucun partenaire trouvé correspondant à vos critères.') }}
                        @else
                            {{ __('Aucun partenaire à afficher pour le moment.') }}
                        @endif
                    </h3>
                    @if($searchTerm || $typeFilter)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('public.partners.index') }}" class="text-primary-600 hover:underline">{{__('Voir tous les partenaires')}}</a>
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
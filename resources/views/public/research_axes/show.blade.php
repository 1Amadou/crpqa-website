@extends('layouts.public')

@section('title', $metaTitle . ' - ' . ($siteSettings->site_name_short ?: $siteSettings->site_name ?: config('app.name')))
@section('meta_description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@if($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'article') {{-- Ou un type plus spécifique si pertinent --}}

@php $currentLocale = app()->getLocale(); @endphp

@push('styles')
<style>
    .ra-show-hero { padding-top: calc(var(--header-height, 4rem) + 1rem); padding-bottom: 3rem; color: white; position: relative; background-size:cover; background-position: center; }
    .ra-show-hero__overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(var(--color-primary-dark-rgb,10,42,77),0.85) 0%, rgba(var(--color-primary-dark-rgb,10,42,77),0.6) 50%, rgba(var(--color-primary-dark-rgb,10,42,77),0.2) 100%); z-index: 0;}
    .ra-show-hero .container { position: relative; z-index: 1; }
    .ra-show-hero__icon-wrapper { width: 4rem; height: 4rem; md:width: 5rem; md:height: 5rem; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-lg); margin-bottom: 1rem; padding: 0.75rem; box-shadow: var(--shadow-lg); }
    .ra-show-hero__icon-wrapper > svg, .ra-show-hero__icon-wrapper > i { width: 100%; height: 100%; }
    .ra-show-hero__title { font-size: clamp(2rem, 5vw, 3rem); font-weight: 700; line-height: 1.2; margin-bottom: 0.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
    .ra-show-hero__subtitle { font-size: clamp(1.1rem, 3vw, 1.5rem); font-weight: 300; opacity: 0.9; margin-bottom: 1.5rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .content-section { padding-top: 2.5rem; padding-bottom: 2.5rem; }
    @media(min-width: 768px){ .content-section { padding-top: 3.5rem; padding-bottom: 3.5rem; } }
</style>
@endpush

@section('content')
    <main class="main">
        <section class="ra-show-hero section--bg"
                 style="background-image: url('{{ $researchAxis->cover_image_url ?: ($siteSettings->default_og_image_url ?: asset('assets/images/backgrounds/research_hero_default.jpg')) }}');"
                 data-aos="fade-in">
            <div class="ra-show-hero__overlay"></div>
            <div class="container">
                <div class="ra-show-hero__data max-w-3xl mx-auto text-center py-8 md:py-10">
                    @if($researchAxis->getTranslation('icon_svg', $currentLocale, false) || $researchAxis->icon_class)
                        <div class="ra-show-hero__icon-wrapper inline-flex mx-auto"
                             style="background-color: {{ $researchAxis->color_hex ? \App\Helpers\ColorHelper::hexToRgba($researchAxis->color_hex, 0.2) : 'rgba(255,255,255,0.1)' }}; 
                                    color: {{ $researchAxis->color_hex ?: 'white' }}; border: 1px solid {{ $researchAxis->color_hex ? \App\Helpers\ColorHelper::hexToRgba($researchAxis->color_hex, 0.5) : 'rgba(255,255,255,0.2)' }};"
                             data-aos="zoom-in" data-aos-delay="50">
                            @if($researchAxis->getTranslation('icon_svg', $currentLocale, false))
                                {!! $researchAxis->getTranslation('icon_svg', $currentLocale, false) !!}
                            @elseif($researchAxis->icon_class)
                                <i class="{{ $researchAxis->icon_class }} text-3xl md:text-4xl"></i>
                            @endif
                        </div>
                    @endif
                    <h1 class="ra-show-hero__title" data-aos="fade-up" data-aos-delay="100">{{ $researchAxis->name }}</h1>
                    @if($researchAxis->subtitle)
                    <p class="ra-show-hero__subtitle" data-aos="fade-up" data-aos-delay="200">
                        {{ $researchAxis->subtitle }}
                    </p>
                    @endif
                     <nav aria-label="breadcrumb" class="mt-4" data-aos="fade-up" data-aos-delay="300">
                        <ol class="breadcrumb text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('public.home') }}">{{ __('Accueil') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('public.research_axes.index') }}">{{ __('Axes de Recherche') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">/ {{ Str::limit($researchAxis->name, 30) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>

        <section class="content-section bg-white dark:bg-gray-800">
            <div class="container max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($researchAxis->description)
                <div class="prose prose-lg lg:prose-xl dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 content-styles text-justify" data-aos="fade-up">
                    {!! $researchAxis->description !!}
                </div>
                @endif

                {{-- Optionnel: Lister les chercheurs ou projets liés à cet axe --}}
                {{-- @if($researchAxis->researchers && $researchAxis->researchers->count() > 0)
                <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700" data-aos="fade-up">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{__('Chercheurs Associés')}}</h2>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($researchAxis->researchers as $researcher)
                            <li><a href="{{ route('public.researchers.show', $researcher->slug) }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $researcher->full_name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif --}}
            </div>
        </section>

        {{-- Axes de Recherche Similaires --}}
        @if(isset($relatedAxes) && $relatedAxes->count() > 0)
        <section class="section related-axes-section bg-slate-50 dark:bg-gray-800/50" id="related-axes">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section__title text-center" data-aos="fade-up">{{ __('Autres Axes de Recherche') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($relatedAxes as $index => $axis)
                    <a href="{{ route('public.research_axes.show', $axis->slug) }}" 
                       class="research__card block p-6 text-center group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 ease-in-out" 
                       data-aos="fade-up" data-aos-delay="{{ ($index * 100) }}">
                        <div class="research__card-icon-wrapper inline-flex items-center justify-center mx-auto"
                             style="background-color: {{ $axis->color_hex ? \App\Helpers\ColorHelper::hexToRgba($axis->color_hex, 0.15) : 'rgba(var(--color-primary-rgb), 0.1)' }}; 
                                    color: {{ $axis->color_hex ?: 'rgb(var(--color-primary))' }};">
                            @if($axis->getTranslation('icon_svg', $currentLocale, false))
                                {!! $axis->getTranslation('icon_svg', $currentLocale, false) !!}
                            @elseif($axis->icon_class)
                                <i class="{{ $axis->icon_class }} text-3xl"></i>
                            @else
                                 <x-heroicon-o-academic-cap class="w-8 h-8"/>
                            @endif
                        </div>
                        <h3 class="research__card-title mt-2">
                            {{ $axis->name }}
                        </h3>
                        @if($axis->subtitle)
                        <p class="research__card-description text-sm text-gray-600 dark:text-gray-400">
                            {{ Str::limit(strip_tags($axis->subtitle), 60) }}
                        </p>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection

@push('scripts')
{{-- Si vous avez des scripts spécifiques --}}
@endpush
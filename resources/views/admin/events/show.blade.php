@extends('layouts.admin')

@php
    $primaryLocale = $availableLocales[0] ?? app()->getLocale() ?? config('app.fallback_locale', 'fr');
@endphp

@section('title', __('Détails de l\'Événement') . ': ' . $event->getTranslation('title', $primaryLocale, false))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Détails de l\'Événement') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($event->getTranslation('title', $primaryLocale, false), 70) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('manage event_registrations')
            <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded-md hover:bg-cyan-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-users class="h-4 w-4 mr-1.5"/>
                {{ __('Inscriptions') }} ({{ $event->registrations_count ?? $event->registrations()->count() }})
            </a>
            @endcan
            @can('manage events')
            <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-pencil-square class="h-4 w-4 mr-1.5"/>
                {{ __('Modifier') }}
            </a>
            @endcan
            <a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline ml-2">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 md:p-8">

        @if($event->cover_image_url) {{-- Utilisation de l'accesseur du modèle --}}
            <div class="mb-6 rounded-lg overflow-hidden shadow-lg max-h-96 flex justify-center bg-gray-100 dark:bg-gray-700">
                <img src="{{ $event->cover_image_url }}" 
                     alt="{{ $event->getTranslation('cover_image_alt', $primaryLocale, false) ?: $event->getTranslation('title', $primaryLocale, false) }}"
                     class="w-auto h-full max-h-96 object-contain">
            </div>
        @endif

        {{-- Système d'onglets pour la localisation --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="languageTabsEventShow" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-500 dark:border-primary-500 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                id="tab-event-show-{{ $locale }}"
                                data-tabs-target="#content-event-show-{{ $locale }}"
                                type="button" role="tab" aria-controls="content-event-show-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ strtoupper($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="languageTabContentEventShow">
            @foreach($availableLocales as $locale)
                <div class="{{ $loop->first ? '' : 'hidden' }} p-1 mb-6" id="content-event-show-{{ $locale }}" role="tabpanel" aria-labelledby="tab-event-show-{{ $locale }}">
                    
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1 break-words leading-tight">
                        {{ $event->getTranslation('title', $locale, false) }}
                    </h3>

                    @if($event->getTranslation('location', $locale, false))
                        <p class="text-md text-gray-600 dark:text-gray-400 mb-4">
                            <x-heroicon-o-map-pin class="h-5 w-5 inline-block mr-1 align-text-bottom"/>
                            {{ $event->getTranslation('location', $locale, false) }}
                        </p>
                    @endif
                    
                    @if($event->getTranslation('description', $locale, false))
                        <div class="mt-4">
                            <h4 class="sr-only">{{ __('Description') }}</h4>
                            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-100 content-styles">
                                {!! $event->getTranslation('description', $locale, false) !!} {{-- Si HTML de WYSIWYG --}}
                            </div>
                        </div>
                    @endif

                    @if($event->getTranslation('target_audience', $locale, false))
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Public Cible') }}:</h4>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-200">{!! nl2br(e($event->getTranslation('target_audience', $locale, false))) !!}</div>
                    </div>
                    @endif

                    @if($event->getTranslation('meta_title', $locale, false) && $event->getTranslation('meta_title', $locale, false) !== $event->getTranslation('title', $locale, false))
                        <div class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Titre (SEO)') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $event->getTranslation('meta_title', $locale, false) }}</p>
                        </div>
                    @endif

                    @if($event->getTranslation('meta_description', $locale, false))
                        <div class="mt-2 pt-2 @if(!($event->getTranslation('meta_title', $locale, false) && $event->getTranslation('meta_title', $locale, false) !== $event->getTranslation('title', $locale, false))) border-t border-gray-100 dark:border-gray-700 @endif">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Meta Description (SEO)') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $event->getTranslation('meta_description', $locale, false) }}</p>
                        </div>
                    @endif

                    @if($event->cover_image_url && $event->getTranslation('cover_image_alt', $locale, false))
                        <div class="mt-2 pt-2 @if(!($event->getTranslation('meta_description', $locale, false))) border-t border-gray-100 dark:border-gray-700 @endif">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Texte Alternatif de l\'Image de Couverture') }}:</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $event->getTranslation('cover_image_alt', $locale, false) }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">{{__('Informations Complémentaires')}}</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Dates') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">
                        {{ $event->start_datetime->translatedFormat('d M Y, H:i') }}
                        @if($event->end_datetime)
                            - {{ $event->end_datetime->translatedFormat('d M Y, H:i') }}
                        @endif
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Organisateur') }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $event->createdBy->name ?? __('N/A') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Statut') }}:</dt>
                    <dd>
                        @php /* Logique de statut copiée de index.blade.php, peut être mise dans un accesseur sur le modèle Event */
                            $now = now(); $start_datetime = $event->start_datetime; $end_datetime = $event->end_datetime;
                            $status_text = ''; $status_class = '';
                            if ($start_datetime->isFuture()) { $status_text = __('À venir'); $status_class = 'bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100'; }
                            elseif ($end_datetime && $end_datetime->isPast()) { $status_text = __('Passé'); $status_class = 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200'; }
                            elseif ($start_datetime->isPast() && (!$end_datetime || $end_datetime->isFuture() || $end_datetime->isToday())) { $status_text = __('En cours'); $status_class = 'bg-amber-100 text-amber-800 dark:bg-amber-600 dark:text-amber-100'; }
                            else { $status_text = __('Actif'); $status_class = 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100'; if ($end_datetime && $end_datetime->isPast()){ $status_text = __('Passé'); $status_class = 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200'; }}
                        @endphp
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status_class }}">
                            {{ $status_text }}
                        </span>
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('En Vedette') }}:</dt>
                    <dd class="{{ $event->is_featured ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-700 dark:text-gray-200' }}">
                        {{ $event->is_featured ? __('Oui') : __('Non') }}
                    </dd>
                </div>
                @if($event->registration_url)
                <div class="flex flex-col md:col-span-2">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">{{ __('Lien d\'inscription') }}:</dt>
                    <dd class="text-primary-600 dark:text-primary-400 hover:underline break-all"><a href="{{ $event->registration_url }}" target="_blank" rel="noopener noreferrer">{{ $event->registration_url }}</a></dd>
                </div>
                @endif
                <div class="flex flex-col">
                    <dt class="font-semibold text-gray-600 dark:text-gray-300">Slug :</dt>
                    <dd class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs text-gray-700 dark:text-gray-200 inline-block">{{ $event->slug }}</dd>
                </div>
            </dl>

            @if($event->partners->isNotEmpty())
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Partenaires Associés') }}</h4>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300">
                        @foreach($event->partners as $partner)
                            <li>
                                @can('view partners') {{-- Supposant une permission pour voir les partenaires --}}
                                <a href="{{ route('admin.partners.show', $partner) }}" class="hover:text-primary-600 dark:hover:text-primary-400 hover:underline">
                                    {{ $partner->name }} {{-- Supposant que Partner a un champ 'name' (et potentiellement traduit) --}}
                                </a>
                                @else
                                {{ $partner->name }}
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                <p>{{ __('Événement créé par') }} {{ $event->createdBy->name ?? __('N/A') }} {{ __('le') }} : {{ $event->created_at->translatedFormat('d F Y à H:i') }}</p>
                <p>{{ __('Dernière mise à jour') }} : {{ $event->updated_at->translatedFormat('d F Y à H:i') }}</p>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 flex flex-wrap justify-start gap-3">
            @can('manage events')
            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cet événement :name ? Ceci est irréversible et supprimera aussi les inscriptions liées.', ['name' => addslashes($event->getTranslation('title', $primaryLocale, false))]) }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                    <x-heroicon-o-trash class="h-4 w-4 mr-1.5"/>
                    {{ __('Supprimer cet Événement') }}
                </button>
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Réutilisation du script d'onglets --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtonsEventShow = document.querySelectorAll('#languageTabsEventShow button');
    const tabContentsEventShow = document.querySelectorAll('#languageTabContentEventShow > div');

    tabButtonsEventShow.forEach((button) => {
        button.addEventListener('click', () => {
            tabButtonsEventShow.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContentsEventShow.forEach(content => {
                content.classList.add('hidden');
            });

            button.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
            button.classList.remove('border-transparent');
            button.setAttribute('aria-selected', 'true');
            const target = document.querySelector(button.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });
    if (tabButtonsEventShow.length > 0 && !document.querySelector('#languageTabsEventShow button.active')) {
         if (tabButtonsEventShow[0]) tabButtonsEventShow[0].click();
    }
});
</script>
@endpush
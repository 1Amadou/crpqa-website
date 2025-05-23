@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Tableau de Bord') }}
    </h2>
@endsection

@section('content')
    {{-- Fond principal du contenu, déjà défini dans le layout parent ou ici pour la page spécifique --}}
    {{-- Assurons-nous que la div principale du contenu a suffisamment de padding --}}
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="space-y-10 md:space-y-12"> {{-- Augmentation de l'espacement vertical principal --}}

            {{-- Message de Bienvenue - Style plus doux --}}
            <div class="bg-sky-600 dark:bg-sky-700 text-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <ion-icon name="happy-outline" class="text-5xl mr-4 text-sky-300 dark:text-sky-400"></ion-icon>
                            <div>
                                <h3 class="text-2xl font-bold">{{ $welcomeMessage ?? "Bienvenue, ". (Auth::user()->name ?? 'Admin') ." !" }}</h3>
                                <p class="mt-1 text-sm text-sky-100 dark:text-sky-200 max-w-2xl">
                                    C'est un plaisir de vous revoir. Voici un aperçu de l'activité récente sur le site du CRPQA.
                                </p>
                            </div>
                        </div>
                        {{-- Bouton d'action optionnel dans le message de bienvenue --}}
                        {{-- <a href="#" class="shrink-0 px-5 py-2.5 bg-white text-sky-700 font-semibold rounded-lg shadow-md hover:bg-sky-50 transition-colors text-sm self-start sm:self-center">
                            Commencer une tâche
                        </a> --}}
                    </div>
                </div>
            </div>

            {{-- Cartes de Statistiques - Design épuré et cohérent --}}
            <div>
                <h3 class="text-xl lg:text-2xl leading-6 font-semibold text-gray-900 dark:text-gray-100 mb-5">
                    Aperçu Général
                </h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-5"> {{-- Ajustement des colonnes --}}
                    @php
                        $statsCards = [
                            ['title' => 'Chercheurs Actifs', 'value' => $stats['researchers_count'] ?? 0, 'route' => route('admin.researchers.index'), 'icon' => 'people-outline', 'color' => 'sky'],
                            ['title' => 'Publications', 'value' => $stats['publications_count'] ?? 0, 'route' => route('admin.publications.index'), 'icon' => 'book-outline', 'color' => 'sky'],
                            ['title' => 'Actualités Publiées', 'value' => $stats['news_count'] ?? 0, 'route' => route('admin.news.index'), 'icon' => 'newspaper-outline', 'color' => 'sky'],
                            ['title' => 'Événements à Venir', 'value' => $stats['upcoming_events_count'] ?? 0, 'route' => route('admin.events.index'), 'icon' => 'calendar-outline', 'color' => 'sky'],
                            ['title' => 'Inscriptions', 'value' => $stats['total_registrations_count'] ?? 0, 'route' => null, 'icon' => 'clipboard-outline', 'color' => 'sky', 'footer_text' => 'Par événement'],
                        ];
                    @endphp

                    @foreach ($statsCards as $card)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 ease-in-out rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="p-5 md:p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-col space-y-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $card['title'] }}</dt>
                                    <dd class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $card['value'] }}</dd>
                                </div>
                                <div class="flex-shrink-0 bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-800/30 rounded-lg p-3">
                                    <ion-icon name="{{ $card['icon'] }}" class="h-7 w-7 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"></ion-icon>
                                </div>
                            </div>
                        </div>
                        @if($card['route'] || isset($card['footer_text']))
                        <div class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 px-5 py-3">
                            @if($card['route'])
                            <a href="{{ $card['route'] }}" class="text-sm font-medium text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:text-{{ $card['color'] }}-800 dark:hover:text-{{ $card['color'] }}-300 transition-colors inline-flex items-center">
                                Voir tout <ion-icon name="arrow-forward-outline" class="ml-1"></ion-icon>
                            </a>
                            @else
                            <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ $card['footer_text'] }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Raccourcis Rapides - Boutons plus aérés et stylés --}}
            <div class="mt-10">
                <h3 class="text-xl lg:text-2xl leading-6 font-semibold text-gray-900 dark:text-gray-100 mb-5">
                    Actions Rapides
                </h3>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3"> {{-- Moins de colonnes pour plus d'air --}}
                    @php
                        $quickActions = [
                            ['route' => route('admin.news.create'), 'label' => 'Nouvelle Actualité', 'icon' => 'add-circle-outline', 'color' => 'sky'],
                            ['route' => route('admin.events.create'), 'label' => 'Nouvel Événement', 'icon' => 'add-circle-outline', 'color' => 'sky'],
                            ['route' => route('admin.publications.create'), 'label' => 'Nouvelle Publication', 'icon' => 'add-circle-outline', 'color' => 'sky'],
                            // Ajoutez d'autres raccourcis ici
                            // ['route' => route('admin.researchers.create'), 'label' => 'Nouveau Chercheur', 'icon' => 'person-add-outline', 'color' => 'sky'],
                        ];
                    @endphp
                    @foreach ($quickActions as $action)
                    <a href="{{ $action['route'] }}"
                       class="group flex items-center justify-start px-6 py-5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700
                              hover:bg-{{ $action['color'] }}-500 dark:hover:bg-{{ $action['color'] }}-600 hover:text-white dark:hover:text-white
                              transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <ion-icon name="{{ $action['icon'] }}" class="text-2xl mr-3 text-{{ $action['color'] }}-500 group-hover:text-white transition-colors duration-300"></ion-icon>
                        <span>{{ $action['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Contenus Récents - Cartes plus spacieuses et présentation améliorée --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-8 mt-12"> {{-- Moins de colonnes --}}
                @php
                    $recentContentConfig = [
                        [
                            'title' => 'Dernières Actualités',
                            'icon' => 'newspaper-outline',
                            'color' => 'amber', // Changement pour varier un peu, mais subtilement
                            'items' => $latestNews,
                            'route_all' => route('admin.news.index'),
                            'route_item_base' => 'admin.news.show',
                            'date_field' => 'published_at',
                            'title_field' => 'title',
                            'empty_message' => 'Aucune actualité publiée récemment.'
                        ],
                        [
                            'title' => 'Prochains Événements',
                            'icon' => 'calendar-outline',
                            'color' => 'rose',
                            'items' => $upcomingEvents,
                            'route_all' => route('admin.events.index'),
                            'route_item_base' => 'admin.events.show',
                            'date_field' => 'start_datetime',
                            'title_field' => 'title',
                            'location_field' => 'location',
                            'empty_message' => 'Aucun événement à venir.'
                        ],
                        [
                            'title' => 'Dernières Publications',
                            'icon' => 'book-outline',
                            'color' => 'teal',
                            'items' => $latestPublications,
                            'route_all' => route('admin.publications.index'),
                            'route_item_base' => 'admin.publications.show',
                            'date_field' => 'publication_date',
                            'title_field' => 'title',
                            'empty_message' => 'Aucune publication ajoutée récemment.'
                        ]
                    ];
                @endphp

                @foreach ($recentContentConfig as $config)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl hover:shadow-2xl transition-shadow duration-300 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col">
                    <div class="p-6">
                        <div class="flex items-center mb-5">
                            <div class="p-2.5 bg-{{ $config['color'] }}-100 dark:bg-{{ $config['color'] }}-500/20 rounded-lg mr-4">
                                <ion-icon name="{{ $config['icon'] }}" class="text-2xl text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400"></ion-icon>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $config['title'] }}</h3>
                        </div>
                        @if($config['items']->count() > 0)
                            <ul class="space-y-2">
                                @foreach($config['items']->take(5) as $item) {{-- Limiter à 5 items pour ne pas surcharger --}}
                                    <li class="group">
                                        <a href="{{ route($config['route_item_base'], $item) }}"
                                           class="block p-3.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors duration-200">
                                            <p class="text-sm font-semibold text-{{ $config['color'] }}-700 dark:text-{{ $config['color'] }}-400 group-hover:text-{{ $config['color'] }}-800 dark:group-hover:text-{{ $config['color'] }}-300 truncate">
                                                {{ Str::limit($item->{$config['title_field']}, 55) }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($item->{$config['date_field']})->isoFormat('D MMM YYYY') }}
                                                @if(isset($config['location_field']) && $item->{$config['location_field']})
                                                    <span class="mx-1">&bull;</span> {{ Str::limit($item->{$config['location_field']}, 20) }}
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">{{ $config['empty_message'] }}</p>
                        @endif
                    </div>
                    <div class="mt-auto bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 px-6 py-4 text-right">
                        <a href="{{ $config['route_all'] }}" class="text-sm font-medium text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400 hover:text-{{ $config['color'] }}-800 dark:hover:text-{{ $config['color'] }}-300 inline-flex items-center">
                            Voir tout <ion-icon name="arrow-forward-outline" class="ml-1"></ion-icon>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

{{-- Pas besoin de @push('scripts') ici pour l'instant, sauf si vous ajoutez des graphiques ou autres JS interactifs --}}
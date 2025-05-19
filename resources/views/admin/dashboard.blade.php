@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Tableau de Bord') }}
    </h2>
@endsection

@section('content')
    <div class="space-y-8">
        {{-- Message de Bienvenue --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium">{{ $welcomeMessage ?? "Bienvenue sur le panneau d'administration du CRPQA !" }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    D'ici, vous pourrez gérer les différents aspects du site web du centre de recherche.
                </p>
            </div>
        </div>

        {{-- Cartes de Statistiques --}}
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-3">
                Aperçu Général
            </h3>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Chercheurs Actifs</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['researchers_count'] ?? 0 }}</dd>
                    </div>
                     <div class="bg-gray-50 px-5 py-3"><a href="{{ route('admin.researchers.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</a></div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Publications</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['publications_count'] ?? 0 }}</dd>
                    </div>
                    <div class="bg-gray-50 px-5 py-3"><a href="{{ route('admin.publications.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</a></div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Actualités Publiées</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['news_count'] ?? 0 }}</dd>
                    </div>
                    <div class="bg-gray-50 px-5 py-3"><a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</a></div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Événements à Venir</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['upcoming_events_count'] ?? 0 }}</dd>
                    </div>
                     <div class="bg-gray-50 px-5 py-3"><a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</a></div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Inscriptions (Appr./Att.)</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_registrations_count'] ?? 0 }}</dd>
                    </div>
                    {{-- Pas de lien direct vers "toutes les inscriptions", car elles sont par événement --}}
                    <div class="bg-gray-50 px-5 py-3"><span class="text-sm text-gray-400 italic">Par événement</span></div>
                </div>
            </dl>
        </div>

        {{-- Raccourcis Rapides (Optionnel) --}}
        <div class="mt-6">
             <h3 class="text-lg leading-6 font-medium text-gray-900 mb-3">
                Actions Rapides
            </h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 shadow-sm">Ajouter une Actualité</a>
                <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 shadow-sm">Ajouter un Événement</a>
                <a href="{{ route('admin.publications.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 shadow-sm">Ajouter une Publication</a>
                {{-- Ajoutez d'autres raccourcis pertinents --}}
            </div>
        </div>


        {{-- Contenus Récents --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            {{-- Dernières Actualités --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Dernières Actualités Publiées</h3>
                    @if($latestNews->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($latestNews as $newsItem)
                                <li class="py-3">
                                    <a href="{{ route('admin.news.show', $newsItem) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">{{ Str::limit($newsItem->title, 60) }}</a>
                                    <p class="text-xs text-gray-500">{{ $newsItem->published_at->format('d/m/Y') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Aucune actualité publiée récemment.</p>
                    @endif
                </div>
            </div>

            {{-- Prochains Événements --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Prochains Événements</h3>
                     @if($upcomingEvents->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($upcomingEvents as $eventItem)
                                <li class="py-3">
                                    <a href="{{ route('admin.events.show', $eventItem) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">{{ Str::limit($eventItem->title, 60) }}</a>
                                    <p class="text-xs text-gray-500">
                                        {{ $eventItem->start_datetime->format('d/m/Y H:i') }}
                                        @if($eventItem->location) - {{ Str::limit($eventItem->location, 30) }} @endif
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Aucun événement à venir.</p>
                    @endif
                </div>
            </div>

            {{-- Dernières Publications --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Dernières Publications Ajoutées</h3>
                    @if($latestPublications->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($latestPublications as $publicationItem)
                                <li class="py-3">
                                    <a href="{{ route('admin.publications.show', $publicationItem) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">{{ Str::limit($publicationItem->title, 60) }}</a>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($publicationItem->publication_date)->format('d/m/Y') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Aucune publication ajoutée récemment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
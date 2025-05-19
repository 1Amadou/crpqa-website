@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-2">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'événement') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($event->title, 80) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour à la liste') }}
            </a>
            <a href="{{ route('admin.events.registrations.index', $event) }}" class="px-4 py-2 bg-cyan-600 text-white rounded-md hover:bg-cyan-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                {{ __('Gérer les Inscriptions') }} ({{ $event->registrations()->count() }}) {{-- Affiche le nombre d'inscriptions --}}
            </a>
            <a href="{{ route('admin.events.edit', $event) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                {{ __('Modifier cet événement') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                {{-- Colonne Informations Principales --}}
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Titre') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $event->title }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Description') }}</h3>
                        <div class="mt-1 prose max-w-none text-gray-700">
                            {!! nl2br(e($event->description)) !!} {{-- nl2br pour les sauts de ligne, e() pour échapper --}}
                            {{-- Plus tard, si la description est stockée en HTML (via un éditeur riche), ce sera juste {!! $event->description !!} (avec prudence et purification si nécessaire) --}}
                        </div>
                    </div>
                    @if($event->target_audience)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Publics Cibles') }}</h3>
                        <div class="mt-1 prose max-w-none text-gray-700">
                            {!! nl2br(e($event->target_audience)) !!}
                        </div>
                    </div>
                    @endif  

                    @if($event->associatedPartners->isNotEmpty())
<div class="mt-6 pt-6 border-t border-gray-200">
    <h3 class="text-sm font-medium text-gray-500">{{ __('Partenaires Associés') }}</h3>
    <ul class="mt-2 list-disc list-inside text-sm text-gray-700">
        @foreach($event->associatedPartners as $partner)
            <li>
                <a href="{{ route('admin.partners.show', $partner) }}" class="text-blue-600 hover:underline">
                    {{ $partner->name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endif

                    <div class="mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Dates et Horaires') }}</h3>
                        <p class="mt-1 text-md text-gray-700">
                            <strong>Début :</strong> {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y \à H:i') }}
                        </p>
                        @if($event->end_datetime)
                        <p class="mt-1 text-md text-gray-700">
                            <strong>Fin :</strong> {{ \Carbon\Carbon::parse($event->end_datetime)->format('d/m/Y \à H:i') }}
                        </p>
                        @endif
                    </div>

                    @if($event->location)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Lieu') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $event->location }}</p>
                    </div>
                    @endif

                    @if($event->registration_url)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Lien d\'inscription') }}</h3>
                        <p class="mt-1 text-md text-blue-600 hover:text-blue-800">
                            <a href="{{ $event->registration_url }}" target="_blank" rel="noopener noreferrer">{{ $event->registration_url }}</a>
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Colonne Informations Secondaires et Méta --}}
                <div class="md:col-span-1 space-y-6">
                    @if($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('Image de couverture') }}</h3>
                        <img src="{{ Storage::url($event->cover_image_path) }}" alt="Image de couverture pour {{ $event->title }}" class="w-full h-auto object-cover rounded-md shadow-md">
                    </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Statut') }}</h3>
                         @php
                            $now = now();
                            $start_datetime = \Carbon\Carbon::parse($event->start_datetime);
                            $end_datetime = $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime) : null;
                            $status_text = '';
                            $status_class = '';

                            if ($start_datetime->isFuture()) {
                                $status_text = 'À venir';
                                $status_class = 'bg-blue-100 text-blue-800';
                            } elseif ($end_datetime && $end_datetime->isPast()) {
                                $status_text = 'Passé';
                                $status_class = 'bg-gray-100 text-gray-800';
                            } elseif ($start_datetime->isPast() && (!$end_datetime || $end_datetime->isFuture() || $end_datetime->isToday())) {
                                $status_text = 'En cours';
                                $status_class = 'bg-yellow-100 text-yellow-800';
                            } else {
                                $status_text = 'Actif'; // Cas par défaut ou si seulement start_datetime est aujourd'hui et pas d'heure de fin
                                $status_class = 'bg-green-100 text-green-800';
                                if ($end_datetime && $end_datetime->isPast()){
                                     $status_text = 'Passé';
                                     $status_class = 'bg-gray-100 text-gray-800';
                                }
                            }
                        @endphp
                        <p class="mt-1">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $status_class }}">
                                {{ $status_text }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Mise en vedette') }}</h3>
                        <p class="mt-1 text-md text-gray-700">
                            @if($event->is_featured)
                                <span class="text-green-600 font-semibold">Oui</span> (⭐ Apparaît en évidence)
                            @else
                                Non
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Organisateur') }}</h3>
                        <p class="mt-1 text-md text-gray-700">{{ $event->user->name ?? 'Non spécifié' }}</p>
                    </div>

                    <hr class="my-4">

                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Informations SEO') }}</h3>
                    <div>
                        <h4 class="text-md font-medium text-gray-800">{{ __('Méta Titre') }}</h4>
                        <p class="mt-1 text-sm text-gray-600">{{ $event->meta_title ?: '(Non défini - le titre principal sera utilisé)' }}</p>
                    </div>
                    <div class="mt-3">
                        <h4 class="text-md font-medium text-gray-800">{{ __('Méta Description') }}</h4>
                        <p class="mt-1 text-sm text-gray-600">{{ $event->meta_description ?: '(Non définie - un extrait sera généré)' }}</p>
                    </div>
                     <div class="mt-3">
                        <h4 class="text-md font-medium text-gray-800">{{ __('Slug (URL)') }}</h4>
                        <p class="mt-1 text-sm text-gray-600 break-all">{{ $event->slug }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-gray-200 flex flex-wrap justify-start gap-3">
                 <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement : \'{{ addslashes(Str::limit($event->title, 30)) }}\' ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Supprimer cet événement') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inscriptions pour :') }} <span class="text-blue-600">{{ Str::limit($event->title, 50) }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ $registrations->total() }} Inscription(s) au total
                {{-- TODO: Afficher des stats plus détaillées ici (approuvées, en attente, etc.) si $event->loadCount('registrationsApproved', 'registrationsPending') --}}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <div class="flex gap-2">
                {{-- TODO: Ajouter les routes et la logique pour ces exports dans le contrôleur --}}
                <a href="{{ route('admin.events.registrations.export.excel', $event) }}" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs font-medium shadow-sm transition ease-in-out duration-150" title="Exporter en Excel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Excel
                </a>
                <a href="{{ route('admin.events.registrations.export.pdf', $event) }}" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs font-medium shadow-sm transition ease-in-out duration-150" title="Exporter en PDF">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    PDF
                </a>
            </div>
            <a href="{{ route('admin.events.show', $event) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Retour à l\'événement') }}
            </a>
            <a href="{{ route('admin.events.registrations.create', $event) }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block -mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                {{ __('Ajouter Inscription') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 space-y-6">

            @if (session('success')) <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md shadow-sm">{{ session('success') }}</div> @endif
            @if (session('error')) <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md shadow-sm">{{ session('error') }}</div> @endif
            @if (session('import_errors'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md">
                    <strong class="font-bold">Erreurs lors de l'importation :</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">@foreach (session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Section Filtres --}}
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                <form method="GET" action="{{ route('admin.events.registrations.index', $event) }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Rechercher (Nom/Email)</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Nom ou email...">
                    </div>
                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status_filter" id="status_filter" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Filtrer</button>
                        <a href="{{ route('admin.events.registrations.index', $event) }}" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">Réinitialiser</a>
                    </div>
                </form>
            </div>

            {{-- Formulaire pour les Actions Groupées et l'Import --}}
            <form method="POST" action="{{ route('admin.event-registrations.bulk-actions') }}" id="bulkActionsForm"> {{-- MISE À JOUR DE L'ACTION --}}
                @csrf
                <input type="hidden" name="event_id_for_redirect" value="{{ $event->id }}"> {{-- CHAMP CACHÉ --}}
                <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <label for="bulk_action" class="sr-only">Action groupée</label>
                        <select name="bulk_action" id="bulk_action" class="block w-full sm:w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Actions groupées...</option>
                            <option value="approve">Approuver la sélection</option>
                            <option value="reject">Rejeter la sélection</option>
                            <option value="delete">Supprimer la sélection</option>
                            {{-- TODO: Ajouter des options pour "Envoyer email confirmation", "Envoyer email rejet" --}}
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Appliquer</button>
                    </div>
                    {{-- Formulaire d'Import (déplacé ici pour un meilleur agencement) --}}
                    <div class="border-l pl-4">
                         <form action="{{ route('admin.events.registrations.import.excel', $event) }}" method="POST" enctype="multipart/form-data" id="importForm" class="flex items-center space-x-2">
                            @csrf
                            <input type="file" name="import_file" id="import_file" required class="block w-full text-sm text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"/>
                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-full hover:bg-green-700">Importer</button>
                        </form>
                        <p class="text-xs text-gray-500 mt-1">
                            Colonnes : NomComplet, Email, etc.
                            <a href="#" class="text-blue-600 hover:underline" onclick="event.preventDefault(); alert('Colonnes attendues : NomComplet, Email, Telephone (optionnel), Organisation (optionnel), Motivation (optionnel), Statut (optionnel : pending, approved, etc.), NotesAdmin (optionnel), DateInscription (optionnel : AAAA-MM-JJ HH:MM:SS)');">
                                Format?
                            </a>
                        </p>
                    </div>
                </div>

                @if($registrations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Contact</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Inscrit le</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($registrations as $registration)
                                    <tr>
                                        <td class="p-3 whitespace-nowrap">
                                            <input type="checkbox" name="selected_registrations[]" value="{{ $registration->id }}" class="registration-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $registration->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $registration->email }}</div>
                                            @if($registration->user) <span class="text-xs text-blue-600">(Utilisateur)</span> @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                            @if($registration->phone_number) <div>Tél: {{ $registration->phone_number }}</div> @endif
                                            @if($registration->organization) <div class="text-xs">Org: {{ Str::limit($registration->organization, 30) }}</div> @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            {{-- Modification individuelle du statut --}}
                                            <form method="POST" action="{{ route('admin.event-registrations.update', $registration) }}" class="inline-flex items-center update-status-form">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1 {{ ($statuses[$registration->status] ?? '') == 'Approuvée' ? 'bg-green-50 text-green-700' : (($statuses[$registration->status] ?? '') == 'Rejetée' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                                                    @foreach($statuses as $key => $value)
                                                        <option value="{{ $key }}" {{ $registration->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">MàJ</button>
                                                {{-- TODO: Ajouter bouton "Notifier" ici --}}
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                            {{ $registration->registered_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.event-registrations.show', $registration) }}" class="text-emerald-600 hover:text-emerald-700 mr-2" title="Voir Détails">
                                                <svg class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            </a>
                                            {{-- Le bouton Edit mène maintenant à une page dédiée si on clique sur son nom, ou on peut modifier le statut directement ici --}}
                                            {{-- <a href="{{ route('admin.event-registrations.edit', $registration) }}" ...>Modifier</a> --}}
                                            <form action="{{ route('admin.event-registrations.destroy', $registration) }}" method="POST" class="inline-block" onsubmit="return confirm('...');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700" title="Supprimer Inscription">
                                                   <svg class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $registrations->appends(request()->query())->links() }}</div> {{-- Conserver les filtres dans la pagination --}}
                @else
                    <p class="text-gray-700 py-4">{{ __('Aucune inscription pour cet événement avec les filtres actuels.') }}</p>
                    <a href="{{ route('admin.events.registrations.create', $event) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                        {{ __('Ajouter la première inscription manuellement') }}
                    </a>
                @endif
            </form> {{-- Fin du formulaire des actions groupées --}}
        </div>
    </div>

    {{--
    Note pour le JavaScript (à externaliser dans resources/js/admin/event-registrations.js ou similaire) :
    - Logique pour la case à cocher "Tout sélectionner" (#selectAllCheckbox) et les cases individuelles (.registration-checkbox).
    - Logique pour la soumission du formulaire #bulkActionsForm :
        - Récupérer les IDs des inscriptions sélectionnées.
        - Récupérer l'action choisie dans #bulk_action.
        - Modifier l'attribut 'action' du formulaire #bulkActionsForm pour pointer vers la bonne route backend (ex: /admin/event-registrations/bulk-update-status).
        - Ajouter un champ caché avec les IDs sélectionnés.
        - Gérer la confirmation pour les actions destructives (ex: suppression).
    - Optionnel : Soumission AJAX pour les mises à jour individuelles de statut pour une meilleure UX.
    --}}
@endsection
@extends('layouts.admin')

@section('title', __('Inscriptions pour l\'événement') . ': ' . Str::limit($event->getTranslation('title', app()->getLocale(), false), 40))

@section('header')
    <div class="flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white leading-tight">
                {{ __('Inscriptions pour :') }} 
                <a href="{{ route('admin.events.show', $event) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                    {{ Str::limit($event->getTranslation('title', app()->getLocale(), false), 50) }}
                </a>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $registrations->total() }} {{ trans_choice('inscription|inscriptions', $registrations->total()) }} {{ __('au total') }}.
                {{-- Pour des stats plus détaillées, le contrôleur devrait passer $approvedCount, $pendingCount, etc. --}}
                {{-- Exemple: ($approvedCount ?? 0) . ' ' . __('approuvée(s)') --}}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('export event_registrations') {{-- Supposant une permission pour exporter --}}
            <a href="{{ route('admin.events.registrations.export.excel', $event->id) }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 shadow-sm transition ease-in-out duration-150" title="{{ __('Exporter en Excel') }}">
                <x-heroicon-o-table-cells class="h-4 w-4 mr-1.5"/>
                {{ __('Excel') }}
            </a>
            <a href="{{ route('admin.events.registrations.export.pdf', $event->id) }}" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 shadow-sm transition ease-in-out duration-150" title="{{ __('Exporter en PDF') }}">
                <x-heroicon-o-document-arrow-down class="h-4 w-4 mr-1.5"/>
                {{ __('PDF') }}
            </a>
            @endcan
            <a href="{{ route('admin.events.show', $event->id) }}" class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-sm font-medium shadow-sm transition ease-in-out duration-150">
                {{ __('Retour à l\'Événement') }}
            </a>
            @can('create event_registrations') {{-- Supposant une permission pour créer/ajouter --}}
            <a href="{{ route('admin.events.registrations.create', $event->id) }}" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 shadow-sm transition ease-in-out duration-150">
                <x-heroicon-o-plus-circle class="h-4 w-4 mr-1.5"/>
                {{ __('Ajouter une Inscription') }}
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-4 sm:p-6">

        @if (session('success')) <div class="mb-4 p-4 text-sm bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md shadow-sm">{{ session('success') }}</div> @endif
        @if (session('error')) <div class="mb-4 p-4 text-sm bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md shadow-sm">{{ session('error') }}</div> @endif
        @if (session('import_errors'))
            <div class="mb-4 p-4 text-sm bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 border border-red-300 dark:border-red-600 rounded-md">
                <strong class="font-bold">{{ __('Erreurs lors de l\'importation :') }}</strong>
                <ul class="mt-2 list-disc list-inside">@foreach (session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-md">
            <form method="GET" action="{{ route('admin.events.registrations.index', $event->id) }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Rechercher (Nom, Email)') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="{{ __('Nom ou email...') }}">
                </div>
                <div>
                    <label for="status_filter" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Statut') }}</label>
                    <select name="status_filter" id="status_filter" class="mt-1 block w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ __('Tous les statuts') }}</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status_filter') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">{{ __('Filtrer') }}</button>
                    <a href="{{ route('admin.events.registrations.index', $event->id) }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">{{ __('Réinitialiser') }}</a>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.event-registrations.bulk-actions') }}" id="bulkActionsForm">
            @csrf
            <input type="hidden" name="event_id_for_redirect" value="{{ $event->id }}">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                @can('manage event_registrations')
                <div class="flex items-center gap-2">
                    <label for="bulk_action" class="sr-only">{{ __('Action groupée') }}</label>
                    <select name="bulk_action" id="bulk_action" class="block w-full sm:w-auto text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ __('Actions groupées...') }}</option>
                        @foreach($statuses as $key => $value)
                            <option value="set_status_{{ $key }}">{{ __('Marquer comme') }} {{ strtolower($value) }}</option>
                        @endforeach
                        <option value="delete">{{ __('Supprimer la sélection') }}</option>
                        {{-- <option value="notify_approved">Notifier (Approuvé)</option> --}}
                        {{-- <option value="notify_rejected">Notifier (Rejeté)</option> --}}
                    </select>
                    <button type="submit" id="applyBulkActionBtn" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">{{ __('Appliquer') }}</button>
                </div>
                @endcan
                @can('import event_registrations')
                <div class="border-l border-gray-200 dark:border-gray-700 pl-4">
                    <form action="{{ route('admin.events.registrations.import.excel', $event->id) }}" method="POST" enctype="multipart/form-data" id="importForm" class="flex items-center space-x-2">
                        @csrf
                        <input type="file" name="import_file" id="import_file" required class="block w-full max-w-xs text-xs text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-700 file:text-primary-700 dark:file:text-primary-100 hover:file:bg-primary-100 dark:hover:file:bg-primary-600"/>
                        <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">{{ __('Importer') }}</button>
                    </form>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline" onclick="event.preventDefault(); alert('Colonnes attendues dans le fichier Excel/CSV : name, email, phone_number (optionnel), organization (optionnel), motivation (optionnel), status (optionnel: pending, approved, rejected, etc.), notes (optionnel), registered_at (optionnel : AAAA-MM-JJ HH:MM:SS)');">
                            {{ __('Format du fichier d\'import ?') }}
                        </a>
                    </p>
                </div>
                @endcan
            </div>

            @if($registrations->count() > 0)
                <div class="overflow-x-auto align-middle inline-block min-w-full shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:checked:bg-primary-600 dark:focus:ring-offset-gray-900">
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Participant') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Contact') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">{{ __('Inscrit le') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($registrations as $registration)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3 whitespace-nowrap">
                                        <input type="checkbox" name="selected_registrations[]" value="{{ $registration->id }}" class="registration-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-900 dark:checked:bg-primary-600 dark:focus:ring-offset-gray-900">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $registration->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $registration->email }}</div>
                                        @if($registration->user_id) <span class="text-xs text-primary-600 dark:text-primary-400">({{ __('Utilisateur') }})</span> @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                        @if($registration->phone_number) <div>{{ __('Tél') }}: {{ $registration->phone_number }}</div> @endif
                                        @if($registration->organization) <div class="text-xs">{{ __('Org') }}: {{ Str::limit($registration->organization, 30) }}</div> @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
                                                'cancelled_by_user' => 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                                                'attended' => 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$registration->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                                            {{ $statuses[$registration->status] ?? Str::title(str_replace('_', ' ', $registration->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                        {{ $registration->registered_at ? $registration->registered_at->translatedFormat('d/m/Y H:i') : '' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        @can('view event_registrations')
                                        <a href="{{ route('admin.event-registrations.show', $registration) }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 p-1 inline-block" title="{{__('Voir Détails')}}">
                                            <x-heroicon-o-eye class="h-5 w-5"/>
                                        </a>
                                        @endcan
                                        @can('manage event_registrations')
                                        <a href="{{ route('admin.event-registrations.edit', $registration) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 inline-block" title="{{__('Modifier')}}">
                                            <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                        </a>
                                        <button type="button" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 inline-block" title="{{__('Supprimer')}}"
                                                onclick="event.preventDefault(); if(confirm('{{ __('Êtes-vous sûr de vouloir supprimer l\'inscription de :name ?', ['name' => addslashes($registration->name)]) }}')) { document.getElementById('delete-registration-{{ $registration->id }}').submit(); }">
                                            <x-heroicon-o-trash class="h-5 w-5"/>
                                        </button>
                                        <form id="delete-registration-{{ $registration->id }}" action="{{ route('admin.event-registrations.destroy', $registration) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $registrations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-3.741-5.58M14.28 14.28a3 3 0 00-5.58 3.741 9.094 9.094 0 003.741.479m0 0v-.002c3.031-.287 4.745-1.524 5.58-3.741M10.22 10.22A3 3 0 004.64 6.48 9.095 9.095 0 00.9 10.22m0 0v.002c.287 3.031 1.524 4.745 3.741 5.58m5.58-5.58a3 3 0 00-3.741-3.741m0 0c-.287-3.031-1.524-4.745-3.741-5.58m11.16 11.16a3 3 0 003.741 3.741m0 0c3.031.287 4.745 1.524 5.58 3.741M13.78 13.78a3 3 0 005.58-3.741 9.095 9.095 0 00-3.741-.479m0 0v-.002c-3.031.287-4.745 1.524-5.58 3.741m5.58 5.58a3 3 0 003.741 3.741M3.84 3.84A3 3 0 006.48 4.64 9.095 9.095 0 0010.22.9m0 0v-.002C7.19.613 5.476 1.85 4.64 4.063M20.16 20.16a3 3 0 00-2.64-3.52 9.095 9.095 0 00-3.741-.479m0 0v.002c-3.031.287-4.745-1.524-5.58-3.741M16 10a6 6 0 11-12 0 6 6 0 0112 0z" /></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-white">{{ __('Aucune inscription trouvée.') }}</h3>
                     @if(!request()->has('search') && !request()->has('status_filter')) {{-- N'afficher que s'il n'y a pas de filtres actifs --}}
                        @can('create event_registrations')
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Commencez par en ajouter une manuellement.') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('admin.events.registrations.create', $event->id) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                                {{ __('Ajouter une Inscription') }}
                            </a>
                        </div>
                        @endcan
                    @endif
                </div>
            @endif
        </form> {{-- Fin du formulaire des actions groupées --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gérer la sélection de toutes les cases à cocher
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const registrationCheckboxes = document.querySelectorAll('.registration-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function (e) {
            registrationCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    }

    registrationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked && selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            } else if (selectAllCheckbox) {
                // Vérifier si toutes les autres sont cochées
                let allChecked = true;
                registrationCheckboxes.forEach(cb => {
                    if (!cb.checked) {
                        allChecked = false;
                    }
                });
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
    
    // Confirmation pour les actions groupées
    const bulkActionsForm = document.getElementById('bulkActionsForm');
    const applyBulkActionBtn = document.getElementById('applyBulkActionBtn');

    if (bulkActionsForm && applyBulkActionBtn) {
        applyBulkActionBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Empêcher la soumission directe
            const bulkActionSelect = document.getElementById('bulk_action');
            const selectedAction = bulkActionSelect.value;
            const selectedActionText = bulkActionSelect.options[bulkActionSelect.selectedIndex].text;
            
            let selectedRegistrationsCount = 0;
            document.querySelectorAll('.registration-checkbox:checked').forEach(cb => {
                selectedRegistrationsCount++;
            });

            if (selectedAction === "") {
                alert("{{ __('Veuillez sélectionner une action groupée.') }}");
                return;
            }
            if (selectedRegistrationsCount === 0) {
                alert("{{ __('Veuillez sélectionner au moins une inscription.') }}");
                return;
            }

            if (confirm(`{{ __('Êtes-vous sûr de vouloir appliquer l\'action') }} "${selectedActionText}" {{ __('sur les') }} ${selectedRegistrationsCount} {{ __('inscription(s) sélectionnée(s) ?') }}`)) {
                bulkActionsForm.submit();
            }
        });
    }
});
</script>
@endpush
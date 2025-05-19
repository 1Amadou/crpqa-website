<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Ajouté pour la suppression potentielle de fichiers d'import erronés
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EventRegistrationsExport;
use App\Imports\EventRegistrationsImport;
use Barryvdh\DomPDF\Facade\Pdf; // Ou use PDF; si vous avez configuré l'alias
use Carbon\Carbon;


class EventRegistrationController extends Controller
{
    public function __construct()
    {
        // Exemple de protection globale pour le contrôleur.
        // Vous devriez créer une permission 'manage event registrations' et l'assigner aux rôles appropriés.
        // $this->middleware(['permission:manage event registrations']);

        // Ou, pour une granularité plus fine, utiliser $this->authorize() dans chaque méthode
        // en se basant sur l'événement parent ou une EventRegistrationPolicy.
    }

    /**
     * Fournit la liste des statuts possibles et leurs libellés.
     * @return array
     */
    private function getStatuses(): array
    {
        return [
            'pending' => __('En attente'),
            'approved' => __('Approuvée'),
            'rejected' => __('Rejetée'),
            'cancelled_by_user' => __('Annulée (Participant)'),
            'attended' => __('A participé')
        ];
    }

    /**
     * Règles de validation pour une inscription.
     */
    private function validationRules(Event $event, EventRegistration $registration = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('event_registrations', 'email')
                    ->where(fn ($query) => $query->where('event_id', $event->id))
                    ->ignore($registration ? $registration->id : null)
            ],
            'phone_number' => 'nullable|string|max:50',
            'organization' => 'nullable|string|max:255',
            'motivation' => 'nullable|string|max:2000',
            'status' => ['required', 'string', Rule::in(array_keys($this->getStatuses()))],
            'notes' => 'nullable|string|max:2000',
            'user_id' => 'nullable|exists:users,id',
            'registered_at' => 'nullable|date_format:Y-m-d\TH:i', // Format pour datetime-local input
        ];
    }

    /**
     * Affiche la liste des inscriptions pour un événement spécifique.
     */
    public function indexForEvent(Event $event, Request $request)
    {
        // $this->authorize('viewRegistrations', $event); // Via EventPolicy ou permission
        
        $registrationsQuery = $event->registrations()
                                   ->with('user') // Charger l'utilisateur si lié
                                   ->orderBy($request->input('sort_by', 'registered_at'), $request->input('sort_direction', 'desc'));

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $registrationsQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status_filter')) {
            $registrationsQuery->where('status', $request->input('status_filter'));
        }

        $registrations = $registrationsQuery->paginate(20)->appends($request->query());
        $statuses = $this->getStatuses();

        return view('admin.event_registrations.index', compact('event', 'registrations', 'statuses'));
    }

    /**
     * Affiche le formulaire pour créer une nouvelle inscription manuellement pour un événement.
     */
    public function create(Event $event)
    {
        // $this->authorize('createRegistrationFor', $event); // Via EventPolicy ou permission
        $statuses = $this->getStatuses();
        $users = User::orderBy('name')->pluck('name', 'id');
        return view('admin.event_registrations.create', compact('event', 'statuses', 'users'));
    }

    /**
     * Enregistre une nouvelle inscription créée manuellement.
     */
    public function store(Request $request, Event $event)
    {
        // $this->authorize('createRegistrationFor', $event);
        $validatedData = $request->validate($this->validationRules($event));

        $registration = new EventRegistration();
        $registration->event_id = $event->id;
        $registration->fill($validatedData); // Utilise $fillable dans le modèle
        $registration->registered_at = $validatedData['registered_at'] ? Carbon::parse($validatedData['registered_at']) : now();
        // $registration->status est déjà dans $validatedData

        $registration->save();

        // Optionnel: Notifier si statut approuvé directement ?
        // if ($registration->status === 'approved') {
        //     try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationApproved($registration)); }
        //     catch (\Exception $e) { Log::error("Email Error (Store Approved): " . $e->getMessage()); }
        // } elseif ($registration->status === 'pending') {
        //     try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationReceived($registration)); }
        //     catch (\Exception $e) { Log::error("Email Error (Store Pending): " . $e->getMessage()); }
        // }
        // Pour notifier l'admin d'un ajout manuel :
        // $adminEmail = config('mail.admin_notification_email'); // A définir
        // if ($adminEmail) {
        //     try { Mail::to($adminEmail)->send(new \App\Mail\AdminNewEventRegistration($registration)); }
        //     catch (\Exception $e) { Log::error("Admin Email Error (Store): " . $e->getMessage()); }
        // }


        return redirect()->route('admin.events.registrations.index', $event->id)
                         ->with('success', 'Inscription pour "' . $registration->name . '" ajoutée avec succès.');
    }

    /**
     * Affiche les détails d'une inscription spécifique.
     */
    public function show(EventRegistration $registration)
    {
        // $this->authorize('view', $registration); // Via EventRegistrationPolicy
        $registration->load('event', 'user');
        $statuses = $this->getStatuses();
        return view('admin.event_registrations.show', compact('registration', 'statuses'));
    }

    /**
     * Affiche le formulaire pour modifier une inscription.
     */
    public function edit(EventRegistration $registration)
    {
        // $this->authorize('update', $registration); // Via EventRegistrationPolicy
        $registration->load('event', 'user');
        $statuses = $this->getStatuses();
        $users = User::orderBy('name')->pluck('name', 'id');
        return view('admin.event_registrations.edit', compact('registration', 'statuses', 'users'));
    }

    /**
     * Met à jour une inscription spécifique.
     */
    public function update(Request $request, EventRegistration $registration)
    {
        // $this->authorize('update', $registration); // Via EventRegistrationPolicy
        $validatedData = $request->validate($this->validationRules($registration->event, $registration));

        $originalStatus = $registration->status;
        
        $registration->fill($validatedData); // Met à jour les champs fillable
        $registration->registered_at = $validatedData['registered_at'] ? Carbon::parse($validatedData['registered_at']) : $registration->registered_at;

        $statusChanged = $registration->isDirty('status');
        $registration->save();

        if ($statusChanged) {
            if ($registration->status === 'approved' && $originalStatus !== 'approved') {
                try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationApproved($registration)); }
                catch (\Exception $e) { Log::error("Email Error (Update Approved): " . $e->getMessage()); }
            } elseif ($registration->status === 'rejected' && $originalStatus !== 'rejected') {
                 try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationRejected($registration)); }
                 catch (\Exception $e) { Log::error("Email Error (Update Rejected): " . $e->getMessage()); }
            }
        }

        return redirect()->route('admin.events.registrations.index', $registration->event_id)
                         ->with('success', 'Inscription de "' . $registration->name . '" mise à jour avec succès.');
    }

    /**
     * Supprime une inscription.
     */
    public function destroy(EventRegistration $registration)
    {
        // $this->authorize('delete', $registration); // Via EventRegistrationPolicy
        $eventId = $registration->event_id;
        $participantName = $registration->name;
        $registration->delete();

        return redirect()->route('admin.events.registrations.index', $eventId)
                         ->with('success', 'Inscription de "' . $participantName . '" supprimée avec succès.');
    }

    /**
     * Gère les actions groupées sur les inscriptions.
     */
    public function bulkActions(Request $request)
    {
        $validatedData = $request->validate([
            'bulk_action' => 'required|string|in:approve,reject,delete',
            'selected_registrations' => 'required|array',
            'selected_registrations.*' => 'exists:event_registrations,id',
            'event_id_for_redirect' => 'required|exists:events,id' // Assurer la redirection
        ]);

        $action = $validatedData['bulk_action'];
        $selectedIds = $validatedData['selected_registrations'];
        $eventIdForRedirect = $validatedData['event_id_for_redirect'];
        
        $registrations = EventRegistration::whereIn('id', $selectedIds)->get();
        $count = 0;
        $actionText = '';

        foreach ($registrations as $registration) {
            // Vérifier si l'inscription appartient bien à l'événement pour lequel on fait l'action groupée
            // par sécurité, même si les IDs sont censés être de cet événement.
            if ($registration->event_id != $eventIdForRedirect) {
                continue; // Skip si ce n'est pas le bon événement
            }
            // $this->authorize('update', $registration); // Ou delete selon l'action, via EventRegistrationPolicy

            $originalStatus = $registration->status;

            switch ($action) {
                case 'approve':
                    if ($originalStatus !== 'approved') {
                        $registration->status = 'approved';
                        $registration->save();
                        try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationApproved($registration)); }
                        catch (\Exception $e) { Log::error("Bulk Email Error (Approve): " . $e->getMessage()); }
                    }
                    $count++;
                    $actionText = __('approuvées');
                    break;
                case 'reject':
                    if ($originalStatus !== 'rejected') {
                        $registration->status = 'rejected';
                        $registration->save();
                        try { Mail::to($registration->email)->send(new \App\Mail\EventRegistrationRejected($registration)); }
                        catch (\Exception $e) { Log::error("Bulk Email Error (Reject): " . $e->getMessage()); }
                    }
                    $count++;
                    $actionText = __('rejetées');
                    break;
                case 'delete':
                    $registration->delete();
                    $count++;
                    $actionText = __('supprimées');
                    break;
            }
        }

        if ($count > 0) {
            return redirect()->route('admin.events.registrations.index', $eventIdForRedirect)
                             ->with('success', __(':count inscription(s) sélectionnée(s) ont été :action.', ['count' => $count, 'action' => $actionText]));
        }

        return redirect()->route('admin.events.registrations.index', $eventIdForRedirect)
                         ->with('info', __('Aucune action effectuée ou aucune inscription sélectionnée/modifiée.'));
    }

    /**
     * Exporte les inscriptions d'un événement en format Excel.
     */
    public function exportExcel(Event $event)
    {
        // $this->authorize('exportRegistrations', $event); // Via EventPolicy ou permission
        $fileName = 'inscriptions_' . Str::slug($event->title) . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new EventRegistrationsExport($event->id), $fileName);
    }

    /**
     * Exporte les inscriptions d'un événement en format PDF.
     */
    public function exportPdf(Event $event)
    {
        // $this->authorize('exportRegistrations', $event);
        $registrations = $event->registrations()->with('user')->orderBy('name')->get();
        $statuses = $this->getStatuses();
        $pdf = Pdf::loadView('admin.event_registrations.pdf_export_template', compact('event', 'registrations', 'statuses'));
        $fileName = 'inscriptions_' . Str::slug($event->title) . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Gère l'upload et l'import d'un fichier d'inscriptions.
     */
    public function importExcel(Request $request, Event $event)
    {
        // $this->authorize('importRegistrations', $event); // Via EventPolicy ou permission
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);

        $file = $request->file('import_file');

        try {
            $import = new EventRegistrationsImport($event);
            Excel::import($import, $file);

            $successMessage = __('Importation terminée.');
            $failureCount = $import->failures()->count();

            if ($failureCount > 0) {
                $successMessage .= __(" :count ligne(s) n'a/n'ont pas pu être importée(s) en raison d'erreurs de validation ou de doublons.", ['count' => $failureCount]);
                // Préparer les erreurs pour les afficher si nécessaire (ex: en session, ou logger pour admin)
                // Session::flash('import_failures', $import->failures());
                 Log::warning("Échecs d'import pour l'événement ID {$event->id}: " . $import->failures()->toJson());
            }

            return redirect()->route('admin.events.registrations.index', $event->id)
                             ->with('success', $successMessage);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = __("Ligne :row: :errors (Valeurs: :values)", [
                    'row' => $failure->row(),
                    'errors' => implode(', ', $failure->errors()),
                    'values' => implode(', ', $failure->values())
                ]);
            }
            return redirect()->route('admin.events.registrations.index', $event->id)
                             ->with('error', __('Des erreurs de validation sont survenues durant l\'import. Veuillez vérifier votre fichier.'))
                             ->with('import_validation_errors', $errorMessages); // Passer les erreurs détaillées
        } catch (\Exception $e) {
            Log::error("Erreur d'import Excel pour l'événement ID {$event->id}: " . $e->getMessage());
            return redirect()->route('admin.events.registrations.index', $event->id)
                             ->with('error', __('Une erreur est survenue lors de l\'importation du fichier : :message', ['message' => $e->getMessage()]));
        }
    }
}
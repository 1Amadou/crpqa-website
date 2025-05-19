<?php

namespace App\Exports;

use App\Models\EventRegistration;
use App\Models\Event; // Importer le modèle Event
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable; 
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\EventRegistrationsExport; 
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Str;

class EventRegistrationsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable; // Permet de définir le nom du fichier, etc.

    protected int $eventId;
    protected string $eventTitle;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
        $event = Event::find($eventId); // Récupérer l'événement pour le titre
        $this->eventTitle = $event ? Str::slug($event->title, '_') : 'evenement';
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return EventRegistration::query()
            ->where('event_id', $this->eventId)
            ->with(['user', 'event']); // Charger les relations si besoin dans le mapping
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID Inscription',
            'Nom du Participant',
            'Email',
            'Téléphone',
            'Organisation',
            'Statut',
            'Date d\'Inscription',
            'Utilisateur Lié (ID)',
            'Utilisateur Lié (Nom)',
            'Notes Admin',
            // 'Motivation', // Décommentez si vous voulez l'exporter
        ];
    }

    /**
    * @param EventRegistration $registration
    * @return array
    */
    public function map($registration): array
    {
        // Pour obtenir le nom lisible du statut
        $statuses = [
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'cancelled_by_user' => 'Annulée (Participant)',
            'attended' => 'A participé'
        ];
        $statusDisplay = $statuses[$registration->status] ?? ucfirst(str_replace('_', ' ', $registration->status));

        return [
            $registration->id,
            $registration->name,
            $registration->email,
            $registration->phone_number,
            $registration->organization,
            $statusDisplay,
            $registration->registered_at ? $registration->registered_at->format('d/m/Y H:i') : '',
            $registration->user_id,
            $registration->user ? $registration->user->name : 'N/A',
            $registration->notes,
            // $registration->motivation, // Décommentez si vous voulez l'exporter
        ];
    }

     /**
     * Exporte les inscriptions d'un événement en format Excel.
     */
    public function exportExcel(Event $event)
    {
        // $this->authorize('view', $event); // Vérifier l'autorisation d'accéder à l'événement
        $fileName = 'inscriptions_' . Str::slug($event->title) . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new EventRegistrationsExport($event->id), $fileName);
    }

    /**
     * Exporte les inscriptions d'un événement en format PDF.
     */
    public function exportPdf(Event $event)
    {
        // $this->authorize('view', $event); // Vérifier l'autorisation
        $registrations = $event->registrations()->with('user')->orderBy('name')->get(); // Récupérer les données
        $statuses = $this->getStatuses(); // Pour afficher les statuts lisibles

        // Créez une vue Blade pour le PDF, par exemple : 'admin.event_registrations.pdf'
        // et passez-lui les données.
        $pdf = Pdf::loadView('admin.event_registrations.pdf_export_template', compact('event', 'registrations', 'statuses'));
        
        $fileName = 'inscriptions_' . Str::slug($event->title) . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
        // Ou pour l'afficher dans le navigateur : return $pdf->stream($fileName);
    }

    /**
     * Optionnel: définir le nom du fichier
     * return string
     */
    // public function title(): string
    // {
    //     return 'Inscriptions_' . $this->eventTitle;
    // }

    /**
     * Il n'est pas nécessaire de surcharger la méthode download si on utilise le helper dans le contrôleur.
     * Le nom du fichier peut être défini dans le contrôleur.
     */
}
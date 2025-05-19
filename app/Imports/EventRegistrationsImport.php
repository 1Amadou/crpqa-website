<?php

namespace App\Imports;

use App\Models\EventRegistration;
use App\Models\Event;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Pour sauter les lignes en échec et continuer
use Maatwebsite\Excel\Concerns\SkipsFailures;   // Pour collecter les échecs
use Illuminate\Support\Facades\Validator;       // Pour la validation personnalisée
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EventRegistrationsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures; // Permet de collecter les erreurs de validation par ligne

    private Event $event;
    private array $statuses;

    public function __construct(Event $event)
    {
        $this->event = $event;
        // Récupérer les statuts possibles pour la validation
        // Idéalement, cette logique de getStatuses devrait être centralisée (ex: dans le modèle EventRegistration ou un Enum)
        $this->statuses = [
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'cancelled_by_user' => 'Annulée (Participant)',
            'attended' => 'A participé'
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
   
    public function model(array $row)
    {
        // Validation supplémentaire au cas où WithValidation ne suffirait pas pour des logiques complexes
        // ou pour gérer les doublons d'email pour CET événement spécifique.
        // La règle unique dans rules() devrait déjà gérer ça.

        return new EventRegistration([
            'event_id'     => $this->event->id,
            'name'         => $row['nomcomplet'], // Doit correspondre à l'en-tête de votre fichier Excel/CSV
            'email'        => $row['email'],
            'phone_number' => $row['telephone'] ?? null,
            'organization' => $row['organisation'] ?? null,
            'motivation'   => $row['motivation'] ?? null,
            'status'       => isset($row['statut']) && array_key_exists(strtolower($row['statut']), array_change_key_case($this->statuses)) ? strtolower($row['statut']) : 'pending',
            'notes'        => $row['notesadmin'] ?? null,
            'registered_at'=> isset($row['dateinscription']) ? Carbon::parse($row['dateinscription']) : now(),
            // 'user_id'    => null, // L'import ne lie pas automatiquement à un user_id, sauf si vous ajoutez cette logique
        ]);
    }

    /**
     * Définition des règles de validation pour chaque ligne.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nomcomplet' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // S'assurer que l'email est unique pour cet événement spécifique
                Rule::unique('event_registrations', 'email')
                    ->where(function ($query) {
                        return $query->where('event_id', $this->event->id);
                    })
            ],
            'telephone' => 'nullable|string|max:50',
            'organisation' => 'nullable|string|max:255',
            'motivation' => 'nullable|string',
            'statut' => ['nullable', 'string', Rule::in(array_keys($this->statuses))],
            'notesadmin' => 'nullable|string',
            'dateinscription' => 'nullable|date',
        ];
    }

    /**
     * Optionnel: Définir des messages de validation personnalisés.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nomcomplet.required' => 'Le nom complet est requis pour chaque inscription.',
            'email.required' => 'L\'email est requis pour chaque inscription.',
            'email.email' => 'L\'email fourni n\'est pas une adresse email valide.',
            'email.unique' => 'L\'email :input est déjà inscrit à cet événement.',
            // ... autres messages personnalisés
        ];
    }

    // Vous pouvez aussi implémenter WithBatchInserts et WithChunkReading pour de gros fichiers
    // public function batchSize(): int { return 1000; }
    // public function chunkSize(): int { return 1000; }
}
<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Optionnel: si vous voulez que cet email soit mis en file d'attente
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Storage; // Pour le logo
use App\Models\SiteSetting; // Pour les paramètres du site

class EventRegistrationReceived extends Mailable // Optionnel: implements ShouldQueue
{
    use Queueable, SerializesModels;

    public EventRegistration $registration;
    public ?SiteSetting $siteSettings; // Pour le logo et nom du site

    /**
     * Create a new message instance.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
        // Charger les paramètres du site pour les utiliser dans l'email (logo, nom du site)
        // Utiliser le cache pour la performance
        $this->siteSettings = cache()->rememberForever('site_settings_mail', function () {
            return SiteSetting::firstOrCreate(['id' => 1]);
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Utiliser les paramètres du site pour l'expéditeur si disponibles, sinon ceux du .env
        $fromAddress = $this->siteSettings?->default_sender_email ?: config('mail.from.address');
        $fromName = $this->siteSettings?->default_sender_name ?: config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            to: [new Address($this->registration->email, $this->registration->name)],
            subject: 'Confirmation de réception de votre inscription : ' . $this->registration->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $eventUrl = '#'; // Placeholder, sera défini si la route existe
        if ($this->registration->event->slug) {
            try {
                // Supposons que vous aurez une route nommée 'public.events.show' pour afficher un événement
                $eventUrl = route('public.events.show', $this->registration->event->slug);
            } catch (\Exception $e) {
                // La route n'existe pas encore, on garde le placeholder ou une URL de base
                $eventUrl = url('/'); // Ou une URL générique vers le site
            }
        }

        $logoUrl = null;
        if ($this->siteSettings?->logo_path && Storage::disk('public')->exists($this->siteSettings->logo_path)) {
            $logoUrl = Storage::url($this->siteSettings->logo_path);
        }


        return new Content(
            markdown: 'emails.events.registration_received',
            with: [
                'eventName' => $this->registration->event->title,
                'participantName' => $this->registration->name,
                'registrationDate' => $this->registration->registered_at->format('d/m/Y H:i'),
                'eventUrl' => $eventUrl,
                'siteName' => $this->siteSettings?->site_name ?? config('app.name'),
                'logoUrl' => $logoUrl, // URL absolue pour les emails
                // 'contactEmail' => $this->siteSettings?->contact_email, // Si besoin dans l'email
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
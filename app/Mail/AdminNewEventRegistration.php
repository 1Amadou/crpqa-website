<?php

namespace App\Mail;

use App\Models\EventRegistration;
// ... autres imports ...
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Storage;
use App\Models\SiteSetting;

class AdminNewEventRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public EventRegistration $registration;
    public ?SiteSetting $siteSettings;
    public string $adminEmail; // L'email de l'admin à notifier

    public function __construct(EventRegistration $registration, string $adminEmail)
    {
        $this->registration = $registration;
        $this->adminEmail = $adminEmail; // Peut être un email des settings ou un email fixe
        $this->siteSettings = cache()->rememberForever('site_settings_mail', function () {
            return SiteSetting::firstOrCreate(['id' => 1]);
        });
    }

    public function envelope(): Envelope
    {
        $fromAddress = $this->siteSettings?->default_sender_email ?: config('mail.from.address');
        $fromName = $this->siteSettings?->default_sender_name ?: config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            to: [new Address($this->adminEmail)], // Destinataire est l'admin
            subject: 'Nouvelle inscription à l\'événement : ' . $this->registration->event->title,
        );
    }

    public function content(): Content
    {
        $eventAdminUrl = '#'; // URL pour gérer les inscriptions de cet événement
        if ($this->registration->event_id) {
            try {
                $eventAdminUrl = route('admin.events.registrations.index', $this->registration->event_id);
            } catch (\Exception $e) {
                $eventAdminUrl = route('admin.dashboard'); // Fallback
            }
        }
        $logoUrl = null;
        if ($this->siteSettings?->logo_path && Storage::disk('public')->exists($this->siteSettings->logo_path)) {
            $logoUrl = Storage::url($this->siteSettings->logo_path);
        }

        return new Content(
            markdown: 'emails.events.admin_new_registration',
            with: [
                'eventName' => $this->registration->event->title,
                'participantName' => $this->registration->name,
                'participantEmail' => $this->registration->email,
                'registrationDate' => $this->registration->registered_at->format('d/m/Y H:i'),
                'eventAdminUrl' => $eventAdminUrl, // Lien vers la gestion des inscriptions
                'siteName' => $this->siteSettings?->site_name ?? config('app.name'),
                'logoUrl' => $logoUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
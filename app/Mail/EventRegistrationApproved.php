<?php

namespace App\Mail;

use App\Models\EventRegistration;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Storage;

class EventRegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public EventRegistration $registration;
    public ?SiteSetting $siteSettings;

    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;

        // ⚠️ Assurez-vous que l'enregistrement #1 existe bien
        $this->siteSettings = cache()->rememberForever('site_settings_mail', function () {
            return SiteSetting::firstOrCreate(['id' => 1]);
        });
    }

    public function envelope(): Envelope
    {
        $fromAddress = $this->siteSettings?->default_sender_email ?? config('mail.from.address');
        $fromName = $this->siteSettings?->default_sender_name ?? config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            to: [
                new Address(
                    $this->registration->email,
                    $this->registration->name ?? $this->registration->email
                ),
            ],
            subject: "Votre inscription à l'événement « {$this->registration->event->title} » a été approuvée !"
        );
    }

    public function content(): Content
    {
        $event = $this->registration->event;

        // 🔒 Sécurisation en cas de null
        $eventUrl = '#';
        if ($event && $event->slug) {
            try {
                $eventUrl = route('public.events.show', $event->slug);
            } catch (\Exception $e) {
                $eventUrl = url('/');
            }
        }

        $logoUrl = null;
        if (
            $this->siteSettings?->logo_path &&
            Storage::disk('public')->exists($this->siteSettings->logo_path)
        ) {
            $logoUrl = Storage::url($this->siteSettings->logo_path);
        }

        return new Content(
            markdown: 'emails.events.registration_approved',
            with: [
                'eventName' => $event->title ?? 'Événement',
                'participantName' => $this->registration->name,
                'eventDate' => $event->start_datetime?->isoFormat('LLLL') ?? '',
                'eventLocation' => $event->location ?? '',
                'eventUrl' => $eventUrl,
                'siteName' => $this->siteSettings?->site_name ?? config('app.name'),
                'logoUrl' => $logoUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

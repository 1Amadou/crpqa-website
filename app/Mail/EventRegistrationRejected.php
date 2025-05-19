<?php

namespace App\Mail;

use App\Models\EventRegistration;
// ... autres imports ...
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Storage;
use App\Models\SiteSetting;

class EventRegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public EventRegistration $registration;
    public ?SiteSetting $siteSettings;

    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
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
            to: [new Address($this->registration->email, $this->registration->name)],
            subject: 'Concernant votre inscription à l\'événement : ' . $this->registration->event->title,
        );
    }

    public function content(): Content
    {
        $eventUrl = '#';
        if ($this->registration->event->slug) {
            try {
                $eventUrl = route('public.events.show', $this->registration->event->slug);
            } catch (\Exception $e) {
                $eventUrl = url('/');
            }
        }
        $logoUrl = null;
        if ($this->siteSettings?->logo_path && Storage::disk('public')->exists($this->siteSettings->logo_path)) {
            $logoUrl = Storage::url($this->siteSettings->logo_path);
        }

        return new Content(
            markdown: 'emails.events.registration_rejected',
            with: [
                'eventName' => $this->registration->event->title,
                'participantName' => $this->registration->name,
                'eventUrl' => $eventUrl,
                'siteName' => $this->siteSettings?->site_name ?? config('app.name'),
                'logoUrl' => $logoUrl,
                // 'rejectionReason' => $this->registration->notes, // Si vous voulez inclure la raison du rejet
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
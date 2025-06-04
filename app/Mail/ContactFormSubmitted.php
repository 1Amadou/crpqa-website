<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address; // NOUVEAU pour Laravel 9+ pour l'expéditeur
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; // Pour stocker les données du formulaire

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        // L'expéditeur de l'email sera celui qui a rempli le formulaire.
        // Le nom de l'expéditeur sera aussi celui fourni dans le formulaire.
        return new Envelope(
            from: new Address($this->data['email'], $this->data['name']),
            replyTo: [ // Important pour que vous puissiez répondre directement à l'utilisateur
                new Address($this->data['email'], $this->data['name'])
            ],
            subject: __('Nouveau Message de Contact CRPQA: ') . $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.submitted', // La vue Blade pour l'email
            with: [ // Passer les données à la vue de l'email
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'mailSubject' => $this->data['subject'], // Renommé pour éviter conflit avec $subject de Envelope
                'messageContent' => $this->data['message'],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
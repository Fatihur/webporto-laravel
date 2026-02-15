<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New Contact Message: :subject', ['subject' => $this->contact->subject]),
            replyTo: $this->contact->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.notification',
            with: [
                'contact' => $this->contact,
                'adminUrl' => route('admin.contacts.index'),
            ],
        );
    }
}

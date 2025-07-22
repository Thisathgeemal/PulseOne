<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietitianRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $defaultPassword;
    public $url;

    public function __construct($user, $defaultPassword, $url)
    {
                $this->user            = $user;
        $this->defaultPassword = $defaultPassword;
        $this->url             = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PulseOne Dietitian Registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dietitianRegistration',
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

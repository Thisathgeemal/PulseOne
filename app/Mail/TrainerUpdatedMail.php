<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerUpdatedMail extends Mailable
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
            subject: 'Your PulseOne Account Details Have Been Updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainerUpdate',
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

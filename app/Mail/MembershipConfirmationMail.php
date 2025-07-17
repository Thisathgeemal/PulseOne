<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $membershipType;

    public function __construct($user, $membershipType)
    {
        $this->user           = $user;
        $this->membershipType = $membershipType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PulseOne Membership Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membershipConfirmation',
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

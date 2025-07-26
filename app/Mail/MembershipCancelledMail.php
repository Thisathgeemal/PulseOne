<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $membershipType;
    public $startDate;
    public $endDate;

    public function __construct($user, $membershipType, $startDate, $endDate)
    {
        $this->user           = $user;
        $this->membershipType = $membershipType;
        $this->startDate      = $startDate;
        $this->endDate        = $endDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your PulseOne Membership Has Been Cancelled',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membershipCancelled',
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

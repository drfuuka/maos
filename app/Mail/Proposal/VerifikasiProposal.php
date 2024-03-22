<?php

namespace App\Mail\Proposal;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiProposal extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $proposal;
    
    /**
     * Create a new message instance.
     */
    public function __construct($user, $proposal)
    {
        $this->user     = $user;
        $this->proposal = $proposal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Verifikasi Proposal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.proposal.verifikasi-proposal',
            with: [
                'userData' => $this->user,
                'proposal' => $this->proposal,
            ]
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

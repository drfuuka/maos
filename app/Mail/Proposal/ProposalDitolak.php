<?php

namespace App\Mail\Proposal;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalDitolak extends Mailable
{
    use Queueable, SerializesModels;

    protected $ketua;
    protected $proposal;
    
    /**
     * Create a new message instance.
     */
    public function __construct($ketua, $proposal)
    {
        $this->ketua    = $ketua;
        $this->proposal = $proposal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Proposal Ditolak',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.proposal.proposal-ditolak',
            with: [
                'ketua'    => $this->ketua,
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

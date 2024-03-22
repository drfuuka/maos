<?php

namespace App\Mail\Lpj;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiLpj extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $lpj;
    
    /**
     * Create a new message instance.
     */
    public function __construct($user, $lpj)
    {
        $this->user = $user;
        $this->lpj  = $lpj;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Verifikasi Lpj',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.lpj.verifikasi-lpj',
            with: [
                'userData' => $this->user,
                'lpj'      => $this->lpj,
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

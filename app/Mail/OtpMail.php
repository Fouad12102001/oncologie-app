<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $email;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, string $email)
    {
        $this->code = $code;
        $this->email = $email;
    }

    /**
     * Subject email
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Code de réinitialisation - CLCC Oncologie',
        );
    }

    /**
     * Email content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'code' => $this->code,
                'email' => $this->email,
            ]
        );
    }

    /**
     * Attachments (none)
     */
    public function attachments(): array
    {
        return [];
    }
}
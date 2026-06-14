<?php

namespace App\Models\Oncologie;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OtpMail extends Mailable
{
    public $code;
    public $email;

    public function __construct($code, $email)
    {
        $this->code = $code;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de réinitialisation du mot de passe'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'oncologie.emails.otp',
            with: [
                'code' => $this->code,
                'email' => $this->email,
            ]
        );
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $token) {}

    public function build()
    {
        return $this->subject('あなたの確認コード')
            ->markdown('emails.verify_token', ['token' => $this->token]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $token;
    public $resetUrl;

    public function __construct($token, $resetUrl)
    {
        $this->token = $token;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Password Reset')
                    ->text('emails.password_reset')
                    ->with([
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
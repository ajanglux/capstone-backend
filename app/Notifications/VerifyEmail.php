<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the verification mail message.
     */
    public function toMail($notifiable)
    {
        $url = URL::signedRoute('verification.verify', [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification())
        ]);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Click the button below to verify your email.')
            ->action('Verify Email', $url)
            ->line('If you did not create an account, no further action is required.');
    }
}
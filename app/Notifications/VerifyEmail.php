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
        // Use $notifiable instead of $user
        $url = URL::signedRoute('verification.verify', [
            'id' => $notifiable->getKey(), // Get the user's ID
            'hash' => sha1($notifiable->getEmailForVerification()) // Generate the email hash
        ]);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Click the button below to verify your email.')
            ->action('Verify Email', $url)  // Ensure it uses the correct API route
            ->line('If you did not create an account, no further action is required.');
    }
}
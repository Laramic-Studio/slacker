<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return parent::via($notifiable);
    }

    public function toMail($notifiable)
    {
        // return parent::toMail($notifiable);
        return (new MailMessage)
            ->subject("Email verification")
            ->markdown('emails.auth.verify-email', [
                'user' => $notifiable
            ]);
    }


    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

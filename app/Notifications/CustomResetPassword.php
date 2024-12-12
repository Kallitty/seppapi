<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    

    public function toMail($notifiable)
{
    $frontendUrl = config('app.frontend_url');
    $actionUrl = $frontendUrl . '/password/reset/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

    // \Log::info('Generated password reset URL: ' . $actionUrl);

    return (new MailMessage)
               ->subject('Your Password Reset Request')
               ->line('You are receiving this email because we received a password reset request for your account.')
               ->action('Reset Password', $actionUrl)
               ->line('If you did not request a password reset, no further action is required.');
}

}

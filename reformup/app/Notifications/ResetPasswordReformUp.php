<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordReformUp extends Notification
{
    use Queueable;

    public $token;

    /**
     * El token de reseteo
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Canales de notificación
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Contenido del email
     */
    public function toMail($notifiable)
    {
        // URL que va en el botón (usa tu ruta password.reset)
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecer tu contraseña en ReformUp')
            ->greeting('Hola ' . ($notifiable->nombre ?? ''))
            ->line('Has recibido este correo porque se ha solicitado un restablecimiento de contraseña para tu cuenta en ReformUp.')
            ->line('Si has sido tú, haz clic en el siguiente botón para elegir una nueva contraseña.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace caducará en 60 minutos.')
            ->line('Si tú no has solicitado este cambio, simplemente ignora este correo y tu contraseña seguirá siendo la misma.')
            ->salutation('Un saludo, 
            ReformUp');
    }
}

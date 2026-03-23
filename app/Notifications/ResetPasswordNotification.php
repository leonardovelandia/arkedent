<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Construye el correo de recuperación en español.
     * Usa config('mail.from.name') que AppServiceProvider ya sincronizó
     * con el nombre_consultorio de la BD — así remitente y contenido siempre coinciden.
     */
    public function toMail($notifiable): MailMessage
    {
        // AppServiceProvider establece mail.from.name desde la BD en cada request.
        // Usamos el mismo valor para que remitente, asunto y saludo sean idénticos.
        $nombre = config('mail.from.name', config('app.name'));
        $url    = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Recuperación de contraseña — ' . $nombre)
            ->greeting('¡Hola!')
            ->line('Recibiste este correo porque se solicitó restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.users.expire', 60) . ' minutos.')
            ->line('Si no solicitaste este cambio, no es necesario que hagas nada.')
            ->salutation('Saludos,  ' . PHP_EOL . $nombre);
    }
}

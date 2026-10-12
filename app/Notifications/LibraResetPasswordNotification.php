<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LibraResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Halaman reset password kita sendiri (blade), bukan route default Laravel.
        $url = url(route('reset-password.view', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Permintaan Reset Kata Sandi - LIBRA')
            ->greeting('Halo, ' . ($notifiable->nama_lengkap ?? $notifiable->username ?? 'Pengguna LIBRA') . '!')
            ->line('Kami menerima permintaan untuk mereset kata sandi akun LIBRA Anda.')
            ->action('Reset Kata Sandi', $url)
            ->line('Tautan reset kata sandi ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak merasa meminta reset kata sandi, abaikan saja email ini — kata sandi Anda tidak akan berubah.');
    }
}
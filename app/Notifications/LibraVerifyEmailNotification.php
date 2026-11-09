<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class LibraVerifyEmailNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Tautan verifikasi bertanda tangan (signed URL) ke halaman web LIBRA.
     * Hash memakai email saat ini, jadi kalau email diganti, tautan lama
     * otomatis tidak berlaku.
     */
    public function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Akun LIBRA')
            ->greeting('Halo, ' . ($notifiable->nama_lengkap ?? 'Pengguna LIBRA') . '!')
            ->line('Terima kasih telah mendaftar di LIBRA. Klik tombol di bawah untuk memastikan alamat email ini benar dan mengaktifkan akun Anda.')
            ->action('Verifikasi Email', $this->verificationUrl($notifiable))
            ->line('Tautan ini berlaku selama ' . (int) config('auth.verification.expire', 60) . ' menit.')
            ->line('Jika Anda tidak merasa mendaftar di LIBRA, abaikan saja email ini.');
    }
}
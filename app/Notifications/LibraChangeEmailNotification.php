<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Dikirim ke email BARU saat pengguna mengganti email dari halaman Profil.
 * Email di akun baru berubah setelah tautan ini diklik.
 */
class LibraChangeEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $userId,
        private string $newEmail,
        private string $nama = ''
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $minutes = (int) config('auth.verification.expire', 60);

        // Hash memakai email baru, jadi tautan hanya berlaku untuk email ini.
        $url = URL::temporarySignedRoute(
            'email.change.verify',
            now()->addMinutes($minutes),
            ['id' => $this->userId, 'hash' => sha1($this->newEmail)]
        );

        return (new MailMessage)
            ->subject('Verifikasi Email Baru Akun LIBRA')
            ->greeting('Halo, ' . ($this->nama !== '' ? $this->nama : 'Pengguna LIBRA') . '!')
            ->line('Kami menerima permintaan untuk mengganti email akun LIBRA Anda ke alamat ini. Klik tombol di bawah untuk mengonfirmasi.')
            ->action('Konfirmasi Email Baru', $url)
            ->line('Tautan ini berlaku selama ' . $minutes . ' menit. Email lama tetap dipakai sampai Anda mengonfirmasi.')
            ->line('Jika Anda tidak merasa meminta perubahan ini, abaikan saja email ini.');
    }
}
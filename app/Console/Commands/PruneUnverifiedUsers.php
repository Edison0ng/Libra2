<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Hapus akun mahasiswa yang mendaftar lewat form tetapi tidak pernah
 * memverifikasi email dalam N hari, supaya NIM & email-nya bisa dipakai
 * mendaftar lagi.
 *
 * Contoh:
 *   php artisan users:prune-unverified --dry-run   (lihat dulu, tidak menghapus)
 *   php artisan users:prune-unverified --days=7
 */
class PruneUnverifiedUsers extends Command
{
    protected $signature = 'users:prune-unverified
                            {--days=3 : Hapus akun yang belum terverifikasi dan lebih tua dari N hari}
                            {--dry-run : Hanya tampilkan akun yang akan dihapus}';

    protected $description = 'Hapus akun mahasiswa yang tidak pernah memverifikasi email';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        // Hanya akun hasil pendaftaran mandiri: mahasiswa, punya email DAN
        // password. Akun yang dibuat admin (tanpa email/password) dan akun
        // admin/kurir sengaja tidak disentuh.
        $query = User::query()
            ->where('role', 'mahasiswa')
            ->whereNull('email_verified_at')
            ->whereNotNull('email')
            ->whereNotNull('password')
            ->where('created_at', '<', now()->subDays($days));

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('Tidak ada akun belum terverifikasi yang perlu dihapus.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("[dry-run] {$count} akun akan dihapus (belum verifikasi > {$days} hari):");
            $this->table(
                ['NIM', 'Nama', 'Email', 'Terdaftar'],
                (clone $query)->get(['nim', 'nama_lengkap', 'email', 'created_at'])
                    ->map(fn ($u) => [$u->nim, $u->nama_lengkap, $u->email, (string) $u->created_at])
                    ->all()
            );
            return self::SUCCESS;
        }

        $deleted = $query->delete();

        Log::info("users:prune-unverified menghapus {$deleted} akun belum terverifikasi (> {$days} hari).");
        $this->info("{$deleted} akun belum terverifikasi dihapus.");

        return self::SUCCESS;
    }
}
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan akun mahasiswa yang tidak pernah verifikasi email (> 3 hari).
// Butuh scheduler berjalan: `php artisan schedule:work` (lokal) atau cron
// `* * * * * php artisan schedule:run` (server).
Schedule::command('users:prune-unverified --days=3')->daily();
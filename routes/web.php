<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama default diarahkan ke login
Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/login', function () {
    return view('login');
})->name('login.view');

Route::get('/register', function () {
    return view('register');
})->name('register');

// Halaman form "Lupa Password" (input NIM + email)
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');

// Halaman form set password baru, dibuka dari link di email.
// Contoh: /reset-password/{token}?email=user@example.com
Route::get('/reset-password/{token}', function (string $token) {
    return view('reset-password', [
        'token' => $token,
        'email' => request()->query('email', ''),
    ]);
})->name('reset-password.view');

// Tautan verifikasi email dari email pendaftaran (signed URL, kedaluwarsa 60 menit).
Route::get('/verify-email/{id}/{hash}', function (\Illuminate\Http\Request $request, string $id, string $hash) {
    if (! $request->hasValidSignature()) {
        return view('verify-email', ['status' => 'expired']);
    }

    $user = \App\Models\User::find($id);
    if (! $user || ! hash_equals(sha1((string) $user->getEmailForVerification()), $hash)) {
        return view('verify-email', ['status' => 'invalid']);
    }

    if ($user->hasVerifiedEmail()) {
        return view('verify-email', ['status' => 'already']);
    }

    $user->markEmailAsVerified();
    return view('verify-email', ['status' => 'success']);
})->name('verification.verify');

// Route untuk halaman libra (menampilkan daftar buku)
// Route untuk entitas Kurir
Route::get('/kurir', function () {
    return view('kurir');
})->name('kurir');

Route::get('/libra', [BookController::class, 'index']);

// Fallback route untuk serving static script jika di-proxy lewat Laravel di serverless/Vercel
Route::get('/js/auth-fetch.js', function () {
    $path = public_path('js/auth-fetch.js');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
    return response('// auth-fetch script not found', 404, ['Content-Type' => 'application/javascript']);
});

// Route untuk user profile dengan username/NIM
Route::get('/{username}', [UserController::class, 'showByUsername'])
    ->where('username', '[a-zA-Z0-9\s\-]+'); // Izinkan huruf, angka, spasi, dan dash
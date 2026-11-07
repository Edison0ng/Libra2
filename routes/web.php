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

// Route untuk halaman libra (menampilkan daftar buku)
// Route untuk entitas Kurir
Route::get('/kurir', function () {
    return view('kurir');
})->name('kurir');

Route::get('/libra', [BookController::class, 'index']);

// Route untuk user profile dengan username/NIM
Route::get('/{username}', [UserController::class, 'showByUsername'])
    ->where('username', '[a-zA-Z0-9\s\-]+'); // Izinkan huruf, angka, spasi, dan dash
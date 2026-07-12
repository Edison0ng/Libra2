<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('login');
// ===================== HALAMAN LOGIN & REGISTER (LIBRA) =====================
// Sengaja dibuat sebagai view terpisah (login.blade.php & register.blade.php),
// BUKAN welcome.blade.php, supaya tidak bentrok (merge conflict) dengan
// welcome.blade.php milik branch/teman lain saat digabung ke main.

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

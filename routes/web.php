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

// Route untuk halaman libra (menampilkan daftar buku)
Route::get('/libra', [BookController::class, 'index']);

// Route untuk user profile dengan username/NIM
Route::get('/{username}', [UserController::class, 'showByUsername'])
    ->where('username', '[a-zA-Z0-9\s\-]+'); // Izinkan huruf, angka, spasi, dan dash

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Api\UserController;

// Route untuk halaman utama
Route::get('/', [BookController::class, 'index']);

// Route untuk halaman libra
Route::get('/libra', [BookController::class, 'index']);

// Route untuk user profile dengan username
Route::get('/{username}', [UserController::class, 'showByUsername'])
    ->where('username', '[a-zA-Z0-9\s\-]+'); // Izinkan huruf, angka, spasi, dan dash
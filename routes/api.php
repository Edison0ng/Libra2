<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - LIBRA
|--------------------------------------------------------------------------
| Tambahkan baris di bawah ini ke routes/api.php pada project Laravel Anda
| (gabungkan dengan route lain yang sudah ada, jangan menimpa file).
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

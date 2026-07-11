<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


// ===================== RUTE PUBLIK =====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ===================== RUTE TERPROTEKSI (butuh token Sanctum) =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
});

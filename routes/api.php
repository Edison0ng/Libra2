<?php

// Update API routes untuk deployment Railway

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route di file ini otomatis memiliki prefix /api
| Contoh:
| /api/login
| /api/register
|
*/

// ===================== TEST BACKEND =====================
Route::get('/test', function () {
    return response()->json([
        'status' => 'Backend Laravel berhasil berjalan',
        'message' => 'API siap digunakan'
    ]);
});


// ===================== RUTE PUBLIK =====================

// Register user baru
Route::post('/register', [AuthController::class, 'register']);

// Login user
Route::post('/login', [AuthController::class, 'login']);


// ===================== RUTE TERPROTEKSI =====================
// Membutuhkan token Laravel Sanctum

Route::middleware('auth:sanctum')->group(function () {

    // Logout user
    Route::post('/logout', [AuthController::class, 'logout']);

    // Mendapatkan data user yang sedang login
    Route::get('/user', [AuthController::class, 'me']);

});
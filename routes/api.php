<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PinjamController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
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

// ===================== NOTIFIKASI =====================
// Route spesifik harus didaftarkan sebelum resource route lain agar tidak bentrok.
Route::get('/notifications', [NotificationController::class, 'index']);
Route::patch('/notifications/read-all', [NotificationController::class, 'readAll']);

// API Resources dari backend-pengguna-v2 & backend-admin-v2
Route::apiResource('books', BookController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('pinjam', PinjamController::class);
Route::apiResource('peminjams', PinjamController::class); // Alias untuk kompatibilitas admin-v2
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('complaints', ComplaintController::class);
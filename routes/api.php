<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PinjamController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProdukAtkController;
use App\Http\Controllers\Api\PembelianAtkController;
use App\Http\Controllers\Api\CourierOrderController;
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

// Lupa password: kirim link reset ke email
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Reset password: submit token + password baru
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ===================== DATA REFERENSI PUBLIK =====================
// Katalog buku, detail buku, produk ATK, dan status buku dipinjam dapat diakses tanpa wajib login
Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::get('produk-atk', [ProdukAtkController::class, 'index']);
Route::get('pinjam-aktif', [PinjamController::class, 'bukuSedangDipinjam']);


// ===================== RUTE TERPROTEKSI =====================
// Semua di bawah ini wajib login (token Laravel Sanctum).
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // ---------- Pengguna biasa (dibatasi ke data miliknya sendiri) ----------
    Route::middleware('owner')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/read-all', [NotificationController::class, 'readAll']);

        Route::get('/wishlist/queue-info', [WishlistController::class, 'queueInfo']);
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist', [WishlistController::class, 'destroy']);

        Route::post('pinjam/{id}/bayar-denda', [PinjamController::class, 'bayarDenda']);
        Route::get('pinjam', [PinjamController::class, 'index']);
        Route::post('pinjam', [PinjamController::class, 'store']);
        Route::get('pinjam/{pinjam}', [PinjamController::class, 'show']);

        Route::post('/pembelian-atk/{id}/bayar', [PembelianAtkController::class, 'bayar']);
        Route::get('pembelian-atk', [PembelianAtkController::class, 'index']);
        Route::post('pembelian-atk', [PembelianAtkController::class, 'store']);

        Route::post('complaints', [ComplaintController::class, 'store']);
        Route::get('/orders', [CourierOrderController::class, 'index']);
        Route::post('/orders', [CourierOrderController::class, 'store']);
    });

    // ---------- Kurir & admin ----------
    Route::middleware('role:admin,kurir')->group(function () {
        Route::patch('/orders/{id}/start', [CourierOrderController::class, 'startDelivery']);
        Route::patch('/orders/{id}/complete', [CourierOrderController::class, 'completeDelivery']);
    });

    // ---------- Khusus admin ----------
    Route::middleware('role:admin')->group(function () {
        Route::get('/wishlist/active-holds', [WishlistController::class, 'activeHolds']);
        Route::post('/wishlist/force-release', [WishlistController::class, 'forceRelease']);

        Route::apiResource('books', BookController::class)->except(['index', 'show']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('pinjam', PinjamController::class)->only(['update', 'destroy']);
        Route::apiResource('peminjams', PinjamController::class); // alias admin-v2
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('complaints', ComplaintController::class)->except(['store']);

        Route::post('/produk-atk/{id}/gambar', [ProdukAtkController::class, 'uploadGambar']);
        Route::delete('/produk-atk/{id}/gambar', [ProdukAtkController::class, 'hapusGambar']);
        Route::apiResource('produk-atk', ProdukAtkController::class)->except(['index', 'show']);
        Route::apiResource('pembelian-atk', PembelianAtkController::class)->only(['update', 'destroy']);
    });
});
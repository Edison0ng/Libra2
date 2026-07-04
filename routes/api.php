<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PinjamController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ComplaintController;

Route::apiResource('books', BookController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('pinjam', PinjamController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('complaints', ComplaintController::class);
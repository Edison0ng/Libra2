<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Mengarahkan halaman utama pasca login ke DashboardController
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
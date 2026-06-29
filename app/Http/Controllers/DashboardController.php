<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Sementara return view statis. Bagian ini yang nantinya akan diisi oleh teman Anda 
        // dengan query database (misal: mengambil data buku, wishlist, dsb.)
        return view('dashboard');
    }
}
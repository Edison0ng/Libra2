<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
{
    $books = \App\Models\Book::all(); // Mengambil semua data dari database

    // Daftar genre unik (buang yg kosong/null), diurutkan abjad, utk dropdown filter.
    $genres = $books->pluck('genre')->filter()->unique()->sort()->values();

    // Skor popularitas tiap buku (ISBN => jumlah), dipakai untuk rekomendasi di beranda.
    // Dihitung dari peminjaman yang tidak dibatalkan/ditolak, ditambah jumlah wishlist/antrian.
    $popularitas = [];
    try {
        $pinjam = \App\Models\Pinjam::selectRaw('book_id, count(*) as total')
            ->where('status', 'not like', '%Batal%')
            ->where('status', 'not like', '%Ditolak%')
            ->groupBy('book_id')
            ->pluck('total', 'book_id');
        $wishlist = \App\Models\Wishlist::selectRaw('book_id, count(*) as total')
            ->groupBy('book_id')
            ->pluck('total', 'book_id');

        foreach ($pinjam as $bookId => $total) {
            $popularitas[(string) $bookId] = (int) $total;
        }
        foreach ($wishlist as $bookId => $total) {
            $popularitas[(string) $bookId] = ($popularitas[(string) $bookId] ?? 0) + (int) $total;
        }
    } catch (\Throwable $e) {
        // Kalau gagal, rekomendasi tetap jalan tanpa skor popularitas.
        $popularitas = [];
    }

    return view('libra', compact('books', 'genres', 'popularitas')); // Mengirim data ke view
}
}
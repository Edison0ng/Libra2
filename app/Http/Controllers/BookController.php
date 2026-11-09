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

    return view('libra', compact('books', 'genres')); // Mengirim data ke view
}
}
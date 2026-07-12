<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
{
    $books = \App\Models\Book::all(); // Mengambil semua data dari database
    return view('libra', compact('books')); // Mengirim data ke view
}
}

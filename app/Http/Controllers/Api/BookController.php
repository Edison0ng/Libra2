<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return response()->json(Book::all());
    }

    public function store(Request $request)
    {
        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Book::findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $book->update($request->all());

        return response()->json($book);
    }

    public function destroy(string $id)
    {
        Book::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Book deleted'
        ]);
    }
}
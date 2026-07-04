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

    public function show($id)
    {
        $book = Book::find($id);
        if (! $book) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($book);
    }

    // Create/Update/Delete not implemented to avoid accidental writes
    public function store(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }
}

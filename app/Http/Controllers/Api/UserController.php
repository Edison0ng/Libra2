<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);
        if (! $user) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($user);
    }

    public function showByUsername($username)
    {
        // Handle username dengan spasi atau dash
        $username = str_replace('-', ' ', $username);
        
        // Cari user berdasarkan username (case insensitive) ATAU NIM
        $user = User::where(function ($query) use ($username) {
            $query->whereRaw('LOWER(username) = ?', [strtolower($username)])
                  ->orWhere('nim', $username);
        })->first();

        if (! $user) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        $books = DB::table('books')->get();

        return view('libra', [
            'userData' => $user,
            'books' => $books
        ]);
    }

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
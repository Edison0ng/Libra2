<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PinjamController extends Controller
{
    public function index()
        {
            // Arahkan langsung ke tabel 'pinjam' sesuai database kamu
            $table = 'pinjam'; 

            if (!Schema::hasTable($table)) {
                return response()->json(['message' => 'Table not found'], 404);
            }

            $loans = DB::table($table.' as l')
                // Tambahkan ::text untuk mengonversi UUID ke text agar cocok dengan user_id
                ->leftJoin('users', DB::raw('l.user_id::text'), '=', DB::raw('users.id::text'))
                ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
                ->select(
                    'l.*', 
                    'users.nama_lengkap as user_name', 
                    DB::raw('books."Book-Title" as book_title')
                    )
                ->get();

            return response()->json($loans);
        }

    public function show($id)
    {
        $table = null;
        if (Schema::hasTable('denda')) {
            $table = 'denda';
        } elseif (Schema::hasTable('loans')) {
            $table = 'loans';
        }

        if (! $table) return response()->json(['message' => 'Not Found'], 404);

        $loan = DB::table($table)->where('id', $id)->first();
        if (! $loan) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($loan);
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

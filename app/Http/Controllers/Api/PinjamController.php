<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PinjamController extends Controller
{
    public function index(Request $request)
    {
        $table = 'pinjam'; 

        if (!Schema::hasTable($table)) {
            return response()->json(['message' => 'Table not found'], 404);
        }

        $query = DB::table($table.' as l')
            ->leftJoin('users', DB::raw('l.user_id::text'), '=', DB::raw('users.id::text'))
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*', 
                'users.nama_lengkap as user_name', 
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            );

        // Filter berdasarkan user_id jika dikirimkan oleh frontend
        if ($request->has('user_id')) {
            $query->where('l.user_id', $request->query('user_id'));
        }

        $loans = $query->orderBy('l.tanggal_pinjam', 'desc')->get();

        return response()->json($loans);
    }

    public function show($id)
    {
        $loan = DB::table('pinjam as l')
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*',
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            )
            ->where('l.id', $id)
            ->first();

        if (! $loan) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($loan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'book_id' => ['required', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tenggat_waktu' => ['required', 'date'],
            'status' => ['required', 'string'],
        ]);

        // Generate UUID untuk ID peminjaman jika belum ada
        $id = $request->input('id') ?: (string) Str::uuid();

        DB::table('pinjam')->insert([
            'id' => $id,
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => $request->status,
            'tanggal_kembali' => $request->tanggal_kembali ?: null,
            'denda' => $request->denda ?: 0
        ]);

        $newLoan = DB::table('pinjam as l')
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*',
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            )
            ->where('l.id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disimpan!',
            'loan' => $newLoan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['sometimes', 'string'],
            'tanggal_kembali' => ['sometimes', 'nullable', 'date'],
            'denda' => ['sometimes', 'integer'],
        ]);

        $data = $request->only(['status', 'tanggal_kembali', 'denda']);
        
        DB::table('pinjam')->where('id', $id)->update($data);

        $loan = DB::table('pinjam')->where('id', $id)->first();

        return response()->json($loan);
    }

    public function destroy($id)
    {
        DB::table('pinjam')->where('id', $id)->delete();

        return response()->json([
            'message' => 'Pinjam deleted'
        ]);
    }
}

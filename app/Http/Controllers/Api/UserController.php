<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // PATCH /api/profil/alamat
    // Pengguna memperbarui alamat pengantarannya sendiri (mis. pindah rumah/kos).
    // Hanya mengubah akun yang sedang login; pesanan lama tidak berubah karena
    // alamat sudah disalin ke pesanan saat pesanan dibuat.
    public function updateAlamat(Request $request)
    {
        $data = $request->validate([
            'alamat_kirim' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alamat_kirim.required' => 'Alamat tidak boleh kosong.',
            'alamat_kirim.min'      => 'Alamat terlalu pendek, tulis alamat selengkapnya.',
            'alamat_kirim.max'      => 'Alamat maksimal 500 karakter.',
        ]);

        $user = $request->user();
        $user->alamat_kirim = trim($data['alamat_kirim']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui.',
            'user'    => $user,
        ]);
    }

    // GET /api/users
    public function index()
    {
        return response()->json(User::all());
    }

    // GET /api/users/{id}
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

        // Daftar genre unik (buang yg kosong/null), diurutkan abjad, utk dropdown filter.
        $genres = $books->pluck('genre')->filter()->unique()->sort()->values();

        return view('libra', [
            'books'    => $books,
            'genres'   => $genres,
        ]);
    }

    // POST /api/users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nim'           => 'required|string|max:50',
            'fakultas'      => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
            'alamat_kirim'  => 'nullable|string',
            'avatar_url'    => 'nullable|string'
        ]);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    // PUT /api/users/{id}
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'  => 'sometimes|required|string|max:255',
            'nim'           => 'sometimes|required|string|max:50',
            'fakultas'      => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
            'alamat_kirim'  => 'nullable|string',
            'avatar_url'    => 'nullable|string'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
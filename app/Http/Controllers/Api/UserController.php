<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        return response()->json(User::all());
    }

    // GET /api/users/{id}
    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

    // POST /api/users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'required|string|unique:users,id',
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
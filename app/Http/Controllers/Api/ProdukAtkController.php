<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukAtk;
use Illuminate\Http\Request;

class ProdukAtkController extends Controller
{
    public function index()
    {
        return response()->json(ProdukAtk::orderBy('nama')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'     => ['required', 'string', 'max:50', 'unique:produk_atk,kode'],
            'nama'     => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'harga'    => ['required', 'integer', 'min:0'],
            'stok'     => ['required', 'integer', 'min:0'],
        ]);

        $produk = ProdukAtk::create($data);

        return response()->json($produk, 201);
    }

    public function update(Request $request, string $id)
    {
        $produk = ProdukAtk::find($id);
        if (! $produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'kode'     => ['sometimes', 'required', 'string', 'max:50', 'unique:produk_atk,kode,' . $produk->id],
            'nama'     => ['sometimes', 'required', 'string', 'max:255'],
            'kategori' => ['sometimes', 'nullable', 'string', 'max:100'],
            'harga'    => ['sometimes', 'required', 'integer', 'min:0'],
            'stok'     => ['sometimes', 'required', 'integer', 'min:0'],
        ]);

        $produk->update($data);

        return response()->json($produk);
    }

    public function destroy(string $id)
    {
        $produk = ProdukAtk::find($id);
        if (! $produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->delete();

        return response()->json(['success' => true]);
    }
}
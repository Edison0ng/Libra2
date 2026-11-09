<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukAtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'gambar'   => ['nullable', 'string'],
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
            'gambar'   => ['nullable', 'string'],
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

        $this->deleteGambarFile($produk->gambar);
        $produk->delete();

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/produk-atk/{id}/gambar  (multipart, field: "gambar")
     * KHUSUS ADMIN: upload/ganti gambar produk ke Supabase Storage.
     * URL publik disimpan ke kolom `gambar`.
     */
    public function uploadGambar(Request $request, string $id)
    {
        $produk = ProdukAtk::find($id);
        if (! $produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            // Maks 2 MB, maks 1920x1920 px
            'gambar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=1920,max_height=1920'],
        ], [
            'gambar.max'        => 'Ukuran gambar maksimal 2 MB.',
            'gambar.dimensions' => 'Resolusi gambar maksimal 1920 x 1920 piksel.',
            'gambar.mimes'      => 'Format gambar harus JPG, PNG, atau WEBP.',
        ]);

        $file = $request->file('gambar');
        $filename = $produk->id . '-' . time() . '.' . strtolower($file->getClientOriginalExtension());

        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.key');

        if ($supabaseUrl && $supabaseKey) {
            try {
                $publicUrl = $this->uploadToSupabase($file, $filename);
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal upload gambar ke Supabase Storage: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Fallback ke penyimpanan lokal jika kredensial Supabase belum diatur
            $dir = public_path('uploads/produk-atk');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $publicUrl = '/uploads/produk-atk/' . $filename;
        }

        $this->deleteGambarFile($produk->gambar);
        $produk->update(['gambar' => $publicUrl]);

        return response()->json($produk);
    }

    /**
     * DELETE /api/produk-atk/{id}/gambar -- KHUSUS ADMIN: hapus gambar produk.
     */
    public function hapusGambar(string $id)
    {
        $produk = ProdukAtk::find($id);
        if (! $produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $this->deleteGambarFile($produk->gambar);
        $produk->update(['gambar' => null]);

        return response()->json($produk);
    }

    /**
     * Upload file ke Supabase Storage via REST API dan return Public URL
     */
    private function uploadToSupabase($file, string $filename): string
    {
        $supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $supabaseKey = config('services.supabase.key');
        $bucket      = config('services.supabase.bucket', 'produk-atk');

        $endpoint = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}";
        $mimeType = $file->getMimeType() ?: 'image/jpeg';

        $fileContent = file_get_contents($file->getRealPath());

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $supabaseKey,
            'apiKey'        => $supabaseKey,
            'Content-Type'  => $mimeType,
            'x-upsert'      => 'true',
        ])->withBody($fileContent, $mimeType)
          ->post($endpoint);

        if (! $response->successful()) {
            $errBody = $response->json();
            $msg = is_array($errBody) ? ($errBody['message'] ?? $errBody['error'] ?? $response->body()) : $response->body();
            Log::error('Supabase Storage upload error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \RuntimeException($msg);
        }

        return "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
    }

    /**
     * Hapus file gambar baik dari Supabase Storage maupun file lokal lama
     */
    private function deleteGambarFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $supabaseUrl = rtrim(config('services.supabase.url', ''), '/');
        $supabaseKey = config('services.supabase.key');
        $bucket      = config('services.supabase.bucket', 'produk-atk');

        // Jika path adalah URL dari Supabase Storage
        $prefix = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/";
        if ($supabaseUrl && str_starts_with($path, $prefix)) {
            $filename = substr($path, strlen($prefix));
            if ($filename && $supabaseKey) {
                try {
                    Http::withHeaders([
                        'Authorization' => 'Bearer ' . $supabaseKey,
                        'apiKey'        => $supabaseKey,
                        'Content-Type'  => 'application/json',
                    ])->delete("{$supabaseUrl}/storage/v1/object/{$bucket}", [
                        'prefixes' => [$filename],
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Gagal menghapus gambar dari Supabase Storage: ' . $e->getMessage());
                }
            }
            return;
        }

        // Jika path lokal lama (/uploads/produk-atk/...)
        if (str_starts_with($path, '/uploads/produk-atk/')) {
            $full = public_path(ltrim($path, '/'));
            if (is_file($full)) {
                @unlink($full);
            }
        }
    }
}

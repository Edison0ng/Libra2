<?php

namespace Database\Seeders;

use App\Models\ProdukAtk;
use Illuminate\Database\Seeder;

class ProdukAtkSeeder extends Seeder
{
    public function run(): void
    {
        $url = rtrim(config('services.supabase.url') ?: env('SUPABASE_URL', 'https://fntkcqdqbarffqyqtqdo.supabase.co'), '/');
        $bucket = config('services.supabase.bucket') ?: env('SUPABASE_STORAGE_BUCKET', 'produk-atk');
        $base = "{$url}/storage/v1/object/public/{$bucket}";

        $produk = [
            ['kode' => 'ATK-001', 'nama' => 'Pulpen Hitam',          'kategori' => 'Alat Tulis', 'harga' => 3000,  'stok' => 100, 'gambar' => "{$base}/images%20%289%29.jpg"],
            ['kode' => 'ATK-002', 'nama' => 'Pulpen Biru',           'kategori' => 'Alat Tulis', 'harga' => 3000,  'stok' => 100, 'gambar' => "{$base}/images%20%288%29.jpg"],
            ['kode' => 'ATK-003', 'nama' => 'Pensil 2B',             'kategori' => 'Alat Tulis', 'harga' => 2500,  'stok' => 80,  'gambar' => "{$base}/images%20%287%29.jpg"],
            ['kode' => 'ATK-004', 'nama' => 'Penghapus',             'kategori' => 'Alat Tulis', 'harga' => 2000,  'stok' => 60,  'gambar' => "{$base}/images%20%286%29.jpg"],
            ['kode' => 'ATK-005', 'nama' => 'Penggaris 30 cm',       'kategori' => 'Alat Tulis', 'harga' => 4000,  'stok' => 50,  'gambar' => "{$base}/images%20%285%29.jpg"],
            ['kode' => 'ATK-006', 'nama' => 'Buku Tulis 38 Lembar',  'kategori' => 'Kertas',     'harga' => 5000,  'stok' => 120, 'gambar' => "{$base}/images%20%281%29.jpg"],
            ['kode' => 'ATK-007', 'nama' => 'Kertas HVS A4 (rim)',   'kategori' => 'Kertas',     'harga' => 55000, 'stok' => 30,  'gambar' => "{$base}/images%20%283%29.jpg"],
            ['kode' => 'ATK-008', 'nama' => 'Map Plastik',           'kategori' => 'Perlengkapan', 'harga' => 3500, 'stok' => 70,  'gambar' => "{$base}/images%20%284%29.jpg"],
            ['kode' => 'ATK-009', 'nama' => 'Stapler Kecil',         'kategori' => 'Perlengkapan', 'harga' => 15000, 'stok' => 25,  'gambar' => "{$base}/images%20%2810%29.jpg"],
            ['kode' => 'ATK-010', 'nama' => 'Highlighter',           'kategori' => 'Alat Tulis', 'harga' => 6000,  'stok' => 40,  'gambar' => "{$base}/images%20%282%29.jpg"],
        ];

        foreach ($produk as $p) {
            ProdukAtk::updateOrCreate(['kode' => $p['kode']], $p);
        }
    }
}

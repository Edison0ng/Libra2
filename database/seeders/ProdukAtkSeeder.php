<?php

namespace Database\Seeders;

use App\Models\ProdukAtk;
use Illuminate\Database\Seeder;

class ProdukAtkSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            ['kode' => 'ATK-001', 'nama' => 'Pulpen Hitam',          'kategori' => 'Alat Tulis', 'harga' => 3000,  'stok' => 100],
            ['kode' => 'ATK-002', 'nama' => 'Pulpen Biru',           'kategori' => 'Alat Tulis', 'harga' => 3000,  'stok' => 100],
            ['kode' => 'ATK-003', 'nama' => 'Pensil 2B',             'kategori' => 'Alat Tulis', 'harga' => 2500,  'stok' => 80],
            ['kode' => 'ATK-004', 'nama' => 'Penghapus',             'kategori' => 'Alat Tulis', 'harga' => 2000,  'stok' => 60],
            ['kode' => 'ATK-005', 'nama' => 'Penggaris 30 cm',       'kategori' => 'Alat Tulis', 'harga' => 4000,  'stok' => 50],
            ['kode' => 'ATK-006', 'nama' => 'Buku Tulis 38 Lembar',  'kategori' => 'Kertas',     'harga' => 5000,  'stok' => 120],
            ['kode' => 'ATK-007', 'nama' => 'Kertas HVS A4 (rim)',   'kategori' => 'Kertas',     'harga' => 55000, 'stok' => 30],
            ['kode' => 'ATK-008', 'nama' => 'Map Plastik',           'kategori' => 'Perlengkapan', 'harga' => 3500, 'stok' => 70],
            ['kode' => 'ATK-009', 'nama' => 'Stapler Kecil',         'kategori' => 'Perlengkapan', 'harga' => 15000, 'stok' => 25],
            ['kode' => 'ATK-010', 'nama' => 'Highlighter',           'kategori' => 'Alat Tulis', 'harga' => 6000,  'stok' => 40],
        ];

        foreach ($produk as $p) {
            // updateOrCreate berdasarkan kode agar aman dijalankan berulang kali
            ProdukAtk::updateOrCreate(['kode' => $p['kode']], $p);
        }
    }
}
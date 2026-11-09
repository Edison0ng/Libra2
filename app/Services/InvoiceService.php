<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Mencatat invoice otomatis dari transaksi nyata.
 * Idempoten (jenis + referensi_id unik) dan TIDAK PERNAH menggagalkan
 * proses utama: error hanya ditulis ke log.
 */
class InvoiceService
{
    public static function catat(string $jenis, string $referensiId, $userId, string $deskripsi, int $jumlah, string $status, ?string $metode = null, array $extra = []): void
    {
        try {
            if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices', 'jenis')) return;

            if (DB::table('invoices')->where('jenis', $jenis)->where('referensi_id', $referensiId)->exists()) return;

            $nama = null;
            if ($userId) {
                $nama = DB::table('users')->whereRaw('id::text = ?', [(string) $userId])->value('nama_lengkap');
            }

            $prefix = ['Peminjaman' => 'INV-PJM', 'Denda' => 'INV-DND', 'ATK' => 'INV-ATK'][$jenis] ?? 'INV';

            DB::table('invoices')->insert(array_merge([
                'nomor'         => $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(md5($jenis . $referensiId), 0, 5)),
                'jenis'         => $jenis,
                'referensi_id'  => $referensiId,
                'peminjam_id'   => (string) $userId,
                'nama_pengguna' => $nama,
                'deskripsi'     => $deskripsi,
                'jumlah'        => $jumlah,
                'status'        => $status,
                'metode_bayar'  => $metode,
                'created_at'    => now(),
                'updated_at'    => now(),
            ], $extra));
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat invoice: ' . $e->getMessage());
        }
    }
}
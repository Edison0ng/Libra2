<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourierOrderController extends Controller
{
    /**
     * GET /api/orders
     * Mengambil daftar semua pesanan pengantaran untuk kurir / pengguna.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $userId = $request->query('user_id');

        // Jika tabel kurir_orders ada di database, baca dari DB
        if (Schema::hasTable('courier_orders')) {
            $query = DB::table('courier_orders')->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $orders = $query->get()->map(function ($order) {
                if (isset($order->items) && is_string($order->items)) {
                    $order->items = json_decode($order->items, true);
                }
                return $order;
            });

            return response()->json($orders);
        }

        // Jika tabel belum ada di database, kembalikan respons kosong / format standar
        return response()->json([
            'success' => true,
            'message' => 'Tabel DB belum dimigrasi, frontend menggunakan sinkronisasi localStorage.',
            'data'    => []
        ]);
    }

    /**
     * POST /api/orders
     * Membuat pesanan pengantaran baru dari Pengguna (Buku atau ATK).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'         => 'nullable|string',
            'user_id'          => 'nullable|string',
            'user_name'        => 'required|string',
            'user_identity'    => 'nullable|string',
            'user_phone'       => 'nullable|string',
            'alamat'           => 'required|string',
            'items'            => 'required|array',
            'total_items'      => 'nullable|numeric',
            'total_pembayaran' => 'required|numeric',
            'status_bayar'     => 'nullable|string|max:50',
            'metode_bayar'     => 'nullable|string|max:50',
        ]);

        $orderId = $validated['order_id'] ?? ('ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4)));
        $now = now()->toISOString();

        $orderData = [
            'id'                       => $orderId,
            'order_id'                 => $orderId,
            'user_id'                  => $validated['user_id'] ?? null,
            'user_name'                => $validated['user_name'],
            'user_identity'            => $validated['user_identity'] ?? '-',
            'user_phone'               => $validated['user_phone'] ?? '-',
            'alamat'                   => $validated['alamat'],
            'items'                    => json_encode($validated['items']),
            'total_items'              => $validated['total_items'] ?? count($validated['items']),
            'total_pembayaran'         => $validated['total_pembayaran'],
            'status'                   => 'Menunggu',
            'waktu_pemesanan'          => $now,
            'waktu_mulai_pengantaran'   => null,
            'waktu_selesai_pengantaran' => null,
            'durasi_pengantaran_detik' => null,
            'total_durasi'             => null,
            'status_bayar'             => $validated['status_bayar'] ?? null,
            'metode_bayar'             => $validated['metode_bayar'] ?? null,
            'created_at'               => now(),
            'updated_at'               => now(),
        ];

        if (Schema::hasTable('courier_orders')) {
            // Kalau order dengan ID yang sama sudah ada (mis. dikirim ulang), jangan dobel.
            $exists = DB::table('courier_orders')->where('id', $orderId)->exists();
            if (!$exists) {
                // Hanya isi kolom yang benar-benar ada (aman kalau migration pembayaran belum dijalankan).
                $cols = array_flip(Schema::getColumnListing('courier_orders'));
                DB::table('courier_orders')->insert(array_intersect_key($orderData, $cols));
            }
        }

        $orderData['items'] = $validated['items'];

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat dan diteruskan ke Kurir.',
            'order'   => $orderData
        ], 201);
    }

    /**
     * PATCH /api/orders/{id}/start
     * Kurir menekan 'Kirim Sekarang': status berubah jadi 'Sedang Diantar' dan mencatat waktu mulai.
     */
    public function startDelivery(Request $request, $id)
    {
        $startTime = now()->toISOString();

        if (Schema::hasTable('courier_orders')) {
            $base = fn () => DB::table('courier_orders')->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('order_id', $id);
            });

            $order = $base()->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
            }

            // Hanya pesanan "Menunggu" yang boleh dikirim (cegah dua kurir mengambil pesanan yang sama).
            $updated = $base()->where('status', 'Menunggu')->update([
                'status'                  => 'Sedang Diantar',
                'waktu_mulai_pengantaran' => $startTime,
                'updated_at'              => now(),
            ]);

            if ($updated === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini sudah diambil kurir lain atau statusnya sudah berubah.',
                ], 409);
            }
        }

        $this->syncLinkedStatus($order->order_id ?? $id, 'Sedang Diantar Kurir');

        return response()->json([
            'success'                 => true,
            'message'                 => 'Pengantaran dimulai!',
            'status'                  => 'Sedang Diantar',
            'waktu_mulai_pengantaran' => $startTime,
        ]);
    }

    /**
     * PATCH /api/orders/{id}/complete
     * Kurir menekan 'Sudah Selesai': status jadi 'Selesai', catat waktu selesai & hitung durasi.
     */
    public function completeDelivery(Request $request, $id)
    {
        $endTime = now()->toISOString();
        $totalDurasi = $request->input('total_durasi', 'Selesai');
        $durasiDetik = (int) $request->input('durasi_detik', 0);

        if (Schema::hasTable('courier_orders')) {
            $base = fn () => DB::table('courier_orders')->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('order_id', $id);
            });

            $order = $base()->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
            }

            $updated = $base()->where('status', 'Sedang Diantar')->update([
                'status'                    => 'Selesai',
                'waktu_selesai_pengantaran' => $endTime,
                'total_durasi'              => $totalDurasi,
                'durasi_pengantaran_detik'  => $durasiDetik,
                'updated_at'                => now(),
            ]);

            if ($updated === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini belum sedang diantar atau sudah selesai.',
                ], 409);
            }

            // Pesanan ATK yang sampai: tandai selesai di sisi pembelian. Kalau pengguna memilih
            // "bayar di tempat", tunai yang diterima kurir di rumah pengguna = lunas.
            $pembelian = \App\Models\PembelianAtk::find($order->order_id ?: $order->id);
            if ($pembelian && !in_array($pembelian->status, ['Selesai', 'Dibatalkan'])) {
                $pembelian->status = 'Selesai';
                $pembelian->tanggal_selesai = now();
                if (Schema::hasColumn('pembelian_atk', 'status_bayar')
                    && $pembelian->status_bayar !== 'Lunas' && $pembelian->metode_bayar === 'Bayar di Tempat') {
                    $pembelian->status_bayar = 'Lunas';
                    $pembelian->dibayar_at = now();
                }
                $pembelian->save();
            }
            if (Schema::hasColumn('courier_orders', 'status_bayar') && ($order->metode_bayar ?? null) === 'Bayar di Tempat') {
                $base()->update(['status_bayar' => 'Lunas']);
            }
        }

        $this->syncLinkedStatus($order->order_id ?? $id, 'Dipinjam');

        return response()->json([
            'success'                   => true,
            'message'                   => 'Pengantaran telah selesai!',
            'status'                    => 'Selesai',
            'waktu_selesai_pengantaran' => $endTime,
            'total_durasi'              => $totalDurasi,
            'durasi_detik'              => $durasiDetik,
        ]);
    }

    /**
     * Samakan status pinjaman buku yang terhubung (id pesanan kurir = id pinjam)
     * dengan progres pengantaran. ATK ditangani terpisah (status pembelian).
     * Pesanan ATK: saat kurir mulai antar, status pembelian = 'Sedang Diantar Kurir'.
     */
    private function syncLinkedStatus($linkId, string $loanStatus): void
    {
        if (!$linkId) return;

        if (Schema::hasTable('pinjam')) {
            $q = DB::table('pinjam')->whereRaw('id::text = ?', [(string) $linkId])
                ->whereNull('tanggal_kembali')
                ->whereIn('status', ['Booking Dikonfirmasi Admin', 'Sedang Dikemas', 'Menunggu Kurir', 'Sedang Diantar Kurir', 'Diantar Kurir']);
            if ($q->update(['status' => $loanStatus]) > 0) {
                return;
            }
        }

        if ($loanStatus === 'Sedang Diantar Kurir') {
            $pembelian = \App\Models\PembelianAtk::find($linkId);
            if ($pembelian && !in_array($pembelian->status, ['Selesai', 'Dibatalkan'])) {
                $pembelian->status = 'Sedang Diantar Kurir';
                $pembelian->save();
            }
        }
    }
}
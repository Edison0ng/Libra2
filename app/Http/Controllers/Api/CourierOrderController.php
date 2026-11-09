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

        // Jika tabel belum ada di database, kembalikan array kosong
        return response()->json([]);
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
            'waktu_pemesanan'  => 'nullable|string',
            'status_bayar'     => 'nullable|string',
            'metode_bayar'     => 'nullable|string',
        ]);

        $orderId = $validated['order_id'] ?? ('ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4)));
        $now = now();
        $waktuPemesanan = $validated['waktu_pemesanan'] ?? $now->toISOString();

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
            'waktu_pemesanan'          => $waktuPemesanan,
            'waktu_mulai_pengantaran'   => null,
            'waktu_selesai_pengantaran' => null,
            'durasi_pengantaran_detik' => null,
            'total_durasi'             => null,
            'status_bayar'             => $validated['status_bayar'] ?? 'Lunas',
            'metode_bayar'             => $validated['metode_bayar'] ?? 'COD / Di Tempat',
            'created_at'               => $now,
            'updated_at'               => $now,
        ];

        if (Schema::hasTable('courier_orders')) {
            DB::table('courier_orders')->updateOrInsert(
                ['id' => $orderId],
                $orderData
            );
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
            DB::table('courier_orders')
                ->where('id', $id)
                ->orWhere('order_id', $id)
                ->update([
                    'status'                  => 'Sedang Diantar',
                    'waktu_mulai_pengantaran' => $startTime,
                    'updated_at'              => now(),
                ]);
        }

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
        $durasiDetik = $request->input('durasi_detik', 0);

        if (Schema::hasTable('courier_orders')) {
            DB::table('courier_orders')
                ->where('id', $id)
                ->orWhere('order_id', $id)
                ->update([
                    'status'                    => 'Selesai',
                    'waktu_selesai_pengantaran' => $endTime,
                    'total_durasi'              => $totalDurasi,
                    'durasi_pengantaran_detik'  => $durasiDetik,
                    'updated_at'                => now(),
                ]);
        }

        return response()->json([
            'success'                   => true,
            'message'                   => 'Pengantaran telah selesai!',
            'status'                    => 'Selesai',
            'waktu_selesai_pengantaran' => $endTime,
            'total_durasi'              => $totalDurasi,
            'durasi_detik'              => $durasiDetik,
        ]);
    }
}

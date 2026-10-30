<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PembelianAtk;
use App\Models\ProdukAtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembelianAtkController extends Controller
{
    /**
     * Jendela waktu ambil sendiri (jam) -- SAMA dgn pola booking ambil sendiri
     * buku (lihat buildDeadlineCard() di frontend: created_at + 2 jam).
     */
    private const PICKUP_WINDOW_HOURS = 2;

    /**
     * Pesanan "ambil sendiri" yang lewat jendela waktu & belum diambil ->
     * otomatis dibatalkan + stoknya dikembalikan. Pola & alasannya identik
     * dengan PinjamController::cancelExpiredBookings() utk peminjaman buku.
     */
    private function cancelExpiredPickups(): void
    {
        $deadline = now()->subHours(self::PICKUP_WINDOW_HOURS);

        $expired = PembelianAtk::where('status', 'Booking')
            ->where('metode_pengiriman', 'ambil_sendiri')
            ->where('created_at', '<', $deadline)
            ->get();

        foreach ($expired as $order) {
            DB::transaction(function () use ($order) {
                foreach (($order->items ?? []) as $item) {
                    if (! empty($item['produk_id'])) {
                        ProdukAtk::where('id', $item['produk_id'])->increment('stok', (int) ($item['qty'] ?? 0));
                    }
                }

                $order->update(['status' => 'Dibatalkan']);

                Notification::create([
                    'id'      => (string) Str::uuid(),
                    'user_id' => $order->user_id,
                    'title'   => 'Pesanan ATK Dibatalkan',
                    'message' => 'Pesanan Toko ATK kamu dibatalkan otomatis karena tidak diambil dalam '
                        . self::PICKUP_WINDOW_HOURS . ' jam. Stok produk sudah dikembalikan.',
                    'type'    => 'warning',
                    'is_read' => false,
                ]);
            });
        }
    }

    /**
     * GET /api/pembelian-atk?user_id=... (mahasiswa) atau tanpa filter (admin, semua pesanan)
     */
    public function index(Request $request)
    {
        $this->cancelExpiredPickups();

        $query = PembelianAtk::orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $orders = $query->get();

        $userIds = $orders->pluck('user_id')->unique()->filter();
        $users = DB::table('users')->whereIn('id', $userIds)->pluck('nama_lengkap', 'id');

        $orders->transform(function ($order) use ($users) {
            $order->customer_name = $users[$order->user_id] ?? null;
            return $order;
        });

        return response()->json($orders);
    }

    /**
     * POST /api/pembelian-atk
     * body: { user_id, items: [{produk_id, kode, nama, harga, qty}], total,
     *         metode_pengiriman: 'ambil_sendiri'|'antar', alamat_kirim?, status? }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'                  => ['required', 'string'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.produk_id'        => ['required', 'string'],
            'items.*.qty'              => ['required', 'integer', 'min:1'],
            'metode_pengiriman'        => ['required', 'in:ambil_sendiri,antar'],
            'alamat_kirim'             => ['nullable', 'string', 'max:500'],
            'status'                   => ['sometimes', 'nullable', 'string'],
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                $total = 0;
                $itemsFinal = [];

                foreach ($data['items'] as $item) {
                    // Kunci baris produk ini supaya aman dari race condition kalau
                    // ada 2 orang checkout stok terakhir bersamaan.
                    $produk = ProdukAtk::where('id', $item['produk_id'])->lockForUpdate()->first();

                    if (! $produk) {
                        throw new \RuntimeException('Produk tidak ditemukan.');
                    }
                    if ($produk->stok < $item['qty']) {
                        throw new \RuntimeException('Stok "' . $produk->nama . '" tidak cukup (tersisa ' . $produk->stok . ').');
                    }

                    $produk->decrement('stok', $item['qty']);

                    $subtotal = $produk->harga * $item['qty'];
                    $total += $subtotal;

                    $itemsFinal[] = [
                        'produk_id' => $produk->id,
                        'kode'      => $produk->kode,
                        'nama'      => $produk->nama,
                        'harga'     => $produk->harga,
                        'qty'       => $item['qty'],
                    ];
                }

                $status = $data['status'] ?? ($data['metode_pengiriman'] === 'antar'
                    ? 'Booking Dikonfirmasi Admin'
                    : 'Booking');

                $order = PembelianAtk::create([
                    'user_id'           => $data['user_id'],
                    'items'             => $itemsFinal,
                    'total'             => $total,
                    'metode_pengiriman' => $data['metode_pengiriman'],
                    'alamat_kirim'      => $data['alamat_kirim'] ?? null,
                    'status'            => $status,
                ]);

                Notification::create([
                    'id'      => (string) Str::uuid(),
                    'user_id' => $data['user_id'],
                    'title'   => 'Pesanan ATK Diterima',
                    'message' => $data['metode_pengiriman'] === 'antar'
                        ? 'Pesanan Toko ATK kamu sedang diproses dan akan diantar ke alamatmu.'
                        : 'Pesanan Toko ATK kamu sudah diterima. Ambil dalam ' . self::PICKUP_WINDOW_HOURS . ' jam di meja sirkulasi perpustakaan.',
                    'type'    => 'success',
                    'is_read' => false,
                ]);

                return $order;
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'order'   => $order,
        ], 201);
    }

    /**
     * PUT/PATCH /api/pembelian-atk/{id}
     * Dipakai ADMIN utk mengubah status pesanan (alur kurir / selesai / dibatalkan).
     */
    public function update(Request $request, string $id)
    {
        $order = PembelianAtk::find($id);
        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $newStatus = $data['status'];
        $wasFinal = in_array($order->status, ['Selesai', 'Dibatalkan']);

        $order->status = $newStatus;

        if (in_array($newStatus, ['Selesai']) && ! $order->tanggal_selesai) {
            $order->tanggal_selesai = now();
        }

        // Kalau dibatalkan DAN sebelumnya belum final, kembalikan stoknya.
        if ($newStatus === 'Dibatalkan' && ! $wasFinal) {
            foreach (($order->items ?? []) as $item) {
                if (! empty($item['produk_id'])) {
                    ProdukAtk::where('id', $item['produk_id'])->increment('stok', (int) ($item['qty'] ?? 0));
                }
            }
        }

        $order->save();

        Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $order->user_id,
            'title'   => 'Status Pesanan ATK Diperbarui',
            'message' => 'Status pesanan Toko ATK kamu sekarang: ' . $newStatus . '.',
            'type'    => $newStatus === 'Dibatalkan' ? 'warning' : 'info',
            'is_read' => false,
        ]);

        return response()->json($order);
    }

    public function destroy(string $id)
    {
        $order = PembelianAtk::find($id);
        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $order->delete();

        return response()->json(['success' => true]);
    }
}
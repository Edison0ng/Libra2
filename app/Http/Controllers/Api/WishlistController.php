<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Hitung info antrian lengkap untuk satu buku: siapa saja yang
     * mengantre (urut), siapa yang sedang kebagian hold (giliran aktif),
     * dan perkiraan tanggal giliran tiap orang.
     */
    private function buildQueueInfo(string $bookId): array
    {
        $bookAvailable = ! Wishlist::bookIsBorrowed($bookId);

        $activeLoan = DB::table('pinjam')
            ->where('book_id', $bookId)
            ->whereNull('tanggal_kembali')
            ->orderByDesc('tenggat_waktu')
            ->first();

        // Basis tanggal utk mulai menghitung perkiraan giliran:
        // - kalau buku masih dipinjam org lain -> tenggat_waktu peminjam saat ini
        // - kalau buku sudah tersedia (lagi nunggu hold org #1 dieksekusi) -> hari ini
        $baseDate = $bookAvailable
            ? now()->startOfDay()
            : ($activeLoan && $activeLoan->tenggat_waktu ? \Carbon\Carbon::parse($activeLoan->tenggat_waktu) : null);

        $hold = Wishlist::activeHold($bookId);

        $queueRows = Wishlist::where('book_id', $bookId)
            ->orderBy('created_at', 'asc')
            ->get(['id', 'user_id', 'created_at', 'hold_started_at']);

        $queue = $queueRows->values()->map(function ($row, $index) use ($baseDate) {
            $position = $index + 1;

            $estimatedDate = $baseDate
                ? $baseDate->copy()->addDays(($position - 1) * Wishlist::DEFAULT_LOAN_DAYS)
                : null;

            return [
                'user_id'        => $row->user_id,
                'position'       => $position,
                'estimated_date' => $estimatedDate?->toDateString(),
            ];
        });

        return [
            'book_available'   => $bookAvailable,
            'current_due_date' => (! $bookAvailable && $activeLoan && $activeLoan->tenggat_waktu)
                ? \Carbon\Carbon::parse($activeLoan->tenggat_waktu)->toDateString()
                : null,
            'total_in_queue'   => $queue->count(),
            'queue'            => $queue,
            'hold'             => $hold ? [
                'user_id'    => $hold->user_id,
                'expires_at' => $hold->holdExpiresAt()?->toIso8601String(),
            ] : null,
        ];
    }

    /**
     * Apakah user ini sedang meminjam buku tsb (belum dikembalikan dan tidak
     * ditolak)? Dipakai untuk melarang antre di buku yang sedang ia pinjam.
     */
    private function userIsBorrowing(?string $userId, string $bookId): bool
    {
        if (! $userId) {
            return false;
        }

        return DB::table('pinjam')
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->whereNull('tanggal_kembali')
            ->whereRaw("LOWER(status) <> 'ditolak'")
            ->exists();
    }

    /**
     * GET /api/wishlist?user_id=...
     */
    public function index(Request $request)
    {
        $request->validate(['user_id' => ['required']]);

        Wishlist::sweepExpiredHolds();

        $wishlist = Wishlist::where('user_id', $request->user_id)
            ->orderByDesc('created_at')
            ->get();

        $queueInfoCache = [];

        $result = $wishlist->map(function ($item) use ($request, &$queueInfoCache) {
            if (! isset($queueInfoCache[$item->book_id])) {
                $queueInfoCache[$item->book_id] = $this->buildQueueInfo($item->book_id);
            }
            $info = $queueInfoCache[$item->book_id];

            $mine = collect($info['queue'])->firstWhere('user_id', $request->user_id);
            $isHolder = $info['hold'] && $info['hold']['user_id'] === $request->user_id;

            return [
                'id'               => $item->id,
                'user_id'          => $item->user_id,
                'book_id'          => $item->book_id,
                'created_at'       => $item->created_at,
                'queue_position'   => $mine['position'] ?? null,
                'total_in_queue'   => $info['total_in_queue'],
                'estimated_date'   => $mine['estimated_date'] ?? null,
                'current_due_date' => $info['current_due_date'],
                'book_available'   => $info['book_available'],
                'is_your_turn'     => $isHolder,
                'hold_expires_at'  => $isHolder ? $info['hold']['expires_at'] : null,
            ];
        });

        return response()->json($result);
    }

    /**
     * GET /api/wishlist/queue-info?book_id=...&user_id=...(opsional)
     *
     * Dipakai di modal detail buku utk menentukan tombol booking:
     * aktif normal / terkunci (sedang jadi hold org lain) / giliranmu /
     * tawaran "antre sekarang jadi urutan ke-N".
     */
    public function queueInfo(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'string'],
            'user_id' => ['sometimes', 'nullable', 'string'],
        ]);

        Wishlist::sweepExpiredHolds();

        $info = $this->buildQueueInfo($request->book_id);

        $mine = $request->user_id
            ? collect($info['queue'])->firstWhere('user_id', $request->user_id)
            : null;

        $holderUserId = $info['hold']['user_id'] ?? null;
        $isYourTurn = $holderUserId !== null && $holderUserId === $request->user_id;
        $lockedForYou = $holderUserId !== null && ! $isYourTurn;

        // Perkiraan posisi KALAU user ini ikut antre sekarang (belum join)
        $wouldBePosition = $info['total_in_queue'] + 1;
        $wouldBeEstimate = null;
        if ($info['book_available']) {
            // Antrian kosong & buku tersedia -> tidak relevan ikut antre, tinggal booking.
            $wouldBeEstimate = null;
        } else {
            $baseDate = $info['current_due_date'] ? \Carbon\Carbon::parse($info['current_due_date']) : null;
            $wouldBeEstimate = $baseDate
                ? $baseDate->copy()->addDays(($wouldBePosition - 1) * Wishlist::DEFAULT_LOAN_DAYS)->toDateString()
                : null;
        }

        return response()->json([
            'book_available'      => $info['book_available'],
            'total_in_queue'      => $info['total_in_queue'],
            'current_due_date'    => $info['current_due_date'],
            'already_in_queue'    => $mine !== null,
            'already_borrowing'   => $this->userIsBorrowing($request->user_id, $request->book_id),
            'your_position'       => $mine['position'] ?? $wouldBePosition,
            'your_estimated_date' => $mine['estimated_date'] ?? $wouldBeEstimate,
            // Status hold:
            'is_your_turn'        => $isYourTurn,
            'locked_for_you'      => $lockedForYou,
            'hold_expires_at'     => $info['hold']['expires_at'] ?? null,
        ]);
    }

    /**
     * POST /api/wishlist
     * body: { user_id, book_id }
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'book_id' => ['required', 'string'],
        ]);

        Wishlist::sweepExpiredHolds();

        // Orang yang sedang meminjam buku ini (belum dikembalikan dan tidak
        // ditolak) tidak boleh ikut antre untuk buku yang sama.
        if ($this->userIsBorrowing($request->user_id, $request->book_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sedang meminjam buku ini, sehingga tidak dapat mengantre untuk buku yang sama.',
            ], 422);
        }

        // Kalau buku memang lagi tersedia & tidak ada antrian/hold sama sekali,
        // tidak ada gunanya antre -- arahkan langsung booking saja.
        if (! Wishlist::bookIsBorrowed($request->book_id) && Wishlist::activeHold($request->book_id) === null) {
            $hasQueue = Wishlist::where('book_id', $request->book_id)->exists();
            if (! $hasQueue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku ini sedang tersedia, langsung booking saja tanpa perlu antre.',
                ], 422);
            }
        }

        $item = Wishlist::firstOrCreate([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);

        // Jaga-jaga: kalau kebetulan dia jadi org pertama & buku tersedia
        // tapi belum ada hold yg jalan, langsung mulai hold-nya.
        Wishlist::startHoldForBook($request->book_id);

        return response()->json($item, 201);
    }

    /**
     * GET /api/wishlist/active-holds
     *
     * KHUSUS ADMIN: daftar semua buku yang SAAT INI sedang ditahan (hold)
     * utk orang pertama di antriannya masing-masing. Dipakai di dashboard
     * admin supaya admin bisa lihat & lepas paksa hold kalau perlu.
     */
    public function activeHolds(Request $request)
    {
        Wishlist::sweepExpiredHolds();

        $holds = Wishlist::whereNotNull('hold_started_at')
            ->orderBy('hold_started_at', 'asc')
            ->get();

        $result = $holds->map(function ($hold) {
            $title = DB::table('books')->where('ISBN', $hold->book_id)->value('Book-Title');

            return [
                'book_id'         => $hold->book_id,
                'book_title'      => $title,
                'user_id'         => $hold->user_id,
                'hold_started_at' => $hold->hold_started_at?->toIso8601String(),
                'expires_at'      => $hold->holdExpiresAt()?->toIso8601String(),
            ];
        });

        return response()->json($result);
    }

    /**
     * POST /api/wishlist/force-release
     * body: { book_id }
     *
     * KHUSUS ADMIN: lepas paksa hold yang sedang berjalan utk satu buku,
     * tanpa menunggu 24 jam. Orang yg sedang kebagian giliran dicoret dari
     * antrian & diberi tahu, lalu giliran otomatis lanjut ke antrian
     * berikutnya kalau ada.
     *
     * CATATAN: endpoint ini TIDAK dibatasi middleware auth (konsisten dgn
     * pola endpoint lain di proyek ini yg juga tanpa auth:sanctum), jadi
     * pastikan ini hanya dipanggil dari halaman admin, bukan ditaruh di
     * tombol yang bisa diakses mahasiswa biasa.
     */
    public function forceRelease(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'string'],
        ]);

        $released = Wishlist::forceReleaseHold($request->book_id);

        if (! $released) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada hold yang sedang berjalan untuk buku ini saat ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hold berhasil dilepas. Giliran otomatis dilempar ke antrian berikutnya (jika ada).',
        ]);
    }

    /**
     * DELETE /api/wishlist?user_id=...&book_id=...
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'book_id' => ['required'],
        ]);

        $entry = Wishlist::where('user_id', $request->user_id)
            ->where('book_id', $request->book_id)
            ->first();

        if ($entry) {
            $wasHolder = $entry->hold_started_at !== null;
            $bookId = $entry->book_id;

            $entry->delete();

            // Kalau yg keluar justru org yg sedang kebagian giliran,
            // langsung lempar giliran ke antrian berikutnya.
            if ($wasHolder) {
                Wishlist::startHoldForBook($bookId);
            }
        }

        return response()->json(['success' => true]);
    }
}
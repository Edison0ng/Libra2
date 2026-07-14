<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PinjamController extends Controller
{
    /**
     * Batalkan (hapus) otomatis booking "Ambil Sendiri" yang sudah lewat
     * batas waktu pengambilan (2 jam sejak created_at) tapi belum diambil
     * (status masih 'Booking'). Dipanggil setiap kali data peminjaman
     * diambil (index()), supaya tidak perlu cron job terpisah -- cukup
     * "numpang" di request yang memang sudah sering terjadi (polling
     * otomatis tiap 30 detik di sisi mahasiswa & admin).
     *
     * PENTING: window 2 jam ini HARUS SAMA dengan pickupWindowMs di
     * resources/views/libra.blade.php (buildDeadlineCard). Kalau salah
     * satu diubah, ubah juga yang satunya supaya tidak ada selisih antara
     * apa yang mahasiswa lihat dan kapan booking benar-benar dihapus.
     */
    private function cancelExpiredBookings(): void
    {
        $expired = DB::table('pinjam as l')
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select('l.id', 'l.user_id', DB::raw('books."Book-Title" as book_title'), 'l.book_id')
            ->where('l.status', 'Booking')
            ->whereNotNull('l.created_at')
            ->where('l.created_at', '<', now()->subHours(2))
            ->get();

        if ($expired->isEmpty()) {
            return;
        }

        foreach ($expired as $loan) {
            Notification::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $loan->user_id,
                'title'   => 'Booking Dibatalkan Otomatis',
                'message' => 'Booking untuk buku "' . ($loan->book_title ?? $loan->book_id)
                    . '" dibatalkan otomatis karena tidak diambil dalam batas waktu 2 jam.',
                'type'    => 'warning',
                'is_read' => false,
            ]);
        }

        DB::table('pinjam')
            ->whereIn('id', $expired->pluck('id'))
            ->delete();
    }

    public function index(Request $request)
    {
        $table = 'pinjam'; 

        if (!Schema::hasTable($table)) {
            return response()->json(['message' => 'Table not found'], 404);
        }

        $this->cancelExpiredBookings();

        $query = DB::table($table.' as l')
            ->leftJoin('users', DB::raw('l.user_id::text'), '=', DB::raw('users.id::text'))
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*', 
                'users.nama_lengkap as user_name', 
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            );

        // Filter berdasarkan user_id jika dikirimkan oleh frontend
        if ($request->has('user_id')) {
            $query->where('l.user_id', $request->query('user_id'));
        }

        $loans = $query->orderBy('l.tanggal_pinjam', 'desc')->get();

        return response()->json($loans);
    }

    public function show($id)
    {
        $loan = DB::table('pinjam as l')
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*',
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            )
            ->where('l.id', $id)
            ->first();

        if (! $loan) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($loan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'book_id' => ['required', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            // tenggat_waktu sekarang optional: jika tidak dikirim, akan di-set otomatis
            'tenggat_waktu' => [
                'sometimes',
                'nullable',
                'date',
                'after:tanggal_pinjam',
                // Aturan bisnis LIBRA: durasi peminjaman maksimal 1 bulan
                function ($attribute, $value, $fail) use ($request) {
                    if (empty($value)) return; // jika tidak dikirim, skip pengecekan durasi
                    $mulai = \Carbon\Carbon::parse($request->tanggal_pinjam);
                    $batasMaksimal = $mulai->copy()->addMonth();
                    if (\Carbon\Carbon::parse($value)->gt($batasMaksimal)) {
                        $fail('Durasi peminjaman maksimal adalah 1 bulan sejak tanggal pinjam (paling lambat ' . $batasMaksimal->format('d-m-Y') . ').');
                    }
                },
            ],
            'status' => ['required', 'string'],
        ]);

        // Generate UUID untuk ID peminjaman jika belum ada
        $id = $request->input('id') ?: (string) Str::uuid();

        // Jika tenggat_waktu tidak dikirim, set default (14 hari sejak tanggal_pinjam)
        $tenggat = $request->input('tenggat_waktu');
        if (empty($tenggat)) {
            $tenggat = \Carbon\Carbon::parse($request->tanggal_pinjam)->addDays(14)->toDateString();
        }

        DB::table('pinjam')->insert([
            'id' => $id,
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tenggat_waktu' => $tenggat,
            'status' => $request->status,
            'tanggal_kembali' => $request->tanggal_kembali ?: null,
            'denda' => $request->denda ?: 0,
            // Dicatat sekali saat booking dibuat, dipakai sebagai basis
            // perhitungan "Batas Pengambilan Resv." (2 jam) di sisi mahasiswa.
            // TIDAK BOLEH berubah lagi setelah ini (bukan updated_at).
            'created_at' => now(),
        ]);

        $newLoan = DB::table('pinjam as l')
            ->leftJoin('books', 'l.book_id', '=', 'books.ISBN')
            ->select(
                'l.*',
                DB::raw('books."Book-Title" as book_title'),
                DB::raw('books."Image-URL-M" as book_cover')
            )
            ->where('l.id', $id)
            ->first();

        // Buat notifikasi nyata untuk user terkait
        Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $request->user_id,
            'title'   => 'Peminjaman Berhasil',
            'message' => 'Buku "' . ($newLoan->book_title ?? $request->book_id) . '" berhasil dipinjam. Batas pengembalian: ' . $tenggat . '.',
            'type'    => 'success',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disimpan!',
            'loan' => $newLoan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['sometimes', 'string'],
            'tanggal_kembali' => ['sometimes', 'nullable', 'date'],
            'tenggat_waktu' => ['sometimes', 'nullable', 'date'],
            'denda' => ['sometimes', 'integer'],
        ]);

        $data = $request->only(['status', 'tanggal_kembali', 'tenggat_waktu', 'denda']);
        
        DB::table('pinjam')->where('id', $id)->update($data);

        $loan = DB::table('pinjam')
            ->leftJoin('books', 'pinjam.book_id', '=', 'books.ISBN')
            ->select('pinjam.*', DB::raw('books."Book-Title" as book_title'))
            ->where('pinjam.id', $id)
            ->first();

        // Buat notifikasi saat buku selesai dikembalikan
        if ($loan && isset($data['status']) && strtolower($data['status']) === 'dikembalikan') {
            $pesan = 'Buku "' . ($loan->book_title ?? $loan->book_id) . '" telah berhasil dikembalikan.';
            if (!empty($loan->denda) && $loan->denda > 0) {
                $pesan .= ' Denda keterlambatan: Rp' . number_format($loan->denda, 0, ',', '.') . '.';
            }

            Notification::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $loan->user_id,
                'title'   => !empty($loan->denda) && $loan->denda > 0 ? 'Pengembalian & Denda' : 'Pengembalian Berhasil',
                'message' => $pesan,
                'type'    => !empty($loan->denda) && $loan->denda > 0 ? 'warning' : 'success',
                'is_read' => false,
            ]);
        }

        return response()->json($loan);
    }

    public function destroy($id)
    {
        DB::table('pinjam')->where('id', $id)->delete();

        return response()->json([
            'message' => 'Pinjam deleted'
        ]);
    }
}
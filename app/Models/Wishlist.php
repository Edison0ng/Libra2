<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Wishlist extends Model
{
    protected $table = 'wishlists';

    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'book_id',
        'hold_started_at',
    ];

    protected $casts = [
        'created_at'      => 'datetime',
        'hold_started_at' => 'datetime',
    ];

    /** Lama jendela waktu eksklusif (jam) buat orang paling depan antrian. */
    public const HOLD_HOURS = 24;

    /** Durasi pinjam standar (hari), basis perkiraan tanggal giliran. */
    public const DEFAULT_LOAN_DAYS = 14;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    /**
     * Apakah buku ini sedang ada di tangan peminjam (belum dikembalikan)?
     */
    public static function bookIsBorrowed(string $bookId): bool
    {
        return DB::table('pinjam')
            ->where('book_id', $bookId)
            ->whereNull('tanggal_kembali')
            ->exists();
    }

    /**
     * Entri yang sedang mendapat jatah hold utk buku ini (null kalau tidak ada).
     */
    public static function activeHold(string $bookId): ?self
    {
        return self::where('book_id', $bookId)
            ->whereNotNull('hold_started_at')
            ->first();
    }

    /** Apakah hold tsb sudah lewat batas HOLD_HOURS? */
    public function holdExpired(): bool
    {
        return $this->hold_started_at !== null
            && $this->hold_started_at->lt(now()->subHours(self::HOLD_HOURS));
    }

    public function holdExpiresAt(): ?\Illuminate\Support\Carbon
    {
        return $this->hold_started_at?->copy()->addHours(self::HOLD_HOURS);
    }

    /**
     * Mulai hold 24 jam utk orang PALING DEPAN di antrian buku ini --
     * HANYA kalau buku sedang tersedia (tidak ada peminjaman aktif) DAN
     * belum ada hold lain yang sedang berjalan utk buku ini.
     *
     * Dipanggil dari 3 tempat: (1) saat buku baru dikembalikan,
     * (2) saat org yg sedang di-hold keluar duluan dari antrian,
     * (3) saat hold org sebelumnya kadaluarsa (lihat sweepExpiredHolds()).
     */
    public static function startHoldForBook(string $bookId): ?self
    {
        if (self::bookIsBorrowed($bookId)) {
            return null;
        }

        if (self::activeHold($bookId) !== null) {
            return null; // sudah ada yg sedang kebagian giliran, jangan ditimpa
        }

        $next = self::where('book_id', $bookId)
            ->orderBy('created_at', 'asc')
            ->first();

        if (! $next) {
            return null; // tidak ada yang mengantre
        }

        $next->hold_started_at = now();
        $next->save();

        $title = DB::table('books')->where('ISBN', $bookId)->value('Book-Title') ?? $bookId;

        Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $next->user_id,
            'title'   => 'Giliranmu Tiba! Buku Tersedia',
            'message' => 'Buku "' . $title . '" sudah tersedia dan sekarang giliranmu. '
                . 'Lakukan booking dalam ' . self::HOLD_HOURS . ' jam, kalau tidak giliranmu akan '
                . 'hangus dan pindah ke antrian berikutnya.',
            'type'    => 'success',
            'is_read' => false,
        ]);

        return $next;
    }

    /**
     * OVERRIDE ADMIN: paksa lepas hold yang sedang berjalan utk satu buku
     * SEKARANG JUGA, tidak peduli masih berapa lama sisa waktunya.
     * Orang yg sedang kebagian hold dicoret dari antrian (sama persis
     * efeknya dengan kalau dia kehabisan waktu 24 jam secara alami),
     * lalu giliran otomatis dilempar ke antrian berikutnya kalau ada.
     *
     * Return true kalau memang ada hold yg dilepas, false kalau tidak ada
     * hold yang sedang berjalan utk buku ini (tidak ada apa-apa yg di-override).
     */
    public static function forceReleaseHold(string $bookId): bool
    {
        $hold = self::activeHold($bookId);
        if (! $hold) {
            return false;
        }

        $title = DB::table('books')->where('ISBN', $bookId)->value('Book-Title') ?? $bookId;

        Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $hold->user_id,
            'title'   => 'Giliran Antrian Dibatalkan Admin',
            'message' => 'Giliranmu untuk buku "' . $title . '" dibatalkan oleh admin perpustakaan sebelum waktu '
                . self::HOLD_HOURS . ' jam habis. Kamu dikeluarkan dari antrian. '
                . 'Silakan hubungi admin atau daftar antre lagi kalau masih berminat.',
            'type'    => 'warning',
            'is_read' => false,
        ]);

        $hold->delete();

        // Lempar ke antrian berikutnya, kalau masih ada.
        self::startHoldForBook($bookId);

        return true;
    }

    /**
     * Cari semua hold yang sudah kadaluarsa (lewat 24 jam tanpa booking):
     * orang itu DICORET dari antrian, lalu giliran otomatis dilempar ke
     * orang berikutnya (kalau masih ada & bukunya masih tersedia).
     *
     * Dipanggil "piggyback" di beberapa endpoint yang sering diakses
     * (list antrian, cek status, bikin peminjaman baru) -- bukan via cron,
     * konsisten dgn pola cancelExpiredBookings() yg sudah ada di proyek ini.
     */
    public static function sweepExpiredHolds(): void
    {
        $deadline = now()->subHours(self::HOLD_HOURS);

        $expired = self::whereNotNull('hold_started_at')
            ->where('hold_started_at', '<', $deadline)
            ->get();

        foreach ($expired as $entry) {
            $bookId = $entry->book_id;
            $title  = DB::table('books')->where('ISBN', $bookId)->value('Book-Title') ?? $bookId;

            Notification::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $entry->user_id,
                'title'   => 'Giliran Antrian Hangus',
                'message' => 'Waktu ' . self::HOLD_HOURS . ' jam untuk booking buku "' . $title . '" sudah habis. '
                    . 'Kamu dikeluarkan dari antrian. Silakan daftar antre lagi kalau masih berminat.',
                'type'    => 'warning',
                'is_read' => false,
            ]);

            $entry->delete();

            // Lempar giliran ke antrian berikutnya, kalau masih ada.
            self::startHoldForBook($bookId);
        }
    }
}
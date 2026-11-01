<?php

namespace App\Models;

use App\Notifications\LibraResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPasswordTrait;

    protected $table = 'users';

    /**
     * Primary key tabel `users` berupa UUID, bukan auto-increment integer.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Tabel `users` hanya punya kolom `created_at`, TIDAK ada `updated_at`.
     * Matikan pengelolaan `updated_at` otomatis oleh Eloquent.
     */
    const UPDATED_AT = null;

    /**
     * Field yang boleh diisi lewat mass-assignment.
     * Disesuaikan dengan struktur tabel `users` yang sudah ada di Supabase.
     */
    protected $fillable = [
        'id',
        'nama_lengkap',
        'username',
        'nim',
        'fakultas',
        'no_telepon',
        'alamat_kirim',
        'avatar_url',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password'   => 'hashed', // otomatis di-hash saat diisi
        ];
    }

    /**
     * Generate UUID otomatis saat user baru dibuat (kalau id belum diisi manual)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Kirim notifikasi reset password memakai template LIBRA sendiri
     * (bukan template default Laravel), supaya link mengarah ke
     * halaman /reset-password milik aplikasi ini.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new LibraResetPasswordNotification($token));
    }

    /**
     * Helper: cek apakah user ini admin.
     * Aturan tim: nim diisi 'admin' -> role admin, selain itu -> mahasiswa.
     */
    public function isAdmin(): bool
    {
        return strtolower($this->nim) === 'admin';
    }

    /**
     * Relasi dengan tabel pinjam (sirkulasi) untuk backend-admin-v2
     */
    public function pinjams()
    {
        return $this->hasMany(Pinjam::class);
    }
}
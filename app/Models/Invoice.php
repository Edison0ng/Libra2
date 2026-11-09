<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'book_isbn',
        'peminjam_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'nomor', 'jenis', 'referensi_id', 'nama_pengguna', 'deskripsi', 'jumlah', 'metode_bayar',
    ];
}
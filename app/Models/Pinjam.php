<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjam extends Model
{
    protected $table = 'pinjam';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'book_id',
        'status',
        'tanggal_pinjam',
        'tenggat_waktu',
        'tanggal_kembali',
        'denda'
    ];

    public $timestamps = false;
}
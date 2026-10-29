<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembelianAtk extends Model
{
    protected $table = 'pembelian_atk';

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'items',
        'total',
        'metode_pengiriman',
        'alamat_kirim',
        'status',
        'tanggal_selesai',
    ];

    protected $casts = [
        'items'           => 'array',
        'total'           => 'integer',
        'created_at'      => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

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
            if (empty($model->status)) {
                $model->status = $model->metode_pengiriman === 'antar'
                    ? 'Booking Dikonfirmasi Admin'
                    : 'Booking';
            }
        });
    }
}
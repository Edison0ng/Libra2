<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProdukAtk extends Model
{
    protected $table = 'produk_atk';

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'kategori',
        'harga',
        'stok',
        'gambar',
    ];

    protected $casts = [
        'harga'      => 'integer',
        'stok'       => 'integer',
        'created_at' => 'datetime',
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
        });
    }
}
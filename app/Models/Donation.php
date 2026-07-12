<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Donation extends Model
{
    protected $table = 'donations';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'author',
        'category',
        'condition',
        'note',
        'status',
        'tanggal_donasi'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($donation) {
            if (empty($donation->id)) {
                $donation->id = (string) Str::uuid();
            }
        });
    }
}

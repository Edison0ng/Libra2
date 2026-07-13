<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $table = 'notifications';

    // gunakan UUID string sebagai primary key bila tabel memakai id string
    protected $keyType = 'string';
    public $incrementing = false;

    // Notifikasi disimpan tanpa managed timestamps di beberapa kasus
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // created_at tidak otomatis terisi karena $timestamps = false,
            // jadi kita isi manual supaya notifikasi bisa diurutkan & ditampilkan waktunya.
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}
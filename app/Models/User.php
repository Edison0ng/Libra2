<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama_lengkap',
        'nim',
        'fakultas',
        'no_telepon',
        'alamat_kirim',
        'avatar_url'
    ];

    public $timestamps = false;

    public function pinjams()
    {
        return $this->hasMany(Pinjam::class);
    }
}
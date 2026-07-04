<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $primaryKey = 'ISBN';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ISBN',
        'Book-Title',
        'Book-Author',
        'Year-Of-Publication',
        'Publisher',
        'Image-URL-S',
        'Image-URL-M',
        'Image-URL-L'
    ];
}
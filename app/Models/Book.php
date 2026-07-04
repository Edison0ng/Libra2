<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $primaryKey = 'ISBN';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $appends = ['title', 'author', 'year', 'image_url', 'status'];

    protected function title(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['Book-Title'] ?? '');
    }

    protected function author(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['Book-Author'] ?? '');
    }

    protected function year(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['Year-Of-Publication'] ?? '');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['Image-URL-L']
            ?? $this->attributes['Image-URL-M']
            ?? $this->attributes['Image-URL-S']
            ?? 'https://via.placeholder.com/300x400?text=No+Cover');
    }

    protected function status(): Attribute
    {
        return Attribute::get(fn () => 'Tersedia');
    }
}

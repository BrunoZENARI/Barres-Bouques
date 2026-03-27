<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'publisher',
        'published_year',
        'genre',
        'location',
        'description',
        'cover_image',
        'stock',
    ];

    protected $appends = ['available_stock'];

    public function getAvailableStockAttribute(): int
    {
        return $this->stock - $this->loans()->whereNull('return_date')->count();
    }

    public $timestamps = true;

    public function loans()
    {
        return $this->hasMany(Loan::class, 'book_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function getBooksCountAttribute()
    {
        return $this->books()->count();
    }
}

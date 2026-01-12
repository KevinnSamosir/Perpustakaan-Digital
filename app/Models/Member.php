<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'member_number',
        'phone',
        'address',
        'join_date',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function activeLoans()
    {
        return $this->loans()->whereIn('status', ['borrowed', 'overdue']);
    }

    public function getActiveLoansCountAttribute()
    {
        return $this->activeLoans()->count();
    }

    public function getTotalLoansCountAttribute()
    {
        return $this->loans()->count();
    }

    public function hasWishlisted($bookId)
    {
        return $this->wishlists()->where('book_id', $bookId)->exists();
    }

    public function hasReviewed($bookId)
    {
        return $this->reviews()->where('book_id', $bookId)->exists();
    }

    public function hasBorrowed($bookId)
    {
        return $this->loans()->where('book_id', $bookId)->whereIn('status', ['borrowed', 'overdue'])->exists();
    }

    public function canBorrow()
    {
        $maxLoans = \App\Models\Setting::get('max_loans_per_member', 5);
        return $this->status === 'active' && $this->activeLoansCount < $maxLoans;
    }
}

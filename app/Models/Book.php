<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    // Konstanta jenis buku
    const TYPE_PHYSICAL = 'physical';
    const TYPE_DIGITAL = 'digital';

    // Konstanta kondisi buku fisik
    const CONDITION_GOOD = 'good';
    const CONDITION_DAMAGED = 'damaged';
    const CONDITION_LOST = 'lost';

    protected $fillable = [
        'title',
        'slug',
        'author',
        'isbn',
        'category_id',
        'author_id',
        'publisher_id',
        'publication_year',
        'category',
        'stock',
        'available_stock',
        'description',
        'cover_image',
        'file_path',
        'file_type',
        'pages',
        'language',
        'is_featured',
        'is_active',
        // Field baru untuk jenis buku
        'book_type',
        'shelf_location',
        'condition',
        'loan_duration_days',
        'access_duration_days',
        'access_limit',
        'current_access_count',
        'allow_download',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'stock' => 'integer',
        'available_stock' => 'integer',
        'pages' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'loan_duration_days' => 'integer',
        'access_duration_days' => 'integer',
        'access_limit' => 'integer',
        'current_access_count' => 'integer',
        'allow_download' => 'boolean',
    ];

    // Tambahkan accessor ke serialization
    protected $appends = ['cover_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(5);
            }
        });
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function authorRelation()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            // Check if it's already a full URL
            if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
                return $this->cover_image;
            }
            return asset('storage/' . $this->cover_image);
        }
        
        // Generate SVG placeholder as data URI (no external request needed)
        $colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#EC4899', '#14B8A6'];
        $colorIndex = abs(crc32($this->title ?? 'book')) % count($colors);
        $bgColor = $colors[$colorIndex];
        $title = htmlspecialchars(substr($this->title ?? 'Book', 0, 25), ENT_QUOTES);
        
        // Split title into lines for better display
        $words = explode(' ', $title);
        $lines = [];
        $currentLine = '';
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) > 12) {
                if ($currentLine) $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = trim($currentLine . ' ' . $word);
            }
        }
        if ($currentLine) $lines[] = $currentLine;
        $lines = array_slice($lines, 0, 3); // Max 3 lines
        
        $textY = 300 - (count($lines) * 15);
        $textElements = '';
        foreach ($lines as $i => $line) {
            $y = $textY + ($i * 35);
            $textElements .= "<text x='200' y='{$y}' font-family='Arial, sans-serif' font-size='28' font-weight='bold' fill='white' text-anchor='middle'>{$line}</text>";
        }
        
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='600' viewBox='0 0 400 600'>
            <rect width='400' height='600' fill='{$bgColor}'/>
            <rect x='20' y='20' width='360' height='560' fill='none' stroke='rgba(255,255,255,0.2)' stroke-width='2' rx='8'/>
            <text x='200' y='80' font-family='Arial, sans-serif' font-size='14' fill='rgba(255,255,255,0.7)' text-anchor='middle'>📚 BUKU</text>
            {$textElements}
            <text x='200' y='550' font-family='Arial, sans-serif' font-size='12' fill='rgba(255,255,255,0.5)' text-anchor='middle'>Perpustakaan Digital</text>
        </svg>";
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function isAvailable()
    {
        return $this->available_stock > 0 && $this->is_active;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_stock', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // ===== HELPER METHODS UNTUK JENIS BUKU =====

    /**
     * Check if book is physical
     */
    public function isPhysical(): bool
    {
        return $this->book_type === self::TYPE_PHYSICAL;
    }

    /**
     * Check if book is digital (e-book)
     */
    public function isDigital(): bool
    {
        return $this->book_type === self::TYPE_DIGITAL;
    }

    /**
     * Get book type label
     */
    public function getBookTypeLabelAttribute(): string
    {
        return $this->isPhysical() ? 'Buku Fisik' : 'E-Book';
    }

    /**
     * Get loan duration for this book
     */
    public function getLoanDuration(): int
    {
        if ($this->isDigital()) {
            return $this->access_duration_days ?? 7;
        }
        return $this->loan_duration_days ?? Setting::get('loan_duration_days', 14);
    }

    /**
     * Check if e-book has access limit
     */
    public function hasAccessLimit(): bool
    {
        return $this->isDigital() && !is_null($this->access_limit);
    }

    /**
     * Check if e-book access is available
     */
    public function isAccessAvailable(): bool
    {
        if (!$this->isDigital()) {
            return $this->available_stock > 0;
        }

        if (!$this->hasAccessLimit()) {
            return true; // Unlimited access
        }

        return $this->current_access_count < $this->access_limit;
    }

    /**
     * Get remaining access slots for e-book
     */
    public function getRemainingAccessSlotsAttribute(): ?int
    {
        if (!$this->isDigital() || !$this->hasAccessLimit()) {
            return null;
        }
        return max(0, $this->access_limit - $this->current_access_count);
    }

    /**
     * Increment e-book access count
     */
    public function incrementAccessCount(): void
    {
        if ($this->isDigital()) {
            $this->increment('current_access_count');
        }
    }

    /**
     * Decrement e-book access count
     */
    public function decrementAccessCount(): void
    {
        if ($this->isDigital() && $this->current_access_count > 0) {
            $this->decrement('current_access_count');
        }
    }

    /**
     * Get condition label
     */
    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_DAMAGED => 'Rusak',
            self::CONDITION_LOST => 'Hilang',
            default => 'Tidak Diketahui',
        };
    }

    // ===== SCOPES UNTUK FILTER JENIS BUKU =====

    public function scopePhysical($query)
    {
        return $query->where('book_type', self::TYPE_PHYSICAL);
    }

    public function scopeDigital($query)
    {
        return $query->where('book_type', self::TYPE_DIGITAL);
    }

    public function scopeEbook($query)
    {
        return $this->scopeDigital($query);
    }
}

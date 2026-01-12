<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    // Konstanta status peminjaman
    const STATUS_PENDING = 'pending';       // Menunggu approval admin (buku fisik)
    const STATUS_APPROVED = 'approved';     // Disetujui, menunggu diambil (buku fisik)
    const STATUS_REJECTED = 'rejected';     // Ditolak admin
    const STATUS_BORROWED = 'borrowed';     // Sedang dipinjam/diakses
    const STATUS_RETURNED = 'returned';     // Sudah dikembalikan
    const STATUS_OVERDUE = 'overdue';       // Terlambat
    const STATUS_COMPLETED = 'completed';   // Selesai (e-book expired)

    // Konstanta tipe peminjaman
    const TYPE_PHYSICAL = 'physical';
    const TYPE_DIGITAL = 'digital';

    protected $fillable = [
        'member_id',
        'book_id',
        'loan_type',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'notes',
        'approved_by',
        'approved_at',
        'picked_up_at',
        'access_started_at',
        'access_expires_at',
        'rejection_reason',
        'return_condition_notes',
        'returned_to',
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'approved_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'access_started_at' => 'datetime',
        'access_expires_at' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    // ===== HELPER METHODS UNTUK TIPE PEMINJAMAN =====

    /**
     * Check if this is a physical book loan
     */
    public function isPhysical(): bool
    {
        return $this->loan_type === self::TYPE_PHYSICAL;
    }

    /**
     * Check if this is a digital book loan
     */
    public function isDigital(): bool
    {
        return $this->loan_type === self::TYPE_DIGITAL;
    }

    /**
     * Get loan type label
     */
    public function getLoanTypeLabelAttribute(): string
    {
        return $this->isPhysical() ? 'Buku Fisik' : 'E-Book';
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui - Siap Diambil',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_BORROWED => 'Sedang Dipinjam',
            self::STATUS_RETURNED => 'Dikembalikan',
            self::STATUS_OVERDUE => 'Terlambat',
            self::STATUS_COMPLETED => 'Selesai',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED => 'bg-blue-100 text-blue-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_BORROWED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_RETURNED => 'bg-green-100 text-green-800',
            self::STATUS_OVERDUE => 'bg-red-100 text-red-800',
            self::STATUS_COMPLETED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // ===== STATUS CHECK METHODS =====

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isBorrowed(): bool
    {
        return $this->status === self::STATUS_BORROWED;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_BORROWED, self::STATUS_OVERDUE]);
    }

    public function isLate(): bool
    {
        if ($this->isDigital()) {
            return false; // E-book tidak bisa terlambat
        }
        return $this->isActive() && now() > $this->due_date;
    }

    public function getDaysLateAttribute(): int
    {
        if (!$this->isLate()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->isActive()) {
            return 0;
        }
        
        $endDate = $this->isDigital() ? $this->access_expires_at : $this->due_date;
        if (!$endDate) {
            return 0;
        }
        
        $days = now()->diffInDays($endDate, false);
        return max(0, $days);
    }

    /**
     * Check if e-book access is still valid
     */
    public function isAccessValid(): bool
    {
        if (!$this->isDigital()) {
            return false;
        }
        
        return $this->isBorrowed() && 
               $this->access_expires_at && 
               now() < $this->access_expires_at;
    }

    public function calculateFine(): float
    {
        if (!$this->isLate() || $this->isDigital()) {
            return 0;
        }
        $feePerDay = Setting::get('late_fee_per_day', 1000);
        return $this->daysLate * $feePerDay;
    }

    // ===== ACTION METHODS =====

    /**
     * Approve loan (Admin only - Physical books)
     */
    public function approve(int $adminId): bool
    {
        if (!$this->isPending() || !$this->isPhysical()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        // Kirim notifikasi ke user
        Notification::send(
            $this->member->user_id,
            'loan_approved',
            'Peminjaman Disetujui',
            "Peminjaman buku '{$this->book->title}' telah disetujui. Silakan ambil di perpustakaan.",
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        return true;
    }

    /**
     * Reject loan (Admin only - Physical books)
     */
    public function reject(int $adminId, string $reason): bool
    {
        if (!$this->isPending() || !$this->isPhysical()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Kirim notifikasi ke user
        Notification::send(
            $this->member->user_id,
            'loan_rejected',
            'Peminjaman Ditolak',
            "Peminjaman buku '{$this->book->title}' ditolak. Alasan: {$reason}",
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        return true;
    }

    /**
     * Mark as picked up (Admin - Physical books)
     */
    public function markAsPickedUp(int $adminId): bool
    {
        if (!$this->isApproved() || !$this->isPhysical()) {
            return false;
        }

        $loanDuration = $this->book->getLoanDuration();
        
        $this->update([
            'status' => self::STATUS_BORROWED,
            'picked_up_at' => now(),
            'loan_date' => now(),
            'due_date' => now()->addDays($loanDuration),
        ]);

        // Kurangi stok
        $this->book->decrement('available_stock');

        // Kirim notifikasi
        Notification::send(
            $this->member->user_id,
            'loan',
            'Buku Berhasil Dipinjam',
            "Anda telah mengambil buku '{$this->book->title}'. Harap kembalikan sebelum " . now()->addDays($loanDuration)->format('d M Y'),
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        ActivityLog::log('book_pickup', "Mengambil buku: {$this->book->title}", $this->book);

        return true;
    }

    /**
     * Return book (Admin verification - Physical books)
     */
    public function returnBook(int $adminId, ?string $conditionNotes = null, ?float $fineAmount = null): bool
    {
        if (!$this->isActive() || !$this->isPhysical()) {
            return false;
        }

        $calculatedFine = $fineAmount ?? $this->calculateFine();

        $this->update([
            'status' => self::STATUS_RETURNED,
            'return_date' => now(),
            'returned_to' => $adminId,
            'return_condition_notes' => $conditionNotes,
            'fine_amount' => $calculatedFine,
        ]);

        // Kembalikan stok
        $this->book->increment('available_stock');

        // Kirim notifikasi
        $message = "Buku '{$this->book->title}' berhasil dikembalikan.";
        if ($calculatedFine > 0) {
            $message .= " Denda: Rp " . number_format($calculatedFine, 0, ',', '.');
        }

        Notification::send(
            $this->member->user_id,
            'return',
            'Pengembalian Berhasil',
            $message,
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        ActivityLog::log('book_return', "Mengembalikan buku: {$this->book->title}", $this->book);

        return true;
    }

    /**
     * Start e-book access (Automatic approval)
     */
    public function startEbookAccess(): bool
    {
        if (!$this->isDigital()) {
            return false;
        }

        $accessDuration = $this->book->getLoanDuration();
        
        $this->update([
            'status' => self::STATUS_BORROWED,
            'loan_date' => now(),
            'access_started_at' => now(),
            'access_expires_at' => now()->addDays($accessDuration),
            'due_date' => now()->addDays($accessDuration),
        ]);

        // Increment access count jika ada limit
        $this->book->incrementAccessCount();

        // Kirim notifikasi
        Notification::send(
            $this->member->user_id,
            'ebook_access',
            'Akses E-Book Aktif',
            "Anda mendapatkan akses ke e-book '{$this->book->title}' selama {$accessDuration} hari. Akses berakhir: " . now()->addDays($accessDuration)->format('d M Y H:i'),
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        ActivityLog::log('ebook_access', "Mengakses e-book: {$this->book->title}", $this->book);

        return true;
    }

    /**
     * End e-book access (Automatic)
     */
    public function endEbookAccess(): bool
    {
        if (!$this->isDigital() || !$this->isBorrowed()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'return_date' => now(),
        ]);

        // Decrement access count
        $this->book->decrementAccessCount();

        // Kirim notifikasi
        Notification::send(
            $this->member->user_id,
            'ebook_expired',
            'Akses E-Book Berakhir',
            "Akses Anda ke e-book '{$this->book->title}' telah berakhir.",
            ['loan_id' => $this->id, 'book_id' => $this->book_id]
        );

        return true;
    }

    // ===== SCOPES =====

    public function scopeBorrowed($query)
    {
        return $query->where('status', self::STATUS_BORROWED);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_BORROWED)
                     ->where('loan_type', self::TYPE_PHYSICAL)
                     ->where('due_date', '<', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePhysical($query)
    {
        return $query->where('loan_type', self::TYPE_PHYSICAL);
    }

    public function scopeDigital($query)
    {
        return $query->where('loan_type', self::TYPE_DIGITAL);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_BORROWED, self::STATUS_OVERDUE]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeExpiredEbooks($query)
    {
        return $query->where('loan_type', self::TYPE_DIGITAL)
                     ->where('status', self::STATUS_BORROWED)
                     ->where('access_expires_at', '<', now());
    }
}


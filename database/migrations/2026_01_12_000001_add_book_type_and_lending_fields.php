<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Jenis buku: physical (fisik) atau digital (e-book)
            $table->enum('book_type', ['physical', 'digital'])->default('physical')->after('id');
            
            // Untuk buku fisik
            $table->string('shelf_location')->nullable()->after('description'); // Lokasi rak
            $table->enum('condition', ['good', 'damaged', 'lost'])->default('good')->after('shelf_location'); // Kondisi buku
            $table->integer('loan_duration_days')->default(14)->after('condition'); // Lama peminjaman (hari)
            
            // Untuk e-book
            $table->integer('access_duration_days')->default(7)->after('loan_duration_days'); // Lama akses e-book (hari)
            $table->integer('access_limit')->nullable()->after('access_duration_days'); // Limit akses bersamaan (null = unlimited)
            $table->integer('current_access_count')->default(0)->after('access_limit'); // Jumlah akses aktif saat ini
            $table->boolean('allow_download')->default(false)->after('current_access_count'); // Izinkan download
        });

        Schema::table('loans', function (Blueprint $table) {
            // Status baru untuk alur peminjaman
            // pending = menunggu approval admin (buku fisik)
            // approved = disetujui admin, menunggu diambil (buku fisik)
            // borrowed = sedang dipinjam
            // returned = sudah dikembalikan
            // rejected = ditolak admin
            // overdue = terlambat
            // completed = selesai (untuk e-book yang expired)
            
            // Tambah kolom untuk tracking
            $table->enum('loan_type', ['physical', 'digital'])->default('physical')->after('book_id');
            $table->timestamp('approved_at')->nullable()->after('notes'); // Waktu disetujui
            $table->timestamp('picked_up_at')->nullable()->after('approved_at'); // Waktu diambil (buku fisik)
            $table->timestamp('access_started_at')->nullable()->after('picked_up_at'); // Waktu mulai akses (e-book)
            $table->timestamp('access_expires_at')->nullable()->after('access_started_at'); // Waktu akses berakhir (e-book)
            $table->text('rejection_reason')->nullable()->after('access_expires_at'); // Alasan penolakan
            $table->text('return_condition_notes')->nullable()->after('rejection_reason'); // Catatan kondisi saat dikembalikan
            $table->foreignId('returned_to')->nullable()->after('return_condition_notes')->constrained('users'); // Admin yang menerima pengembalian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'book_type',
                'shelf_location',
                'condition',
                'loan_duration_days',
                'access_duration_days',
                'access_limit',
                'current_access_count',
                'allow_download',
            ]);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['returned_to']);
            $table->dropColumn([
                'loan_type',
                'approved_at',
                'picked_up_at',
                'access_started_at',
                'access_expires_at',
                'rejection_reason',
                'return_condition_notes',
                'returned_to',
            ]);
        });
    }
};

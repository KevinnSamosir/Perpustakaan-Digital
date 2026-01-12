<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use App\Models\User;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Setting;

// Home
Route::get('/', function () {
    $latestBooks = Book::active()->latest()->take(4)->get();
    $totalBooks = Book::count();
    $totalMembers = Member::count();
    $totalLoans = Loan::count();
    $categories = Category::withCount('books')->take(8)->get();
    
    return view('home', compact('latestBooks', 'totalBooks', 'totalMembers', 'totalLoans', 'categories'));
});

// Books (Public)
Route::get('/books', function () {
    $query = Book::active();
    
    if (request('search')) {
        $search = request('search');
        $query->search($search);
    }
    
    if (request('category')) {
        $query->where('category', request('category'));
    }

    if (request('category_id')) {
        $query->where('category_id', request('category_id'));
    }

    if (request('author_id')) {
        $query->where('author_id', request('author_id'));
    }

    if (request('availability') === 'available') {
        $query->available();
    }

    $sortBy = request('sort', 'latest');
    switch ($sortBy) {
        case 'title':
            $query->orderBy('title');
            break;
        case 'year':
            $query->orderBy('publication_year', 'desc');
            break;
        case 'popular':
            $query->withCount('loans')->orderBy('loans_count', 'desc');
            break;
        default:
            $query->latest();
    }
    
    $books = $query->paginate(12);
    $categories = Category::all();
    
    return view('books.index', compact('books', 'categories'));
});

Route::get('/books/{book}', function (Book $book) {
    return view('books.show', compact('book'));
});

// User Routes (Authenticated Users)
Route::middleware('auth')->group(function () {
    
    // Profile
    Route::get('/profile', function () {
        $member = Auth::user()->member;
        return view('profile.index', compact('member'));
    });

    Route::put('/profile', function (Request $request) {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->member) {
            $user->member->update([
                'phone' => $validated['phone'] ?? '',
                'address' => $validated['address'] ?? '',
            ]);
        }

        ActivityLog::log('profile_update', 'User memperbarui profil');

        return back()->with('success', 'Profil berhasil diperbarui!');
    });

    Route::put('/profile/password', function (Request $request) {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::log('password_change', 'User mengubah password');

        return back()->with('success', 'Password berhasil diubah!');
    });

    // My Loans - User can only see their own loans
    Route::get('/loans', function () {
        $member = Auth::user()->member;
        if (!$member) {
            // Auto-create member
            $member = Member::create([
                'user_id' => Auth::id(),
                'member_number' => 'MBR-' . date('Y') . str_pad(Auth::id(), 4, '0', STR_PAD_LEFT),
                'phone' => '',
                'address' => '',
                'join_date' => now(),
                'status' => 'active',
            ]);
        }
        
        // Update status for overdue physical loans only
        Loan::where('member_id', $member->id)
            ->where('status', 'borrowed')
            ->where('loan_type', 'physical')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);
        
        // Auto-complete expired e-book access
        $expiredEbooks = Loan::where('member_id', $member->id)
            ->where('status', 'borrowed')
            ->where('loan_type', 'digital')
            ->where('access_expires_at', '<', now())
            ->get();
        
        foreach ($expiredEbooks as $loan) {
            $loan->update(['status' => 'completed', 'return_date' => now()]);
            if ($loan->book && $loan->book->access_limit) {
                $loan->book->decrement('current_access_count');
            }
        }
        
        $query = Loan::with(['book'])->where('member_id', $member->id);
        
        // Filter by status
        $statusFilter = request('status', 'all');
        if ($statusFilter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($statusFilter === 'approved') {
            $query->where('status', 'approved');
        } elseif ($statusFilter === 'borrowed') {
            $query->whereIn('status', ['borrowed', 'overdue']);
        } elseif ($statusFilter === 'returned') {
            $query->whereIn('status', ['returned', 'completed']);
        } elseif ($statusFilter === 'overdue') {
            $query->where('status', 'overdue');
        }
        
        $loans = $query->latest()->paginate(10);
        return view('loans.index', compact('loans'));
    });

    // Return book by user
    Route::put('/loans/{loan}/return', function (Loan $loan) {
        $member = Auth::user()->member;
        
        // Verify ownership
        if (!$member || $loan->member_id !== $member->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk peminjaman ini.');
        }

        // Check if already returned
        if ($loan->status === 'returned') {
            return back()->with('error', 'Buku ini sudah dikembalikan.');
        }

        // Calculate fine if overdue
        $fineAmount = 0;
        $finePerDay = Setting::get('fine_per_day', 1000);
        
        if ($loan->due_date < now()) {
            $daysLate = now()->diffInDays($loan->due_date);
            $fineAmount = $daysLate * $finePerDay;
        }

        // Update loan status
        $loan->update([
            'status' => 'returned',
            'return_date' => now(),
            'fine_amount' => $fineAmount,
        ]);

        // Restore book stock
        $loan->book->increment('available_stock');

        // Create notification
        $message = "Buku '{$loan->book->title}' berhasil dikembalikan.";
        if ($fineAmount > 0) {
            $message .= " Denda keterlambatan: Rp " . number_format($fineAmount, 0, ',', '.');
        }
        
        Notification::send(
            Auth::id(),
            'return',
            'Pengembalian Berhasil',
            $message,
            ['loan_id' => $loan->id, 'fine' => $fineAmount]
        );

        ActivityLog::log('book_return', "Mengembalikan buku: {$loan->book->title}", $loan);

        return back()->with('success', $message);
    })->name('loans.return');

    // Borrow book - dengan alur berbeda untuk buku fisik dan e-book
    Route::post('/books/{book}/borrow', function (Book $book) {
        $user = Auth::user();
        $member = $user->member;
        
        // Auto-create member if not exists
        if (!$member) {
            $member = Member::create([
                'user_id' => $user->id,
                'member_number' => 'MBR-' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'phone' => '',
                'address' => '',
                'join_date' => now(),
                'status' => 'active',
            ]);
            $user->load('member');
            $member = $user->member;
        }

        if (!$member->canBorrow()) {
            return back()->with('error', 'Anda telah mencapai batas maksimal peminjaman atau akun tidak aktif.');
        }

        // Check if user already has active/pending loan for this book
        $existingLoan = Loan::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed', 'overdue'])
            ->first();
        
        if ($existingLoan) {
            $statusMessage = match($existingLoan->status) {
                'pending' => 'Anda sudah mengajukan peminjaman buku ini dan sedang menunggu persetujuan.',
                'approved' => 'Peminjaman Anda sudah disetujui. Silakan ambil buku di perpustakaan.',
                'borrowed', 'overdue' => 'Anda sudah meminjam buku ini.',
                default => 'Anda sudah memiliki peminjaman aktif untuk buku ini.',
            };
            return back()->with('error', $statusMessage);
        }

        $isDigitalBook = ($book->book_type ?? 'physical') === 'digital';

        if ($isDigitalBook) {
            // === ALUR E-BOOK: Persetujuan Otomatis ===
            
            // Check if e-book is active
            if (!$book->is_active) {
                return back()->with('error', 'E-Book ini sedang tidak aktif.');
            }

            // Check access limit
            if ($book->access_limit && $book->current_access_count >= $book->access_limit) {
                return back()->with('error', 'Batas akses e-book tercapai. Silakan coba lagi nanti.');
            }

            $accessDuration = $book->access_duration_days ?? 7;

            $loan = Loan::create([
                'member_id' => $member->id,
                'book_id' => $book->id,
                'loan_type' => 'digital',
                'loan_date' => now(),
                'due_date' => now()->addDays($accessDuration),
                'access_started_at' => now(),
                'access_expires_at' => now()->addDays($accessDuration),
                'status' => 'borrowed', // Langsung borrowed untuk e-book
            ]);

            // Increment access count
            if ($book->access_limit) {
                $book->increment('current_access_count');
            }

            // Create notification
            Notification::send(
                Auth::id(),
                'ebook_access',
                'Akses E-Book Aktif',
                "Anda mendapatkan akses ke e-book '{$book->title}' selama {$accessDuration} hari. Akses berakhir: " . now()->addDays($accessDuration)->format('d M Y H:i'),
                ['book_id' => $book->id, 'loan_id' => $loan->id]
            );

            ActivityLog::log('ebook_access', "Mengakses e-book: {$book->title}", $book);

            return back()->with('success', "E-Book berhasil diakses! Anda memiliki akses selama {$accessDuration} hari hingga " . now()->addDays($accessDuration)->format('d M Y H:i'));

        } else {
            // === ALUR BUKU FISIK: Perlu Persetujuan Admin ===
            
            if ($book->available_stock <= 0) {
                return back()->with('error', 'Stok buku tidak tersedia.');
            }

            $loanDuration = $book->loan_duration_days ?? Setting::get('loan_duration_days', 14);

            $loan = Loan::create([
                'member_id' => $member->id,
                'book_id' => $book->id,
                'loan_type' => 'physical',
                'status' => 'pending', // Menunggu persetujuan admin
                'notes' => "Durasi peminjaman: {$loanDuration} hari",
            ]);

            // Create notification for user
            Notification::send(
                Auth::id(),
                'loan_request',
                'Permintaan Peminjaman Dikirim',
                "Permintaan peminjaman buku '{$book->title}' telah dikirim. Menunggu persetujuan admin.",
                ['book_id' => $book->id, 'loan_id' => $loan->id]
            );

            // Create notification for admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::send(
                    $admin->id,
                    'loan_request_admin',
                    'Permintaan Peminjaman Baru',
                    "Ada permintaan peminjaman buku '{$book->title}' dari {$user->name}.",
                    ['book_id' => $book->id, 'loan_id' => $loan->id, 'member_id' => $member->id]
                );
            }

            ActivityLog::log('loan_request', "Mengajukan peminjaman buku: {$book->title}", $book);

            return back()->with('success', 'Permintaan peminjaman berhasil dikirim! Silakan tunggu persetujuan admin.');
        }
    });

    // Read book online (requires active loan)
    Route::get('/books/{book}/read', function (Book $book) {
        $member = Auth::user()->member;
        
        if (!$member) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Check if user has active loan for this book
        $activeLoan = Loan::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->first();
            
        if (!$activeLoan) {
            return redirect('/books/' . $book->id)->with('error', 'Anda harus meminjam buku ini terlebih dahulu untuk dapat membacanya.');
        }
        
        // Log activity
        ActivityLog::log('book_read', "Membaca buku online: {$book->title}", $book);
        
        return view('books.read', compact('book', 'activeLoan'));
    })->name('books.read');

    // Download book (requires active loan)
    Route::get('/books/{book}/download', function (Book $book) {
        $member = Auth::user()->member;
        
        if (!$member) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Check if user has active loan for this book
        $activeLoan = Loan::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->first();
            
        if (!$activeLoan) {
            return redirect('/books/' . $book->id)->with('error', 'Anda harus meminjam buku ini terlebih dahulu untuk dapat mendownloadnya.');
        }
        
        // Check if book has downloadable file
        if (!$book->file_path) {
            return redirect('/books/' . $book->id)->with('error', 'File buku tidak tersedia untuk didownload.');
        }
        
        // Log activity
        ActivityLog::log('book_download', "Mendownload buku: {$book->title}", $book);
        
        // Return file download
        $filePath = storage_path('app/public/' . $book->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $book->title . '.pdf');
        }
        
        return redirect('/books/' . $book->id)->with('error', 'File tidak ditemukan.');
    })->name('books.download');

    // Wishlist
    Route::get('/wishlist', function () {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect('/')->with('error', 'Anda belum terdaftar sebagai anggota.');
        }
        $wishlists = Wishlist::with('book')->where('member_id', $member->id)->latest()->paginate(12);
        return view('wishlist.index', compact('wishlists'));
    });

    Route::post('/wishlist/{book}', function (Book $book) {
        $member = Auth::user()->member;
        if (!$member) {
            return back()->with('error', 'Anda belum terdaftar sebagai anggota.');
        }

        $existing = Wishlist::where('member_id', $member->id)->where('book_id', $book->id)->first();
        if ($existing) {
            return back()->with('error', 'Buku sudah ada di wishlist.');
        }

        Wishlist::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
        ]);

        return back()->with('success', 'Buku ditambahkan ke wishlist!');
    });

    Route::delete('/wishlist/{book}', function (Book $book) {
        $member = Auth::user()->member;
        if (!$member) {
            return back()->with('error', 'Anda belum terdaftar sebagai anggota.');
        }

        Wishlist::where('member_id', $member->id)->where('book_id', $book->id)->delete();

        return back()->with('success', 'Buku dihapus dari wishlist.');
    });

    // Reviews
    Route::post('/books/{book}/review', function (Request $request, Book $book) {
        $member = Auth::user()->member;
        if (!$member) {
            return back()->with('error', 'Anda belum terdaftar sebagai anggota.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('member_id', $member->id)->where('book_id', $book->id)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk buku ini.');
        }

        Review::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        ActivityLog::log('book_review', "Memberikan ulasan untuk buku: {$book->title}", $book);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    });

    // Notifications
    Route::get('/notifications', function () {
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    });

    Route::post('/notifications/{notification}/read', function (Notification $notification) {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        $notification->markAsRead();
        return back();
    });

    Route::post('/notifications/mark-all-read', function () {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    });
});

// Auth Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect admin to admin dashboard
            if (Auth::user()->role === 'admin') {
                return redirect('/admin')->with('success', 'Selamat datang, Admin!');
            }
            
            return redirect()->intended('/')->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    });

    // Register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Create member record
        Member::create([
            'user_id' => $user->id,
            'member_number' => 'MBR-' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'phone' => $validated['phone'],
            'address' => '',
            'join_date' => now(),
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Pendaftaran berhasil! Selamat datang di Perpustakaan Digital.');
    });
});

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('success', 'Anda telah keluar.');
})->middleware('auth')->name('logout');

// =====================
// ADMIN ROUTES
// =====================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/', function () {
        $totalBooks = Book::count();
        $totalMembers = Member::count();
        $totalUsers = User::count();
        $activeLoans = Loan::where('status', 'borrowed')->count();
        $returnedLoans = Loan::where('status', 'returned')->count();
        $overdueLoans = Loan::where('status', 'borrowed')->where('due_date', '<', now())->count();
        $recentLoans = Loan::with(['book', 'member.user'])->latest()->take(5)->get();
        $newMembers = Member::with('user')->latest()->take(5)->get();
        $totalCategories = Category::count();
        $totalAuthors = Author::count();
        $totalPublishers = Publisher::count();
        $totalReviews = Review::count();
        
        // Monthly stats
        $monthlyLoans = Loan::whereMonth('created_at', now()->month)->count();
        $monthlyReturns = Loan::whereMonth('return_date', now()->month)->where('status', 'returned')->count();
        
        return view('admin.dashboard', compact(
            'totalBooks', 'totalMembers', 'totalUsers', 'activeLoans', 'returnedLoans',
            'overdueLoans', 'recentLoans', 'newMembers', 'totalCategories',
            'totalAuthors', 'totalPublishers', 'totalReviews', 'monthlyLoans', 'monthlyReturns'
        ));
    });

    // =====================
    // Books Management
    // =====================
    Route::get('/books', function () {
        $query = Book::with(['categoryRelation', 'authorRelation', 'publisher']);
        
        if (request('search')) {
            $search = request('search');
            $query->search($search);
        }
        
        if (request('category')) {
            $query->where('category', request('category'));
        }

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }
        
        $books = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('admin.books.index', compact('books', 'categories'));
    });

    Route::get('/books/create', function () {
        $categories = Category::all();
        $authors = Author::all();
        $publishers = Publisher::all();
        return view('admin.books.create', compact('categories', 'authors', 'publishers'));
    });

    Route::post('/books', function (Request $request) {
        $bookType = $request->input('book_type', 'physical');
        
        // Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_id' => 'nullable|exists:authors,id',
            'publisher_id' => 'nullable|exists:publishers,id',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'is_featured' => 'nullable|boolean',
            'book_type' => 'required|in:physical,digital',
        ];
        
        // Add type-specific validation rules
        if ($bookType === 'physical') {
            $rules['stock'] = 'required|integer|min:1';
            $rules['shelf_location'] = 'nullable|string|max:50';
            $rules['loan_duration_days'] = 'nullable|integer|min:1|max:90';
        } else {
            $rules['ebook_file'] = 'nullable|mimes:pdf,epub|max:102400';
            $rules['access_duration_days'] = 'nullable|integer|min:1|max:365';
            $rules['access_limit'] = 'nullable|integer|min:0';
            $rules['allow_download'] = 'nullable|boolean';
            $rules['stock'] = 'nullable|integer|min:0';
        }
        
        $validated = $request->validate($rules);
        
        // Set defaults based on book type
        if ($bookType === 'physical') {
            $validated['available_stock'] = $validated['stock'];
            $validated['loan_duration_days'] = $validated['loan_duration_days'] ?? 14;
            $validated['condition'] = 'good';
        } else {
            $validated['stock'] = $validated['stock'] ?? 9999;
            $validated['available_stock'] = $validated['stock'];
            $validated['access_duration_days'] = $validated['access_duration_days'] ?? 7;
            $validated['current_access_count'] = 0;
            $validated['allow_download'] = $request->boolean('allow_download');
            
            // Handle e-book file upload
            if ($request->hasFile('ebook_file')) {
                $validated['file_path'] = $request->file('ebook_file')->store('ebooks', 'public');
            }
        }
        
        $validated['is_featured'] = $request->boolean('is_featured');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        // Handle book file upload (for backward compatibility)
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('books', 'public');
        }
        
        // Remove ebook_file from validated data as it's not a DB column
        unset($validated['ebook_file']);

        $book = Book::create($validated);

        ActivityLog::log('book_create', "Admin menambahkan buku: {$book->title}", $book);

        return redirect('/admin/books')->with('success', 'Buku berhasil ditambahkan!');
    });

    Route::get('/books/{book}/edit', function (Book $book) {
        $categories = Category::all();
        $authors = Author::all();
        $publishers = Publisher::all();
        return view('admin.books.edit', compact('book', 'categories', 'authors', 'publishers'));
    });

    Route::put('/books/{book}', function (Request $request, Book $book) {
        $bookType = $request->input('book_type', $book->book_type ?? 'physical');
        
        // Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $book->id,
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_id' => 'nullable|exists:authors,id',
            'publisher_id' => 'nullable|exists:publishers,id',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'book_type' => 'required|in:physical,digital',
        ];
        
        // Add type-specific validation rules
        if ($bookType === 'physical') {
            $rules['stock'] = 'required|integer|min:0';
            $rules['shelf_location'] = 'nullable|string|max:50';
            $rules['loan_duration_days'] = 'nullable|integer|min:1|max:90';
            $rules['condition'] = 'nullable|in:good,damaged,lost';
        } else {
            $rules['ebook_file'] = 'nullable|mimes:pdf,epub|max:102400';
            $rules['access_duration_days'] = 'nullable|integer|min:1|max:365';
            $rules['access_limit'] = 'nullable|integer|min:0';
            $rules['allow_download'] = 'nullable|boolean';
            $rules['stock'] = 'nullable|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Calculate available stock difference for physical books
        if ($bookType === 'physical') {
            $stockDiff = $validated['stock'] - $book->stock;
            $validated['available_stock'] = max(0, $book->available_stock + $stockDiff);
            $validated['loan_duration_days'] = $validated['loan_duration_days'] ?? $book->loan_duration_days ?? 14;
        } else {
            $validated['stock'] = $validated['stock'] ?? $book->stock ?? 9999;
            $validated['available_stock'] = $validated['stock'];
            $validated['access_duration_days'] = $validated['access_duration_days'] ?? $book->access_duration_days ?? 7;
            $validated['allow_download'] = $request->boolean('allow_download');
            
            // Handle e-book file upload
            if ($request->hasFile('ebook_file')) {
                // Delete old file if exists
                if ($book->file_path) {
                    \Storage::disk('public')->delete($book->file_path);
                }
                $validated['file_path'] = $request->file('ebook_file')->store('ebooks', 'public');
            }
        }
        
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($book->cover_image) {
                \Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        // Handle book file upload (for backward compatibility)
        if ($request->hasFile('file_path')) {
            if ($book->file_path) {
                \Storage::disk('public')->delete($book->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('books', 'public');
        }
        
        // Remove ebook_file from validated data as it's not a DB column
        unset($validated['ebook_file']);

        $book->update($validated);

        ActivityLog::log('book_update', "Admin memperbarui buku: {$book->title}", $book);

        return redirect('/admin/books')->with('success', 'Buku berhasil diperbarui!');
    });

    Route::delete('/books/{book}', function (Book $book) {
        $title = $book->title;
        
        // Delete associated files
        if ($book->cover_image) {
            \Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->file_path) {
            \Storage::disk('public')->delete($book->file_path);
        }
        
        $book->delete();

        ActivityLog::log('book_delete', "Admin menghapus buku: {$title}");

        return redirect('/admin/books')->with('success', 'Buku berhasil dihapus!');
    });

    // =====================
    // Categories Management
    // =====================
    Route::get('/categories', function () {
        $categories = Category::withCount('books')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    });

    Route::post('/categories', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        Category::create($validated);

        ActivityLog::log('category_create', "Admin menambahkan kategori: {$validated['name']}");

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    });

    Route::put('/categories/{category}', function (Request $request, Category $category) {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $category->update($validated);

        ActivityLog::log('category_update', "Admin memperbarui kategori: {$validated['name']}", $category);

        return back()->with('success', 'Kategori berhasil diperbarui!');
    });

    Route::delete('/categories/{category}', function (Category $category) {
        $name = $category->name;
        $category->delete();

        ActivityLog::log('category_delete', "Admin menghapus kategori: {$name}");

        return back()->with('success', 'Kategori berhasil dihapus!');
    });

    // =====================
    // Authors Management
    // =====================
    Route::get('/authors', function () {
        $authors = Author::withCount('books')->latest()->paginate(15);
        return view('admin.authors.index', compact('authors'));
    });

    Route::post('/authors', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        }

        Author::create($validated);

        ActivityLog::log('author_create', "Admin menambahkan penulis: {$validated['name']}");

        return back()->with('success', 'Penulis berhasil ditambahkan!');
    });

    Route::put('/authors/{author}', function (Request $request, Author $author) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($author->photo) {
                \Storage::disk('public')->delete($author->photo);
            }
            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        }

        $author->update($validated);

        ActivityLog::log('author_update', "Admin memperbarui penulis: {$validated['name']}", $author);

        return back()->with('success', 'Penulis berhasil diperbarui!');
    });

    Route::delete('/authors/{author}', function (Author $author) {
        $name = $author->name;
        if ($author->photo) {
            \Storage::disk('public')->delete($author->photo);
        }
        $author->delete();

        ActivityLog::log('author_delete', "Admin menghapus penulis: {$name}");

        return back()->with('success', 'Penulis berhasil dihapus!');
    });

    // =====================
    // Publishers Management
    // =====================
    Route::get('/publishers', function () {
        $publishers = Publisher::withCount('books')->latest()->paginate(15);
        return view('admin.publishers.index', compact('publishers'));
    });

    Route::post('/publishers', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        Publisher::create($validated);

        ActivityLog::log('publisher_create', "Admin menambahkan penerbit: {$validated['name']}");

        return back()->with('success', 'Penerbit berhasil ditambahkan!');
    });

    Route::put('/publishers/{publisher}', function (Request $request, Publisher $publisher) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $publisher->update($validated);

        ActivityLog::log('publisher_update', "Admin memperbarui penerbit: {$validated['name']}", $publisher);

        return back()->with('success', 'Penerbit berhasil diperbarui!');
    });

    Route::delete('/publishers/{publisher}', function (Publisher $publisher) {
        $name = $publisher->name;
        $publisher->delete();

        ActivityLog::log('publisher_delete', "Admin menghapus penerbit: {$name}");

        return back()->with('success', 'Penerbit berhasil dihapus!');
    });

    // =====================
    // Users Management
    // =====================
    Route::get('/users', function () {
        $query = User::with('member');
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if (request('role')) {
            $query->where('role', request('role'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $users = $query->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    });

    Route::post('/users', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        if ($validated['role'] === 'user') {
            Member::create([
                'user_id' => $user->id,
                'member_number' => 'MBR-' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'phone' => $validated['phone'] ?? '',
                'address' => '',
                'join_date' => now(),
                'status' => 'active',
            ]);
        }

        ActivityLog::log('user_create', "Admin menambahkan user: {$user->name}", $user);

        return back()->with('success', 'User berhasil ditambahkan!');
    });

    Route::patch('/users/{user}/toggle-status', function (User $user) {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengubah status akun sendiri.');
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        if ($user->member) {
            $user->member->status = $user->status === 'active' ? 'active' : 'suspended';
            $user->member->save();
        }

        ActivityLog::log('user_status_change', "Admin mengubah status user: {$user->name} menjadi {$user->status}", $user);

        return back()->with('success', 'Status user berhasil diubah!');
    });

    Route::patch('/users/{user}/reset-password', function (User $user) {
        $newPassword = 'password123';
        $user->update(['password' => Hash::make($newPassword)]);

        ActivityLog::log('user_password_reset', "Admin mereset password user: {$user->name}", $user);

        return back()->with('success', "Password user berhasil direset menjadi: {$newPassword}");
    });

    Route::delete('/users/{user}', function (User $user) {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('user_delete', "Admin menghapus user: {$name}");

        return back()->with('success', 'User berhasil dihapus!');
    });

    // =====================
    // Members Management
    // =====================
    Route::get('/members', function () {
        $query = Member::with('user');
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('member_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $members = $query->latest()->paginate(15);
        return view('admin.members.index', compact('members'));
    });

    Route::patch('/members/{member}/toggle-status', function (Member $member) {
        $member->status = $member->status === 'active' ? 'suspended' : 'active';
        $member->save();

        ActivityLog::log('member_status_change', "Admin mengubah status anggota: {$member->user->name} menjadi {$member->status}", $member);

        return back()->with('success', 'Status anggota berhasil diubah!');
    });

    // =====================
    // Loans Management
    // =====================
    Route::get('/loans', function () {
        $query = Loan::with(['book', 'member.user']);
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('book', function($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                })->orWhereHas('member.user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        // Filter by loan type
        if (request('type')) {
            $query->where('loan_type', request('type'));
        }
        
        if (request('status') == 'overdue') {
            $query->overdue();
        } elseif (request('status')) {
            $query->where('status', request('status'));
        }
        
        $loans = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Loan::count(),
            'pending' => Loan::where('status', 'pending')->count(),
            'approved' => Loan::where('status', 'approved')->count(),
            'borrowed' => Loan::where('status', 'borrowed')->count(),
            'returned' => Loan::where('status', 'returned')->count(),
            'overdue' => Loan::overdue()->count(),
            'completed' => Loan::where('status', 'completed')->count(),
        ];
        
        return view('admin.loans.index', compact('loans', 'stats'));
    });

    // Approve loan (Physical book)
    Route::patch('/loans/{loan}/approve', function (Loan $loan) {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu persetujuan.');
        }

        if (($loan->loan_type ?? 'physical') !== 'physical') {
            return back()->with('error', 'E-Book tidak memerlukan persetujuan.');
        }

        // Check stock availability
        if ($loan->book->available_stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $loan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send notification to user
        Notification::send(
            $loan->member->user_id,
            'loan_approved',
            'Peminjaman Disetujui',
            "Peminjaman buku '{$loan->book->title}' telah disetujui. Silakan ambil di perpustakaan." . ($loan->book->shelf_location ? " Lokasi: {$loan->book->shelf_location}" : ''),
            ['loan_id' => $loan->id, 'book_id' => $loan->book_id]
        );

        ActivityLog::log('loan_approve', "Admin menyetujui peminjaman buku: {$loan->book->title} untuk {$loan->member->user->name}", $loan);

        return back()->with('success', 'Peminjaman berhasil disetujui!');
    });

    // Reject loan (Physical book)
    Route::patch('/loans/{loan}/reject', function (Request $request, Loan $loan) {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu persetujuan.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $loan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send notification to user
        Notification::send(
            $loan->member->user_id,
            'loan_rejected',
            'Peminjaman Ditolak',
            "Peminjaman buku '{$loan->book->title}' ditolak. Alasan: {$validated['rejection_reason']}",
            ['loan_id' => $loan->id, 'book_id' => $loan->book_id]
        );

        ActivityLog::log('loan_reject', "Admin menolak peminjaman buku: {$loan->book->title}. Alasan: {$validated['rejection_reason']}", $loan);

        return back()->with('success', 'Peminjaman berhasil ditolak.');
    });

    // Mark as picked up (Physical book)
    Route::patch('/loans/{loan}/pickup', function (Loan $loan) {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Peminjaman belum disetujui atau sudah diambil.');
        }

        // Check stock one more time
        if ($loan->book->available_stock <= 0) {
            $loan->update(['status' => 'rejected', 'rejection_reason' => 'Stok habis']);
            return back()->with('error', 'Stok buku habis. Peminjaman dibatalkan.');
        }

        $loanDuration = $loan->book->loan_duration_days ?? Setting::get('loan_duration_days', 14);

        $loan->update([
            'status' => 'borrowed',
            'picked_up_at' => now(),
            'loan_date' => now(),
            'due_date' => now()->addDays($loanDuration),
        ]);

        // Decrease stock
        $loan->book->decrement('available_stock');

        // Send notification to user
        Notification::send(
            $loan->member->user_id,
            'loan',
            'Buku Berhasil Dipinjam',
            "Anda telah mengambil buku '{$loan->book->title}'. Harap kembalikan sebelum " . now()->addDays($loanDuration)->format('d M Y'),
            ['loan_id' => $loan->id, 'book_id' => $loan->book_id]
        );

        ActivityLog::log('loan_pickup', "Buku '{$loan->book->title}' diambil oleh {$loan->member->user->name}", $loan);

        return back()->with('success', 'Buku berhasil diserahkan! Jatuh tempo: ' . now()->addDays($loanDuration)->format('d M Y'));
    });

    // Return book with admin verification
    Route::patch('/loans/{loan}/return', function (Request $request, Loan $loan) {
        if (!in_array($loan->status, ['borrowed', 'overdue'])) {
            return back()->with('error', 'Buku sudah dikembalikan atau belum dipinjam.');
        }

        // Only physical books need admin verification
        if (($loan->loan_type ?? 'physical') !== 'physical') {
            return back()->with('error', 'E-Book dikembalikan secara otomatis.');
        }

        $fineAmount = $request->input('fine_amount', $loan->calculateFine());
        $conditionNotes = $request->input('condition_notes');

        $loan->update([
            'status' => 'returned',
            'return_date' => now(),
            'fine_amount' => $fineAmount,
            'return_condition_notes' => $conditionNotes,
            'returned_to' => Auth::id(),
        ]);

        // Increase stock
        $loan->book->increment('available_stock');

        // Send notification to user
        $message = "Buku '{$loan->book->title}' telah berhasil dikembalikan.";
        if ($fineAmount > 0) {
            $message .= " Denda keterlambatan: Rp " . number_format($fineAmount, 0, ',', '.');
        }

        Notification::send(
            $loan->member->user_id,
            'return',
            'Pengembalian Buku',
            $message,
            ['loan_id' => $loan->id]
        );

        ActivityLog::log('loan_return', "Admin memproses pengembalian buku: {$loan->book->title} dari {$loan->member->user->name}", $loan);

        return back()->with('success', 'Buku berhasil dikembalikan!' . ($fineAmount > 0 ? " Denda: Rp " . number_format($fineAmount, 0, ',', '.') : ''));
    });

    Route::patch('/loans/{loan}/extend', function (Loan $loan) {
        if ($loan->status !== 'borrowed') {
            return back()->with('error', 'Peminjaman sudah selesai.');
        }

        $loan->update([
            'due_date' => $loan->due_date->addDays(7),
        ]);

        Notification::send(
            $loan->member->user_id,
            'loan',
            'Perpanjangan Peminjaman',
            "Masa peminjaman buku '{$loan->book->title}' diperpanjang hingga " . $loan->due_date->format('d M Y'),
            ['loan_id' => $loan->id]
        );

        ActivityLog::log('loan_extend', "Admin memperpanjang peminjaman buku: {$loan->book->title}", $loan);

        return back()->with('success', 'Masa peminjaman berhasil diperpanjang!');
    });

    // =====================
    // Reports
    // =====================
    Route::get('/reports', function () {
        // Loan statistics
        $loanStats = [
            'total' => Loan::count(),
            'thisMonth' => Loan::whereMonth('created_at', now()->month)->count(),
            'lastMonth' => Loan::whereMonth('created_at', now()->subMonth()->month)->count(),
            'borrowed' => Loan::where('status', 'borrowed')->count(),
            'returned' => Loan::where('status', 'returned')->count(),
            'overdue' => Loan::overdue()->count(),
        ];

        // Popular books
        $popularBooks = Book::withCount('loans')
            ->orderBy('loans_count', 'desc')
            ->take(10)
            ->get();

        // Active members
        $activeMembers = Member::withCount(['loans' => function($q) {
            $q->whereMonth('created_at', now()->month);
        }])
            ->orderBy('loans_count', 'desc')
            ->take(10)
            ->get();

        // Monthly loan chart data
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'loans' => Loan::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'returns' => Loan::whereYear('return_date', $date->year)
                    ->whereMonth('return_date', $date->month)
                    ->where('status', 'returned')
                    ->count(),
            ];
        }

        return view('admin.reports.index', compact('loanStats', 'popularBooks', 'activeMembers', 'monthlyData'));
    });

    // =====================
    // Activity Logs
    // =====================
    Route::get('/logs', function () {
        $query = ActivityLog::with('user');
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (request('action')) {
            $query->where('action', request('action'));
        }
        
        $logs = $query->latest('created_at')->paginate(20);
        
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        
        return view('admin.logs.index', compact('logs', 'actions'));
    });

    // =====================
    // Settings
    // =====================
    Route::get('/settings', function () {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    });

    Route::post('/settings', function (Request $request) {
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log('settings_update', "Admin memperbarui pengaturan sistem");

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    });
});

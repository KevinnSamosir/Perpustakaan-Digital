@extends('layouts.dashboard')

@section('title', ($book->title ?? 'Detail Buku'))
@section('page-title', 'Detail Buku')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ url('/') }}" class="text-gray-500 hover:text-primary">Beranda</a></li>
                <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                <li><a href="{{ url('/books') }}" class="text-gray-500 hover:text-primary">Katalog Buku</a></li>
                <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                <li class="text-gray-800 font-medium">{{ $book->title ?? 'Detail' }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Book Cover -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <!-- Book Type Badge -->
                    <div class="flex justify-center mb-4">
                        @if($book->book_type === 'digital')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-tablet-alt mr-2"></i>E-Book
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-book mr-2"></i>Buku Fisik
                        </span>
                        @endif
                    </div>

                    <div class="aspect-[3/4] bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mb-6 overflow-hidden relative">
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($book->title) }}&background=6366f1&color=fff&size=400&font-size=0.15';">
                        
                        @if($book->book_type === 'digital')
                        <div class="absolute top-2 right-2 bg-purple-600 text-white px-2 py-1 rounded text-xs">
                            <i class="fas fa-file-pdf mr-1"></i>{{ strtoupper($book->file_type ?? 'PDF') }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Rating Display -->
                    <div class="text-center mb-4">
                        <div class="flex items-center justify-center space-x-1 mb-1">
                            @php
                                $avgRating = $book->averageRating ?? 0;
                                $reviewsCount = $book->reviewsCount ?? 0;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600">{{ number_format($avgRating, 1) }} ({{ $reviewsCount }} ulasan)</p>
                    </div>

                    @auth
                        @php
                            $member = Auth::user()->member;
                            $isWishlisted = $member ? $member->hasWishlisted($book->id) : false;
                            $hasBorrowed = $member ? $member->hasBorrowed($book->id) : false;
                            
                            // Get active loan or pending request
                            $activeLoan = $member ? \App\Models\Loan::where('member_id', $member->id)
                                ->where('book_id', $book->id)
                                ->whereIn('status', ['pending', 'approved', 'borrowed', 'overdue'])
                                ->first() : null;
                            
                            $isPhysicalBook = ($book->book_type ?? 'physical') === 'physical';
                            $isDigitalBook = ($book->book_type ?? 'physical') === 'digital';
                        @endphp

                        @if($activeLoan)
                            @if($activeLoan->status === 'pending')
                            <!-- Pending Approval (Physical Book) -->
                            <div class="mb-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                                    </div>
                                    <p class="text-sm text-yellow-800 font-medium mb-1">Menunggu Persetujuan Admin</p>
                                    <p class="text-xs text-yellow-600">Permintaan peminjaman Anda sedang diproses</p>
                                    <p class="text-xs text-gray-500 mt-2">Diajukan: {{ $activeLoan->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @elseif($activeLoan->status === 'approved')
                            <!-- Approved - Ready for Pickup (Physical Book) -->
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                    </div>
                                    <p class="text-sm text-blue-800 font-medium mb-1">Peminjaman Disetujui!</p>
                                    <p class="text-xs text-blue-600">Silakan ambil buku di perpustakaan</p>
                                    @if($book->shelf_location)
                                    <p class="text-xs text-gray-600 mt-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Lokasi: {{ $book->shelf_location }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            
                            @elseif(in_array($activeLoan->status, ['borrowed', 'overdue']))
                            <!-- Currently Borrowed -->
                            <div class="mb-4 p-4 {{ $activeLoan->status === 'overdue' ? 'bg-red-50 border-red-200' : 'bg-indigo-50 border-indigo-100' }} rounded-lg border">
                                <div class="text-center mb-3">
                                    <p class="text-sm {{ $activeLoan->status === 'overdue' ? 'text-red-700' : 'text-indigo-700' }} font-medium mb-1">
                                        <i class="fas fa-book-reader mr-1"></i>
                                        @if($isDigitalBook)
                                            Anda sedang mengakses e-book ini
                                        @else
                                            Anda sedang meminjam buku ini
                                        @endif
                                    </p>
                                    <p class="text-xs {{ $activeLoan->status === 'overdue' ? 'text-red-600' : 'text-indigo-600' }}">
                                        @if($isDigitalBook)
                                            Akses berakhir: {{ $activeLoan->access_expires_at ? $activeLoan->access_expires_at->format('d M Y H:i') : $activeLoan->due_date->format('d M Y') }}
                                        @else
                                            Jatuh tempo: {{ $activeLoan->due_date->format('d M Y') }}
                                        @endif
                                        @if($activeLoan->status === 'overdue' || ($activeLoan->due_date && $activeLoan->due_date < now()))
                                        <span class="text-red-600 font-bold block mt-1">(TERLAMBAT!)</span>
                                        @elseif($activeLoan->due_date && $activeLoan->due_date->diffInDays(now()) <= 3)
                                        <span class="text-amber-600">({{ $activeLoan->due_date->diffInDays(now()) }} hari lagi)</span>
                                        @endif
                                    </p>
                                </div>
                                
                                @if($isDigitalBook)
                                <!-- E-Book Access Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ url('/books/' . $book->id . '/read') }}" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition text-center text-sm">
                                        <i class="fas fa-book-open mr-1"></i>Baca Online
                                    </a>
                                    @if($book->allow_download && $book->file_path)
                                    <a href="{{ url('/books/' . $book->id . '/download') }}" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition text-center text-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                    @endif
                                </div>
                                @else
                                <!-- Physical Book - Return Button -->
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>Untuk mengembalikan, kunjungi perpustakaan
                                    </p>
                                </div>
                                @endif
                            </div>
                            @endif
                            
                        @else
                            <!-- No Active Loan - Show Borrow Button -->
                            @if($isDigitalBook)
                                <!-- E-Book: Langsung akses -->
                                @if($book->is_active)
                                    @php
                                        $canAccess = !$book->access_limit || ($book->current_access_count < $book->access_limit);
                                    @endphp
                                    @if($canAccess)
                                    <form action="{{ url('/books/' . $book->id . '/borrow') }}" method="POST" class="mb-3">
                                        @csrf
                                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                                            <i class="fas fa-tablet-alt mr-2"></i>Akses E-Book
                                        </button>
                                    </form>
                                    <p class="text-center text-xs text-gray-500 mb-2">
                                        <i class="fas fa-clock mr-1"></i>Durasi akses: {{ $book->access_duration_days ?? 7 }} hari
                                    </p>
                                    @else
                                    <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed mb-3">
                                        <i class="fas fa-users mr-2"></i>Limit Akses Tercapai
                                    </button>
                                    <p class="text-center text-xs text-gray-500 mb-2">
                                        Silakan coba lagi nanti
                                    </p>
                                    @endif
                                @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed mb-3">
                                    <i class="fas fa-ban mr-2"></i>E-Book Tidak Aktif
                                </button>
                                @endif
                            @else
                                <!-- Physical Book: Ajukan Peminjaman -->
                                @if(($book->available_stock ?? 0) > 0)
                                <form action="{{ url('/books/' . $book->id . '/borrow') }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-secondary transition">
                                        <i class="fas fa-hand-holding mr-2"></i>Ajukan Peminjaman
                                    </button>
                                </form>
                                <p class="text-center text-xs text-gray-500 mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>Menunggu persetujuan admin
                                </p>
                                @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed mb-3">
                                    <i class="fas fa-times-circle mr-2"></i>Stok Habis
                                </button>
                                @endif
                            @endif
                        @endif

                        <!-- Wishlist Button -->
                        <form action="{{ url('/wishlist/' . $book->id) }}" method="POST">
                            @csrf
                            @if($isWishlisted)
                            @method('DELETE')
                            <button type="submit" class="w-full border-2 border-pink-500 text-pink-500 py-2 rounded-lg font-semibold hover:bg-pink-50 transition">
                                <i class="fas fa-heart mr-2"></i>Hapus dari Wishlist
                            </button>
                            @else
                            <button type="submit" class="w-full border-2 border-gray-300 text-gray-600 py-2 rounded-lg font-semibold hover:border-pink-500 hover:text-pink-500 transition">
                                <i class="far fa-heart mr-2"></i>Tambah ke Wishlist
                            </button>
                            @endif
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-secondary transition text-center mb-3">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Meminjam
                        </a>
                    @endauth
                    
                    <!-- Availability Info -->
                    <div class="text-center text-sm mt-3">
                        @if($book->book_type === 'digital')
                            @if($book->access_limit)
                                @php $remaining = max(0, $book->access_limit - ($book->current_access_count ?? 0)); @endphp
                                <p class="{{ $remaining > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    @if($remaining > 0)
                                    <i class="fas fa-check-circle mr-1"></i>{{ $remaining }} slot akses tersedia
                                    @else
                                    <i class="fas fa-clock mr-1"></i>Semua slot sedang digunakan
                                    @endif
                                </p>
                            @else
                                <p class="text-green-600">
                                    <i class="fas fa-infinity mr-1"></i>Akses tidak terbatas
                                </p>
                            @endif
                        @else
                            <p class="{{ ($book->available_stock ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">
                                @if(($book->available_stock ?? 0) > 0)
                                <i class="fas fa-check-circle mr-1"></i>{{ $book->available_stock }} buku tersedia
                                @else
                                <i class="fas fa-clock mr-1"></i>Sedang dipinjam semua
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Book Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-8 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <span class="bg-blue-100 text-primary text-sm px-4 py-1 rounded-full">{{ $book->category ?? 'Novel' }}</span>
                        <span class="text-gray-500 text-sm">{{ $book->publication_year ?? '2020' }}</span>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $book->title ?? 'Laskar Pelangi' }}</h1>
                    <p class="text-xl text-gray-600 mb-6">oleh <span class="text-primary font-medium">{{ $book->author ?? 'Andrea Hirata' }}</span></p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">ISBN</p>
                            <p class="font-semibold text-gray-800">{{ $book->isbn ?? '978-602-xxx' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Tahun Terbit</p>
                            <p class="font-semibold text-gray-800">{{ $book->publication_year ?? '2005' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Total Stok</p>
                            <p class="font-semibold text-gray-800">{{ $book->stock ?? 10 }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Tersedia</p>
                            <p class="font-semibold {{ ($book->available_stock ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $book->available_stock ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-3">Deskripsi</h2>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $book->description ?? 'Deskripsi buku belum tersedia.' }}
                        </p>
                    </div>

                    <!-- Borrow Section (Desktop) -->
                    <div class="hidden lg:block border-t pt-6">
                        @auth
                            @php
                                $memberDetail = Auth::user()->member;
                                $isCurrentlyBorrowed = $memberDetail ? \App\Models\Loan::where('member_id', $memberDetail->id)
                                    ->where('book_id', $book->id)
                                    ->whereIn('status', ['borrowed', 'overdue'])
                                    ->exists() : false;
                                $activeLoanDetail = $memberDetail ? \App\Models\Loan::where('member_id', $memberDetail->id)
                                    ->where('book_id', $book->id)
                                    ->whereIn('status', ['borrowed', 'overdue'])
                                    ->first() : null;
                            @endphp

                            @if($isCurrentlyBorrowed && $activeLoanDetail)
                            <!-- Currently Borrowed Status -->
                            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-book-reader text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-indigo-800">Anda Sedang Meminjam Buku Ini</h3>
                                        <p class="text-sm text-indigo-600">
                                            Jatuh tempo: {{ $activeLoanDetail->due_date->format('d M Y') }}
                                            @if($activeLoanDetail->due_date < now())
                                            <span class="text-red-600 font-bold ml-2">(TERLAMBAT!)</span>
                                            @elseif($activeLoanDetail->due_date->diffInDays(now()) <= 3)
                                            <span class="text-amber-600 ml-2">({{ $activeLoanDetail->due_date->diffInDays(now()) }} hari lagi)</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-3 mt-4">
                                    <a href="{{ url('/books/' . $book->id . '/read') }}" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition text-center">
                                        <i class="fas fa-book-open mr-2"></i>Baca Online
                                    </a>
                                    @if($book->file_path)
                                    <a href="{{ url('/books/' . $book->id . '/download') }}" class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-semibold hover:bg-emerald-700 transition text-center">
                                        <i class="fas fa-download mr-2"></i>Download
                                    </a>
                                    @endif
                                    <form action="{{ route('loans.return', $activeLoanDetail->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" onclick="return confirm('Konfirmasi pengembalian buku ini?')" 
                                                class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold hover:bg-amber-600 transition">
                                            <i class="fas fa-undo-alt mr-2"></i>Kembalikan
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @elseif(($book->available_stock ?? 0) > 0)
                            <!-- Borrow Button -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-lg">Tertarik dengan buku ini?</h3>
                                        <p class="text-sm text-gray-600">{{ $book->available_stock }} eksemplar tersedia untuk dipinjam</p>
                                    </div>
                                    <form action="{{ url('/books/' . $book->id . '/borrow') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center gap-2 shadow-lg hover:shadow-xl">
                                            <i class="fas fa-hand-holding text-lg"></i>
                                            <span>Pinjam Buku</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <!-- Out of Stock -->
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-gray-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-700">Stok Sedang Habis</h3>
                                        <p class="text-sm text-gray-500">Semua buku sedang dipinjam. Silakan tambahkan ke wishlist untuk notifikasi ketersediaan.</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @else
                            <!-- Login Required -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-lg">Ingin meminjam buku ini?</h3>
                                        <p class="text-sm text-gray-600">Silakan login terlebih dahulu untuk meminjam buku</p>
                                    </div>
                                    <a href="{{ url('/login') }}" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-secondary transition flex items-center gap-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Login untuk Meminjam</span>
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-xl shadow-md p-8 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-star text-yellow-400 mr-2"></i>Ulasan & Rating
                        </h2>
                    </div>

                    @auth
                    @php
                        $member = Auth::user()->member;
                        $hasReviewed = $member ? $member->hasReviewed($book->id) : false;
                        $userReview = $member ? $book->reviews()->where('member_id', $member->id)->first() : null;
                    @endphp

                    @if(!$hasReviewed)
                    <!-- Review Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Tulis Ulasan Anda</h4>
                        <form action="{{ url('/books/' . $book->id . '/review') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex space-x-2" id="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn" data-rating="{{ $i }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="5" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ulasan (opsional)</label>
                                <textarea name="review" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Bagikan pengalaman Anda membaca buku ini..."></textarea>
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Ulasan
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <p class="text-green-700"><i class="fas fa-check-circle mr-2"></i>Anda sudah memberikan ulasan untuk buku ini.</p>
                    </div>
                    @endif
                    @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-center">
                        <p class="text-blue-700 mb-2">Silakan login untuk memberikan ulasan</p>
                        <a href="{{ url('/login') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    </div>
                    @endauth

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        @forelse($book->reviews()->with('member.user')->latest()->take(5)->get() as $review)
                        <div class="border-b border-gray-100 pb-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $review->member->user->avatarUrl ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->member->user->name) }}" 
                                         alt="{{ $review->member->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $review->member->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($review->review)
                            <p class="text-gray-600 text-sm">{{ $review->review }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comment-slash text-4xl mb-3"></i>
                            <p>Belum ada ulasan untuk buku ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Related Books -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Buku Terkait</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $relatedBooks = \App\Models\Book::where('category', $book->category)
                                ->where('id', '!=', $book->id)
                                ->take(4)
                                ->get();
                        @endphp
                        @forelse($relatedBooks as $related)
                        <a href="{{ url('/books/' . $related->id) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                            <div class="aspect-[3/4] bg-gradient-to-br from-blue-400 to-purple-500 rounded flex items-center justify-center mb-3 overflow-hidden">
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($related->title) }}&background=6366f1&color=fff&size=200&font-size=0.2';">
                            </div>
                            <h3 class="font-medium text-gray-800 text-sm truncate">{{ $related->title }}</h3>
                            <p class="text-gray-500 text-xs">{{ $related->author }}</p>
                        </a>
                        @empty
                        <p class="col-span-4 text-center text-gray-500 py-4">Tidak ada buku terkait</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setRating(rating) {
        document.getElementById('rating-input').value = rating;
        document.querySelectorAll('.star-btn').forEach((btn, index) => {
            if (index < rating) {
                btn.classList.remove('text-gray-300');
                btn.classList.add('text-yellow-400');
            } else {
                btn.classList.remove('text-yellow-400');
                btn.classList.add('text-gray-300');
            }
        });
    }
    // Set default 5 stars
    setRating(5);
</script>
@endpush

@extends('layouts.app')

@section('title', 'Beranda - Perpustakaan Digital')

@section('content')
    <!-- Hero Section - Modern Gradient -->
    <section class="hero-gradient text-white py-24 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-20 w-96 h-96 bg-indigo-400 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span>Perpustakaan Digital Terbaik</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Baca, Pinjam &<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-purple-200">Kembangkan</span> Wawasan
                    </h1>
                    
                    <p class="text-lg text-indigo-100 mb-10 max-w-lg">
                        Akses ribuan koleksi buku dengan mudah. Pinjam, baca, dan kembangkan pengetahuan Anda kapan saja, di mana saja.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/books') }}" class="inline-flex items-center justify-center gap-2 bg-white text-indigo-700 px-6 py-3.5 rounded-xl font-semibold hover:bg-indigo-50 transition shadow-lg shadow-indigo-900/30">
                            <i class="fas fa-search"></i>
                            <span>Jelajahi Katalog</span>
                        </a>
                        @guest
                        <a href="{{ url('/register') }}" class="inline-flex items-center justify-center gap-2 btn-secondary text-white px-6 py-3.5 rounded-xl font-semibold">
                            <i class="fas fa-user-plus"></i>
                            <span>Daftar Gratis</span>
                        </a>
                        @else
                        <a href="{{ url('/loans') }}" class="inline-flex items-center justify-center gap-2 btn-secondary text-white px-6 py-3.5 rounded-xl font-semibold">
                            <i class="fas fa-book-reader"></i>
                            <span>Peminjaman Saya</span>
                        </a>
                        @endguest
                    </div>
                </div>
                
                <div class="hidden lg:flex justify-center">
                    <div class="relative">
                        <!-- Floating Cards -->
                        <div class="absolute -top-8 -left-8 w-32 h-44 bg-white/10 backdrop-blur-lg rounded-2xl transform rotate-12 border border-white/20"></div>
                        <div class="absolute -bottom-8 -right-8 w-32 h-44 bg-white/10 backdrop-blur-lg rounded-2xl transform -rotate-12 border border-white/20"></div>
                        
                        <!-- Main Card -->
                        <div class="relative bg-white/10 backdrop-blur-lg rounded-3xl p-10 border border-white/20">
                            <div class="text-center">
                                <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-book-open text-white text-4xl"></i>
                                </div>
                                <p class="text-lg font-medium text-white/80">Perpustakaan Digital</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - Modern Clean -->
    <section class="py-16 bg-white relative -mt-12 rounded-t-3xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 -mt-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 text-center card-modern">
                    <div class="w-14 h-14 mx-auto mb-4 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-indigo-600 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-1">{{ $totalBooks ?? '1,000+' }}</div>
                    <div class="text-sm text-slate-500">Koleksi Buku</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 text-center card-modern">
                    <div class="w-14 h-14 mx-auto mb-4 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-1">{{ $totalMembers ?? '500+' }}</div>
                    <div class="text-sm text-slate-500">Anggota Aktif</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 text-center card-modern">
                    <div class="w-14 h-14 mx-auto mb-4 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-amber-600 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-1">{{ $totalLoans ?? '2,500+' }}</div>
                    <div class="text-sm text-slate-500">Peminjaman</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 text-center card-modern">
                    <div class="w-14 h-14 mx-auto mb-4 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-1">50+</div>
                    <div class="text-sm text-slate-500">Kategori</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 rounded-full text-sm font-medium text-indigo-600 mb-4">
                    <i class="fas fa-star"></i> Fitur Unggulan
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">Kemudahan dalam Genggaman</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Nikmati berbagai kemudahan dalam mengelola dan mengakses perpustakaan digital kami</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 border border-slate-100 card-modern">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-search text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Pencarian Canggih</h3>
                    <p class="text-slate-500 leading-relaxed">Temukan buku dengan cepat melalui pencarian berdasarkan judul, penulis, ISBN, atau kategori.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 border border-slate-100 card-modern">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Peminjaman Online</h3>
                    <p class="text-slate-500 leading-relaxed">Pinjam buku secara online tanpa perlu antre. Cukup pilih dan ajukan peminjaman dari rumah.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 border border-slate-100 card-modern">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Pengingat Otomatis</h3>
                    <p class="text-slate-500 leading-relaxed">Dapatkan notifikasi pengingat sebelum masa peminjaman berakhir agar tidak terkena denda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Books Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 mb-2">Buku Terbaru</h2>
                    <p class="text-slate-500">Koleksi terbaru yang baru ditambahkan</p>
                </div>
                <a href="{{ url('/books') }}" class="mt-4 sm:mt-0 inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($latestBooks ?? [] as $book)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden card-modern">
                    <div class="h-56 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative">
                        @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                        <i class="fas fa-book text-white text-5xl opacity-50"></i>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white/90 text-xs font-medium {{ $book->available_stock > 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $book->available_stock > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-xs font-medium text-indigo-600 mb-3">
                            {{ $book->category ?? 'Umum' }}
                        </span>
                        <h3 class="font-semibold text-slate-800 mb-1 truncate">{{ $book->title }}</h3>
                        <p class="text-sm text-slate-500 truncate">{{ $book->author }}</p>
                    </div>
                </div>
                @empty
                @foreach([
                    ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'category' => 'Novel'],
                    ['title' => 'Bumi Manusia', 'author' => 'Pramoedya A. T.', 'category' => 'Sejarah'],
                    ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'category' => 'Self-Help'],
                    ['title' => 'Atomic Habits', 'author' => 'James Clear', 'category' => 'Produktivitas'],
                ] as $book)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden card-modern">
                    <div class="h-56 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative">
                        <i class="fas fa-book text-white text-5xl opacity-50"></i>
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white/90 text-xs font-medium text-emerald-700">
                                Tersedia
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-xs font-medium text-indigo-600 mb-3">
                            {{ $book['category'] }}
                        </span>
                        <h3 class="font-semibold text-slate-800 mb-1 truncate">{{ $book['title'] }}</h3>
                        <p class="text-sm text-slate-500 truncate">{{ $book['author'] }}</p>
                    </div>
                </div>
                @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    @guest
    <section class="py-20 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Menjelajahi Dunia Literasi?</h2>
            <p class="text-indigo-100 mb-10 text-lg max-w-2xl mx-auto">Daftar sekarang dan dapatkan akses ke ribuan koleksi buku kami secara gratis!</p>
            <a href="{{ url('/register') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-50 transition shadow-xl">
                <i class="fas fa-rocket"></i>
                <span>Mulai Sekarang</span>
            </a>
        </div>
    </section>
    @else
    <section class="py-20 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative">
            <div class="w-20 h-20 mx-auto mb-6 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <span class="text-4xl">👋</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Hai, {{ Auth::user()->name }}!</h2>
            <p class="text-indigo-100 mb-10 text-lg">Temukan buku-buku menarik dan mulai pinjam sekarang!</p>
            <a href="{{ url('/books') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-50 transition shadow-xl">
                <i class="fas fa-book"></i>
                <span>Lihat Katalog Buku</span>
            </a>
        </div>
    </section>
    @endguest
@endsection

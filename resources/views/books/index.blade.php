@extends('layouts.dashboard')

@section('title', 'Katalog Buku')
@section('page-title', 'Katalog Buku')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Katalog Buku</h1>
            <p class="text-sm text-slate-500 mt-1">Temukan buku favorit Anda dari koleksi kami</p>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form action="{{ url('/books') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Cari Buku</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Judul, penulis, atau ISBN..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition">
                    <i class="fas fa-search absolute left-3 top-3.5 text-slate-400"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                <select name="category" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $cat)
                    <option value="{{ $cat->slug ?? strtolower($cat->name) }}" {{ request('category') == ($cat->slug ?? strtolower($cat->name)) ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full btn-primary px-6 py-2.5 rounded-lg text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span class="text-sm">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span class="text-sm">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($books ?? [] as $book)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden card-modern group">
            <a href="{{ url('/books/'.$book->id) }}" class="block">
                <div class="h-48 bg-gradient-to-br from-accent-500 to-purple-600 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($book->title) }}&background=6366f1&color=fff&size=400&font-size=0.2';">
                    @if($book->available_stock > 0)
                    <span class="absolute top-3 right-3 bg-emerald-500 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-sm">Tersedia</span>
                    @else
                    <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-sm">Habis</span>
                    @endif
                </div>
            </a>
            <div class="p-4">
                <a href="{{ url('/books/'.$book->id) }}" class="block">
                    <h3 class="font-semibold text-slate-800 mb-1 truncate group-hover:text-accent-600 transition" title="{{ $book->title }}">{{ $book->title }}</h3>
                </a>
                <p class="text-slate-500 text-sm mb-2">{{ $book->authorRelation->name ?? $book->author ?? 'Penulis tidak diketahui' }}</p>
                <p class="text-slate-400 text-xs mb-3">ISBN: {{ $book->isbn }}</p>
                <div class="flex justify-between items-center mb-4">
                    <span class="badge badge-info">{{ $book->categoryRelation->name ?? $book->category ?? 'Umum' }}</span>
                    <span class="text-xs text-slate-500">{{ $book->publication_year }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/books/'.$book->id) }}" class="flex-1 text-center bg-slate-100 text-slate-700 px-3 py-2 rounded-lg hover:bg-slate-200 transition text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($book->available_stock > 0)
                        @auth
                            @php
                                $member = Auth::user()->member;
                                $activeLoan = $member ? \App\Models\Loan::where('member_id', $member->id)
                                    ->where('book_id', $book->id)
                                    ->whereIn('status', ['borrowed', 'overdue'])
                                    ->first() : null;
                                $canBorrow = $member ? $member->canBorrow() : true;
                            @endphp
                            @if($activeLoan)
                            <a href="{{ url('/books/'.$book->id.'/read') }}" class="flex-1 text-center bg-indigo-600 text-white px-3 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                                <i class="fas fa-book-open mr-1"></i>Baca
                            </a>
                            @elseif($member && !$canBorrow)
                            <span class="flex-1 text-center badge-danger px-3 py-2 rounded-lg text-sm font-medium" title="Batas peminjaman tercapai">
                                <i class="fas fa-ban mr-1"></i>Limit
                            </span>
                            @else
                            <form action="{{ url('/books/'.$book->id.'/borrow') }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin meminjam buku ini?')">
                                @csrf
                                <button type="submit" class="w-full btn-primary px-3 py-2 rounded-lg text-sm font-medium">
                                    <i class="fas fa-hand-holding mr-1"></i>Pinjam
                                </button>
                            </form>
                            @endif
                        @else
                        <a href="{{ url('/login') }}" class="flex-1 text-center btn-primary px-3 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        @endauth
                    @else
                        @auth
                            @php
                                $member = Auth::user()->member;
                                $activeLoan = $member ? \App\Models\Loan::where('member_id', $member->id)
                                    ->where('book_id', $book->id)
                                    ->whereIn('status', ['borrowed', 'overdue'])
                                    ->first() : null;
                            @endphp
                            @if($activeLoan)
                            <a href="{{ url('/books/'.$book->id.'/read') }}" class="flex-1 text-center bg-indigo-600 text-white px-3 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                                <i class="fas fa-book-open mr-1"></i>Baca
                            </a>
                            @else
                            <span class="flex-1 text-center bg-slate-200 text-slate-500 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                                <i class="fas fa-times-circle mr-1"></i>Habis
                            </span>
                            @endif
                        @else
                        <span class="flex-1 text-center bg-slate-200 text-slate-500 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                            <i class="fas fa-times-circle mr-1"></i>Habis
                        </span>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-full">
            <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                <div class="w-20 h-20 rounded-full empty-state-icon flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-open text-accent-600 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 mb-2">Belum Ada Buku</h3>
                <p class="text-slate-500 text-sm mb-6 max-w-md mx-auto">Koleksi buku sedang dalam proses penambahan. Silakan kunjungi kembali nanti.</p>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 btn-primary px-5 py-2.5 rounded-lg text-sm font-medium">
                    <i class="fas fa-home"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($books) && $books->hasPages())
    <div class="flex justify-center">
        {{ $books->links() }}
    </div>
    @endif
</div>
@endsection

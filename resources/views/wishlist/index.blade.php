@extends('layouts.dashboard')

@section('title', 'Wishlist')
@section('page-title', 'Wishlist')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Wishlist Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar buku favorit yang ingin Anda baca</p>
        </div>
        <a href="{{ url('/books') }}" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary rounded-lg text-sm font-medium">
            <i class="fas fa-plus"></i>
            <span>Jelajahi Buku</span>
        </a>
    </div>

    @if($wishlists->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <div class="w-20 h-20 rounded-full empty-state-icon flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-heart text-accent-600 text-3xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">Wishlist Kosong</h3>
        <p class="text-slate-500 text-sm mb-6">Anda belum menambahkan buku ke wishlist</p>
        <a href="{{ url('/books') }}" class="inline-flex items-center gap-2 btn-primary px-5 py-2.5 rounded-lg text-sm font-medium">
            <i class="fas fa-search"></i>
            <span>Jelajahi Katalog Buku</span>
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($wishlists as $wishlist)
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover transition duration-300 relative group">
            <!-- Remove from Wishlist Button -->
            <form action="{{ url('/wishlist/' . $wishlist->book_id) }}" method="POST" class="absolute top-2 right-2 z-10">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition hover:bg-red-600" title="Hapus dari Wishlist">
                    <i class="fas fa-times"></i>
                </button>
            </form>

            <a href="{{ url('/books/' . $wishlist->book->id) }}">
                <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center relative">
                    @if($wishlist->book->cover_image)
                    <img src="{{ $wishlist->book->coverUrl }}" alt="{{ $wishlist->book->title }}" class="w-full h-full object-cover">
                    @else
                    <i class="fas fa-book text-white text-5xl"></i>
                    @endif
                </div>
            </a>
            
            <div class="p-5">
                <a href="{{ url('/books/' . $wishlist->book->id) }}">
                    <h3 class="font-bold text-gray-800 mb-1 truncate hover:text-blue-600">{{ $wishlist->book->title }}</h3>
                </a>
                <p class="text-gray-500 text-sm mb-3">{{ $wishlist->book->author }}</p>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="bg-blue-100 text-primary text-xs px-3 py-1 rounded-full">{{ $wishlist->book->category }}</span>
                    <span class="text-sm {{ $wishlist->book->available_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $wishlist->book->available_stock > 0 ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>

                @if($wishlist->book->available_stock > 0)
                <form action="{{ url('/books/' . $wishlist->book->id . '/borrow') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg font-semibold hover:bg-green-600 transition">
                        <i class="fas fa-hand-holding mr-1"></i>Pinjam Sekarang
                    </button>
                </form>
                @else
                <button disabled class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg font-semibold cursor-not-allowed">
                    <i class="fas fa-times-circle mr-1"></i>Stok Habis
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-center">
        {{ $wishlists->links() }}
    </div>
    @endif
</div>
@endsection

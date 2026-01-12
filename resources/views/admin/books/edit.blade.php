@extends('layouts.admin')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku: ' . $book->title)

@php
    $isPhysical = ($book->book_type ?? 'physical') === 'physical';
@endphp

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <form action="{{ url('/admin/books/' . $book->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <!-- Book Type Selection -->
            <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Buku <span class="text-red-500">*</span></label>
                <div class="flex gap-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="book_type" value="physical" {{ $isPhysical ? 'checked' : '' }} onchange="toggleBookTypeFields()" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 flex items-center">
                            <i class="fas fa-book text-blue-600 mr-2"></i>
                            <span class="font-medium">Buku Fisik</span>
                        </span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="book_type" value="digital" {{ !$isPhysical ? 'checked' : '' }} onchange="toggleBookTypeFields()" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                        <span class="ml-2 flex items-center">
                            <i class="fas fa-tablet-alt text-purple-600 mr-2"></i>
                            <span class="font-medium">E-Book</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author (Text) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('author')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author (Select) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penulis (Database)</label>
                    <select name="author_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Penulis</option>
                        @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ISBN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('isbn')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publication Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit <span class="text-red-500">*</span></label>
                    <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" required min="1900" max="{{ date('Y') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('publication_year')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category (Text) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category', $book->category) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category (Select) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori (Database)</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Publisher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                    <select name="publisher_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Penerbit</option>
                        @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" {{ old('publisher_id', $book->publisher_id) == $publisher->id ? 'selected' : '' }}>{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pages -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Halaman</label>
                    <input type="number" name="pages" value="{{ old('pages', $book->pages) }}" min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Language -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                    <select name="language" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Indonesia" {{ old('language', $book->language) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                        <option value="English" {{ old('language', $book->language) == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Arabic" {{ old('language', $book->language) == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                        <option value="Other" {{ old('language', $book->language) == 'Other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <!-- Physical Book Fields -->
            <div id="physicalBookFields" class="border border-blue-200 rounded-lg p-4 bg-blue-50" style="{{ !$isPhysical ? 'display:none' : '' }}">
                <h4 class="font-semibold text-blue-800 mb-4"><i class="fas fa-book mr-2"></i>Pengaturan Buku Fisik</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Total <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Stok tersedia: {{ $book->available_stock }}</p>
                        @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Rak</label>
                        <input type="text" name="shelf_location" value="{{ old('shelf_location', $book->shelf_location) }}" placeholder="Contoh: R1-A3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Pinjam (hari)</label>
                        <input type="number" name="loan_duration_days" value="{{ old('loan_duration_days', $book->loan_duration_days ?? 14) }}" min="1" max="90" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Buku</label>
                        <select name="condition" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="good" {{ old('condition', $book->condition) == 'good' ? 'selected' : '' }}>Baik</option>
                            <option value="damaged" {{ old('condition', $book->condition) == 'damaged' ? 'selected' : '' }}>Rusak</option>
                            <option value="lost" {{ old('condition', $book->condition) == 'lost' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Digital Book Fields -->
            <div id="digitalBookFields" class="border border-purple-200 rounded-lg p-4 bg-purple-50" style="{{ $isPhysical ? 'display:none' : '' }}">
                <h4 class="font-semibold text-purple-800 mb-4"><i class="fas fa-tablet-alt mr-2"></i>Pengaturan E-Book</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File E-Book</label>
                        @if($book->file_path)
                        <div class="mb-2 p-2 bg-white rounded flex items-center justify-between">
                            <a href="{{ Storage::url($book->file_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800">
                                <i class="fas fa-file-pdf mr-2"></i>Lihat File Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="ebook_file" accept=".pdf,.epub" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, EPUB. Maks: 100MB. Kosongkan jika tidak ingin mengubah.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Akses (hari)</label>
                        <input type="number" name="access_duration_days" value="{{ old('access_duration_days', $book->access_duration_days ?? 7) }}" min="1" max="365" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">Lama akses e-book sejak dipinjam</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batas Peminjam Bersamaan</label>
                        <input type="number" name="access_limit" value="{{ old('access_limit', $book->access_limit) }}" min="0" placeholder="0 = Unlimited" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">0 atau kosong = tidak terbatas. Saat ini: {{ $book->current_access_count ?? 0 }} pembaca aktif</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_download" id="allow_download" value="1" {{ old('allow_download', $book->allow_download) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <label for="allow_download" class="ml-2 text-sm text-gray-700">Izinkan Download</label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $book->description) }}</textarea>
            </div>

            <!-- Cover Image -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($book->cover_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Saat Ini</label>
                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="h-40 w-auto rounded-lg shadow">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Gambar Cover</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                </div>
            </div>

            <!-- Options -->
            <div class="flex gap-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $book->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $book->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                    <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured/Unggulan</label>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
            <a href="{{ url('/admin/books') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
            <div class="flex gap-3">
                <a href="{{ url('/books/' . $book->id) }}" target="_blank" class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    <i class="fas fa-eye mr-1"></i>Lihat di Website
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-1"></i>Update Buku
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Book Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center">
            <div class="h-10 w-10 {{ $isPhysical ? 'bg-blue-100' : 'bg-purple-100' }} rounded-lg flex items-center justify-center">
                <i class="fas {{ $isPhysical ? 'fa-book' : 'fa-tablet-alt' }} {{ $isPhysical ? 'text-blue-600' : 'text-purple-600' }}"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Jenis</p>
                <p class="text-lg font-bold text-gray-900">{{ $isPhysical ? 'Buku Fisik' : 'E-Book' }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center">
            <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exchange-alt text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Peminjaman</p>
                <p class="text-xl font-bold text-gray-900">{{ $book->loans()->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center">
            <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-star text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Rating</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($book->reviews()->avg('rating') ?? 0, 1) }}/5</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center">
            <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-heart text-red-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Wishlist</p>
                <p class="text-xl font-bold text-gray-900">{{ $book->wishlists()->count() }}</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBookTypeFields() {
    const bookType = document.querySelector('input[name="book_type"]:checked').value;
    const physicalFields = document.getElementById('physicalBookFields');
    const digitalFields = document.getElementById('digitalBookFields');
    
    if (bookType === 'physical') {
        physicalFields.style.display = 'block';
        digitalFields.style.display = 'none';
    } else {
        physicalFields.style.display = 'none';
        digitalFields.style.display = 'block';
    }
}
</script>
@endsection

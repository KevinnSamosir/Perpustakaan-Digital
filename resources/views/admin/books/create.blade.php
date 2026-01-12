@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku Baru')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <form action="{{ url('/admin/books') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan judul buku">
                    @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author (Text) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nama penulis">
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
                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ISBN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ISBN <span class="text-red-500">*</span></label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="978-xxx-xxx-xxx-x">
                    @error('isbn')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publication Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit <span class="text-red-500">*</span></label>
                    <input type="number" name="publication_year" value="{{ old('publication_year', date('Y')) }}" required min="1900" max="{{ date('Y') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('publication_year')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category (Text) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Kategori buku">
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
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Publisher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                    <select name="publisher_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Penerbit</option>
                        @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 1) }}" required min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('stock')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pages -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Halaman</label>
                    <input type="number" name="pages" value="{{ old('pages') }}" min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Language -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                    <select name="language" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Indonesia" {{ old('language') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                        <option value="English" {{ old('language') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Arabic" {{ old('language') == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                        <option value="Other" {{ old('language') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Deskripsi atau sinopsis buku">{{ old('description') }}</textarea>
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Cover</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                </div>

                <!-- Book File (PDF) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Buku (PDF)</label>
                    <input type="file" name="file_path" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF. Maksimal 50MB</p>
                </div>

                <!-- Options -->
                <div class="md:col-span-2 flex gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured/Unggulan</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
            <a href="{{ url('/admin/books') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-1"></i>Simpan Buku
            </button>
        </div>
    </form>
</div>
@endsection

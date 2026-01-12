@extends('layouts.admin')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600">Total: {{ $books->total() }} buku</p>
        </div>
        <button onclick="openModal('addBookModal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Tambah Buku
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ url('/admin/books') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari judul, penulis, atau ISBN..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <option value="novel" {{ request('category') == 'novel' ? 'selected' : '' }}>Novel</option>
                <option value="sejarah" {{ request('category') == 'sejarah' ? 'selected' : '' }}>Sejarah</option>
                <option value="sains" {{ request('category') == 'sains' ? 'selected' : '' }}>Sains</option>
                <option value="teknologi" {{ request('category') == 'teknologi' ? 'selected' : '' }}>Teknologi</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Buku</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">ISBN</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Stok/Akses</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($books as $book)
                @php
                    $isPhysical = ($book->book_type ?? 'physical') === 'physical';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-16 rounded overflow-hidden flex-shrink-0 bg-gray-100">
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br {{ $isPhysical ? 'from-blue-500 to-indigo-600' : 'from-purple-500 to-pink-600' }} flex items-center justify-center\'><i class=\'fas {{ $isPhysical ? 'fa-book' : 'fa-tablet-alt' }} text-white\'></i></div>';">
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $book->title }}</p>
                                <p class="text-sm text-gray-500">{{ $book->author }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($isPhysical)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-book mr-1"></i>Fisik
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                            <i class="fas fa-tablet-alt mr-1"></i>E-Book
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->isbn ?: '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $book->category }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($isPhysical)
                        <span class="{{ $book->available_stock > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                            {{ $book->available_stock }}/{{ $book->stock }}
                        </span>
                        @else
                        <span class="text-purple-600 font-medium">
                            @if($book->access_limit)
                                {{ max(0, $book->access_limit - ($book->current_access_count ?? 0)) }}/{{ $book->access_limit }}
                            @else
                                <i class="fas fa-infinity"></i> Unlimited
                            @endif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button onclick="editBook({{ $book->id }})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ url('/admin/books/'.$book->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-book text-4xl mb-4 text-gray-300"></i>
                        <p>Belum ada buku</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $books->links() }}
    </div>

    <!-- Add Book Modal -->
    <div id="addBookModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 my-8">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Tambah Buku Baru</h3>
                <button onclick="closeModal('addBookModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            @if($errors->any())
            <div class="px-6 pt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <form action="{{ url('/admin/books') }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <!-- Book Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Buku</label>
                        <div class="flex gap-4">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition book-type-option" data-type="physical">
                                <input type="radio" name="book_type" value="physical" checked class="mr-3" onchange="toggleBookTypeFields()">
                                <div class="flex items-center">
                                    <i class="fas fa-book text-blue-600 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Buku Fisik</p>
                                        <p class="text-xs text-gray-500">Buku cetak yang dipinjam langsung</p>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition book-type-option" data-type="digital">
                                <input type="radio" name="book_type" value="digital" class="mr-3" onchange="toggleBookTypeFields()">
                                <div class="flex items-center">
                                    <i class="fas fa-tablet-alt text-purple-600 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">E-Book</p>
                                        <p class="text-xs text-gray-500">Buku digital (PDF/ePub)</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
                            <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                            <input type="text" name="author" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ISBN <span class="text-gray-400 text-xs" id="isbnOptional">(opsional untuk e-book)</span></label>
                            <input type="text" name="isbn" id="isbnField" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                            <input type="number" name="publication_year" value="{{ date('Y') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="novel">Novel</option>
                                <option value="sejarah">Sejarah</option>
                                <option value="sains">Sains</option>
                                <option value="teknologi">Teknologi</option>
                                <option value="bisnis">Bisnis</option>
                                <option value="self-help">Self-Help</option>
                            </select>
                        </div>
                        <div id="stockField">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                            <input type="number" name="stock" value="1" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Physical Book Fields -->
                    <div id="physicalFields" class="space-y-4 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-800 flex items-center">
                            <i class="fas fa-book mr-2"></i>Pengaturan Buku Fisik
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Rak</label>
                                <input type="text" name="shelf_location" placeholder="Contoh: A-01-03" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lama Peminjaman (hari)</label>
                                <input type="number" name="loan_duration_days" value="14" min="1" max="90" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Digital Book Fields -->
                    <div id="digitalFields" class="space-y-4 p-4 bg-purple-50 rounded-lg hidden">
                        <h4 class="font-medium text-purple-800 flex items-center">
                            <i class="fas fa-tablet-alt mr-2"></i>Pengaturan E-Book
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File E-Book</label>
                                <input type="file" name="ebook_file" accept=".pdf,.epub" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-gray-500 mt-1">Format: PDF, ePub (Max 50MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lama Akses (hari)</label>
                                <input type="number" name="access_duration_days" value="7" min="1" max="30" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Limit Akses Bersamaan</label>
                                <input type="number" name="access_limit" placeholder="Kosongkan untuk unlimited" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk akses tidak terbatas</p>
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_download" value="1" class="mr-2 rounded text-purple-600">
                                    <span class="text-sm text-gray-700">Izinkan Download</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('addBookModal')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

function toggleBookTypeFields() {
    const isDigital = document.querySelector('input[name="book_type"]:checked').value === 'digital';
    const physicalFields = document.getElementById('physicalFields');
    const digitalFields = document.getElementById('digitalFields');
    const stockField = document.getElementById('stockField');
    const isbnOptional = document.getElementById('isbnOptional');
    const isbnField = document.getElementById('isbnField');
    
    if (isDigital) {
        physicalFields.classList.add('hidden');
        digitalFields.classList.remove('hidden');
        stockField.classList.add('hidden');
        isbnOptional.classList.remove('hidden');
        isbnField.removeAttribute('required');
    } else {
        physicalFields.classList.remove('hidden');
        digitalFields.classList.add('hidden');
        stockField.classList.remove('hidden');
        isbnOptional.classList.add('hidden');
        isbnField.setAttribute('required', 'required');
    }
}

// Auto open modal if there are errors
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    openModal('addBookModal');
});
@endif
</script>
@endpush
@endsection

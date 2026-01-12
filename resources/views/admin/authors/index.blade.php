@extends('layouts.admin')

@section('title', 'Kelola Penulis')
@section('page-title', 'Kelola Penulis')
@section('page-description', 'Kelola data penulis buku')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                Total: {{ $authors->total() ?? count($authors) }} Penulis
            </span>
        </div>
        <button onclick="openModal('createModal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Tambah Penulis
        </button>
    </div>

    <!-- Authors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($authors as $author)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="h-16 w-16 flex-shrink-0">
                        @if($author->photo)
                        <img src="{{ Storage::url($author->photo) }}" alt="{{ $author->name }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                        <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-green-600"></i>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $author->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $author->nationality ?? 'Indonesia' }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-book mr-1"></i>{{ $author->books_count ?? 0 }} buku
                        </span>
                    </div>
                </div>
                
                @if($author->bio)
                <p class="mt-4 text-sm text-gray-600 line-clamp-2">{{ $author->bio }}</p>
                @endif
                
                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                    @if($author->is_active)
                    <span class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                    @else
                    <span class="text-xs text-red-600 font-medium"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>
                    @endif
                    
                    <div class="flex gap-2">
                        <button onclick="editAuthor({{ json_encode($author) }})" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ url('/admin/authors/' . $author->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus penulis ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl p-12 text-center">
                <i class="fas fa-user-edit text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Belum ada data penulis</p>
                <button onclick="openModal('createModal')" class="mt-4 text-green-600 hover:text-green-700 font-medium">
                    <i class="fas fa-plus mr-1"></i>Tambah Penulis Pertama
                </button>
            </div>
        </div>
        @endforelse
    </div>
    
    @if(method_exists($authors, 'links'))
    <div class="mt-6">
        {{ $authors->links() }}
    </div>
    @endif
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Penulis Baru</h3>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ url('/admin/authors') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Nama lengkap penulis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Email penulis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kebangsaan</label>
                    <input type="text" name="nationality" value="Indonesia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografi</label>
                    <textarea name="bio" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Biografi singkat penulis"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="create_is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Edit Penulis</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kebangsaan</label>
                    <input type="text" name="nationality" id="edit_nationality" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografi</label>
                    <textarea name="bio" id="edit_bio" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Baru (kosongkan jika tidak diubah)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="edit_is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Update</button>
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

function editAuthor(author) {
    document.getElementById('editForm').action = '/admin/authors/' + author.id;
    document.getElementById('edit_name').value = author.name;
    document.getElementById('edit_email').value = author.email || '';
    document.getElementById('edit_nationality').value = author.nationality || '';
    document.getElementById('edit_bio').value = author.bio || '';
    document.getElementById('edit_is_active').checked = author.is_active;
    openModal('editModal');
}

// Close modal when clicking outside
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal.id);
        }
    });
});
</script>
@endpush
@endsection

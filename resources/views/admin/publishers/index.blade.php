@extends('layouts.admin')

@section('title', 'Kelola Penerbit')
@section('page-title', 'Kelola Penerbit')
@section('page-description', 'Kelola data penerbit buku')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                Total: {{ $publishers->total() ?? count($publishers) }} Penerbit
            </span>
        </div>
        <button onclick="openModal('createModal')" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Tambah Penerbit
        </button>
    </div>

    <!-- Publishers Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerbit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($publishers as $index => $publisher)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($publishers->currentPage() - 1) * $publishers->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $publisher->name }}</div>
                                    @if($publisher->website)
                                    <a href="{{ $publisher->website }}" target="_blank" class="text-xs text-purple-600 hover:underline">{{ $publisher->website }}</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ $publisher->city ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($publisher->email)
                            <div><i class="fas fa-envelope text-gray-400 mr-1"></i>{{ $publisher->email }}</div>
                            @endif
                            @if($publisher->phone)
                            <div><i class="fas fa-phone text-gray-400 mr-1"></i>{{ $publisher->phone }}</div>
                            @endif
                            @if(!$publisher->email && !$publisher->phone)
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $publisher->books_count ?? 0 }} buku
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($publisher->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                            @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button onclick="editPublisher({{ json_encode($publisher) }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ url('/admin/publishers/' . $publisher->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus penerbit ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-building text-4xl mb-4 text-gray-300"></i>
                            <p>Belum ada data penerbit</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($publishers, 'links'))
        <div class="px-6 py-4 border-t">
            {{ $publishers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Penerbit Baru</h3>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ url('/admin/publishers') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Nama penerbit">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="email@penerbit.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="021-xxxxxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="https://www.penerbit.com">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Jakarta">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Negara</label>
                        <input type="text" name="country" value="Indonesia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Alamat lengkap penerbit"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="create_is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Edit Penerbit</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="phone" id="edit_phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" id="edit_website" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" id="edit_city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Negara</label>
                        <input type="text" name="country" id="edit_country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" id="edit_address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="edit_is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Update</button>
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

function editPublisher(publisher) {
    document.getElementById('editForm').action = '/admin/publishers/' + publisher.id;
    document.getElementById('edit_name').value = publisher.name;
    document.getElementById('edit_email').value = publisher.email || '';
    document.getElementById('edit_phone').value = publisher.phone || '';
    document.getElementById('edit_website').value = publisher.website || '';
    document.getElementById('edit_city').value = publisher.city || '';
    document.getElementById('edit_country').value = publisher.country || '';
    document.getElementById('edit_address').value = publisher.address || '';
    document.getElementById('edit_is_active').checked = publisher.is_active;
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

@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')
@section('page-title', 'Kelola Peminjaman')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 text-sm">Semua</span>
                <span class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</span>
            </div>
        </div>
        <div class="bg-orange-50 rounded-lg shadow-sm p-4 border border-orange-200">
            <div class="flex items-center justify-between">
                <span class="text-orange-700 text-sm">Pending</span>
                <span class="text-2xl font-bold text-orange-700">{{ $stats['pending'] ?? 0 }}</span>
            </div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm p-4 border border-blue-200">
            <div class="flex items-center justify-between">
                <span class="text-blue-700 text-sm">Disetujui</span>
                <span class="text-2xl font-bold text-blue-700">{{ $stats['approved'] ?? 0 }}</span>
            </div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm p-4 border border-yellow-200">
            <div class="flex items-center justify-between">
                <span class="text-yellow-700 text-sm">Dipinjam</span>
                <span class="text-2xl font-bold text-yellow-700">{{ $stats['borrowed'] ?? 0 }}</span>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-200">
            <div class="flex items-center justify-between">
                <span class="text-green-700 text-sm">Dikembalikan</span>
                <span class="text-2xl font-bold text-green-700">{{ $stats['returned'] ?? 0 }}</span>
            </div>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm p-4 border border-red-200">
            <div class="flex items-center justify-between">
                <span class="text-red-700 text-sm">Terlambat</span>
                <span class="text-2xl font-bold text-red-700">{{ $stats['overdue'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Pending Approval Alert -->
    @if(($stats['pending'] ?? 0) > 0)
    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                <div>
                    <p class="font-bold">Ada {{ $stats['pending'] }} permintaan peminjaman menunggu persetujuan</p>
                    <p class="text-sm">Silakan proses permintaan peminjaman buku fisik</p>
                </div>
            </div>
            <a href="{{ url('/admin/loans?status=pending') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm">
                Lihat Pending
            </a>
        </div>
    </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ url('/admin/loans') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari judul buku atau nama peminjam..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Siap Diambil)</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai (E-Book)</option>
            </select>
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Semua Jenis</option>
                <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Buku Fisik</option>
                <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>E-Book</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            @if(request('search') || request('status') || request('type'))
            <a href="{{ url('/admin/loans') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Buku</th>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Peminjam</th>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($loans as $loan)
                @php
                    $isPhysical = ($loan->loan_type ?? 'physical') === 'physical';
                    $isOverdue = $loan->status == 'borrowed' && $isPhysical && $loan->due_date && $loan->due_date < now();
                    $bgClass = match($loan->status) {
                        'pending' => 'bg-orange-50',
                        'approved' => 'bg-blue-50',
                        'rejected' => 'bg-red-50',
                        default => $isOverdue ? 'bg-red-50' : '',
                    };
                @endphp
                <tr class="hover:bg-gray-50 {{ $bgClass }}">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-12 bg-gradient-to-br {{ $isPhysical ? 'from-blue-500 to-purple-600' : 'from-purple-500 to-pink-600' }} rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $isPhysical ? 'fa-book' : 'fa-tablet-alt' }} text-white text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $loan->book->title ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $loan->book->author ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="font-medium text-gray-900">{{ $loan->member->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $loan->member->member_number ?? '' }}</p>
                    </td>
                    <td class="px-4 py-4">
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
                    <td class="px-4 py-4 text-sm">
                        @if($loan->status === 'pending')
                            <p class="text-gray-600">Diajukan: {{ $loan->created_at->format('d M Y H:i') }}</p>
                        @elseif($loan->loan_date)
                            <p class="text-gray-600">Pinjam: {{ $loan->loan_date->format('d M Y') }}</p>
                            @if($loan->due_date)
                            <p class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                Jatuh tempo: {{ $loan->due_date->format('d M Y') }}
                                @if($isOverdue)
                                <br><span class="text-xs">({{ $loan->due_date->diffForHumans() }})</span>
                                @endif
                            </p>
                            @endif
                        @else
                            <p class="text-gray-400">-</p>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @php
                            $statusConfig = match($loan->status) {
                                'pending' => ['bg-orange-100 text-orange-800', 'Menunggu'],
                                'approved' => ['bg-blue-100 text-blue-800', 'Siap Diambil'],
                                'rejected' => ['bg-red-100 text-red-800', 'Ditolak'],
                                'borrowed' => $isOverdue ? ['bg-red-100 text-red-800', 'Terlambat'] : ['bg-yellow-100 text-yellow-800', 'Dipinjam'],
                                'returned' => ['bg-green-100 text-green-800', 'Dikembalikan'],
                                'completed' => ['bg-gray-100 text-gray-800', 'Selesai'],
                                'overdue' => ['bg-red-100 text-red-800', 'Terlambat'],
                                default => ['bg-gray-100 text-gray-800', ucfirst($loan->status)],
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusConfig[0] }}">
                            {{ $statusConfig[1] }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-1">
                            @if($loan->status === 'pending' && $isPhysical)
                                <!-- Approve Button -->
                                <form action="{{ url('/admin/loans/'.$loan->id.'/approve') }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Setujui peminjaman ini?')" 
                                            class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i>Setujui
                                    </button>
                                </form>
                                <!-- Reject Button -->
                                <button onclick="showRejectModal({{ $loan->id }})" 
                                        class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">
                                    <i class="fas fa-times mr-1"></i>Tolak
                                </button>
                            @elseif($loan->status === 'approved' && $isPhysical)
                                <!-- Mark as Picked Up -->
                                <form action="{{ url('/admin/loans/'.$loan->id.'/pickup') }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Konfirmasi buku sudah diambil?')" 
                                            class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700">
                                        <i class="fas fa-hand-holding mr-1"></i>Sudah Diambil
                                    </button>
                                </form>
                            @elseif($loan->status === 'borrowed' && $isPhysical)
                                <!-- Return Button -->
                                <button onclick="showReturnModal({{ $loan->id }}, {{ $loan->calculateFine() }})" 
                                        class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                    <i class="fas fa-undo mr-1"></i>Kembalikan
                                </button>
                            @elseif($loan->status === 'returned' || $loan->status === 'completed')
                                @if($loan->return_date)
                                <span class="text-gray-500 text-xs">
                                    {{ $loan->return_date->format('d M Y') }}
                                </span>
                                @endif
                            @elseif($loan->status === 'rejected')
                                <span class="text-red-500 text-xs" title="{{ $loan->rejection_reason }}">
                                    <i class="fas fa-info-circle"></i> {{ Str::limit($loan->rejection_reason, 20) }}
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-clipboard-list text-4xl mb-4 text-gray-300"></i>
                        <p>Belum ada peminjaman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $loans->links() }}
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold mb-4">Tolak Peminjaman</h3>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                              placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeRejectModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Modal -->
    <div id="returnModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold mb-4">Konfirmasi Pengembalian</h3>
            <form id="returnForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Denda (jika ada)</label>
                    <input type="number" name="fine_amount" id="fineAmount" min="0" step="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada denda</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Kondisi Buku</label>
                    <textarea name="condition_notes" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                              placeholder="Catatan kondisi buku saat dikembalikan (opsional)..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeReturnModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(loanId) {
            document.getElementById('rejectForm').action = '/admin/loans/' + loanId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }

        function showReturnModal(loanId, calculatedFine) {
            document.getElementById('returnForm').action = '/admin/loans/' + loanId + '/return';
            document.getElementById('fineAmount').value = calculatedFine > 0 ? calculatedFine : '';
            document.getElementById('returnModal').classList.remove('hidden');
            document.getElementById('returnModal').classList.add('flex');
        }
        
        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
            document.getElementById('returnModal').classList.remove('flex');
        }

        // Close modal on outside click
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
        document.getElementById('returnModal').addEventListener('click', function(e) {
            if (e.target === this) closeReturnModal();
        });
    </script>
@endsection

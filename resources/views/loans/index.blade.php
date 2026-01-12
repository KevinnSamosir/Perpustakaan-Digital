@extends('layouts.dashboard')

@section('title', 'Peminjaman Saya')
@section('page-title', 'Peminjaman Saya')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Peminjaman Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau status peminjaman buku Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('/books') }}" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary rounded-lg text-sm font-medium">
                <i class="fas fa-plus"></i>
                <span>Pinjam Buku</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $loans = $loans ?? collect([]);
        $pendingCount = $loans->where('status', 'pending')->count();
        $approvedCount = $loans->where('status', 'approved')->count();
        $activeLoanCount = $loans->whereIn('status', ['borrowed', 'overdue'])->count();
        $returnedCount = $loans->whereIn('status', ['returned', 'completed'])->count();
        $overdueCount = $loans->where('status', 'overdue')->count() + $loans->filter(function($loan) {
            return $loan->status == 'borrowed' && isset($loan->due_date) && \Carbon\Carbon::parse($loan->due_date)->isPast();
        })->count();
    @endphp
    
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Menunggu Persetujuan -->
        <div class="card-modern bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Menunggu</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Siap Diambil -->
        <div class="card-modern bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-check text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Siap Diambil</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ $approvedCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Sedang Dipinjam -->
        <div class="card-modern bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-book-reader text-indigo-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Dipinjam</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ $activeLoanCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Dikembalikan -->
        <div class="card-modern bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Dikembalikan</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ $returnedCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Terlambat -->
        <div class="card-modern bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Terlambat</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ $overdueCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <nav class="flex flex-wrap gap-1 p-1 bg-slate-100 rounded-lg">
            @php $status = request('status', 'all'); @endphp
            <a href="{{ url('/loans?status=all') }}" class="px-3 py-2 text-sm font-medium rounded-md transition {{ $status == 'all' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Semua
            </a>
            <a href="{{ url('/loans?status=pending') }}" class="px-3 py-2 text-sm font-medium rounded-md transition {{ $status == 'pending' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Menunggu
            </a>
            <a href="{{ url('/loans?status=approved') }}" class="px-3 py-2 text-sm font-medium rounded-md transition {{ $status == 'approved' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Siap Diambil
            </a>
            <a href="{{ url('/loans?status=borrowed') }}" class="px-3 py-2 text-sm font-medium rounded-md transition {{ $status == 'borrowed' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Dipinjam
            </a>
            <a href="{{ url('/loans?status=returned') }}" class="px-3 py-2 text-sm font-medium rounded-md transition {{ $status == 'returned' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Selesai
            </a>
        </nav>
    </div>

    <!-- Loans Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
        <!-- Table Content -->
        @if($loans->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Buku</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Denda</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($loans as $loan)
                    @php
                        $isPhysical = ($loan->loan_type ?? 'physical') === 'physical';
                        $isOverdue = $loan->status == 'borrowed' && $isPhysical && isset($loan->due_date) && \Carbon\Carbon::parse($loan->due_date)->isPast();
                    @endphp
                    <tr class="table-row-hover transition {{ $isOverdue ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($loan->book && $loan->book->cover_image)
                                <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="w-10 h-14 object-cover rounded-lg shadow-sm">
                                @else
                                <div class="w-10 h-14 bg-gradient-to-br {{ $isPhysical ? 'from-blue-100 to-blue-50' : 'from-purple-100 to-purple-50' }} rounded-lg flex items-center justify-center">
                                    <i class="fas {{ $isPhysical ? 'fa-book' : 'fa-tablet-alt' }} {{ $isPhysical ? 'text-blue-400' : 'text-purple-400' }}"></i>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate max-w-[200px]">{{ $loan->book->title ?? 'Buku tidak ditemukan' }}</p>
                                    <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $loan->book->author ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($isPhysical)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                <i class="fas fa-book"></i> Fisik
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-purple-50 text-purple-700">
                                <i class="fas fa-tablet-alt"></i> E-Book
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($loan->status === 'pending')
                                <p class="text-slate-600">Diajukan: {{ $loan->created_at->format('d M Y') }}</p>
                            @elseif($loan->status === 'approved')
                                <p class="text-slate-600">Disetujui: {{ $loan->approved_at ? $loan->approved_at->format('d M Y') : '-' }}</p>
                            @elseif($loan->loan_date)
                                <p class="text-slate-600">Pinjam: {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</p>
                                @if($loan->due_date)
                                <p class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-slate-500' }} text-xs">
                                    Jatuh tempo: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                </p>
                                @endif
                            @else
                                <p class="text-slate-400">-</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusConfig = match($loan->status) {
                                    'pending' => ['bg-orange-50 text-orange-700 ring-orange-600/20', 'bg-orange-500', 'Menunggu'],
                                    'approved' => ['bg-blue-50 text-blue-700 ring-blue-600/20', 'bg-blue-500', 'Siap Diambil'],
                                    'rejected' => ['bg-red-50 text-red-700 ring-red-600/20', 'bg-red-500', 'Ditolak'],
                                    'borrowed' => $isOverdue 
                                        ? ['bg-red-50 text-red-700 ring-red-600/20', 'bg-red-500', 'Terlambat']
                                        : ['bg-indigo-50 text-indigo-700 ring-indigo-600/20', 'bg-indigo-500', 'Dipinjam'],
                                    'returned' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'bg-emerald-500', 'Dikembalikan'],
                                    'completed' => ['bg-gray-50 text-gray-700 ring-gray-500/20', 'bg-gray-500', 'Selesai'],
                                    'overdue' => ['bg-red-50 text-red-700 ring-red-600/20', 'bg-red-500', 'Terlambat'],
                                    default => ['bg-slate-50 text-slate-600 ring-slate-500/20', 'bg-slate-400', ucfirst($loan->status)],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $statusConfig[0] }} ring-1 ring-inset">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig[1] }}"></span>
                                {{ $statusConfig[2] }}
                            </span>
                            @if($loan->status === 'rejected' && $loan->rejection_reason)
                            <p class="text-xs text-red-600 mt-1" title="{{ $loan->rejection_reason }}">
                                {{ Str::limit($loan->rejection_reason, 30) }}
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($loan->fine_amount && $loan->fine_amount > 0)
                                <span class="text-sm font-medium text-rose-600">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @if($loan->status === 'pending')
                                    <span class="text-xs text-orange-600">
                                        <i class="fas fa-clock mr-1"></i>Menunggu admin
                                    </span>
                                @elseif($loan->status === 'approved')
                                    <span class="text-xs text-blue-600">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Ambil di perpustakaan
                                    </span>
                                @elseif(in_array($loan->status, ['borrowed', 'overdue']))
                                    @if(!$isPhysical)
                                    <!-- E-Book: Read & Download -->
                                    <a href="{{ url('/books/' . $loan->book_id . '/read') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition" title="Baca">
                                        <i class="fas fa-book-open"></i>
                                        Baca
                                    </a>
                                    @if($loan->book && $loan->book->allow_download && $loan->book->file_path)
                                    <a href="{{ url('/books/' . $loan->book_id . '/download') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    @else
                                    <!-- Physical Book: Only view -->
                                    <a href="{{ url('/books/' . $loan->book_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 transition" title="Lihat Buku">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                    <span class="text-xs text-gray-500 ml-1" title="Kembalikan buku ke perpustakaan">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                    @endif
                                @else
                                    <span class="text-sm text-slate-400">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-medium text-slate-700">{{ $loans->count() }}</span> data
            </p>
            @if(method_exists($loans, 'hasPages') && $loans->hasPages())
            {{ $loans->links() }}
            @endif
        </div>
        
        @else
        <!-- Empty State -->
        <div class="px-6 py-20 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum ada peminjaman</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">Anda belum memiliki riwayat peminjaman. Mulai jelajahi katalog buku dan pinjam buku favorit Anda.</p>
            <a href="{{ url('/books') }}" class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary rounded-lg text-sm font-medium">
                <i class="fas fa-book-open"></i>
                <span>Jelajahi Katalog</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

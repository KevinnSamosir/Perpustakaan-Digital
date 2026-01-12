@extends('layouts.admin')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota: ' . $member->user->name)

@section('content')
<div class="mb-6">
    <a href="{{ url('/admin/members') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Anggota
    </a>
</div>

<!-- Member Info Card -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto flex items-center justify-center mb-4">
                    <span class="text-3xl font-bold text-white">{{ strtoupper(substr($member->user->name, 0, 2)) }}</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $member->user->name }}</h2>
                <p class="text-gray-500">{{ $member->member_number }}</p>
                <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            
            <div class="mt-6 space-y-4">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span>{{ $member->user->email }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-5 mr-3"></i>
                    <span>{{ $member->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-map-marker-alt w-5 mr-3"></i>
                    <span>{{ $member->address ?: '-' }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-calendar w-5 mr-3"></i>
                    <span>Bergabung: {{ $member->join_date ? \Carbon\Carbon::parse($member->join_date)->format('d M Y') : '-' }}</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <form action="{{ url('/admin/members/' . $member->id . '/toggle-status') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2 rounded-lg {{ $member->status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        <i class="fas {{ $member->status === 'active' ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                        {{ $member->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_loans'] }}</div>
                <div class="text-sm text-gray-500">Total Pinjaman</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['active_loans'] }}</div>
                <div class="text-sm text-gray-500">Sedang Dipinjam</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-green-600">{{ $stats['returned_loans'] }}</div>
                <div class="text-sm text-gray-500">Dikembalikan</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-red-600">{{ $stats['overdue_loans'] }}</div>
                <div class="text-sm text-gray-500">Terlambat</div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Peminjaman Aktif</h3>
            </div>
            <div class="p-6">
                @forelse($activeLoans as $loan)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b' : '' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded flex items-center justify-center">
                            <i class="fas fa-book text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $loan->book->title ?? 'Buku tidak ditemukan' }}</p>
                            <p class="text-sm text-gray-500">
                                Dipinjam: {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}
                                @if($loan->due_date)
                                | Tenggat: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $loan->status === 'borrowed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $loan->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($loan->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Tidak ada peminjaman aktif</p>
                @endforelse
            </div>
        </div>

        <!-- Loan History -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Riwayat Peminjaman Terakhir</h3>
            </div>
            <div class="p-6">
                @forelse($loanHistory as $loan)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b' : '' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-book text-gray-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $loan->book->title ?? 'Buku tidak ditemukan' }}</p>
                            <p class="text-sm text-gray-500">
                                Dipinjam: {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}
                                @if($loan->return_date)
                                | Dikembalikan: {{ \Carbon\Carbon::parse($loan->return_date)->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Selesai
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada riwayat peminjaman</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', now()->isoFormat('dddd, D MMMM Y'))
@section('breadcrumb', 'Dashboard')

@section('content')
    <!-- Welcome Message -->
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        <p>Selamat Datang, <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong> di Administrator Perpustakaan Digital.</p>
    </div>

    <!-- Stat Boxes - AdminLTE Style with Colors -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Anggota - Blue/Aqua -->
        <div class="stat-box bg-info rounded overflow-hidden shadow">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-4xl font-bold text-white">{{ $totalMembers ?? 0 }}</p>
                        <p class="text-white text-lg">Anggota</p>
                    </div>
                    <div class="opacity-30">
                        <i class="fas fa-users text-6xl text-white"></i>
                    </div>
                </div>
            </div>
            <a href="{{ url('/admin/members') }}" class="block bg-black/10 text-white text-center py-2 text-sm hover:bg-black/20 transition">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
            </a>
        </div>

        <!-- Buku - Green -->
        <div class="stat-box bg-success rounded overflow-hidden shadow">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-4xl font-bold text-white">{{ $totalBooks ?? 0 }}</p>
                        <p class="text-white text-lg">Buku</p>
                    </div>
                    <div class="opacity-30">
                        <i class="fas fa-book text-6xl text-white"></i>
                    </div>
                </div>
            </div>
            <a href="{{ url('/admin/books') }}" class="block bg-black/10 text-white text-center py-2 text-sm hover:bg-black/20 transition">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
            </a>
        </div>

        <!-- Peminjaman - Yellow/Warning -->
        <div class="stat-box bg-warning rounded overflow-hidden shadow">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-4xl font-bold text-white">{{ $activeLoans ?? 0 }}</p>
                        <p class="text-white text-lg">Peminjaman</p>
                    </div>
                    <div class="opacity-30">
                        <i class="fas fa-exchange-alt text-6xl text-white"></i>
                    </div>
                </div>
            </div>
            <a href="{{ url('/admin/loans?status=borrowed') }}" class="block bg-black/10 text-white text-center py-2 text-sm hover:bg-black/20 transition">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
            </a>
        </div>

        <!-- Pengembalian - Red/Danger -->
        <div class="stat-box bg-danger rounded overflow-hidden shadow">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-4xl font-bold text-white">{{ $returnedLoans ?? 0 }}</p>
                        <p class="text-white text-lg">Pengembalian</p>
                    </div>
                    <div class="opacity-30">
                        <i class="fas fa-undo text-6xl text-white"></i>
                    </div>
                </div>
            </div>
            <a href="{{ url('/admin/loans?status=returned') }}" class="block bg-black/10 text-white text-center py-2 text-sm hover:bg-black/20 transition">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Logo Perpustakaan Digital Section -->
    <div class="text-center py-12 mb-8">
        <div class="inline-block">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-book-reader text-white text-3xl"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-700">Perpustakaan Digital</h2>
            <p class="text-gray-500 mt-1">Digital Library Management System</p>
        </div>
        <div class="mt-6 text-gray-500 text-sm">
            <p><i class="fas fa-map-marker-alt mr-2"></i>JL. Perpustakaan No. 123, Kota Digital</p>
            <p class="mt-1"><i class="fas fa-envelope mr-2"></i>contact@perpustakaandigital.com | <i class="fas fa-phone mr-2"></i>(022) 8298492</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Loans Table -->
        <div class="bg-white rounded shadow">
            <div class="bg-info text-white px-4 py-3 rounded-t flex items-center gap-2">
                <i class="fas fa-exchange-alt"></i>
                <h3 class="font-semibold">Peminjaman Terbaru</h3>
            </div>
            <div class="p-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-gray-600">Buku</th>
                            <th class="text-left py-2 text-gray-600">Peminjam</th>
                            <th class="text-left py-2 text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLoans ?? [] as $loan)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ Str::limit($loan->book->title ?? '-', 20) }}</td>
                            <td class="py-2">{{ $loan->member->user->name ?? 'N/A' }}</td>
                            <td class="py-2">
                                @if($loan->status == 'borrowed' || $loan->status == 'dipinjam')
                                    <span class="px-2 py-0.5 bg-warning text-white text-xs rounded">Dipinjam</span>
                                @elseif($loan->status == 'returned' || $loan->status == 'dikembalikan')
                                    <span class="px-2 py-0.5 bg-success text-white text-xs rounded">Kembali</span>
                                @elseif($loan->status == 'overdue' || $loan->status == 'terlambat')
                                    <span class="px-2 py-0.5 bg-danger text-white text-xs rounded">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada data peminjaman</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-4 py-2 rounded-b border-t">
                <a href="{{ url('/admin/loans') }}" class="text-info text-sm hover:underline">
                    Lihat semua peminjaman <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- New Members Table -->
        <div class="bg-white rounded shadow">
            <div class="bg-success text-white px-4 py-3 rounded-t flex items-center gap-2">
                <i class="fas fa-users"></i>
                <h3 class="font-semibold">Anggota Baru</h3>
            </div>
            <div class="p-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-gray-600">Nama</th>
                            <th class="text-left py-2 text-gray-600">Email</th>
                            <th class="text-left py-2 text-gray-600">Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newMembers ?? [] as $member)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $member->user->name ?? '-' }}</td>
                            <td class="py-2">{{ $member->user->email ?? '-' }}</td>
                            <td class="py-2">{{ $member->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada anggota baru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-4 py-2 rounded-b border-t">
                <a href="{{ url('/admin/members') }}" class="text-success text-sm hover:underline">
                    Lihat semua anggota <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="bg-white rounded shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-layer-group text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalCategories ?? 0 }}</p>
                <p class="text-sm text-gray-500">Kategori</p>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-pen-fancy text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalAuthors ?? 0 }}</p>
                <p class="text-sm text-gray-500">Penulis</p>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPublishers ?? 0 }}</p>
                <p class="text-sm text-gray-500">Penerbit</p>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $overdueLoans ?? 0 }}</p>
                <p class="text-sm text-gray-500">Terlambat</p>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Daftar Anggota - Perpustakaan Digital')

@section('content')
    <!-- Header -->
    <section class="gradient-bg text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Daftar Anggota</h1>
                <p class="text-blue-100">Kelola anggota perpustakaan</p>
            </div>
            <button class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-blue-100 transition">
                <i class="fas fa-user-plus mr-2"></i>Tambah Anggota
            </button>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form class="flex gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari berdasarkan nama, nomor anggota, atau email..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </form>
        </div>

        <!-- Members Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($members ?? [] as $member)
            <div class="bg-white rounded-xl shadow-md p-6 card-hover transition duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="font-bold text-gray-800">{{ $member->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $member->member_number }}</p>
                        </div>
                    </div>
                    @if($member->status == 'active')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Aktif</span>
                    @endif
                </div>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p><i class="fas fa-envelope w-5 text-gray-400"></i> {{ $member->user->email }}</p>
                    <p><i class="fas fa-phone w-5 text-gray-400"></i> {{ $member->phone }}</p>
                    <p><i class="fas fa-calendar w-5 text-gray-400"></i> Bergabung: {{ $member->join_date->format('d M Y') }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/members/'.$member->id) }}" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    <button class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
            </div>
            @empty
            <!-- Sample Data -->
            @foreach([
                ['name' => 'Ahmad Rizki', 'number' => 'MBR-2024001', 'email' => 'ahmad.rizki@email.com', 'phone' => '081234567890', 'join_date' => '15 Mar 2024', 'status' => 'active', 'loans' => 3],
                ['name' => 'Siti Nurhaliza', 'number' => 'MBR-2024002', 'email' => 'siti.nur@email.com', 'phone' => '081234567891', 'join_date' => '20 Mar 2024', 'status' => 'active', 'loans' => 1],
                ['name' => 'Budi Santoso', 'number' => 'MBR-2024003', 'email' => 'budi.s@email.com', 'phone' => '081234567892', 'join_date' => '25 Mar 2024', 'status' => 'active', 'loans' => 5],
                ['name' => 'Dewi Lestari', 'number' => 'MBR-2024004', 'email' => 'dewi.l@email.com', 'phone' => '081234567893', 'join_date' => '01 Apr 2024', 'status' => 'inactive', 'loans' => 0],
                ['name' => 'Eko Prasetyo', 'number' => 'MBR-2024005', 'email' => 'eko.p@email.com', 'phone' => '081234567894', 'join_date' => '10 Apr 2024', 'status' => 'active', 'loans' => 2],
                ['name' => 'Fitri Handayani', 'number' => 'MBR-2024006', 'email' => 'fitri.h@email.com', 'phone' => '081234567895', 'join_date' => '15 Apr 2024', 'status' => 'active', 'loans' => 4],
            ] as $member)
            <div class="bg-white rounded-xl shadow-md p-6 card-hover transition duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($member['name'], 0, 1)) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="font-bold text-gray-800">{{ $member['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $member['number'] }}</p>
                        </div>
                    </div>
                    @if($member['status'] == 'active')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Tidak Aktif</span>
                    @endif
                </div>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p><i class="fas fa-envelope w-5 text-gray-400"></i> {{ $member['email'] }}</p>
                    <p><i class="fas fa-phone w-5 text-gray-400"></i> {{ $member['phone'] }}</p>
                    <p><i class="fas fa-calendar w-5 text-gray-400"></i> Bergabung: {{ $member['join_date'] }}</p>
                    <p><i class="fas fa-book w-5 text-gray-400"></i> Pinjaman Aktif: {{ $member['loans'] }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="#" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    <button class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <span class="px-3 py-2 text-gray-400 cursor-not-allowed"><i class="fas fa-chevron-left"></i></span>
                <span class="px-4 py-2 bg-primary text-white rounded-lg">1</span>
                <a href="#" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">2</a>
                <a href="#" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"><i class="fas fa-chevron-right"></i></a>
            </nav>
        </div>
    </div>
@endsection

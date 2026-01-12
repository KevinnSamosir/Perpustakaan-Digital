@extends('layouts.dashboard')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Profil Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola informasi profil Anda</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="mb-4">
                    <img src="{{ Auth::user()->avatarUrl }}" alt="Avatar" class="w-32 h-32 rounded-full mx-auto border-4 border-blue-500">
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                <p class="text-gray-500">{{ Auth::user()->email }}</p>
                @if($member)
                <p class="text-sm text-blue-600 mt-2">
                    <i class="fas fa-id-card mr-1"></i>{{ $member->member_number }}
                </p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($member->status) }}
                </span>
                @endif
            </div>

            <!-- Statistics Card -->
            @if($member)
            <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                <h3 class="font-bold text-gray-800 mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Peminjaman</span>
                        <span class="font-bold text-blue-600">{{ $member->totalLoansCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sedang Dipinjam</span>
                        <span class="font-bold text-green-600">{{ $member->activeLoansCount }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Wishlist</span>
                        <span class="font-bold text-pink-600">{{ $member->wishlists()->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Review Diberikan</span>
                        <span class="font-bold text-yellow-600">{{ $member->reviews()->count() }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-edit mr-2 text-blue-600"></i>Edit Profil
                </h3>
                
                <form action="{{ url('/profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($member)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ $member->phone }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                            <input type="text" value="{{ $member->join_date->format('d M Y') }}" disabled
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 text-gray-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="address" rows="3" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $member->address }}</textarea>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-lock mr-2 text-blue-600"></i>Ubah Password
                </h3>
                
                <form action="{{ url('/profile/password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg font-medium">
                            <i class="fas fa-key mr-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

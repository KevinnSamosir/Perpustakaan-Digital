@extends('layouts.app')

@section('title', 'Daftar - Perpustakaan Digital')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
                <p class="text-gray-500 mt-2">Daftar untuk memulai meminjam buku</p>
            </div>

            <form action="{{ url('/register') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Nama lengkap Anda">
                        <i class="fas fa-user absolute left-3 top-4 text-gray-400"></i>
                    </div>
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="nama@email.com">
                        <i class="fas fa-envelope absolute left-3 top-4 text-gray-400"></i>
                    </div>
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <div class="relative">
                        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="08xxxxxxxxxx">
                        <i class="fas fa-phone absolute left-3 top-4 text-gray-400"></i>
                    </div>
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Minimal 8 karakter">
                        <i class="fas fa-lock absolute left-3 top-4 text-gray-400"></i>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Ulangi password">
                        <i class="fas fa-lock absolute left-3 top-4 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-start">
                    <input type="checkbox" name="terms" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
                    <span class="ml-2 text-sm text-gray-600">
                        Saya menyetujui <a href="#" class="text-primary hover:text-secondary">Syarat & Ketentuan</a> dan <a href="#" class="text-primary hover:text-secondary">Kebijakan Privasi</a>
                    </span>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-secondary transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Sudah punya akun?
                    <a href="{{ url('/login') }}" class="text-primary hover:text-secondary font-semibold">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

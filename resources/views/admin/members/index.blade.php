@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')

@section('content')
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600">Total: {{ $members->total() }} anggota</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ url('/admin/members') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, email, atau nomor anggota..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Anggota</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">No. Anggota</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Telepon</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Bergabung</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $member->user->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $member->user->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->member_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->phone }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->join_date->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $member->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ url('/admin/members/'.$member->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ url('/admin/members/'.$member->id.'/toggle-status') }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="{{ $member->status == 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $member->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                        <p>Belum ada anggota</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $members->links() }}
    </div>
@endsection

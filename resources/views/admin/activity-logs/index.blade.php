@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas Sistem')

@section('content')
<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form action="{{ url('/admin/activity-logs') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
            <select name="action" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua</option>
                @foreach($actionTypes as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            <a href="{{ url('/admin/activity-logs') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Activity Log Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aktivitas</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Deskripsi</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($logs as $log)
            @php
                $actionColors = [
                    'login' => 'bg-green-100 text-green-800',
                    'logout' => 'bg-gray-100 text-gray-800',
                    'loan_request' => 'bg-blue-100 text-blue-800',
                    'loan_return' => 'bg-purple-100 text-purple-800',
                    'book_create' => 'bg-indigo-100 text-indigo-800',
                    'book_update' => 'bg-yellow-100 text-yellow-800',
                    'book_delete' => 'bg-red-100 text-red-800',
                    'user_create' => 'bg-teal-100 text-teal-800',
                    'settings_update' => 'bg-orange-100 text-orange-800',
                ];
                $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                    <div>{{ $log->created_at->format('d M Y') }}</div>
                    <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-white">{{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500">{{ $log->user->role ?? '-' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">
                    {{ $log->description }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $log->ip_address ?? '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                    <p>Belum ada log aktivitas</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($logs->hasPages())
<div class="mt-6">
    {{ $logs->withQueryString()->links() }}
</div>
@endif
@endsection

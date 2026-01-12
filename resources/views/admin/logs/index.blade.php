@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('page-description', 'Riwayat aktivitas sistem')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="action" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            <div>
                <select name="model_type" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Model</option>
                    <option value="Book" {{ request('model_type') == 'Book' ? 'selected' : '' }}>Book</option>
                    <option value="Member" {{ request('model_type') == 'Member' ? 'selected' : '' }}>Member</option>
                    <option value="Loan" {{ request('model_type') == 'Loan' ? 'selected' : '' }}>Loan</option>
                    <option value="Category" {{ request('model_type') == 'Category' ? 'selected' : '' }}>Category</option>
                    <option value="User" {{ request('model_type') == 'User' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            <a href="{{ url('/admin/logs') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['create'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Create</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['update'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Update</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['delete'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Delete</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['today'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Hari Ini</p>
        </div>
    </div>

    <!-- Activity Log Timeline -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-history text-blue-500 mr-2"></i>Riwayat Aktivitas
            </h3>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($logs ?? [] as $log)
            <div class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-start gap-4">
                    <!-- Action Icon -->
                    <div class="flex-shrink-0">
                        @switch($log->action)
                            @case('create')
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-plus text-green-600"></i>
                                </div>
                                @break
                            @case('update')
                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-edit text-yellow-600"></i>
                                </div>
                                @break
                            @case('delete')
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-trash text-red-600"></i>
                                </div>
                                @break
                            @case('login')
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt text-blue-600"></i>
                                </div>
                                @break
                            @case('logout')
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-sign-out-alt text-gray-600"></i>
                                </div>
                                @break
                            @default
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-cog text-purple-600"></i>
                                </div>
                        @endswitch
                    </div>

                    <!-- Activity Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full
                                @if($log->action == 'create') bg-green-100 text-green-700
                                @elseif($log->action == 'update') bg-yellow-100 text-yellow-700
                                @elseif($log->action == 'delete') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ strtoupper($log->action) }}</span>
                            @if($log->model_type)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">{{ class_basename($log->model_type) }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $log->description }}</p>
                        
                        @if($log->old_values || $log->new_values)
                        <button onclick="toggleDetails('details-{{ $log->id }}')" class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                            <i class="fas fa-chevron-down mr-1"></i>Lihat Detail
                        </button>
                        <div id="details-{{ $log->id }}" class="hidden mt-2 p-3 bg-gray-50 rounded-lg text-xs">
                            @if($log->old_values)
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Data Lama:</span>
                                <pre class="mt-1 text-gray-600 whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                            @if($log->new_values)
                            <div>
                                <span class="font-medium text-gray-700">Data Baru:</span>
                                <pre class="mt-1 text-gray-600 whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Timestamp & IP -->
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</p>
                        @if($log->ip_address)
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-globe mr-1"></i>{{ $log->ip_address }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada aktivitas tercatat</p>
            </div>
            @endforelse
        </div>

        @if(method_exists($logs ?? collect(), 'links'))
        <div class="px-6 py-4 border-t">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <!-- Clear Logs -->
    <div class="flex justify-end">
        <form action="{{ url('/admin/logs/clear') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua log? Tindakan ini tidak dapat dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i>Hapus Semua Log
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleDetails(id) {
    const element = document.getElementById(id);
    element.classList.toggle('hidden');
}
</script>
@endpush
@endsection

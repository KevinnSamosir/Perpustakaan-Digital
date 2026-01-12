@extends('layouts.admin')

@section('title', 'Laporan & Statistik')
@section('page-title', 'Laporan & Statistik')
@section('page-description', 'Laporan dan analisis perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            <a href="{{ url('/admin/reports') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-redo mr-1"></i>Reset
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Buku</p>
                    <p class="text-3xl font-bold">{{ $stats['total_books'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-blue-100 text-sm">
                <span class="text-green-300"><i class="fas fa-arrow-up mr-1"></i>{{ $stats['available_books'] ?? 0 }}</span> tersedia
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Anggota</p>
                    <p class="text-3xl font-bold">{{ $stats['total_members'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-green-100 text-sm">
                <span class="text-green-300"><i class="fas fa-user-check mr-1"></i>{{ $stats['active_members'] ?? 0 }}</span> aktif
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Peminjaman Aktif</p>
                    <p class="text-3xl font-bold">{{ $stats['active_loans'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-purple-100 text-sm">
                <span class="text-yellow-300"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $stats['overdue_loans'] ?? 0 }}</span> terlambat
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Total Denda</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($stats['total_fines'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-orange-100 text-sm">
                Periode ini
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Loan Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-blue-500 mr-2"></i>Tren Peminjaman
            </h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <canvas id="loanTrendsChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>Distribusi Kategori
            </h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Books -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-fire text-orange-500 mr-2"></i>Buku Terpopuler
                </h3>
            </div>
            <div class="p-6">
                @if(isset($popularBooks) && count($popularBooks) > 0)
                <div class="space-y-4">
                    @foreach($popularBooks as $index => $book)
                    <div class="flex items-center gap-4">
                        <span class="h-8 w-8 rounded-full bg-{{ ['blue', 'green', 'yellow', 'purple', 'red'][$index % 5] }}-100 text-{{ ['blue', 'green', 'yellow', 'purple', 'red'][$index % 5] }}-600 flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $book->title }}</p>
                            <p class="text-sm text-gray-500">{{ $book->author }}</p>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $book->loans_count }} pinjaman</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">Belum ada data peminjaman</p>
                @endif
            </div>
        </div>

        <!-- Active Members -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Anggota Teraktif
                </h3>
            </div>
            <div class="p-6">
                @if(isset($activeMembers) && count($activeMembers) > 0)
                <div class="space-y-4">
                    @foreach($activeMembers as $index => $member)
                    <div class="flex items-center gap-4">
                        <span class="h-8 w-8 rounded-full bg-{{ ['blue', 'green', 'yellow', 'purple', 'red'][$index % 5] }}-100 text-{{ ['blue', 'green', 'yellow', 'purple', 'red'][$index % 5] }}-600 flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $member->name }}</p>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $member->loans_count }} pinjaman</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">Belum ada data anggota aktif</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Overdue Loans Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-red-50">
            <h3 class="font-semibold text-red-800">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Peminjaman Terlambat
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterlambatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($overdueLoans ?? [] as $loan)
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $loan->member->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $loan->book->title ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $loan->due_date ? abs($loan->due_date->diffInDays(now())) : 0 }} hari
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                            Rp {{ number_format($loan->fine_amount ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p>Tidak ada peminjaman yang terlambat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="flex justify-end gap-4">
        <a href="{{ url('/admin/reports/export/pdf') }}?{{ http_build_query(request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-file-pdf mr-2"></i>Export PDF
        </a>
        <a href="{{ url('/admin/reports/export/excel') }}?{{ http_build_query(request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-file-excel mr-2"></i>Export Excel
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Loan Trends Chart
const loanCtx = document.getElementById('loanTrendsChart');
if (loanCtx) {
    new Chart(loanCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['loan_trends']['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Peminjaman',
                data: {!! json_encode($chartData['loan_trends']['data'] ?? [10, 15, 8, 22, 18, 25]) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
}

// Category Distribution Chart
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['category_distribution']['labels'] ?? ['Fiksi', 'Non-Fiksi', 'Sains', 'Sejarah', 'Lainnya']) !!},
            datasets: [{
                data: {!! json_encode($chartData['category_distribution']['data'] ?? [30, 25, 20, 15, 10]) !!},
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(139, 92, 246)',
                    'rgb(239, 68, 68)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>
@endpush
@endsection

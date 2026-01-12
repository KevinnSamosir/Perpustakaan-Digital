@extends('layouts.dashboard')

@section('title', 'Baca: ' . $book->title)
@section('page-title', 'Baca Buku')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-xl p-6 border border-slate-200/60 shadow-sm">
        <div class="flex items-center gap-4">
            @if($book->cover_image)
            <img src="{{ $book->coverUrl }}" alt="{{ $book->title }}" class="w-16 h-20 object-cover rounded-lg shadow-sm">
            @else
            <div class="w-16 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-indigo-500 text-xl"></i>
            </div>
            @endif
            <div>
                <h1 class="text-xl font-semibold text-slate-800">{{ $book->title }}</h1>
                <p class="text-sm text-slate-500">oleh {{ $book->author }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                        <i class="fas fa-clock"></i>
                        Pinjam sampai {{ $activeLoan->due_date->format('d M Y') }}
                    </span>
                    @if($activeLoan->due_date < now())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                        <i class="fas fa-exclamation-triangle"></i>
                        TERLAMBAT
                    </span>
                    @elseif($activeLoan->due_date->diffInDays(now()) <= 3)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        <i class="fas fa-clock"></i>
                        {{ $activeLoan->due_date->diffInDays(now()) }} hari lagi
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/books/' . $book->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali</span>
            </a>
            <form action="{{ route('loans.return', $activeLoan->id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" onclick="return confirm('Selesai membaca? Kembalikan buku ini?')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 transition">
                    <i class="fas fa-undo-alt"></i>
                    <span>Kembalikan Buku</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Book Reader -->
    <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-600"><i class="fas fa-book-open mr-2"></i>Pembaca Buku</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="decreaseFontSize()" class="p-2 rounded-lg hover:bg-slate-200 transition" title="Perkecil Font">
                    <i class="fas fa-minus text-slate-600"></i>
                </button>
                <span id="fontSizeDisplay" class="text-sm text-slate-600 w-12 text-center">16px</span>
                <button onclick="increaseFontSize()" class="p-2 rounded-lg hover:bg-slate-200 transition" title="Perbesar Font">
                    <i class="fas fa-plus text-slate-600"></i>
                </button>
                <div class="w-px h-6 bg-slate-200 mx-2"></div>
                <button onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-slate-200 transition" title="Mode Gelap" id="darkModeBtn">
                    <i class="fas fa-moon text-slate-600"></i>
                </button>
            </div>
        </div>
        
        <div id="readerContent" class="p-8 md:p-12 lg:p-16 min-h-[70vh] max-w-4xl mx-auto">
            <article class="prose prose-slate max-w-none" id="bookContent" style="font-size: 16px; line-height: 1.8;">
                <h1 class="text-2xl font-bold text-slate-800 mb-6">{{ $book->title }}</h1>
                <p class="text-slate-500 mb-8"><em>oleh {{ $book->author }}</em></p>
                
                @if($book->description)
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-8 rounded-r-lg">
                    <p class="text-indigo-800 italic">{{ $book->description }}</p>
                </div>
                @endif

                @if($book->content)
                {!! nl2br(e($book->content)) !!}
                @else
                <div class="text-center py-20">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-book-open text-slate-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-600 mb-2">Konten Buku Dalam Proses Digitalisasi</h3>
                    <p class="text-slate-500 mb-4">Konten lengkap buku ini sedang dalam proses digitalisasi.</p>
                    <p class="text-sm text-slate-400">Silakan gunakan buku fisik atau hubungi pustakawan untuk informasi lebih lanjut.</p>
                </div>
                
                <!-- Sample content for demonstration -->
                <div class="mt-8 pt-8 border-t border-dashed border-slate-200">
                    <h2 class="text-xl font-bold text-slate-700 mb-4">Sinopsis</h2>
                    <p class="text-slate-700 mb-4">{{ $book->description ?: 'Buku ini adalah salah satu karya terbaik dari penulisnya yang telah menginspirasi banyak pembaca di Indonesia.' }}</p>
                    
                    <h2 class="text-xl font-bold text-slate-700 mb-4 mt-8">Tentang Penulis</h2>
                    <p class="text-slate-700 mb-4">{{ $book->author }} adalah penulis yang telah menghasilkan banyak karya yang digemari oleh pembaca di seluruh Indonesia.</p>
                    
                    <h2 class="text-xl font-bold text-slate-700 mb-4 mt-8">Informasi Buku</h2>
                    <ul class="list-disc list-inside text-slate-700 space-y-2">
                        <li><strong>ISBN:</strong> {{ $book->isbn ?: 'Tidak tersedia' }}</li>
                        <li><strong>Penerbit:</strong> {{ $book->publisher ?? 'Tidak tersedia' }}</li>
                        <li><strong>Tahun Terbit:</strong> {{ $book->publication_year ?: 'Tidak tersedia' }}</li>
                        <li><strong>Kategori:</strong> {{ $book->category ?? 'Umum' }}</li>
                    </ul>
                </div>
                @endif
            </article>
        </div>
    </div>

    <!-- Reading Progress -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/60 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-600">Progress Membaca</span>
            <span id="progressPercent" class="text-sm text-slate-500">0%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2">
            <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let fontSize = 16;
    let isDarkMode = false;
    
    function increaseFontSize() {
        if (fontSize < 24) {
            fontSize += 2;
            updateFontSize();
        }
    }
    
    function decreaseFontSize() {
        if (fontSize > 12) {
            fontSize -= 2;
            updateFontSize();
        }
    }
    
    function updateFontSize() {
        document.getElementById('bookContent').style.fontSize = fontSize + 'px';
        document.getElementById('fontSizeDisplay').textContent = fontSize + 'px';
    }
    
    function toggleDarkMode() {
        isDarkMode = !isDarkMode;
        const reader = document.getElementById('readerContent');
        const btn = document.getElementById('darkModeBtn');
        
        if (isDarkMode) {
            reader.classList.add('bg-slate-900');
            reader.querySelector('article').classList.remove('prose-slate');
            reader.querySelector('article').classList.add('prose-invert');
            btn.innerHTML = '<i class="fas fa-sun text-amber-400"></i>';
        } else {
            reader.classList.remove('bg-slate-900');
            reader.querySelector('article').classList.add('prose-slate');
            reader.querySelector('article').classList.remove('prose-invert');
            btn.innerHTML = '<i class="fas fa-moon text-slate-600"></i>';
        }
    }
    
    // Track reading progress
    window.addEventListener('scroll', function() {
        const reader = document.getElementById('readerContent');
        const rect = reader.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const readerHeight = reader.scrollHeight;
        
        const scrolled = window.scrollY;
        const total = document.documentElement.scrollHeight - windowHeight;
        const progress = Math.min(Math.round((scrolled / total) * 100), 100);
        
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressPercent').textContent = progress + '%';
    });
</script>
@endpush

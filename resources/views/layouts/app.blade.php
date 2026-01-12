<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#6366f1',
                        accent: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        
        /* Glassmorphism Navbar */
        .glass-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        
        /* Hero gradient */
        .hero-gradient {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        }
        
        /* Card hover effect */
        .card-modern {
            transition: all 0.3s ease;
        }
        .card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        
        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.5);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen font-inter">
    <!-- Navigation - Modern Glassmorphism -->
    <nav class="glass-navbar fixed w-full top-0 z-50 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-lg"></i>
                        </div>
                        <span class="font-bold text-xl text-slate-800">Perpustakaan</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ url('/') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request()->is('/') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Beranda
                    </a>
                    <a href="{{ url('/books') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request()->is('books*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Katalog
                    </a>
                    @auth
                    <a href="{{ url('/loans') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request()->is('loans*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Peminjaman
                    </a>
                    <a href="{{ url('/wishlist') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request()->is('wishlist*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Wishlist
                    </a>
                    @endauth
                </div>

                <div class="flex items-center space-x-3">
                    @auth
                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <a href="{{ url('/notifications') }}" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition">
                            <i class="fas fa-bell"></i>
                            @php $unreadCount = Auth::user()->unreadNotificationsCount ?? 0; @endphp
                            @if($unreadCount > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </a>
                        
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ url('/admin') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-100 text-amber-700 font-medium text-sm hover:bg-amber-200 transition">
                            <i class="fas fa-crown"></i>
                            <span>Admin</span>
                        </a>
                        @endif
                        
                        <!-- Profile dropdown -->
                        <div class="relative">
                            <button onclick="document.getElementById('user-dropdown').classList.toggle('hidden')" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                                <a href="{{ url('/profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-user w-4 text-slate-400"></i> Profil
                                </a>
                                <a href="{{ url('/loans') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-book-reader w-4 text-slate-400"></i> Peminjaman
                                </a>
                                <hr class="my-1 border-slate-100">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-4"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ url('/login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        Masuk
                    </a>
                    <a href="{{ url('/register') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-primary">
                        Daftar
                    </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-200 pb-4">
            <a href="{{ url('/') }}" class="block px-4 py-3 text-slate-700 hover:bg-slate-50">Beranda</a>
            <a href="{{ url('/books') }}" class="block px-4 py-3 text-slate-700 hover:bg-slate-50">Katalog Buku</a>
            @auth
            <a href="{{ url('/loans') }}" class="block px-4 py-3 text-slate-700 hover:bg-slate-50">Peminjaman</a>
            <a href="{{ url('/wishlist') }}" class="block px-4 py-3 text-slate-700 hover:bg-slate-50">Wishlist</a>
            @endauth
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div class="h-16"></div>

    <!-- Main Content -->
    <main>
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation text-white text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer - Modern Design -->
    <footer class="bg-slate-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <span class="font-bold text-xl">Perpustakaan</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed">Sistem manajemen perpustakaan modern untuk kemudahan akses literasi digital.</p>
                    <div class="flex gap-3 mt-6">
                        <a href="#" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-indigo-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-indigo-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-indigo-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider text-slate-400 mb-4">Menu</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}" class="text-slate-300 hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ url('/books') }}" class="text-slate-300 hover:text-white transition">Katalog Buku</a></li>
                        <li><a href="{{ url('/loans') }}" class="text-slate-300 hover:text-white transition">Peminjaman</a></li>
                        <li><a href="{{ url('/about') }}" class="text-slate-300 hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider text-slate-400 mb-4">Kontak</h4>
                    <ul class="space-y-3 text-slate-300">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-indigo-400"></i>
                            <span>Jl. Perpustakaan No. 123<br>Jakarta Selatan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-indigo-400"></i>
                            <span>(021) 123-4567</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-indigo-400"></i>
                            <span>info@perpusdigital.com</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider text-slate-400 mb-4">Jam Operasional</h4>
                    <ul class="space-y-3 text-slate-300">
                        <li class="flex justify-between">
                            <span>Senin - Jumat</span>
                            <span class="text-white font-medium">08:00 - 20:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sabtu</span>
                            <span class="text-white font-medium">09:00 - 17:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Minggu</span>
                            <span class="text-red-400">Tutup</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-slate-400 text-sm">
                <p>&copy; {{ date('Y') }} Perpustakaan Digital. All rights reserved.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && !e.target.closest('[onclick*="user-dropdown"]') && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

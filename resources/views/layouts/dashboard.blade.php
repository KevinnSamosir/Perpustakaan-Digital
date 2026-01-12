<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Perpustakaan Digital</title>
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
                        // Modern Indigo accent
                        accent: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        // Slate for sidebar
                        slate: {
                            750: '#293548',
                            850: '#172033',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        
        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Glassmorphism navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        /* Sidebar hover effect */
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: rgba(99, 102, 241, 0.1);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
        }
        
        /* Card hover effect */
        .card-modern {
            transition: all 0.2s ease;
        }
        .card-modern:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        
        /* Table row hover */
        .table-row-hover:hover {
            background-color: #f8fafc;
        }
        
        /* Badge styles */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0e7ff; color: #3730a3; }
        .badge-neutral { background: #f1f5f9; color: #475569; }
        
        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }
        
        /* Empty state illustration */
        .empty-state-icon {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        }
    </style>
</head>
<body class="h-full bg-slate-50 font-inter">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white z-40 hidden lg:block">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-800">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book-open text-white text-sm"></i>
                </div>
                <span class="font-semibold text-lg">Perpustakaan</span>
            </div>
            
            <!-- User Profile Mini -->
            <div class="px-4 py-4 border-b border-slate-800">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="px-4 py-6 space-y-1">
                <p class="px-3 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu Utama</p>
                
                <a href="{{ url('/') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="text-sm font-medium">Beranda</span>
                </a>
                
                <a href="{{ url('/books') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('books*') && !request()->is('books/*/edit') ? 'active' : '' }}">
                    <i class="fas fa-book w-5 text-center"></i>
                    <span class="text-sm font-medium">Katalog Buku</span>
                </a>
                
                <a href="{{ url('/loans') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('loans*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt w-5 text-center"></i>
                    <span class="text-sm font-medium">Peminjaman Saya</span>
                </a>
                
                <a href="{{ url('/wishlist') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('wishlist*') ? 'active' : '' }}">
                    <i class="fas fa-heart w-5 text-center"></i>
                    <span class="text-sm font-medium">Wishlist</span>
                </a>
                
                <a href="{{ url('/notifications') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('notifications*') ? 'active' : '' }}">
                    <i class="fas fa-bell w-5 text-center"></i>
                    <span class="text-sm font-medium">Notifikasi</span>
                    @php $unreadCount = Auth::user()->unreadNotificationsCount ?? 0; @endphp
                    @if($unreadCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                
                <div class="pt-6">
                    <p class="px-3 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun</p>
                    
                    <a href="{{ url('/profile') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white {{ request()->is('profile*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle w-5 text-center"></i>
                        <span class="text-sm font-medium">Profil Saya</span>
                    </a>
                    
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ url('/admin') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-amber-400 hover:text-amber-300">
                        <i class="fas fa-crown w-5 text-center"></i>
                        <span class="text-sm font-medium">Admin Panel</span>
                    </a>
                    @endif
                </div>
            </nav>
            
            <!-- Logout Button -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span class="text-sm font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Navbar (Glassmorphism) -->
            <header class="glass-nav sticky top-0 z-30 border-b border-slate-200/80">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-sidebar-btn" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <!-- Page Title -->
                    <div class="hidden lg:block">
                        <h1 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="text" placeholder="Cari buku..." class="w-64 pl-10 pr-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                        
                        <!-- Notifications -->
                        <a href="{{ url('/notifications') }}" class="relative p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition">
                            <i class="fas fa-bell"></i>
                            @if(($unreadCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </a>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button onclick="document.getElementById('profile-dropdown').classList.toggle('hidden')" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            
                            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                                <a href="{{ url('/profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-user w-4"></i> Profil Saya
                                </a>
                                <a href="{{ url('/loans') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-history w-4"></i> Riwayat Pinjam
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
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-4 lg:p-8">
                <!-- Alerts -->
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3">
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
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
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
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white z-50 transform -translate-x-full transition-transform lg:hidden">
        <!-- Same content as desktop sidebar -->
        <div class="flex items-center justify-between px-6 h-16 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book-open text-white text-sm"></i>
                </div>
                <span class="font-semibold">Perpustakaan</span>
            </div>
            <button id="close-mobile-sidebar" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="px-4 py-6 space-y-1">
            <a href="{{ url('/') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-sm font-medium">Beranda</span>
            </a>
            <a href="{{ url('/books') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <i class="fas fa-book w-5 text-center"></i>
                <span class="text-sm font-medium">Katalog Buku</span>
            </a>
            <a href="{{ url('/loans') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <i class="fas fa-exchange-alt w-5 text-center"></i>
                <span class="text-sm font-medium">Peminjaman</span>
            </a>
            <a href="{{ url('/wishlist') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <i class="fas fa-heart w-5 text-center"></i>
                <span class="text-sm font-medium">Wishlist</span>
            </a>
            <a href="{{ url('/profile') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                <i class="fas fa-user-circle w-5 text-center"></i>
                <span class="text-sm font-medium">Profil</span>
            </a>
        </nav>
    </div>
    
    <script>
        // Mobile sidebar toggle
        const mobileSidebarBtn = document.getElementById('mobile-sidebar-btn');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
        const closeMobileSidebar = document.getElementById('close-mobile-sidebar');
        
        function openMobileSidebar() {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileSidebarOverlay.classList.remove('hidden');
        }
        
        function closeMobileSidebarFn() {
            mobileSidebar.classList.add('-translate-x-full');
            mobileSidebarOverlay.classList.add('hidden');
        }
        
        mobileSidebarBtn?.addEventListener('click', openMobileSidebar);
        closeMobileSidebar?.addEventListener('click', closeMobileSidebarFn);
        mobileSidebarOverlay?.addEventListener('click', closeMobileSidebarFn);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown && !e.target.closest('[onclick*="profile-dropdown"]') && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

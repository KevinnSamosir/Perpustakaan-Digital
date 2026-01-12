<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Source Sans Pro', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        sidebar: {
                            dark: '#222d32',
                            darker: '#1a2226',
                            hover: '#1e282c',
                        },
                        primary: '#3c8dbc',
                        success: '#00a65a',
                        warning: '#f39c12',
                        danger: '#dd4b39',
                        info: '#00c0ef',
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Source Sans Pro', system-ui, sans-serif; }
        
        .sidebar-menu a, .sidebar-menu button {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .sidebar-menu a:hover, .sidebar-menu button:hover {
            background: #1e282c;
            border-left-color: #3c8dbc;
        }
        .sidebar-menu a.active {
            background: #1e282c;
            border-left-color: #3c8dbc;
        }
        
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .submenu.open {
            max-height: 500px;
        }
        
        .menu-arrow {
            transition: transform 0.2s ease;
        }
        .menu-arrow.rotate {
            transform: rotate(90deg);
        }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1a2226; }
        ::-webkit-scrollbar-thumb { background: #3c8dbc; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-sidebar-dark min-h-screen fixed left-0 top-0 z-40 overflow-y-auto">
            <!-- Logo -->
            <div class="bg-primary px-4 py-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book-reader text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">Perpustakaan</h1>
                    <p class="text-white/70 text-xs">Digital Admin</p>
                </div>
            </div>
            
            <!-- User Panel -->
            <div class="px-4 py-3 border-b border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center">
                    <i class="fas fa-user-shield text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-white font-semibold text-sm">Administrator</p>
                    <p class="text-green-400 text-xs flex items-center gap-1">
                        <i class="fas fa-circle text-[6px]"></i> Online
                    </p>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="sidebar-menu py-2">
                
                <!-- Dashboard -->
                <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Manajemen Buku -->
                <div>
                    <button onclick="toggleSubmenu('buku')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book w-5 text-center"></i>
                            <span>Manajemen Buku</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="buku-arrow"></i>
                    </button>
                    <div id="buku-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/books') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/books') ? 'text-white' : '' }}">
                            <i class="fas fa-list w-4"></i> Daftar Buku
                        </a>
                        <a href="{{ url('/admin/books/create') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/books/create') ? 'text-white' : '' }}">
                            <i class="fas fa-plus w-4"></i> Tambah Buku
                        </a>
                        <a href="{{ url('/admin/categories') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/categories*') ? 'text-white' : '' }}">
                            <i class="fas fa-tags w-4"></i> Kategori Buku
                        </a>
                        <a href="{{ url('/admin/authors') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/authors*') ? 'text-white' : '' }}">
                            <i class="fas fa-pen-fancy w-4"></i> Penulis
                        </a>
                        <a href="{{ url('/admin/publishers') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/publishers*') ? 'text-white' : '' }}">
                            <i class="fas fa-building w-4"></i> Penerbit
                        </a>
                    </div>
                </div>
                
                <!-- Manajemen User -->
                <div>
                    <button onclick="toggleSubmenu('user')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users w-5 text-center"></i>
                            <span>Manajemen User</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="user-arrow"></i>
                    </button>
                    <div id="user-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/users') ? 'text-white' : '' }}">
                            <i class="fas fa-list w-4"></i> Daftar User
                        </a>
                        <a href="{{ url('/admin/users/create') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/users/create') ? 'text-white' : '' }}">
                            <i class="fas fa-plus w-4"></i> Tambah User
                        </a>
                        <a href="{{ url('/admin/members') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/members*') ? 'text-white' : '' }}">
                            <i class="fas fa-id-card w-4"></i> Data Anggota
                        </a>
                    </div>
                </div>
                
                <!-- Manajemen Peminjaman -->
                <div>
                    <button onclick="toggleSubmenu('peminjaman')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exchange-alt w-5 text-center"></i>
                            <span>Manajemen Peminjaman</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="peminjaman-arrow"></i>
                    </button>
                    <div id="peminjaman-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/loans') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/loans') && !request('status') ? 'text-white' : '' }}">
                            <i class="fas fa-list w-4"></i> Semua Peminjaman
                        </a>
                        <a href="{{ url('/admin/loans?status=borrowed') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request('status') == 'borrowed' ? 'text-white' : '' }}">
                            <i class="fas fa-clock w-4"></i> Sedang Dipinjam
                        </a>
                        <a href="{{ url('/admin/loans?status=returned') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request('status') == 'returned' ? 'text-white' : '' }}">
                            <i class="fas fa-check w-4"></i> Sudah Dikembalikan
                        </a>
                        <a href="{{ url('/admin/loans?status=overdue') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request('status') == 'overdue' ? 'text-white' : '' }}">
                            <i class="fas fa-exclamation-triangle w-4"></i> Terlambat
                        </a>
                    </div>
                </div>
                
                <!-- Laporan -->
                <div>
                    <button onclick="toggleSubmenu('laporan')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span>Laporan</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="laporan-arrow"></i>
                    </button>
                    <div id="laporan-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/reports') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/reports') ? 'text-white' : '' }}">
                            <i class="fas fa-file-alt w-4"></i> Laporan Peminjaman
                        </a>
                        <a href="{{ url('/admin/reports?type=popular') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-fire w-4"></i> Buku Populer
                        </a>
                        <a href="{{ url('/admin/reports?type=users') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-user-check w-4"></i> User Aktif
                        </a>
                        <a href="{{ url('/admin/reports?export=true') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-download w-4"></i> Export Data
                        </a>
                    </div>
                </div>
                
                <!-- Pengaturan -->
                <div>
                    <button onclick="toggleSubmenu('pengaturan')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span>Pengaturan</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="pengaturan-arrow"></i>
                    </button>
                    <div id="pengaturan-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/settings') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/settings') ? 'text-white' : '' }}">
                            <i class="fas fa-sliders-h w-4"></i> Pengaturan Umum
                        </a>
                        <a href="{{ url('/admin/settings?tab=loan') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-calendar-alt w-4"></i> Durasi Peminjaman
                        </a>
                        <a href="{{ url('/admin/settings?tab=fine') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-money-bill w-4"></i> Denda Keterlambatan
                        </a>
                    </div>
                </div>
                
                <!-- Log Aktivitas -->
                <div>
                    <button onclick="toggleSubmenu('log')" class="w-full flex items-center justify-between px-4 py-3 text-gray-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-history w-5 text-center"></i>
                            <span>Log Aktivitas</span>
                        </div>
                        <i class="fas fa-angle-right menu-arrow text-xs" id="log-arrow"></i>
                    </button>
                    <div id="log-submenu" class="submenu bg-sidebar-darker">
                        <a href="{{ url('/admin/activity-logs') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white {{ request()->is('admin/activity-logs') ? 'text-white' : '' }}">
                            <i class="fas fa-user-shield w-4"></i> Log Admin
                        </a>
                        <a href="{{ url('/admin/activity-logs?type=user') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-user w-4"></i> Log User
                        </a>
                        <a href="{{ url('/admin/activity-logs?type=system') }}" class="flex items-center gap-3 px-4 py-2 pl-12 text-gray-400 text-sm hover:text-white">
                            <i class="fas fa-server w-4"></i> Log Sistem
                        </a>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-700 my-2"></div>
                
                <!-- Website Link -->
                <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 text-gray-300">
                    <i class="fas fa-globe w-5 text-center"></i>
                    <span>Lihat Website</span>
                    <i class="fas fa-external-link-alt text-xs ml-auto"></i>
                </a>
                
                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 hover:bg-sidebar-hover">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Top Header -->
            <header class="bg-primary text-white px-6 py-3 flex items-center justify-between sticky top-0 z-30 shadow">
                <div class="flex items-center gap-4">
                    <h2 class="font-semibold">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <nav class="text-sm text-white/80">
                        <a href="{{ url('/admin') }}" class="hover:text-white"><i class="fas fa-home"></i></a>
                        <span class="mx-2">/</span>
                        <span>@yield('breadcrumb', 'Dashboard')</span>
                    </nav>
                </div>
            </header>
            
            <!-- Alert Messages -->
            @if(session('success'))
            <div class="mx-6 mt-4">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded relative">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded relative">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t px-6 py-4 text-center text-gray-500 text-sm">
                <p><strong>Hak Cipta &copy; {{ date('Y') }} Perpustakaan Digital</strong> | Digital Library Management System</p>
                <p class="text-xs text-gray-400 mt-1">Versi 1.0</p>
            </footer>
        </div>
    </div>
    
    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id + '-submenu');
            const arrow = document.getElementById(id + '-arrow');
            
            // Close other submenus
            document.querySelectorAll('.submenu').forEach(menu => {
                if (menu.id !== id + '-submenu') {
                    menu.classList.remove('open');
                }
            });
            document.querySelectorAll('.menu-arrow').forEach(arr => {
                if (arr.id !== id + '-arrow') {
                    arr.classList.remove('rotate');
                }
            });
            
            submenu.classList.toggle('open');
            arrow.classList.toggle('rotate');
        }
        
        // Auto-open submenu based on current page
        document.addEventListener('DOMContentLoaded', function() {
            const path = window.location.pathname;
            
            if (path.includes('/admin/books') || path.includes('/admin/categories') || path.includes('/admin/authors') || path.includes('/admin/publishers')) {
                toggleSubmenu('buku');
            } else if (path.includes('/admin/users') || path.includes('/admin/members')) {
                toggleSubmenu('user');
            } else if (path.includes('/admin/loans')) {
                toggleSubmenu('peminjaman');
            } else if (path.includes('/admin/reports')) {
                toggleSubmenu('laporan');
            } else if (path.includes('/admin/settings')) {
                toggleSubmenu('pengaturan');
            } else if (path.includes('/admin/activity-logs')) {
                toggleSubmenu('log');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

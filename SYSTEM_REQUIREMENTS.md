# 📚 SYSTEM REQUIREMENTS DOCUMENT
## Perpustakaan Digital - Digital Library Management System

---

## 📋 1. DESKRIPSI UMUM SISTEM

### 1.1 Ringkasan
Perpustakaan Digital adalah sistem berbasis web yang dirancang untuk mengelola koleksi buku digital (ebook, PDF, jurnal) dan memfasilitasi peminjaman atau akses baca online secara efisien dan aman. Sistem ini menyediakan platform terpusat untuk manajemen perpustakaan yang dapat diakses oleh administrator dan pengguna.

### 1.2 Tujuan Sistem
- Menyediakan akses mudah ke koleksi buku digital
- Mengotomatisasi proses peminjaman dan pengembalian buku
- Memudahkan pencarian dan penemuan buku
- Menyediakan sistem manajemen yang efisien untuk administrator
- Meningkatkan pengalaman membaca digital bagi pengguna

### 1.3 Ruang Lingkup
- Manajemen katalog buku digital
- Sistem keanggotaan dan autentikasi
- Peminjaman dan pengembalian buku online
- Akses baca online untuk konten digital
- Pelaporan dan analitik perpustakaan
- Notifikasi dan pengingat otomatis

---

## 👥 2. USER ROLE & HAK AKSES

### 2.1 Admin (Administrator)

| No | Fitur | Deskripsi | Status |
|----|-------|-----------|--------|
| 1 | Login & Logout | Autentikasi admin ke sistem | ✅ Implemented |
| 2 | Dashboard Admin | Overview statistik perpustakaan | ✅ Implemented |
| 3 | Manajemen User | CRUD user (tambah, edit, hapus, blokir) | 🔄 Partial |
| 4 | Manajemen Buku | CRUD buku (tambah, edit, hapus, upload file) | 🔄 Partial |
| 5 | Manajemen Kategori | CRUD kategori buku | ❌ Not Implemented |
| 6 | Manajemen Penulis | CRUD data penulis | ❌ Not Implemented |
| 7 | Manajemen Penerbit | CRUD data penerbit | ❌ Not Implemented |
| 8 | Manajemen Peminjaman | Kelola transaksi peminjaman | ✅ Implemented |
| 9 | Validasi Peminjaman | Approve/reject peminjaman | 🔄 Partial |
| 10 | Monitoring | Pantau aktivitas peminjaman real-time | 🔄 Partial |
| 11 | Laporan | Generate laporan (buku, user, peminjaman) | ❌ Not Implemented |
| 12 | Log Aktivitas | Tracking aktivitas sistem | ❌ Not Implemented |

### 2.2 User (Member/Anggota)

| No | Fitur | Deskripsi | Status |
|----|-------|-----------|--------|
| 1 | Registrasi | Pendaftaran akun baru | ✅ Implemented |
| 2 | Login & Logout | Autentikasi pengguna | ✅ Implemented |
| 3 | Profil User | Lihat & edit profil pribadi | ❌ Not Implemented |
| 4 | Katalog Buku | Melihat daftar buku | ✅ Implemented |
| 5 | Pencarian Buku | Cari berdasarkan judul, kategori, penulis | ✅ Implemented |
| 6 | Filter Buku | Filter berdasarkan berbagai kriteria | 🔄 Partial |
| 7 | Detail Buku | Lihat informasi lengkap buku | ✅ Implemented |
| 8 | Baca Online | Membaca buku digital secara online | ❌ Not Implemented |
| 9 | Download Buku | Download file buku (sesuai aturan) | ❌ Not Implemented |
| 10 | Peminjaman | Ajukan peminjaman buku | ✅ Implemented |
| 11 | Riwayat Peminjaman | Lihat riwayat transaksi | ✅ Implemented |
| 12 | Wishlist/Favorit | Simpan buku ke daftar favorit | ❌ Not Implemented |
| 13 | Rating & Review | Beri rating dan ulasan buku | ❌ Not Implemented |

---

## ⚙️ 3. FITUR UTAMA

### 3.1 Autentikasi & Otorisasi
```
├── Login dengan email & password
├── Registrasi member baru
├── Role-based access control (Admin/User)
├── Session management
├── Remember me functionality
├── Password hashing (bcrypt)
└── CSRF protection
```

### 3.2 Manajemen Buku
```
├── CRUD Buku
│   ├── Tambah buku baru
│   ├── Edit informasi buku
│   ├── Hapus buku
│   └── Upload file buku (PDF/ePub)
├── Kategorisasi
│   ├── Kategori buku
│   ├── Sub-kategori
│   └── Tag/label
├── Informasi Buku
│   ├── Judul, ISBN, Penulis
│   ├── Penerbit, Tahun terbit
│   ├── Deskripsi/sinopsis
│   ├── Cover image
│   └── File digital (PDF/ePub)
└── Ketersediaan
    ├── Stock management
    └── Available stock tracking
```

### 3.3 Sistem Pencarian
```
├── Quick search (title, author, ISBN)
├── Advanced search
│   ├── By category
│   ├── By author
│   ├── By publisher
│   ├── By year
│   └── By availability
├── Auto-complete suggestions
└── Search history
```

### 3.4 Sistem Peminjaman
```
├── Request peminjaman
├── Approval workflow
├── Due date management
├── Return processing
├── Late return handling
├── Fine calculation
└── Borrowing history
```

### 3.5 Notifikasi
```
├── Email notifications
│   ├── Peminjaman berhasil
│   ├── Pengingat pengembalian
│   ├── Keterlambatan
│   └── Buku baru
├── In-app notifications
└── Push notifications (optional)
```

### 3.6 Keamanan File
```
├── Protected file storage
├── Watermarking (optional)
├── Download limits
├── Access logging
└── DRM protection (optional)
```

### 3.7 Reporting & Analytics
```
├── Dashboard statistics
├── Borrowing reports
├── User activity reports
├── Book popularity reports
├── Export to Excel/PDF
└── Custom date range
```

---

## 📊 4. REQUIREMENT FUNGSIONAL

### FR-01: Autentikasi
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-01.1 | Sistem harus menyediakan halaman login | High |
| FR-01.2 | Sistem harus menyediakan halaman registrasi | High |
| FR-01.3 | Sistem harus memvalidasi email unik | High |
| FR-01.4 | Sistem harus mengenkripsi password | High |
| FR-01.5 | Sistem harus mendukung fitur logout | High |
| FR-01.6 | Sistem harus mendukung fitur "Remember Me" | Medium |
| FR-01.7 | Sistem harus redirect berdasarkan role | High |

### FR-02: Manajemen User (Admin)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-02.1 | Admin dapat melihat daftar semua user | High |
| FR-02.2 | Admin dapat menambah user baru | High |
| FR-02.3 | Admin dapat mengedit data user | High |
| FR-02.4 | Admin dapat menghapus user | Medium |
| FR-02.5 | Admin dapat memblokir/mengaktifkan user | High |
| FR-02.6 | Admin dapat mereset password user | Medium |
| FR-02.7 | Admin dapat melihat aktivitas user | Low |

### FR-03: Manajemen Buku (Admin)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-03.1 | Admin dapat melihat daftar semua buku | High |
| FR-03.2 | Admin dapat menambah buku baru | High |
| FR-03.3 | Admin dapat mengedit informasi buku | High |
| FR-03.4 | Admin dapat menghapus buku | Medium |
| FR-03.5 | Admin dapat upload file buku (PDF) | High |
| FR-03.6 | Admin dapat upload cover buku | Medium |
| FR-03.7 | Admin dapat mengatur stok buku | High |
| FR-03.8 | Admin dapat mengelola kategori buku | High |

### FR-04: Manajemen Peminjaman (Admin)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-04.1 | Admin dapat melihat semua peminjaman | High |
| FR-04.2 | Admin dapat memproses pengembalian | High |
| FR-04.3 | Admin dapat melihat peminjaman terlambat | High |
| FR-04.4 | Admin dapat menghitung denda | Medium |
| FR-04.5 | Admin dapat memperpanjang peminjaman | Medium |
| FR-04.6 | Admin dapat export data peminjaman | Low |

### FR-05: Katalog Buku (User)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-05.1 | User dapat melihat katalog buku | High |
| FR-05.2 | User dapat mencari buku | High |
| FR-05.3 | User dapat filter buku berdasarkan kategori | High |
| FR-05.4 | User dapat melihat detail buku | High |
| FR-05.5 | User dapat melihat ketersediaan buku | High |
| FR-05.6 | User dapat melihat preview buku | Medium |

### FR-06: Peminjaman (User)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-06.1 | User dapat meminjam buku | High |
| FR-06.2 | User dapat melihat riwayat peminjaman | High |
| FR-06.3 | User dapat melihat status peminjaman | High |
| FR-06.4 | User mendapat notifikasi batas waktu | Medium |
| FR-06.5 | User dapat memperpanjang peminjaman | Medium |

### FR-07: Profil User
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-07.1 | User dapat melihat profil | High |
| FR-07.2 | User dapat mengedit profil | High |
| FR-07.3 | User dapat mengubah password | High |
| FR-07.4 | User dapat melihat statistik peminjaman | Low |

### FR-08: Wishlist & Review
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-08.1 | User dapat menambah buku ke wishlist | Medium |
| FR-08.2 | User dapat menghapus buku dari wishlist | Medium |
| FR-08.3 | User dapat memberikan rating buku | Medium |
| FR-08.4 | User dapat menulis review buku | Medium |
| FR-08.5 | User dapat melihat review buku lain | Medium |

### FR-09: Laporan (Admin)
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-09.1 | Admin dapat melihat laporan peminjaman | High |
| FR-09.2 | Admin dapat melihat laporan buku populer | Medium |
| FR-09.3 | Admin dapat melihat laporan aktivitas user | Medium |
| FR-09.4 | Admin dapat export laporan ke Excel/PDF | Low |

---

## 🔒 5. REQUIREMENT NON-FUNGSIONAL

### NFR-01: Performance
| ID | Requirement | Target |
|----|-------------|--------|
| NFR-01.1 | Response time halaman < 3 detik | High |
| NFR-01.2 | Support minimal 100 concurrent users | Medium |
| NFR-01.3 | Database query < 500ms | High |
| NFR-01.4 | File upload max 50MB | Medium |

### NFR-02: Security
| ID | Requirement | Target |
|----|-------------|--------|
| NFR-02.1 | Password hashing menggunakan bcrypt | High |
| NFR-02.2 | CSRF token pada semua form | High |
| NFR-02.3 | XSS protection | High |
| NFR-02.4 | SQL injection prevention | High |
| NFR-02.5 | HTTPS support | High |
| NFR-02.6 | Role-based middleware | High |
| NFR-02.7 | Session timeout 120 menit | Medium |

### NFR-03: Usability
| ID | Requirement | Target |
|----|-------------|--------|
| NFR-03.1 | Responsive design (mobile & desktop) | High |
| NFR-03.2 | UI modern & user-friendly | High |
| NFR-03.3 | Konsisten navigasi | High |
| NFR-03.4 | Pesan error yang jelas | High |
| NFR-03.5 | Loading indicators | Medium |

### NFR-04: Reliability
| ID | Requirement | Target |
|----|-------------|--------|
| NFR-04.1 | Uptime 99.5% | High |
| NFR-04.2 | Data backup harian | High |
| NFR-04.3 | Error logging | High |
| NFR-04.4 | Graceful error handling | High |

### NFR-05: Maintainability
| ID | Requirement | Target |
|----|-------------|--------|
| NFR-05.1 | Kode terstruktur (MVC pattern) | High |
| NFR-05.2 | Dokumentasi kode | Medium |
| NFR-05.3 | Version control (Git) | High |
| NFR-05.4 | Environment configuration | High |

---

## 📐 6. USE CASE DIAGRAM (DESKRIPSI)

### 6.1 Aktor
```
┌─────────────────────────────────────────────────────────────┐
│                         ACTORS                               │
├─────────────────────────────────────────────────────────────┤
│  👤 Guest     - Pengunjung yang belum login                 │
│  👥 User      - Member yang sudah terdaftar & login         │
│  👑 Admin     - Administrator sistem                        │
└─────────────────────────────────────────────────────────────┘
```

### 6.2 Use Case - Guest
```
┌─────────────────────────────────────────┐
│              GUEST USE CASES            │
├─────────────────────────────────────────┤
│  UC-G01: Melihat halaman beranda        │
│  UC-G02: Melihat katalog buku           │
│  UC-G03: Mencari buku                   │
│  UC-G04: Melihat detail buku            │
│  UC-G05: Melakukan registrasi           │
│  UC-G06: Melakukan login                │
└─────────────────────────────────────────┘
```

### 6.3 Use Case - User
```
┌─────────────────────────────────────────┐
│              USER USE CASES             │
├─────────────────────────────────────────┤
│  UC-U01: Login/Logout                   │
│  UC-U02: Melihat & Edit Profil          │
│  UC-U03: Melihat Katalog Buku           │
│  UC-U04: Mencari & Filter Buku          │
│  UC-U05: Melihat Detail Buku            │
│  UC-U06: Membaca Buku Online            │
│  UC-U07: Download Buku                  │
│  UC-U08: Meminjam Buku                  │
│  UC-U09: Melihat Riwayat Peminjaman     │
│  UC-U10: Mengelola Wishlist             │
│  UC-U11: Memberikan Rating & Review     │
│  UC-U12: Menerima Notifikasi            │
└─────────────────────────────────────────┘
```

### 6.4 Use Case - Admin
```
┌─────────────────────────────────────────┐
│             ADMIN USE CASES             │
├─────────────────────────────────────────┤
│  UC-A01: Login/Logout                   │
│  UC-A02: Melihat Dashboard              │
│  UC-A03: Manajemen User                 │
│  UC-A04: Manajemen Buku                 │
│  UC-A05: Manajemen Kategori             │
│  UC-A06: Manajemen Penulis              │
│  UC-A07: Manajemen Penerbit             │
│  UC-A08: Manajemen Peminjaman           │
│  UC-A09: Validasi Peminjaman            │
│  UC-A10: Monitoring Peminjaman          │
│  UC-A11: Generate Laporan               │
│  UC-A12: Melihat Log Aktivitas          │
└─────────────────────────────────────────┘
```

### 6.5 Use Case Diagram Visual
```
                                    ┌─────────────────────────────────────────┐
                                    │        PERPUSTAKAAN DIGITAL             │
                                    │                                         │
    ┌──────┐                        │  ┌─────────────────────────────────┐   │
    │Guest │─────────────────────────┼──│ Melihat Katalog                 │   │
    └──┬───┘                        │  └─────────────────────────────────┘   │
       │                            │  ┌─────────────────────────────────┐   │
       ├────────────────────────────┼──│ Mencari Buku                    │   │
       │                            │  └─────────────────────────────────┘   │
       │                            │  ┌─────────────────────────────────┐   │
       ├────────────────────────────┼──│ Registrasi                      │   │
       │                            │  └─────────────────────────────────┘   │
       │                            │  ┌─────────────────────────────────┐   │
       └────────────────────────────┼──│ Login                           │   │
                                    │  └─────────────────────────────────┘   │
    ┌──────┐                        │                                         │
    │ User │─────────────────────────┼──┌─────────────────────────────────┐   │
    └──┬───┘                        │  │ Meminjam Buku                   │   │
       │                            │  └─────────────────────────────────┘   │
       ├────────────────────────────┼──┌─────────────────────────────────┐   │
       │                            │  │ Melihat Riwayat                 │   │
       │                            │  └─────────────────────────────────┘   │
       ├────────────────────────────┼──┌─────────────────────────────────┐   │
       │                            │  │ Rating & Review                 │   │
       │                            │  └─────────────────────────────────┘   │
       └────────────────────────────┼──┌─────────────────────────────────┐   │
                                    │  │ Wishlist                        │   │
                                    │  └─────────────────────────────────┘   │
    ┌──────┐                        │                                         │
    │Admin │─────────────────────────┼──┌─────────────────────────────────┐   │
    └──┬───┘                        │  │ Dashboard                       │   │
       │                            │  └─────────────────────────────────┘   │
       ├────────────────────────────┼──┌─────────────────────────────────┐   │
       │                            │  │ Manajemen Buku                  │   │
       │                            │  └─────────────────────────────────┘   │
       ├────────────────────────────┼──┌─────────────────────────────────┐   │
       │                            │  │ Manajemen User                  │   │
       │                            │  └─────────────────────────────────┘   │
       ├────────────────────────────┼──┌─────────────────────────────────┐   │
       │                            │  │ Manajemen Peminjaman            │   │
       │                            │  └─────────────────────────────────┘   │
       └────────────────────────────┼──┌─────────────────────────────────┐   │
                                    │  │ Laporan                         │   │
                                    │  └─────────────────────────────────┘   │
                                    └─────────────────────────────────────────┘
```

---

## 🗂️ 7. STRUKTUR MENU

### 7.1 Menu Admin
```
📊 ADMIN PANEL
│
├── 🏠 Dashboard
│   ├── Statistik Overview
│   ├── Peminjaman Terbaru
│   ├── User Baru
│   └── Buku Terlambat
│
├── 📚 Manajemen Buku
│   ├── Daftar Buku
│   ├── Tambah Buku
│   ├── Kategori Buku
│   ├── Penulis
│   └── Penerbit
│
├── 👥 Manajemen User
│   ├── Daftar User
│   ├── Tambah User
│   ├── User Aktif
│   └── User Diblokir
│
├── 📋 Manajemen Peminjaman
│   ├── Semua Peminjaman
│   ├── Sedang Dipinjam
│   ├── Sudah Dikembalikan
│   └── Terlambat
│
├── 📈 Laporan
│   ├── Laporan Peminjaman
│   ├── Laporan Buku Populer
│   ├── Laporan User Aktif
│   └── Export Data
│
├── ⚙️ Pengaturan
│   ├── Pengaturan Umum
│   ├── Durasi Peminjaman
│   └── Denda Keterlambatan
│
└── 📝 Log Aktivitas
    ├── Log Admin
    ├── Log User
    └── Log Sistem
```

### 7.2 Menu User
```
📖 PERPUSTAKAAN DIGITAL
│
├── 🏠 Beranda
│   ├── Hero Section
│   ├── Statistik
│   ├── Fitur Unggulan
│   └── Buku Terbaru
│
├── 📚 Katalog Buku
│   ├── Semua Buku
│   ├── Pencarian
│   ├── Filter Kategori
│   └── Detail Buku
│
├── 📋 Peminjaman Saya
│   ├── Peminjaman Aktif
│   ├── Riwayat Peminjaman
│   └── Buku Terlambat
│
├── ❤️ Wishlist
│   └── Daftar Buku Favorit
│
├── 👤 Profil
│   ├── Informasi Pribadi
│   ├── Edit Profil
│   ├── Ubah Password
│   └── Statistik Saya
│
└── 🔔 Notifikasi
    ├── Pengingat Pengembalian
    └── Buku Baru
```

---

## 🗄️ 8. DATABASE DESIGN (ERD)

### 8.1 Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           DATABASE SCHEMA                                        │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│     USERS       │         │    MEMBERS      │         │     BOOKS       │
├─────────────────┤         ├─────────────────┤         ├─────────────────┤
│ id (PK)         │◄────────│ user_id (FK)    │         │ id (PK)         │
│ name            │    1:1  │ id (PK)         │         │ title           │
│ email           │         │ member_number   │         │ author_id (FK)  │──────┐
│ password        │         │ phone           │         │ publisher_id(FK)│──┐   │
│ role            │         │ address         │         │ category_id(FK) │─┐│   │
│ email_verified  │         │ join_date       │         │ isbn            │ ││   │
│ remember_token  │         │ status          │         │ publication_year│ ││   │
│ created_at      │         │ created_at      │         │ description     │ ││   │
│ updated_at      │         │ updated_at      │         │ cover_image     │ ││   │
└─────────────────┘         └────────┬────────┘         │ file_path       │ ││   │
                                     │                  │ stock           │ ││   │
                                     │                  │ available_stock │ ││   │
                                     │                  │ created_at      │ ││   │
                                     │                  │ updated_at      │ ││   │
                                     │                  └────────┬────────┘ ││   │
                                     │                           │          ││   │
                                     │         ┌─────────────────┤          ││   │
                                     │         │                 │          ││   │
                                     ▼         ▼                 ▼          ││   │
                            ┌─────────────────────┐    ┌─────────────────┐  ││   │
                            │       LOANS         │    │    WISHLISTS    │  ││   │
                            ├─────────────────────┤    ├─────────────────┤  ││   │
                            │ id (PK)             │    │ id (PK)         │  ││   │
                            │ member_id (FK)      │    │ member_id (FK)  │  ││   │
                            │ book_id (FK)        │    │ book_id (FK)    │  ││   │
                            │ loan_date           │    │ created_at      │  ││   │
                            │ due_date            │    └─────────────────┘  ││   │
                            │ return_date         │                         ││   │
                            │ status              │                         ││   │
                            │ fine_amount         │                         ││   │
                            │ notes               │    ┌─────────────────┐  ││   │
                            │ created_at          │    │    REVIEWS      │  ││   │
                            │ updated_at          │    ├─────────────────┤  ││   │
                            └─────────────────────┘    │ id (PK)         │  ││   │
                                                       │ member_id (FK)  │  ││   │
                                                       │ book_id (FK)    │  ││   │
┌─────────────────┐         ┌─────────────────┐       │ rating          │  ││   │
│   CATEGORIES    │◄────────│   PUBLISHERS    │       │ review          │  ││   │
├─────────────────┤    │    ├─────────────────┤       │ created_at      │  ││   │
│ id (PK)         │◄───┼────│ id (PK)         │◄──────┴─────────────────┘  ││   │
│ name            │    │    │ name            │                            │└───┘
│ description     │    │    │ address         │                            │
│ icon            │    │    │ phone           │       ┌─────────────────┐  │
│ created_at      │    │    │ email           │       │    AUTHORS      │  │
│ updated_at      │    │    │ created_at      │       ├─────────────────┤  │
└─────────────────┘    │    │ updated_at      │       │ id (PK)         │◄─┘
                       │    └─────────────────┘       │ name            │
                       │                              │ bio             │
                       │    ┌─────────────────┐       │ photo           │
                       │    │ ACTIVITY_LOGS   │       │ created_at      │
                       │    ├─────────────────┤       │ updated_at      │
                       │    │ id (PK)         │       └─────────────────┘
                       │    │ user_id (FK)    │
                       │    │ action          │       ┌─────────────────┐
                       │    │ description     │       │  NOTIFICATIONS  │
                       │    │ ip_address      │       ├─────────────────┤
                       │    │ user_agent      │       │ id (PK)         │
                       │    │ created_at      │       │ user_id (FK)    │
                       │    └─────────────────┘       │ type            │
                       │                              │ title           │
                       └──────────────────────────────│ message         │
                                                      │ is_read         │
                                                      │ created_at      │
                                                      └─────────────────┘
```

### 8.2 Daftar Tabel Database

| No | Nama Tabel | Deskripsi | Status |
|----|------------|-----------|--------|
| 1 | users | Data user sistem | ✅ Exists |
| 2 | members | Data anggota perpustakaan | ✅ Exists |
| 3 | books | Data buku | ✅ Exists |
| 4 | loans | Data peminjaman | ✅ Exists |
| 5 | categories | Kategori buku | ❌ Need Create |
| 6 | authors | Data penulis | ❌ Need Create |
| 7 | publishers | Data penerbit | ❌ Need Create |
| 8 | wishlists | Daftar favorit user | ❌ Need Create |
| 9 | reviews | Rating & review buku | ❌ Need Create |
| 10 | notifications | Notifikasi user | ❌ Need Create |
| 11 | activity_logs | Log aktivitas sistem | ❌ Need Create |
| 12 | settings | Pengaturan sistem | ❌ Need Create |

### 8.3 Detail Struktur Tabel

#### 8.3.1 Tabel users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### 8.3.2 Tabel members
```sql
CREATE TABLE members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    member_number VARCHAR(20) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    join_date DATE NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 8.3.3 Tabel categories
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### 8.3.4 Tabel authors
```sql
CREATE TABLE authors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT NULL,
    photo VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### 8.3.5 Tabel publishers
```sql
CREATE TABLE publishers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### 8.3.6 Tabel books (Updated)
```sql
CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    category_id BIGINT UNSIGNED NULL,
    author_id BIGINT UNSIGNED NULL,
    publisher_id BIGINT UNSIGNED NULL,
    publication_year YEAR NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    file_type ENUM('pdf', 'epub', 'both') DEFAULT 'pdf',
    pages INT NULL,
    language VARCHAR(50) DEFAULT 'Indonesia',
    stock INT DEFAULT 0,
    available_stock INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
);
```

#### 8.3.7 Tabel loans
```sql
CREATE TABLE loans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    loan_date DATETIME NOT NULL,
    due_date DATETIME NOT NULL,
    return_date DATETIME NULL,
    status ENUM('pending', 'borrowed', 'returned', 'late', 'lost') DEFAULT 'borrowed',
    fine_amount DECIMAL(10,2) DEFAULT 0,
    notes TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 8.3.8 Tabel wishlists
```sql
CREATE TABLE wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    UNIQUE KEY unique_wishlist (member_id, book_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
```

#### 8.3.9 Tabel reviews
```sql
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT NULL,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_review (member_id, book_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
```

#### 8.3.10 Tabel notifications
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 8.3.11 Tabel activity_logs
```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    model_type VARCHAR(100) NULL,
    model_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 8.3.12 Tabel settings
```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NULL,
    type VARCHAR(20) DEFAULT 'string',
    group VARCHAR(50) DEFAULT 'general',
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 💻 9. TEKNOLOGI YANG DIGUNAKAN

### 9.1 Backend
| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Framework | Laravel | 11.x |
| Language | PHP | 8.2+ |
| Authentication | Laravel Sanctum | - |
| ORM | Eloquent | - |

### 9.2 Database
| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| RDBMS | MySQL | 8.0+ |
| Cache | Redis (optional) | - |

### 9.3 Frontend
| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Template Engine | Blade | - |
| CSS Framework | Tailwind CSS | 3.x |
| Icons | Font Awesome | 6.x |
| JavaScript | Vanilla JS | - |

### 9.4 Storage
| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| Local Storage | Laravel Storage | File buku, cover |
| Cloud (optional) | AWS S3 / MinIO | Production |

### 9.5 Development Tools
| Komponen | Teknologi |
|----------|-----------|
| Package Manager | Composer, NPM |
| Version Control | Git |
| Local Server | Laragon |
| API Testing | Postman |

---

## 📅 10. IMPLEMENTATION ROADMAP

### Phase 1: Foundation (Current)
- [x] Project setup
- [x] Database design (basic)
- [x] Authentication system
- [x] Basic CRUD books
- [x] Basic loans management
- [x] Admin & User layouts

### Phase 2: Enhanced Features
- [ ] Category management
- [ ] Author management
- [ ] Publisher management
- [ ] Book file upload
- [ ] User profile management
- [ ] Enhanced search & filter

### Phase 3: Advanced Features
- [ ] Wishlist system
- [ ] Rating & review
- [ ] Online book reader
- [ ] Notification system
- [ ] Activity logging

### Phase 4: Reporting & Optimization
- [ ] Reports & analytics
- [ ] Export functionality
- [ ] Performance optimization
- [ ] Security hardening
- [ ] Documentation

---

## 📝 11. REVISION HISTORY

| Versi | Tanggal | Penulis | Deskripsi |
|-------|---------|---------|-----------|
| 1.0 | 2026-01-11 | System | Initial document |

---

*Document generated for Perpustakaan Digital Project*

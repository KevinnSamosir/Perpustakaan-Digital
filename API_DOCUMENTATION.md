# API Sistem Perpustakaan Digital

Sistem API untuk manajemen perpustakaan digital dengan autentikasi JWT, CRUD operations untuk buku, anggota, dan peminjaman buku.

## 📋 Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Postman Collection](#postman-collection)
- [Database Schema](#database-schema)
- [Error Handling](#error-handling)

## ✨ Fitur

### Autentikasi & Autorisasi
- ✅ Register dan Login dengan JWT (Sanctum)
- ✅ Role-based access control (Admin & Member)
- ✅ Token management dan logout
- ✅ Password hashing dengan bcrypt

### Admin Features
- ✅ Manajemen Buku (CRUD)
- ✅ Manajemen Anggota (CRUD)
- ✅ Monitoring peminjaman & pengembalian
- ✅ Tracking status peminjaman (dipinjam/dikembalikan/terlambat)

### Member Features
- ✅ Daftar dan cari buku
- ✅ Lihat detail buku
- ✅ Peminjaman buku
- ✅ Pengembalian buku
- ✅ Lihat riwayat peminjaman pribadi

### Validasi & Error Handling
- ✅ Input validation dengan Laravel Validator
- ✅ Consistent JSON response format
- ✅ HTTP status codes (200, 201, 400, 401, 403, 404, 409, 422, 500)
- ✅ Detailed error messages

## 🛠️ Teknologi

- **Framework**: Laravel 11
- **Database**: MySQL / SQLite
- **Authentication**: Laravel Sanctum (Token-based)
- **Testing**: PHPUnit
- **Validation**: Laravel Validation Rules

## 📦 Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- MySQL / SQLite

### Steps

1. **Clone Repository**
```bash
cd c:\laragon\www\PerpustakaanDigital
```

2. **Install Dependencies**
```bash
composer install
```

3. **Setup Environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Migration**
```bash
php artisan migrate
```

5. **Start Server**
```bash
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`

## ⚙️ Konfigurasi

### Environment Variables
```env
APP_NAME="Perpustakaan Digital"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_digital
DB_USERNAME=root
DB_PASSWORD=
```

## 📚 API Endpoints

### Base URL
```
http://127.0.0.1:8000/api
```

### Authentication Endpoints

#### 1. Register User
```http
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "member"  // or "admin"
}
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "member"
  },
  "token": "1|ABC123XYZ..."
}
```

#### 2. Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": { ... },
  "token": "1|ABC123XYZ..."
}
```

#### 3. Get Current User
```http
GET /auth/user
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User retrieved successfully",
  "user": { ... }
}
```

#### 4. Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logout successful"
}
```

### Books Endpoints

#### List Books
```http
GET /books?per_page=15&search=Laravel&category=Programming
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page`: Jumlah items per halaman (default: 15)
- `search`: Cari berdasarkan judul atau penulis
- `category`: Filter berdasarkan kategori

**Response (200):**
```json
{
  "message": "Books retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Clean Code",
      "author": "Robert C. Martin",
      "isbn": "978-0-13-235088-1",
      "publication_year": 2008,
      "category": "Programming",
      "stock": 5,
      "available_stock": 3
    }
  ],
  "pagination": {
    "total": 10,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

#### Get Book Details
```http
GET /books/{id}
Authorization: Bearer {token}
```

#### Check Book Availability
```http
GET /books/{id}/check-availability
Authorization: Bearer {token}
```

#### Create Book (Admin Only)
```http
POST /books
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Clean Code",
  "author": "Robert C. Martin",
  "isbn": "978-0-13-235088-1",
  "publication_year": 2008,
  "category": "Programming",
  "stock": 5,
  "description": "A Handbook of Agile Software Craftsmanship"
}
```

**Response (201):**
```json
{
  "message": "Book created successfully",
  "data": { ... }
}
```

#### Update Book (Admin Only)
```http
PUT /books/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Updated Title",
  "stock": 10
}
```

#### Delete Book (Admin Only)
```http
DELETE /books/{id}
Authorization: Bearer {admin_token}
```

---

### Members Endpoints (Admin Only)

#### List Members
```http
GET /members?per_page=15&status=active&search=John
Authorization: Bearer {admin_token}
```

#### Get Member Details
```http
GET /members/{id}
Authorization: Bearer {admin_token}
```

#### Create Member
```http
POST /members
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Anggota Baru",
  "email": "anggota@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890",
  "address": "Jalan Merdeka No. 1"
}
```

#### Update Member
```http
PUT /members/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Updated Name",
  "phone": "082234567890",
  "status": "active"
}
```

#### Delete Member
```http
DELETE /members/{id}
Authorization: Bearer {admin_token}
```

---

### Loans Endpoints

#### Get Loans
```http
GET /loans?per_page=15
Authorization: Bearer {token}
```

- **Member**: Hanya melihat loan miliknya sendiri
- **Admin**: Melihat semua loans

#### Get Loan Details
```http
GET /loans/{id}
Authorization: Bearer {token}
```

#### Borrow a Book
```http
POST /loans
Authorization: Bearer {member_token}
Content-Type: application/json

{
  "book_id": 1
}
```

**Response (201):**
```json
{
  "message": "Book borrowed successfully",
  "data": {
    "loan": { ... },
    "due_date": "2024-12-29",
    "days_to_return": 14
  }
}
```

#### Return a Book
```http
PUT /loans/{id}/return
Authorization: Bearer {member_token}
```

**Response (200):**
```json
{
  "message": "Book returned successfully",
  "data": {
    "loan": { ... },
    "return_date": "2024-12-15",
    "is_late": false,
    "days_late": 0
  }
}
```

## 🧪 Testing

### Menjalankan Tests

**Semua Tests:**
```bash
php artisan test tests/Feature/Feature/
```

**Specific Test File:**
```bash
php artisan test tests/Feature/Feature/AuthTest.php
php artisan test tests/Feature/Feature/BookTest.php
php artisan test tests/Feature/Feature/LoanTest.php
```

### Test Coverage

✅ **Authentication Tests (10 tests)**
- Register dengan data valid
- Register dengan email duplikat
- Register dengan password pendek
- Login dengan kredensial valid
- Login dengan email tidak valid
- Login dengan password salah
- Get authenticated user
- Logout
- Akses endpoint protected tanpa token

✅ **Book Tests (9 tests)**
- Member dapat melihat daftar buku
- Admin dapat membuat buku
- Member tidak dapat membuat buku
- Duplikat ISBN validation
- Admin dapat update buku
- Admin dapat delete buku
- Get detail buku
- Search buku berdasarkan judul
- Check availability buku

✅ **Loan Tests (10 tests)**
- Member dapat meminjam buku
- Member tidak dapat meminjam buku yang tidak tersedia
- Member tidak dapat meminjam buku yang sama 2x
- Member dapat melihat loan pribadi
- Member dapat mengembalikan buku
- Pengembalian setelah due date marked as late
- Admin dapat melihat semua loans
- Member tidak dapat melihat loan member lain
- Tidak dapat mengembalikan buku yang sudah dikembalikan
- Member inactive tidak dapat meminjam

### Test Results
```
PASS  Tests\Feature\AuthTest (10 tests) - 0.63s
PASS  Tests\Feature\BookTest (9 tests) - 0.05s
PASS  Tests\Feature\LoanTest (10 tests) - 0.04s

Total: 29 tests passed in 1.76s ✅
Response time: < 2 seconds ✅
```

## 📮 Postman Collection

### Import Collection

1. Buka Postman
2. Click **Import**
3. Pilih file `Perpustakaan_Digital_API.postman_collection.json`
4. Click **Import**

### Setup Environment Variables

Dalam Postman, set variables:
```
base_url: http://127.0.0.1:8000
auth_token: [token dari login response]
```

### Testing Scenarios Included

1. ✅ Health Check
2. ✅ Authentication (Register, Login, Get User, Logout)
3. ✅ Books CRUD
4. ✅ Members CRUD (Admin)
5. ✅ Loans (Borrow, View, Return)
6. ✅ Error Cases (Invalid email, Duplicate ISBN, etc)

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  role ENUM('admin', 'member') DEFAULT 'member',
  remember_token VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Books Table
```sql
CREATE TABLE books (
  id BIGINT PRIMARY KEY,
  title VARCHAR(255),
  author VARCHAR(255),
  isbn VARCHAR(255) UNIQUE,
  publication_year YEAR,
  category VARCHAR(100),
  stock INT DEFAULT 0,
  available_stock INT DEFAULT 0,
  description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Members Table
```sql
CREATE TABLE members (
  id BIGINT PRIMARY KEY,
  user_id BIGINT FOREIGN KEY,
  member_number VARCHAR(255) UNIQUE,
  phone VARCHAR(20),
  address TEXT,
  join_date DATE,
  status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Loans Table
```sql
CREATE TABLE loans (
  id BIGINT PRIMARY KEY,
  member_id BIGINT FOREIGN KEY,
  book_id BIGINT FOREIGN KEY,
  loan_date DATETIME,
  due_date DATETIME,
  return_date DATETIME NULL,
  status ENUM('borrowed', 'returned', 'late') DEFAULT 'borrowed',
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## ❌ Error Handling

### Error Response Format
```json
{
  "message": "Error message",
  "error": "Detailed error (optional)",
  "errors": { "field": ["Validation error message"] }
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Malformed request |
| 401 | Unauthorized - Missing/invalid token |
| 403 | Forbidden - Access denied / Admin only |
| 404 | Not Found - Resource tidak ditemukan |
| 409 | Conflict - Duplicate data / Conflict state |
| 422 | Unprocessable Entity - Validation errors |
| 500 | Internal Server Error - Server error |

### Common Errors

#### 401 Unauthorized
```json
{
  "message": "Invalid email or password"
}
```

#### 403 Forbidden (Admin Only)
```json
{
  "message": "Unauthorized. Admin access required."
}
```

#### 422 Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

#### 409 Conflict
```json
{
  "message": "Book is not available for borrowing",
  "available_stock": 0
}
```

## 📊 Performance

- ✅ Response time: **< 2 seconds** untuk semua endpoints
- ✅ Database queries: Optimized dengan eager loading
- ✅ Pagination: Default 15 items per page
- ✅ Index: Pada ISBN, email, member_number fields

## 🔐 Security Features

- ✅ Password hashing dengan bcrypt
- ✅ API token-based authentication (Sanctum)
- ✅ Role-based authorization (middleware)
- ✅ Input validation & sanitization
- ✅ SQL injection prevention (eloquent ORM)

## 📝 Development Notes

### Models dengan Relationships
- `User hasOne Member`
- `Member hasMany Loans`
- `Book hasMany Loans`
- `Loan belongsTo Member, Book`

### Middleware
- `auth:sanctum` - Authentication check
- `admin.only` - Admin role verification

### Loan Logic
- Durasi peminjaman: **14 hari** dari loan_date
- Stock otomatis berkurang saat borrow
- Stock otomatis bertambah saat return
- Automatic late detection jika return setelah due_date

## 🚀 Production Deployment

1. Update `.env` dengan production settings
2. Run migrations: `php artisan migrate --force`
3. Cache config: `php artisan config:cache`
4. Setup proper logging
5. Enable CORS if needed
6. Implement rate limiting

## 📄 License

MIT License

---

**Dibuat untuk Studi Kasus: Sistem Manajemen Pemesanan Layanan Online**

**Developer**: [Copilot AI]  
**Last Updated**: December 15, 2024

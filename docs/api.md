# Dokumentasi API Duitku

Dokumentasi lengkap REST API untuk aplikasi manajemen keuangan Duitku.

**Base URL:** `/api`

**Authentication:** Menggunakan Laravel Sanctum (Bearer Token)

---

## Daftar Isi

1. [Authentication](#authentication)
2. [Dashboard](#dashboard)
3. [Transactions](#transactions)
4. [Budgets](#budgets)
5. [Categories](#categories)
6. [Profile](#profile)
7. [Reports](#reports)
8. [Badges](#badges)
9. [Insights](#insights)
10. [Notifications](#notifications)

---

---

# AUTHENTICATION

## Register Pengguna Baru

**Method:** POST
**URL:** `/api/register`

### Deskripsi

Endpoint untuk mendaftarkan pengguna baru ke sistem. User akan mendapatkan token yang bisa digunakan untuk request ke endpoint yang memerlukan autentikasi.

### Request

Kirim data dalam format JSON di request body.

**Body Parameters:**

* `name` - string (required, max: 255) - Nama lengkap user
* `email` - string (required, unique) - Email user (harus unik)
* `password` - string (required, min: 6) - Password user
* `password_confirmation` - string (required) - Konfirmasi password (harus sama dengan password)
* `currency_code` - string (optional, length: 3) - Kode mata uang (IDR, USD, SGD, MYR)
* `initial_balance` - number (optional, min: 0) - Saldo awal user
* `onboarding_template` - string (optional) - Template onboarding yang digunakan (standard, freelancer, mahasiswa)

**Contoh Request:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "currency_code": "IDR",
  "initial_balance": 5000000,
  "onboarding_template": "standard"
}
```

### Response Success (201)

```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "john@example.com",
      "currency_code": "IDR",
      "initial_balance": 5000000,
      "current_balance": 5000000,
      "streak_days": 0,
      "is_premium": false,
      "premium_until": null,
      "avatar": null,
      "settings": {
        "theme": "light",
        "language": "id",
        "notifications_enabled": true,
        "daily_reminder": true
      },
      "created_at": "2024-05-14 10:30:00"
    },
    "token": "1|eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Response Error

* **400** - Validation error (email sudah terdaftar, password terlalu pendek, dll)
* **422** - Unprocessable Entity (validation failed)

#### Contoh Error (400):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email sudah digunakan"],
    "password": ["Password minimal 6 karakter"]
  }
}
```

### Catatan

* Email harus unik di sistem
* Password minimal 6 karakter
* Password confirmation harus sama persis dengan field password
* Newline currency_code jika disediakan harus valid (IDR, USD, SGD, MYR)
* Setiap user akan mendapatkan token bearer yang harus disimpan untuk mengubah request autentikasi

---

## Login User

**Method:** POST
**URL:** `/api/login`

### Deskripsi

Endpoint untuk login user menggunakan email dan password. Endpoint ini akan mengembalikan token bearer yang bisa digunakan untuk autentikasi request berikutnya.

### Request

Kirim data dalam format JSON di request body.

**Body Parameters:**

* `email` - string (required) - Email user
* `password` - string (required, min: 6) - Password user

**Contoh Request:**

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "john@example.com",
      "currency_code": "IDR",
      "initial_balance": 5000000,
      "current_balance": 5000000,
      "streak_days": 0,
      "is_premium": false,
      "premium_until": null,
      "avatar": null,
      "settings": {
        "theme": "light",
        "language": "id",
        "notifications_enabled": true,
        "daily_reminder": true
      },
      "created_at": "2024-05-14 10:30:00"
    },
    "token": "1|eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Response Error

* **401** - Unauthorized (email/password tidak cocok)
* **400** - Validation error (email/password kosong)
* **422** - Unprocessable Entity (validation failed)

#### Contoh Error (401):

```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Catatan

* Email harus terdaftar di sistem
* Password harus cocok dengan yang tersimpan
* Jika login berhasil, gunakan token yang diterima di header `Authorization: Bearer {token}` untuk request selanjutnya
* Token tidak akan expired secara otomatis, gunakan endpoint logout untuk menghapus token

---

## Logout User

**Method:** POST
**URL:** `/api/logout`

### Deskripsi

Endpoint untuk logout user dan menghapus token yang digunakan. Setelah logout, token tidak bisa digunakan lagi untuk autentikasi.

### Request

Kirim request tanpa body, tapi harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required) - Bearer token dari login/register

### Response Success (200)

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Response Error

* **401** - Unauthorized (token tidak valid atau sudah expired)

### Catatan

- Request ini harus diautentikasi dengan token yang valid
- Setelah logout, token akan dihapus dari database dan tidak bisa digunakan lagi

---

## Get Data User Saat Ini

**Method:** GET
**URL:** `/api/user`

### Deskripsi

Endpoint untuk mengambil data user yang sedang login. Data yang dikembalikan berisi informasi lengkap user termasuk pengaturan dan statistik.

### Request

Tidak ada body parameter. Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required) - Bearer token dari login/register

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "currency_code": "IDR",
    "initial_balance": 5000000,
    "current_balance": 4800000,
    "streak_days": 5,
    "is_premium": true,
    "premium_until": "2024-12-31",
    "avatar": "https://example.com/avatars/user.jpg",
    "settings": {
      "theme": "dark",
      "language": "id",
      "notifications_enabled": true,
      "daily_reminder": true
    },
    "created_at": "2024-05-14 10:30:00"
  }
}
```

### Response Error

* **401** - Unauthorized (token tidak valid atau sudah expired)

### Catatan

- `current_balance` adalah saldo user saat ini setelah semua transaksi
- `streak_days` menunjukkan berapa hari berturut-turut user melakukan transaksi
- `settings` berisi preferensi user yang bisa diubah

---

---

# DASHBOARD

## Get Dashboard Data

**Method:** GET
**URL:** `/api/dashboard`

### Deskripsi

Endpoint untuk mengambil semua data dashboard user. Data yang dikembalikan berisi ringkasan transaksi, budget status, insights, dll yang dibutuhkan untuk tampilan dashboard.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required) - Bearer token dari login/register

**Query Parameters:**

Tidak ada query parameter khusus.

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "balance": {
      "current": 4800000,
      "total_income": 10000000,
      "total_expense": 5200000
    },
    "recent_transactions": [
      {
        "id": "660e8400-e29b-41d4-a716-446655440001",
        "amount": 200000,
        "formatted_amount": "Rp 200.000",
        "type": "expense",
        "date": "2024-05-14",
        "note": "Beli kebutuhan sekolah",
        "category": {
          "id": "770e8400-e29b-41d4-a716-446655440000",
          "name": "Belanja",
          "type": "expense",
          "icon": "shopping_bag",
          "color": "#FF6B6B"
        }
      }
    ],
    "budget_status": {
      "total_budgets": 5,
      "budgets_on_track": 3,
      "budgets_warning": 2,
      "budgets_exceeded": 0
    },
    "upcoming_bills": [
      {
        "id": "880e8400-e29b-41d4-a716-446655440000",
        "title": "Cicilan Motor",
        "amount": 3000000,
        "due_date": "2024-05-25"
      }
    ]
  }
}
```

### Response Error

* **401** - Unauthorized (token tidak valid)

### Catatan

- Data dashboard adalah agregasi dari berbagai sumber (transaksi, budget, bills, dll)
- Direkomendasikan untuk cache response ini di client karena can be expensive computationally
- Recent transactions biasanya menampilkan 5-10 transaksi terakhir
- Budget status mengkategorikan budget berdasarkan persentase penggunaan

---

---

# TRANSACTIONS

## Get Semua Transaksi User

**Method:** GET
**URL:** `/api/transactions`

### Deskripsi

Endpoint untuk mengambil semua transaksi user dengan pagination. Data diurutkan dari transaksi terbaru.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required) - Bearer token

**Query Parameters:**

* `per_page` - integer (optional, default: 15) - Jumlah item per halaman
* `page` - integer (optional, default: 1) - Halaman yang diminta

**Contoh:** `/api/transactions?per_page=20&page=1`

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "660e8400-e29b-41d4-a716-446655440001",
      "amount": 200000,
      "formatted_amount": "Rp 200.000",
      "type": "expense",
      "date": "2024-05-14",
      "note": "Beli kebutuhan sekolah",
      "photo_url": "https://example.com/receipts/123.jpg",
      "location_name": "Indomaret",
      "is_duplicate": false,
      "category": {
        "id": "770e8400-e29b-41d4-a716-446655440000",
        "name": "Belanja",
        "type": "expense",
        "icon": "shopping_bag",
        "color": "#FF6B6B",
        "budget_default": null,
        "is_default": true,
        "user_id": null
      },
      "created_at": "2024-05-14 10:30:00"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/transactions?page=1",
    "last": "http://localhost:8000/api/transactions?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/transactions?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### Response Error

* **401** - Unauthorized (token tidak valid)

### Catatan

- Pagination support dengan meta info lengkap
- Transaksi diurutkan dari yang terbaru (desc by date)
- Category diload together dengan transaction
- `is_duplicate` bisa digunakan untuk deteksi transaksi kembar
- Pagination links memudahkan navigasi antar halaman

---

## Buat Transaksi Baru

**Method:** POST
**URL:** `/api/transactions`

### Deskripsi

Endpoint untuk membuat transaksi baru. User hanya bisa membuat transaksi untuk dirinya sendiri.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `amount` - number (required, min: 1) - Nominal transaksi
* `type` - string (required) - Tipe transaksi: `income` atau `expense`
* `category_id` - UUID (required, must exist) - ID kategori yang valid
* `date` - string (optional, format: Y-m-d) - Tanggal transaksi (default: hari ini)
* `note` - string (optional, max: 255) - Catatan transaksi
* `photo_url` - string (optional, valid URL) - URL foto bukti transaksi
* `location_name` - string (optional, max: 255) - Nama lokasi transaksi

**Contoh Request:**

```json
{
  "amount": 200000,
  "type": "expense",
  "category_id": "770e8400-e29b-41d4-a716-446655440000",
  "date": "2024-05-14",
  "note": "Beli kebutuhan sekolah di Indomaret",
  "photo_url": "https://example.com/receipts/123.jpg",
  "location_name": "Indomaret Jl. Sudirman"
}
```

### Response Success (201)

```json
{
  "success": true,
  "message": "Transaction created successfully",
  "data": {
    "id": "660e8400-e29b-41d4-a716-446655440001",
    "amount": 200000,
    "formatted_amount": "Rp 200.000",
    "type": "expense",
    "date": "2024-05-14",
    "note": "Beli kebutuhan sekolah di Indomaret",
    "photo_url": "https://example.com/receipts/123.jpg",
    "location_name": "Indomaret Jl. Sudirman",
    "is_duplicate": false,
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "created_at": "2024-05-14 14:30:00"
  }
}
```

### Response Error

* **400** - Validation error (amount 0, type invalid, category tidak ada, dll)
* **401** - Unauthorized
* **422** - Unprocessable Entity

#### Contoh Error (400):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "amount": ["Nominal minimal Rp 1"],
    "category_id": ["Kategori tidak ditemukan"],
    "type": ["Tipe transaksi harus income atau expense"]
  }
}
```

### Catatan

- Amount harus lebih dari 0
- Type harus `income` atau `expense`
- Category harus ada dan diterima oleh user tersebut atau kategori global
- Date otomatis menggunakan hari ini jika tidak disediakan
- Service akan otomatis update saldo user
- System akan mendeteksi kemungkinan duplicate transaction

---

## Get Detail Transaksi

**Method:** GET
**URL:** `/api/transactions/{id}`

### Deskripsi

Endpoint untuk mengambil detail transaksi spesifik. User hanya bisa melihat transaksi miliknya sendiri.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID transaksi

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "660e8400-e29b-41d4-a716-446655440001",
    "amount": 200000,
    "formatted_amount": "Rp 200.000",
    "type": "expense",
    "date": "2024-05-14",
    "note": "Beli kebutuhan sekolah di Indomaret",
    "photo_url": "https://example.com/receipts/123.jpg",
    "location_name": "Indomaret Jl. Sudirman",
    "is_duplicate": false,
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "created_at": "2024-05-14 14:30:00"
  }
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden (transaksi milik user lain)
* **404** - Not Found (transaksi tidak ada)

### Catatan

- Endpoint menggunakan policy untuk memastikan user hanya bisa akses transaksi miliknya
- Model binding otomatis mencari transaksi berdasarkan ID

---

## Update Transaksi

**Method:** PUT
**URL:** `/api/transactions/{id}`

### Deskripsi

Endpoint untuk update data transaksi. User hanya bisa update transaksi miliknya sendiri. Validation rules sama dengan create endpoint.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**URL Parameters:**

* `id` - UUID - ID transaksi

**Body Parameters:**

* `amount` - number (required, min: 1)
* `type` - string (required) - `income` atau `expense`
* `category_id` - UUID (required, must exist)
* `date` - string (optional)
* `note` - string (optional)
* `photo_url` - string (optional)
* `location_name` - string (optional)

**Contoh Request:**

```json
{
  "amount": 250000,
  "type": "expense",
  "category_id": "770e8400-e29b-41d4-a716-446655440000",
  "note": "Update: Beli kebutuhan sekolah dan buku"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Transaction updated successfully",
  "data": {
    "id": "660e8400-e29b-41d4-a716-446655440001",
    "amount": 250000,
    "formatted_amount": "Rp 250.000",
    "type": "expense",
    "date": "2024-05-14",
    "note": "Update: Beli kebutuhan sekolah dan buku",
    "photo_url": "https://example.com/receipts/123.jpg",
    "location_name": "Indomaret Jl. Sudirman",
    "is_duplicate": false,
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "created_at": "2024-05-14 14:30:00"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **403** - Forbidden (bukan pemilik transaksi)
* **404** - Not Found
* **422** - Unprocessable Entity

### Catatan

- Service akan otomatis recalculate saldo user ketika transaksi diupdate
- Budget status akan diupdate jika kategori-nya memiliki budget

---

## Hapus Transaksi

**Method:** DELETE
**URL:** `/api/transactions/{id}`

### Deskripsi

Endpoint untuk menghapus transaksi. User hanya bisa menghapus transaksi miliknya sendiri. Saldo user akan otomatis diupdate.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID transaksi

### Response Success (200)

```json
{
  "success": true,
  "message": "Transaction deleted successfully"
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden (bukan pemilik transaksi)
* **404** - Not Found

### Catatan

- Penghapusan bersifat hard delete (permanent)
- Saldo user akan dikurangi/ditambah sesuai dengan tipe transaksi yang dihapus

---

## Get Ringkasan Transaksi Per Kategori

**Method:** GET
**URL:** `/api/transactions/summary/by-category`

### Deskripsi

Endpoint untuk mengambil ringkasan total pengeluaran/pemasukan berdasarkan kategori. Endpoint ini berguna untuk visualisasi pie chart atau bar chart di dashboard.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `period` - string (optional, default: `monthly`) - Periode ringkasan: `daily`, `weekly`, `monthly`, `yearly`

**Contoh:** `/api/transactions/summary/by-category?period=monthly`

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "period": "monthly",
    "summary": [
      {
        "category_id": "770e8400-e29b-41d4-a716-446655440000",
        "category_name": "Belanja",
        "category_icon": "shopping_bag",
        "category_color": "#FF6B6B",
        "type": "expense",
        "total": 2500000,
        "formatted_total": "Rp 2.500.000",
        "percentage": 54.35,
        "transaction_count": 12
      },
      {
        "category_id": "880e8400-e29b-41d4-a716-446655440001",
        "category_name": "Makan & Minum",
        "category_icon": "restaurant",
        "category_color": "#4ECDC4",
        "type": "expense",
        "total": 1200000,
        "formatted_total": "Rp 1.200.000",
        "percentage": 26.09,
        "transaction_count": 8
      }
    ],
    "total_income": 10000000,
    "total_expense": 4600000,
    "net": 5400000
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Endpoint support multiple period filter untuk different time ranges
- Percentage dihitung dari total kategori sejenis (income/expense)
- Berguna untuk membuat chart visualization
- Include transaction count untuk estimasi frequency

---

---

# BUDGETS

## Get Semua Budget User

**Method:** GET
**URL:** `/api/budgets`

### Deskripsi

Endpoint untuk mengambil semua budget user dengan optional filter berdasarkan bulan. Budget digunakan untuk limit pengeluaran per kategori.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `month_year` - string (optional, format: Y-m) - Filter berdasarkan bulan (contoh: `2024-05`)

**Contoh:** `/api/budgets?month_year=2024-05`

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "990e8400-e29b-41d4-a716-446655440000",
      "category": {
        "id": "770e8400-e29b-41d4-a716-446655440000",
        "name": "Belanja",
        "type": "expense",
        "icon": "shopping_bag",
        "color": "#FF6B6B",
        "budget_default": null,
        "is_default": true,
        "user_id": null
      },
      "month_year": "2024-05",
      "limit_amount": 3000000,
      "spent_amount": 2500000,
      "remaining_amount": 500000,
      "percentage": 83.33,
      "is_overspent": false,
      "status": "warning"
    },
    {
      "id": "a90e8400-e29b-41d4-a716-446655440001",
      "category": {
        "id": "880e8400-e29b-41d4-a716-446655440001",
        "name": "Makan & Minum",
        "type": "expense",
        "icon": "restaurant",
        "color": "#4ECDC4",
        "budget_default": null,
        "is_default": true,
        "user_id": null
      },
      "month_year": "2024-05",
      "limit_amount": 1500000,
      "spent_amount": 1200000,
      "remaining_amount": 300000,
      "percentage": 80,
      "is_overspent": false,
      "status": "warning"
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Status budget bisa `on_track`, `warning`, atau `exceeded` berdasarkan persentase spent
- `spent_amount` dihitung dari transaksi yang sudah dicatat
- `remaining_amount` = `limit_amount` - `spent_amount`
- Filter `month_year` harus dalam format `YYYY-MM`

---

## Get Status Budget Bulan Berjalan

**Method:** GET
**URL:** `/api/budgets/current`

### Deskripsi

Endpoint untuk mengambil status budget user untuk bulan saat ini (current month). Include status overall dan detail setiap budget.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "overall": {
      "total_budget": 10000000,
      "total_spent": 7800000,
      "total_remaining": 2200000,
      "percentage": 78,
      "status": "warning",
      "is_overspent": false
    },
    "budgets": [
      {
        "id": "990e8400-e29b-41d4-a716-446655440000",
        "category": {
          "id": "770e8400-e29b-41d4-a716-446655440000",
          "name": "Belanja",
          "type": "expense",
          "icon": "shopping_bag",
          "color": "#FF6B6B",
          "budget_default": null,
          "is_default": true,
          "user_id": null
        },
        "month_year": "2024-05",
        "limit_amount": 3000000,
        "spent_amount": 2500000,
        "remaining_amount": 500000,
        "percentage": 83.33,
        "is_overspent": false,
        "status": "warning"
      }
    ]
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Status overall dihitung dari aggregate semua budget di bulan saat ini
- Berguna untuk quick overview di halaman dashboard

---

## Buat Budget Baru

**Method:** POST
**URL:** `/api/budgets`

### Deskripsi

Endpoint untuk membuat budget baru untuk kategori tertentu di bulan tertentu.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `category_id` - UUID (required, must exist) - ID kategori
* `month_year` - string (required, format: Y-m) - Bulan budget (contoh: 2024-05)
* `limit_amount` - number (required, min: 0) - Limit budget dalam bulan

**Contoh Request:**

```json
{
  "category_id": "770e8400-e29b-41d4-a716-446655440000",
  "month_year": "2024-05",
  "limit_amount": 3000000
}
```

### Response Success (201)

```json
{
  "success": true,
  "message": "Budget created successfully",
  "data": {
    "id": "990e8400-e29b-41d4-a716-446655440000",
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "month_year": "2024-05",
    "limit_amount": 3000000,
    "spent_amount": 0,
    "remaining_amount": 3000000,
    "percentage": 0,
    "is_overspent": false,
    "status": "on_track"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **422** - Unprocessable Entity

#### Contoh Error (400):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "category_id": ["Kategori tidak ditemukan"],
    "month_year": ["Format bulan harus YYYY-MM"],
    "limit_amount": ["Limit harus angka positif"]
  }
}
```

### Catatan

- Satu kategori hanya bisa memiliki satu budget per bulan
- Month_year harus dalam format YYYY-MM
- Limit amount harus numeric dan minimal 0

---

## Get Detail Budget

**Method:** GET
**URL:** `/api/budgets/{id}`

### Deskripsi

Endpoint untuk mengambil detail budget spesifik beserta detail kategorinya.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID budget

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "990e8400-e29b-41d4-a716-446655440000",
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "month_year": "2024-05",
    "limit_amount": 3000000,
    "spent_amount": 2500000,
    "remaining_amount": 500000,
    "percentage": 83.33,
    "is_overspent": false,
    "status": "warning"
  }
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden (bukan pemilik budget)
* **404** - Not Found

### Catatan

- Model binding otomatis mencari budget berdasarkan ID

---

## Update Budget

**Method:** PUT
**URL:** `/api/budgets/{id}`

### Deskripsi

Endpoint untuk update data budget. User hanya bisa update budget miliknya sendiri.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**URL Parameters:**

* `id` - UUID - ID budget

**Body Parameters:**

* `category_id` - UUID (required, must exist)
* `month_year` - string (required, format: Y-m)
* `limit_amount` - number (required, min: 0)

**Contoh Request:**

```json
{
  "category_id": "770e8400-e29b-41d4-a716-446655440000",
  "month_year": "2024-05",
  "limit_amount": 3500000
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Budget updated successfully",
  "data": {
    "id": "990e8400-e29b-41d4-a716-446655440000",
    "category": {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": null,
      "is_default": true,
      "user_id": null
    },
    "month_year": "2024-05",
    "limit_amount": 3500000,
    "spent_amount": 2500000,
    "remaining_amount": 1000000,
    "percentage": 71.43,
    "is_overspent": false,
    "status": "on_track"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **403** - Forbidden
* **404** - Not Found

### Catatan

- Update budget akan otomatis recalculate status berdasarkan spending terbaru

---

## Hapus Budget

**Method:** DELETE
**URL:** `/api/budgets/{id}`

### Deskripsi

Endpoint untuk menghapus budget. User hanya bisa menghapus budget miliknya sendiri.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID budget

### Response Success (200)

```json
{
  "success": true,
  "message": "Budget deleted successfully"
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden
* **404** - Not Found

### Catatan

- Penghapusan bersifat hard delete
- Transaksi yang terkait tidak akan terhapus

---

---

# CATEGORIES

## Get Semua Kategori User

**Method:** GET
**URL:** `/api/categories`

### Deskripsi

Endpoint untuk mengambil semua kategori user (kategori user + kategori global). Optional filter berdasarkan type kategori (income/expense).

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `type` - string (optional) - Filter tipe kategori: `income` atau `expense`

**Contoh:** `/api/categories?type=expense`

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "770e8400-e29b-41d4-a716-446655440000",
      "name": "Belanja",
      "type": "expense",
      "icon": "shopping_bag",
      "color": "#FF6B6B",
      "budget_default": 3000000,
      "is_default": true,
      "user_id": null
    },
    {
      "id": "880e8400-e29b-41d4-a716-446655440001",
      "name": "Transport",
      "type": "expense",
      "icon": "directions_car",
      "color": "#4ECDC4",
      "budget_default": 500000,
      "is_default": false,
      "user_id": "550e8400-e29b-41d4-a716-446655440000"
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- `is_default: true` berarti kategori global/bawaan sistem
- `is_default: false` berarti kategori custom user
- `budget_default` adalah budget suggestion untuk kategori tersebut
- User bisa customize kategori global dengan bikin versi custom-nya

---

## Get Detail Kategori

**Method:** GET
**URL:** `/api/categories/{id}`

### Deskripsi

Endpoint untuk mengambil detail kategori tertentu.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID kategori

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "770e8400-e29b-41d4-a716-446655440000",
    "name": "Belanja",
    "type": "expense",
    "icon": "shopping_bag",
    "color": "#FF6B6B",
    "budget_default": 3000000,
    "is_default": true,
    "user_id": null
  }
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden (kategori milik orang lain)
* **404** - Not Found

### Catatan

- Model binding otomatis mencari kategori

---

## Buat Kategori Baru

**Method:** POST
**URL:** `/api/categories`

### Deskripsi

Endpoint untuk membuat kategori custom milik user. Kategori ini hanya bisa digunakan oleh user yang membuatnya.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `name` - string (required, max: 50) - Nama kategori
* `type` - string (required) - Tipe kategori: `income` atau `expense`
* `icon` - string (optional, max: 50) - Icon material design name
* `color` - string (optional, max: 7) - Warna hex (contoh: #FF6B6B)
* `budget_default` - number (optional, min: 0) - Default budget

**Contoh Request:**

```json
{
  "name": "Freelance",
  "type": "income",
  "icon": "work",
  "color": "#00D084",
  "budget_default": null
}
```

### Response Success (201)

```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": "bb0e8400-e29b-41d4-a716-446655440002",
    "name": "Freelance",
    "type": "income",
    "icon": "work",
    "color": "#00D084",
    "budget_default": null,
    "is_default": false,
    "user_id": "550e8400-e29b-41d4-a716-446655440000"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **422** - Unprocessable Entity

#### Contoh Error:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["Nama kategori wajib diisi"],
    "type": ["Type harus income atau expense"],
    "color": ["Warna hex tidak valid"]
  }
}
```

### Catatan

- Kategori custom hanya bisa digunakan oleh user yang membuatnya
- Icon harus nama material design icon yang valid
- Color harus format hex color yang valid
- Name maksimal 50 karakter

---

## Update Kategori

**Method:** PUT
**URL:** `/api/categories/{id}`

### Deskripsi

Endpoint untuk update data kategori. User hanya bisa update kategori custom miliknya (bukan kategori global/default).

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**URL Parameters:**

* `id` - UUID - ID kategori

**Body Parameters:**

* `name` - string (required, max: 50)
* `type` - string (required)
* `icon` - string (optional)
* `color` - string (optional)
* `budget_default` - number (optional, min: 0)

**Contoh Request:**

```json
{
  "name": "Freelance Indonesia",
  "type": "income",
  "color": "#00D084"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": "bb0e8400-e29b-41d4-a716-446655440002",
    "name": "Freelance Indonesia",
    "type": "income",
    "icon": "work",
    "color": "#00D084",
    "budget_default": null,
    "is_default": false,
    "user_id": "550e8400-e29b-41d4-a716-446655440000"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **403** - Forbidden (tidak bisa update kategori default)
* **404** - Not Found

### Catatan

- Hanya kategori custom (user-created) yang bisa diupdate
- Kategori default/global tidak bisa dimodifikasi

---

## Hapus Kategori

**Method:** DELETE
**URL:** `/api/categories/{id}`

### Deskripsi

Endpoint untuk menghapus kategori custom. User hanya bisa menghapus kategori custom miliknya sendiri.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - UUID - ID kategori

### Response Success (200)

```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

### Response Error

* **401** - Unauthorized
* **403** - Forbidden (tidak bisa delete kategori default atau milik orang lain)
* **404** - Not Found

### Catatan

- Penghapusan bersifat hard delete
- Tidak bisa delete kategori default/global
- Transaksi yang menggunakan kategori ini akan menjadi invalid jika dihapus (tergantung business logic)

---

---

# PROFILE

## Get Data Profile User

**Method:** GET
**URL:** `/api/profile`

### Deskripsi

Endpoint untuk mengambil profil user yang sedang login. Sama dengan endpoint `/api/user` tapi lebih specifically untuk profile page.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "currency_code": "IDR",
    "initial_balance": 5000000,
    "current_balance": 4800000,
    "streak_days": 5,
    "is_premium": true,
    "premium_until": "2024-12-31",
    "avatar": "https://example.com/avatars/user.jpg",
    "settings": {
      "theme": "dark",
      "language": "id",
      "notifications_enabled": true,
      "daily_reminder": true
    },
    "created_at": "2024-05-14 10:30:00"
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Endpoint ini identik dengan GET /api/user untuk kompatibilitas

---

## Update Profile User

**Method:** PUT
**URL:** `/api/profile`

### Deskripsi

Endpoint untuk update profil user (name, email, avatar, currency, dll).

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `name` - string (optional, max: 255) - Nama user
* `email` - string (optional, unique) - Email user (harus unik)
* `password` - string (optional, min: 6) - Password baru
* `password_confirmation` - string (optional) - Konfirmasi password baru
* `currency_code` - string (optional, size: 3) - Kode mata uang (IDR, USD, SGD, MYR)
* `avatar` - string (optional, max: 255) - URL atau path avatar
* `notification_token` - string (optional) - Token push notification

**Contoh Request:**

```json
{
  "name": "John Doe Updated",
  "avatar": "https://example.com/avatars/user-new.jpg",
  "currency_code": "USD",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe Updated",
    "email": "john@example.com",
    "currency_code": "USD",
    "initial_balance": 5000000,
    "current_balance": 4800000,
    "streak_days": 5,
    "is_premium": true,
    "premium_until": "2024-12-31",
    "avatar": "https://example.com/avatars/user-new.jpg",
    "settings": {
      "theme": "dark",
      "language": "id",
      "notifications_enabled": true,
      "daily_reminder": true
    },
    "created_at": "2024-05-14 10:30:00"
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized
* **422** - Unprocessable Entity

#### Contoh Error:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email sudah digunakan user lain"],
    "password": ["Password minimal 6 karakter"],
    "password_confirmation": ["Konfirmasi password tidak cocok"]
  }
}
```

### Catatan

- Email harus unique (tidak boleh sama dengan user lain)
- Password opsional, jika dikirim harus disertai confirmation
- Currency code harus dalam list yang diizinkan

---

## Update Settings User

**Method:** PUT
**URL:** `/api/profile/settings`

### Deskripsi

Endpoint untuk update pengaturan user (theme, language, notification preferences, dll).

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `theme` - string (optional) - Tema UI: `light` atau `dark`
* `language` - string (optional, max: 5) - Kode bahasa (contoh: `id`, `en`)
* `notifications_enabled` - boolean (optional) - Enable/disable notifikasi
* `daily_reminder` - boolean (optional) - Enable/disable daily reminder

**Contoh Request:**

```json
{
  "theme": "dark",
  "language": "id",
  "notifications_enabled": true,
  "daily_reminder": true
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Settings updated successfully",
  "data": {
    "theme": "dark",
    "language": "id",
    "notifications_enabled": true,
    "daily_reminder": true
  }
}
```

### Response Error

* **400** - Validation error
* **401** - Unauthorized

#### Contoh Error:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "theme": ["Theme harus light atau dark"]
  }
}
```

### Catatan

- Settings dimenyimpan dalam format JSON di database
- Update settings akan merge dengan settings yang sudah ada
- Theme `light` atau `dark`
- Language menggunakan kode ISO 639-1 (id, en, etc)

---

## Update Notification Token

**Method:** POST
**URL:** `/api/profile/notification-token`

### Deskripsi

Endpoint untuk update push notification token (dari Firebase Cloud Messaging atau push service manapun). Token ini digunakan untuk mengirim push notification ke user.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `notification_token` - string (required, max: 255) - Push notification token dari FCM

**Contoh Request:**

```json
{
  "notification_token": "c-eFVF8N2XG_xJJPbJCk_Q:APA91bGkdpXsKdXrX_6eXXXXXXXXXXXXXXXXXXXXXX"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Notification token updated successfully"
}
```

### Response Error

* **400** - Validation error (token empty atau terlalu panjang)
* **401** - Unauthorized

### Catatan

- Token digunakan untuk mengirim push notification ke device
- Biasanya di-update setiap kali app launch atau login
- Token dari FCM bisa berubah kapan saja, jadi update token secara regular

---

---

# REPORTS

## Get Laporan Mingguan

**Method:** GET
**URL:** `/api/reports/weekly`

### Deskripsi

Endpoint untuk mengambil laporan transaksi mingguan (minggu ini = Monday - Sunday).

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "period": "weekly",
    "period_start": "2024-05-13",
    "period_end": "2024-05-19",
    "summary": {
      "total_income": 2000000,
      "total_expense": 1500000,
      "net": 500000
    },
    "by_category": [
      {
        "category": "Belanja",
        "type": "expense",
        "total": 800000,
        "percentage": 53.33
      },
      {
        "category": "Makan & Minum",
        "type": "expense",
        "total": 700000,
        "percentage": 46.67
      }
    ],
    "by_day": {
      "2024-05-13": 100000,
      "2024-05-14": 300000,
      "2024-05-15": 250000
    },
    "transactions_count": 12,
    "average_daily_spending": 214285.71
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Report otomatis menggunakan minggu kalender (Monday - Sunday)
- Period start/end menunjukkan range tanggal report
- Include breakdown by category dan by day

---

## Get Laporan Bulanan

**Method:** GET
**URL:** `/api/reports/monthly`

### Deskripsi

Endpoint untuk mengambil laporan transaksi bulanan (bulan ini).

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "period": "monthly",
    "period_start": "2024-05-01",
    "period_end": "2024-05-31",
    "summary": {
      "total_income": 8000000,
      "total_expense": 6500000,
      "net": 1500000
    },
    "by_category": [
      {
        "category": "Belanja",
        "type": "expense",
        "total": 3000000,
        "percentage": 46.15
      },
      {
        "category": "Makan & Minum",
        "type": "expense",
        "total": 2500000,
        "percentage": 38.46
      },
      {
        "category": "Transport",
        "type": "expense",
        "total": 1000000,
        "percentage": 15.39
      }
    ],
    "by_week": {
      "week_1": 1200000,
      "week_2": 1800000,
      "week_3": 1500000,
      "week_4": 1500000
    },
    "transactions_count": 45,
    "average_daily_spending": 209677.42
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Report untuk bulan kalender (1 - akhir bulan)
- Include breakdown by category dan by week

---

## Get Laporan Custom Range Tanggal

**Method:** GET
**URL:** `/api/reports/custom`

### Deskripsi

Endpoint untuk mengambil laporan transaksi untuk range tanggal custom yang ditentukan user.

### Request

Request harus include token authentication di header dan parameter.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `start_date` - string (required, format: Y-m-d) - Tanggal mulai report
* `end_date` - string (required, format: Y-m-d, >= start_date) - Tanggal akhir report
* `format` - string (optional) - Format report (default: json)
* `include_charts` - boolean (optional) - Include chart data
* `categories` - array (optional) - Filter kategori (multiple)
* `type` - string (optional) - Filter tipe (income, expense, both)

**Contoh:** `/api/reports/custom?start_date=2024-04-01&end_date=2024-05-14&type=expense`

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "period": "custom",
    "period_start": "2024-04-01",
    "period_end": "2024-05-14",
    "summary": {
      "total_income": 15000000,
      "total_expense": 12500000,
      "net": 2500000
    },
    "by_category": [
      {
        "category": "Belanja",
        "type": "expense",
        "total": 6000000,
        "percentage": 48
      }
    ],
    "transactions_count": 89,
    "average_daily_spending": 280898.88
  }
}
```

### Response Error

* **400** - Validation error (date format invalid, end_date < start_date)
* **401** - Unauthorized
* **422** - Unprocessable Entity

### Catatan

- Tanggal harus format Y-m-d (contoh: 2024-05-14)
- End date harus >= start date
- Optional parameters untuk filter kategori dan tipe

---

## Export Laporan ke File

**Method:** POST
**URL:** `/api/reports/export`

### Deskripsi

Endpoint untuk export laporan transaksi ke file (PDF, Excel, atau CSV). Response adalah file binary yang bisa langsung di-download.

### Request

Request harus include token authentication di header dan data dalam body.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `format` - string (required) - Format export: `pdf`, `excel`, atau `csv`
* `start_date` - string (required, format: Y-m-d) - Tanggal mulai
* `end_date` - string (required, format: Y-m-d, >= start_date) - Tanggal akhir

**Contoh Request:**

```json
{
  "format": "pdf",
  "start_date": "2024-04-01",
  "end_date": "2024-05-14"
}
```

### Response Success (200)

Response adalah file binary PDF/Excel/CSV yang bisa di-download.

**Headers dalam Response:**

```
Content-Type: application/pdf (atau application/vnd.openxmlformats-officedocument.spreadsheetml.sheet untuk excel)
Content-Disposition: attachment; filename="laporan_keuangan_2024-04-01_to_2024-05-14.pdf"
```

### Response Error

* **400** - Validation error (format invalid, date format invalid)
* **401** - Unauthorized
* **422** - Unprocessable Entity

#### Contoh Error:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "format": ["Format harus pdf, excel, atau csv"],
    "start_date": ["Format tanggal tidak valid"],
    "end_date": ["Tanggal akhir harus setelah atau sama dengan tanggal mulai"]
  }
}
```

### Catatan

- Export diproses secara synchronous
- Format export:
  - PDF: Laporan dalam format PDF dengan formatting nice
  - Excel: Format .xlsx dengan multiple sheets (transactions, summary, charts)
  - CSV: Format CSV dengan comma delimiter
- File akan langsung di-download setelah response

---

---

# BADGES

## Get Badge yang Sudah Dimiliki User

**Method:** GET
**URL:** `/api/badges`

### Deskripsi

Endpoint untuk mengambil semua badge yang sudah dimiliki/earned oleh user.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "cc0e8400-e29b-41d4-a716-446655440000",
      "name": "First Transaction",
      "slug": "first_transaction",
      "description": "Membuat transaksi pertama",
      "icon": "🎉",
      "color": "#FFD700",
      "points": 10,
      "awarded_at": "2024-05-14 10:45:00"
    },
    {
      "id": "dd0e8400-e29b-41d4-a716-446655440001",
      "name": "Saving Master",
      "slug": "saving_master",
      "description": "Menghemat 1 juta dalam sebulan",
      "icon": "💰",
      "color": "#00D084",
      "points": 50,
      "awarded_at": "2024-05-10 14:20:00"
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Badge yang ditampilkan hanya yang sudah earned
- `awarded_at` menunjukkan kapan user earned badge tersebut
- Points berguna untuk gamification

---

## Get Semua Badge Tersedia + Status Earn User

**Method:** GET
**URL:** `/api/badges/all`

### Deskripsi

Endpoint untuk mengambil semua badge yang tersedia di sistem beserta informasi apakah user sudah earn atau belum.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "cc0e8400-e29b-41d4-a716-446655440000",
      "name": "First Transaction",
      "slug": "first_transaction",
      "description": "Membuat transaksi pertama",
      "icon": "🎉",
      "color": "#FFD700",
      "points": 10,
      "is_earned": true,
      "awarded_at": "2024-05-14 10:45:00"
    },
    {
      "id": "ee0e8400-e29b-41d4-a716-446655440002",
      "name": "Week Consistent",
      "slug": "week_consistent",
      "description": "Mencatat transaksi selama 7 hari berturut-turut",
      "icon": "📅",
      "color": "#FF6B6B",
      "points": 25,
      "is_earned": false,
      "awarded_at": null
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Badge dengan `is_earned: false` belum dimiliki user
- Berguna untuk motivate user untuk unlock semua badge
- `awarded_at` hanya ada jika `is_earned: true`

---

---

# INSIGHTS

## Get Semua Insight User

**Method:** GET
**URL:** `/api/insights`

### Deskripsi

Endpoint untuk mengambil semua insight yang sudah di-generate untuk user. Insight memberikan analytics dan rekomendasi keuangan.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `type` - string (optional) - Filter tipe insight (weekly, monthly, dll)

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "ff0e8400-e29b-41d4-a716-446655440000",
      "period_type": "weekly",
      "period_start": "2024-05-13",
      "period_end": "2024-05-19",
      "period_label": "Minggu ini",
      "summary": "Pengeluaran Anda minggu ini 20% lebih tinggi dari rata-rata",
      "insights": [
        {
          "title": "Belanja membengkak",
          "description": "Kategori Belanja mengalami kenaikan 30% dibanding minggu lalu"
        }
      ],
      "recommendations": [
        "Kurangi pengeluaran di kategori Belanja minggu depan",
        "Fokus ke budget category yang sudah ditetapkan"
      ],
      "is_read": false,
      "generated_at": "2024-05-20 08:00:00",
      "generated_at_human": "12 jam lalu",
      "insights_count": 1
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Insight di-generate otomatis berdasarkan periode (weekly/monthly)
- Include actionable recommendations untuk user
- `is_read` menunjukkan apakah user sudah baca insight ini

---

## Get Insight Terbaru yang Belum Dibaca

**Method:** GET
**URL:** `/api/insights/latest`

### Deskripsi

Endpoint untuk mengambil insight terbaru yang belum dibaca oleh user. Berguna untuk quick notification.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "id": "ff0e8400-e29b-41d4-a716-446655440000",
    "period_type": "weekly",
    "period_start": "2024-05-13",
    "period_end": "2024-05-19",
    "period_label": "Minggu ini",
    "summary": "Pengeluaran Anda minggu ini 20% lebih tinggi dari rata-rata",
    "insights": [
      {
        "title": "Belanja membengkak",
        "description": "Kategori Belanja mengalami kenaikan 30% dibanding minggu lalu"
      }
    ],
    "recommendations": [
      "Kurangi pengeluaran di kategori Belanja minggu depan",
      "Fokus ke budget category yang sudah ditetapkan"
    ],
    "is_read": false,
    "generated_at": "2024-05-20 08:00:00",
    "generated_at_human": "12 jam lalu",
    "insights_count": 1
  }
}
```

Jika tidak ada insight yang belum dibaca, response data bisa null.

### Response Error

* **401** - Unauthorized

### Catatan

- Endpoint return single insight object (bukan array)
- Return null jika tidak ada unread insight

---

## Tandai Insight Sebagai Sudah Dibaca

**Method:** POST
**URL:** `/api/insights/{id}/read`

### Deskripsi

Endpoint untuk mark insight sebagai sudah dibaca.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - integer - ID insight

### Response Success (200)

```json
{
  "success": true,
  "message": "Insight marked as read"
}
```

### Response Error

* **401** - Unauthorized
* **404** - Not Found (insight tidak ada)

### Catatan

- Update status `is_read` menjadi true di database

---

## Generate Insight Secara Manual

**Method:** POST
**URL:** `/api/insights/generate`

### Deskripsi

Endpoint untuk trigger generation insight secara manual. Process dilakukan secara asynchronous melalui job queue.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)
* `Content-Type: application/json`

**Body Parameters:**

* `period_type` - string (optional, default: weekly) - Tipe periode insight: `weekly`, `monthly`, dll

**Contoh Request:**

```json
{
  "period_type": "weekly"
}
```

### Response Success (200)

```json
{
  "success": true,
  "message": "Insight generation (weekly) has been queued.",
  "period_type": "weekly"
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Insight generation diproses secara async menggunakan job queue
- Response langsung kembali sambil proses berjalan di background
- User akan menerima notification ketika insight sudah selesai di-generate
- Biasanya memakan waktu beberapa detik sampai beberapa menit tergantung volume data

---

---

# NOTIFICATIONS

## Get Semua Notifikasi User

**Method:** GET
**URL:** `/api/notifications`

### Deskripsi

Endpoint untuk mengambil semua notifikasi user dengan pagination. Notifikasi diurutkan dari yang terbaru.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**Query Parameters:**

* `per_page` - integer (optional, default: 20) - Jumlah item per halaman
* `page` - integer (optional, default: 1) - Halaman yang diminta

**Contoh:** `/api/notifications?per_page=20&page=1`

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "budget_warning",
      "title": "Budget Warning - Belanja",
      "body": "Budget kategori Belanja sudah mencapai 85%",
      "data": {
        "category_id": "770e8400-e29b-41d4-a716-446655440000",
        "percentage": 85
      },
      "is_read": false,
      "read_at": null,
      "created_at": "2024-05-14 14:30:00"
    },
    {
      "id": 2,
      "type": "daily_reminder",
      "title": "Daily Spending Reminder",
      "body": "Jangan lupa catat spending Anda hari ini",
      "data": {},
      "is_read": true,
      "read_at": "2024-05-14 10:00:00",
      "created_at": "2024-05-14 08:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 35
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Pagination support dengan meta info lengkap
- Notifikasi diurutkan dari yang terbaru (desc)
- `read_at` hanya ada jika `is_read: true`

---

## Get Notifikasi yang Belum Dibaca

**Method:** GET
**URL:** `/api/notifications/unread`

### Deskripsi

Endpoint untuk mengambil semua notifikasi yang belum dibaca (is_read: false).

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "budget_warning",
      "title": "Budget Warning - Belanja",
      "body": "Budget kategori Belanja sudah mencapai 85%",
      "data": {
        "category_id": "770e8400-e29b-41d4-a716-446655440000",
        "percentage": 85
      },
      "is_read": false,
      "read_at": null,
      "created_at": "2024-05-14 14:30:00"
    }
  ]
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Endpoint return array dari unread notifications
- Berguna untuk badge notification count di UI

---

## Get Jumlah Notifikasi yang Belum Dibaca

**Method:** GET
**URL:** `/api/notifications/unread-count`

### Deskripsi

Endpoint untuk mengambil jumlah notifikasi yang belum dibaca. Berguna untuk badge counter di icon notification.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "data": {
    "count": 5
  }
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Endpoint return simple integer count
- Lightweight endpoint for UI badge counter

---

## Tandai Satu Notifikasi Sebagai Sudah Dibaca

**Method:** POST
**URL:** `/api/notifications/{id}/read`

### Deskripsi

Endpoint untuk mark notifikasi spesifik sebagai sudah dibaca.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

**URL Parameters:**

* `id` - integer - ID notifikasi

### Response Success (200)

```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

### Response Error

* **401** - Unauthorized
* **404** - Not Found

### Catatan

- Update `is_read` menjadi true dan set `read_at` ke current timestamp

---

## Tandai Semua Notifikasi Sebagai Sudah Dibaca

**Method:** POST
**URL:** `/api/notifications/read-all`

### Deskripsi

Endpoint untuk mark semua notifikasi user sebagai sudah dibaca.

### Request

Request harus include token authentication di header.

**Headers:**

* `Authorization: Bearer {token}` (required)

### Response Success (200)

```json
{
  "success": true,
  "message": "All notifications marked as read"
}
```

### Response Error

* **401** - Unauthorized

### Catatan

- Update semua notifikasi user yang belum dibaca (is_read: false) menjadi read
- Useful untuk "Mark all as read" button di notification center

---

---

## General API Information

### Error Response Format

Semua error response mengikuti format yang sama:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail 1", "Error detail 2"]
  }
}
```

### Authentication

Semua endpoint yang dilindungi memerlukan:

```
Header: Authorization: Bearer {token}
```

Token didapatkan dari response login atau register endpoint.

### Pagination

Endpoints dengan pagination mengembalikan:

```json
{
  "data": [],
  "links": {
    "first": "url",
    "last": "url",
    "prev": "url or null",
    "next": "url or null"
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### HTTP Status Codes

- **200** - OK (GET, PUT, DELETE berhasil)
- **201** - Created (POST berhasil membuat resource)
- **400** - Bad Request (validation error)
- **401** - Unauthorized (token invalid/missing)
- **403** - Forbidden (tidak punya akses/otorisasi)
- **404** - Not Found (resource tidak ditemukan)
- **422** - Unprocessable Entity (parsing/validation error)
- **500** - Internal Server Error (server error)

### Response Headers

```
Content-Type: application/json
```

### DateTime Format

Semua datetime menggunakan format:
- **Date:** Y-m-d (contoh: 2024-05-14)
- **DateTime:** Y-m-d H:i:s (contoh: 2024-05-14 14:30:00)
- **Month:** Y-m (contoh: 2024-05)

### Currency Format

- Disimpan dalam database sebagai value numeric
- `formatted_amount` dalam response sudah dalam format mata uang dengan simbol dan separator

---

## Best Practices

1. **Handle Pagination:** Selalu check `meta` dan `links` untuk navigasi data yang bukan di halaman pertama
2. **Error Handling:** Selalu check field `errors` untuk detail validation error
3. **Token Management:** Simpan token dengan aman, jangan include di URL
4. **Rate Limiting:** Implementasi rate limiting di client untuk menghindari throttling
5. **Caching:** Cache response dashboard dan list untuk performa
6. **WebSocket/Real-time:** Untuk notifikasi real-time, pertimbangkan menggunakan WebSocket atau polling
7. **Retry Logic:** Implementasi retry dengan exponential backoff untuk network errors

---

## Contoh Implementasi (JavaScript/Fetch)

### Login

```javascript
const response = await fetch('/api/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'password123'
  })
});

const data = await response.json();
localStorage.setItem('token', data.data.token);
```

### Request dengan Authentication

```javascript
const token = localStorage.getItem('token');
const response = await fetch('/api/transactions', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

const data = await response.json();
```

### Create Transaction

```javascript
const token = localStorage.getItem('token');
const response = await fetch('/api/transactions', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({
    amount: 200000,
    type: 'expense',
    category_id: '770e8400-e29b-41d4-a716-446655440000',
    note: 'Beli kebutuhan sekolah'
  })
});

const data = await response.json();
```

---

**API Documentation v1.0**  
Last Updated: May 14, 2024  
Maintained by: Backend Team

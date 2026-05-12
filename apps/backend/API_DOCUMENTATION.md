# DUITKU - Authentication System Documentation

## 📋 Ringkasan Sistem

Sistem autentikasi DUITKU menggunakan **Laravel Sanctum** dengan clean architecture yang terpisah antara:
- **Controller**: Request/Response handling saja
- **Service**: Business logic (register, login, logout)
- **Model**: Data access

## 🏗️ Struktur Project

```
app/
├── Services/
│   └── AuthService.php                 # Business logic untuk auth
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php          # Handle HTTP requests
│   ├── Requests/
│   │   ├── LoginRequest.php            # Login validation
│   │   └── RegisterRequest.php         # Register validation
│   └── Resources/
│       └── UserResource.php            # API response formatting
├── Models/
│   └── User.php                        # User model dengan Sanctum
└── Exceptions/
    └── (Folder untuk custom exceptions di masa depan)

config/
├── sanctum.php                         # Sanctum configuration

database/
└── migrations/
    ├── 0001_01_01_000000_create_users_table
    └── 2026_05_12_153817_create_personal_access_tokens_table

routes/
└── api.php                             # API routes
```

## 📊 Database Schema - Users Table

```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    currency VARCHAR(255) DEFAULT 'IDR',          -- Untuk multi-currency support
    initial_balance DECIMAL(15,2) DEFAULT 0,      -- Starting balance
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX users_email_index ON users(email);
CREATE INDEX users_created_at_index ON users(created_at);
```

## 🔐 API Endpoints

### 1. Register - `POST /api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "currency": "IDR",
    "initial_balance": 1000000
}
```

**Rules:**
- `name`: Required, string, max 255
- `email`: Required, unique, valid email
- `password`: Required, min 8 characters, must match confirmation
- `currency`: Optional (default: IDR)
- `initial_balance`: Optional, numeric, min 0

**Success Response (201):**
```json
{
    "message": "Registrasi berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "currency": "IDR",
            "initial_balance": "1000000.00",
            "email_verified_at": null,
            "created_at": "2024-05-12T10:00:00.000000Z",
            "updated_at": "2024-05-12T10:00:00.000000Z"
        },
        "token": "1|abcdef..."
    }
}
```

### 2. Login - `POST /api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Rules:**
- `email`: Required, valid email
- `password`: Required

**Success Response (200):**
```json
{
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "currency": "IDR",
            "initial_balance": "1000000.00",
            "email_verified_at": null,
            "created_at": "2024-05-12T10:00:00.000000Z",
            "updated_at": "2024-05-12T10:00:00.000000Z"
        },
        "token": "1|abcdef..."
    }
}
```

**Error Response (401):**
```json
{
    "message": "Login gagal",
    "errors": {
        "email": ["Kredensial tidak sesuai"]
    }
}
```

### 3. Get Current User - `GET /api/me`

**Headers Required:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "message": "Data user berhasil diambil",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "currency": "IDR",
        "initial_balance": "1000000.00",
        "email_verified_at": null,
        "created_at": "2024-05-12T10:00:00.000000Z",
        "updated_at": "2024-05-12T10:00:00.000000Z"
    }
}
```

### 4. Logout - `POST /api/logout`

**Headers Required:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "message": "Logout berhasil"
}
```

## 🔗 Authentication dengan Sanctum Token

Setiap request ke endpoint protected harus menyertakan header:

```
Authorization: Bearer {token}
```

**Contoh Curl:**
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer 1|abcdef..." \
  -H "Accept: application/json"
```

## 🛡️ Keamanan

1. **Password Hashing**: Menggunakan bcrypt (BCRYPT_ROUNDS=12)
2. **Sanctum Tokens**: Token api-token dengan enkripsi database
3. **Email Validation**: Email harus unique dan valid format
4. **Password Validation**: Minimal 8 karakter, harus dikonfirmasi saat register
5. **CORS Configuration**: Dikonfigurasi di `.env` untuk frontend

## 🚀 Setup Instruksi

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database di .env
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_duitku
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Start Server
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## 🔄 Architecture Pattern

### Clean Architecture Layers

**1. Controller Layer** (`AuthController`)
- Handle HTTP request
- Call service
- Return formatted response
- Validation via FormRequest

**2. Service Layer** (`AuthService`)
- Business logic
- User registration
- Password validation
- Token generation
- User retrieval

**3. Model Layer** (`User`)
- Database interaction
- Relationships
- Casts & attributes
- Token management via Sanctum

### Flow Diagram

```
HTTP Request
    ↓
FormRequest (Validation)
    ↓
AuthController (Request/Response)
    ↓
AuthService (Business Logic)
    ↓
User Model (Database)
    ↓
Response → UserResource → JSON
```

## 📝 Contoh Testing dengan Postman

### 1. Register
```
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### 2. Login
```
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

### 3. Get Current User
```
GET http://localhost:8000/api/me
Authorization: Bearer {token_dari_login}
```

### 4. Logout
```
POST http://localhost:8000/api/logout
Authorization: Bearer {token}
```

## 🎯 Next Steps untuk Fitur Lanjutan

1. **User Verification Email**
   - Implement email verification
   - Add verification middleware

2. **Refresh Tokens**
   - Implement token refresh logic
   - Add token expiration

3. **Password Reset**
   - Implement forgot password
   - Email with reset link

4. **Two-Factor Authentication**
   - Add 2FA support
   - OTP generation

5. **Role-Based Access Control (RBAC)**
   - User roles & permissions
   - Authorization middleware

6. **Audit Logging**
   - Track login/logout
   - Log sensitive changes

## 📚 Key Files

| File | Purpose |
|------|---------|
| `app/Services/AuthService.php` | Business logic for authentication |
| `app/Http/Controllers/AuthController.php` | API endpoints handler |
| `app/Http/Requests/LoginRequest.php` | Login validation rules |
| `app/Http/Requests/RegisterRequest.php` | Register validation rules |
| `app/Http/Resources/UserResource.php` | API response transformer |
| `app/Models/User.php` | User database model |
| `routes/api.php` | API route definitions |
| `config/sanctum.php` | Sanctum configuration |


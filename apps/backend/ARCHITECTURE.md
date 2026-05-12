# DUITKU - Clean Architecture Documentation

## 🏗️ Daftar Isi
1. [Overview](#overview)
2. [Arsitektur Layers](#arsitektur-layers)
3. [Separation of Concerns](#separation-of-concerns)
4. [Scalability Design](#scalability-design)
5. [Struktur Folder](#struktur-folder)
6. [Best Practices](#best-practices)

---

## Overview

DUITKU menggunakan **Clean Architecture** dengan pemisahan strict antara berbagai layers. Filosofi ini memungkinkan:

- ✅ **Easy Testing**: Business logic terpisah dari framework
- ✅ **Easy Scaling**: Mudah untuk menambah fitur baru
- ✅ **Easy Maintenance**: Kode terorganisir dengan jelas
- ✅ **Easy Refactoring**: Framework bisa diganti tanpa mengubah business logic

---

## Arsitektur Layers

### 1. **HTTP Layer** (Routes & Middleware)

**File**: `routes/api.php`

```php
// Routes yang mendefinisikan endpoint publik dan protected
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});
```

**Tanggung Jawab**:
- Mendefinisikan routing
- Menentukan middleware
- Grouping endpoints

---

### 2. **Controller Layer** (Request/Response Handler)

**File**: `app/Http/Controllers/AuthController.php`

```php
public function register(RegisterRequest $request): JsonResponse
{
    // 1. Validate (via RegisterRequest)
    // 2. Call Service
    // 3. Return Response
    $user = $this->authService->register($request->validated());
    return response()->json([...]);
}
```

**Tanggung Jawab**:
- ✅ Handle HTTP request
- ✅ Validate input (via FormRequest)
- ✅ Call service layer
- ✅ Format dan return response
- ❌ NO business logic
- ❌ NO database queries directly

**Key Point**: Controller HANYA untuk orchestration, bukan logic!

---

### 3. **Request/Response Objects**

#### FormRequest (Validation)
**Files**: 
- `app/Http/Requests/RegisterRequest.php`
- `app/Http/Requests/LoginRequest.php`

```php
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }
}
```

**Benefit**: 
- Centralized validation
- Reusable across controllers
- Automatic 422 error handling

#### Resources (Output Formatting)
**File**: `app/Http/Resources/UserResource.php`

```php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Hide sensitive fields like password
        ];
    }
}
```

**Benefit**:
- Consistent API responses
- Hide sensitive fields
- Easy to refactor output format

---

### 4. **Service Layer** (Business Logic)

**File**: `app/Services/AuthService.php`

```php
class AuthService
{
    public function register(array $data): User
    {
        // Business Logic
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        return $user;
    }

    public function login(string $email, string $password): array
    {
        // Validate credentials
        // Generate token
        // Return user & token
    }
}
```

**Tanggung Jawab**:
- ✅ Core business logic
- ✅ Password hashing
- ✅ Token generation
- ✅ User authentication
- ✅ Exception throwing

**Why Separate?**
- Business logic bisa ditest tanpa HTTP
- Bisa digunakan dari berbagai tempat (CLI, Queue, webhook)
- Framework-agnostic dalam hal logika

---

### 5. **Model Layer** (Data Access)

**File**: `app/Models/User.php`

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', ...];
    
    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}
```

**Tanggung Jawab**:
- Database queries
- Relationships
- Casts & mutators
- Scopes (optional)

**Note**: Direct access ke Model dari Service adalah OK untuk project ini. Repository pattern optional di masa depan.

---

### 6. **Exception Layer**

**Folder**: `app/Exceptions/`

Untuk fitur advanced nanti:
```php
class AuthenticationException extends \Exception {}
class PasswordResetException extends \Exception {}
```

---

## Separation of Concerns

### ❌ ❌ ❌ BAD Pattern (Tightly Coupled)

```php
// DON'T DO THIS!
class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $request->validate([...]);
        
        // Direct DB query - NO!
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);
        
        // Direct token creation - NO!
        $token = $user->createToken('api-token')->plainTextToken;
        
        // Return - OK
        return response()->json([...]);
    }
}
```

**Problems**:
- Hard to test
- Hard to reuse logic
- Hard to change
- Mixed concerns

---

### ✅ ✅ ✅ GOOD Pattern (Clean Architecture)

```php
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}
    
    public function register(RegisterRequest $request): JsonResponse
    {
        // Call service
        $user = $this->authService->register($request->validated());
        
        // Format & return
        return response()->json([
            'data' => new UserResource($user),
            'token' => $token,
        ], 201);
    }
}

// Service berisi logic
class AuthService
{
    public function register(array $data): User
    {
        // Validasi, create user, semua logic ada di sini
        return User::create([...]);
    }
}
```

**Benefits**:
- Easy to test (mock AuthService)
- Easy to reuse (call AuthService dari CLI)
- Single Responsibility
- Dependency Injection

---

## Scalability Design

### 1. **Untuk Menambah Fitur**

**Contoh**: Tambah fitur "Email Verification"

Struktur tetap sama, hanya tambah di layer yang sesuai:

```
1. UpdateRegisterRequest (add email_verified field)
2. UpdateAuthService (add verification logic)
3. AuthController (handle email verification)
4. Routes (add verification endpoint)
5. Migrations (add email_verified_at handling)
6. Tests (test verification flow)
```

**No breaking changes** di layer lain!

### 2. **Untuk Kompleksitas Data**

Jika ada logic yang complex untuk User:

Buatkan sesuai kebutuhan:
- `UserService` untuk user-specific logic
- `EmailService` untuk email handling
- `TokenService` untuk token management

Masing-masing service punya single responsibility.

### 3. **Untuk Performance**

Jika query lambat:

```php
// Option 1: Add query optimization di Model
class User extends Model
{
    public function scopeWithLatestToken($query)
    {
        return $query->with('tokens:id,user_id,created_at');
    }
}

// Option 2: Create dedicated repository (future)
class UserRepository
{
    public function getWithCache($id)
    {
        return Cache::remember("user.{$id}", 3600, 
            fn() => User::find($id)
        );
    }
}
```

---

## Struktur Folder

```
app/
├── Exceptions/                          # Custom exceptions (future)
│   └── AuthenticationException.php
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php           # ← Request/Response only
│   ├── Middleware/                      # (Akan ditambah nanti)
│   ├── Requests/
│   │   ├── LoginRequest.php             # ← Validation
│   │   └── RegisterRequest.php
│   └── Resources/
│       └── UserResource.php             # ← Response formatting
├── Models/
│   └── User.php                         # ← Data access
├── Services/
│   └── AuthService.php                  # ← Business logic
└── Providers/                           # (AppServiceProvider sudah ada)
    └── AppServiceProvider.php

config/
└── sanctum.php                          # Sanctum config

database/
├── migrations/
│   └── 0001_01_01_000000_create_users_table.php
└── (factories, seeders di masa depan)

routes/
└── api.php                              # ← API routing

tests/
├── Feature/
│   ├── AuthTest.php
│   └── RegistrationTest.php
└── Unit/
    └── Services/
        └── AuthServiceTest.php
```

---

## Best Practices

### 1. **Dependency Injection**

```php
// ✅ Good
class AuthController
{
    public function __construct(private AuthService $authService) {}
}

// ❌ Bad
class AuthController
{
    public function register()
    {
        $service = new AuthService(); // ← Don't do this
    }
}
```

### 2. **Type Hinting**

```php
// ✅ Good - clear types
public function register(RegisterRequest $request): JsonResponse
public function login(string $email, string $password): array

// ❌ Bad - vague types
public function register($data)
public function login($email, $password)
```

### 3. **Return Types**

```php
// ✅ Good - explicit
public function register(array $data): User
public function logout(User $user): bool

// ❌ Bad - implicit
public function register($data)
public function logout($user)
```

### 4. **Validation vs Exception Handling**

```php
// ✅ Request-level validation untuk user input
class RegisterRequest extends FormRequest
{
    public function rules() { return [...]; }
}

// ✅ Service-level exception untuk business logic
if (!$user || !Hash::check($password, $user->password)) {
    throw ValidationException::withMessages([...]);
}

// ❌ Don't mix di Controller
```

### 5. **Single Responsibility**

```php
// ✅ Each class has ONE responsibility
- AuthController: Handle HTTP request
- AuthService: Manage auth logic
- User Model: Manage database
- RegisterRequest: Validate input

// ❌ Don't do like this
- AuthController (handle request + validate + query + format)
```

### 6. **Testability**

```php
// ✅ Easy to test
public function testUserCanRegister()
{
    $service = new AuthService();
    $user = $service->register(['name' => 'John', 'email' => '...']);
    $this->assertNotNull($user->id);
}

// ❌ Hard to test
// Jika logic ada di Controller, sulit untuk test tanpa HTTP
```

---

## Next Steps untuk Scalability

Ketika fitur berkembang, struktur yang bisa ditambahkan:

### Level 2 - RBAC & Permissions
```
app/
├── Services/
│   ├── AuthService.php
│   ├── PermissionService.php         ← Tambahan
│   └── RoleService.php               ← Tambahan
├── Models/
│   ├── User.php
│   ├── Role.php                      ← Tambahan
│   └── Permission.php                ← Tambahan
```

### Level 3 - Domain-Driven Design
```
app/
├── Domain/
│   ├── Auth/                         ← Auth domain
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Events/
│   ├── Finance/                      ← Finance domain
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Events/
```

### Level 4 - CQRS Pattern
```
app/
├── Commands/                         ← Write operations
│   └── RegisterUserCommand.php
├── Queries/                          ← Read operations
│   └── GetUserByIdQuery.php
├── QueryHandlers/
└── CommandHandlers/
```

---

## Performance Optimization

### Caching Strategy
```php
// Service dengan caching
class AuthService
{
    public function getCurrentUser(User $user): User
    {
        return Cache::remember("user.{$user->id}", 3600, 
            fn() => $user->load('roles', 'permissions')
        );
    }
}
```

### Database Optimization
```php
// Use eager loading
User::with('roles', 'permissions', 'tokens')->find($id);

// Avoid N+1 queries
$users = User::with('tokens')->get();
```

### API Response Caching
```php
// Cache GET /api/me responses
Route::get('/me', [AuthController::class, 'me'])
    ->middleware('throttle:60,1')
    ->name('profile');
```

---

## Kesimpulan

Clean Architecture pada DUITKU memastikan bahwa:

1. **Scalable** - Mudah untuk menambah fitur baru tanpa mengubah yang lama
2. **Maintainable** - Kode terorganisir dan mudah dipahami
3. **Testable** - Business logic bisa ditest tanpa HTTP
4. **Reusable** - Logic bisa digunakan dari berbagai place
5. **Separate of Concerns** - Setiap layer punya tanggung jawab yang jelas

Dengan struktur ini, DUITKU siap untuk berkembang ke fitur AI, insight, dan gamification di masa depan! 🚀


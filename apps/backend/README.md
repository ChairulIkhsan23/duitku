# DUITKU Backend API

> Personal Finance Management Application - REST API Backend built with Laravel 13 & PostgreSQL

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Composer
- PostgreSQL 12+
- Node.js 18+ (for frontend assets)

### Installation

1. **Clone repository**
```bash
git clone <repo-url>
cd apps/backend
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database di `.env`**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_duitku
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Start development server**
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

---

## 📚 Documentation

### API Documentation
- **Main Guide**: [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- **Endpoints**: 
  - `POST /api/register` - Register new user
  - `POST /api/login` - Login user
  - `GET /api/me` - Get current user profile
  - `POST /api/logout` - Logout user

### Architecture
- **Design Pattern**: [ARCHITECTURE.md](./ARCHITECTURE.md)
- **Clean Architecture**: Service + Controller + Model separation
- **Scalability**: Ready for AI features, insights, and gamification

### Testing
- **Postman Collection**: [DUITKU-Auth-API.postman_collection.json](../DUITKU-Auth-API.postman_collection.json)

---

## 🏗️ Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 13 |
| Database | PostgreSQL |
| Authentication | Laravel Sanctum |
| API Style | REST |
| Architecture | Clean Architecture |

---

## 📁 Project Structure

```
app/
├── Services/              # Business Logic Layer
├── Http/
│   ├── Controllers/       # HTTP Request Handlers
│   ├── Requests/          # Form Validation
│   └── Resources/         # API Response Formatters
├── Models/                # Database Models
└── Exceptions/            # Custom Exceptions

config/
└── sanctum.php            # Authentication Config

database/
├── migrations/            # Schema Migrations
├── factories/             # Model Factories
└── seeders/               # Database Seeders

routes/
└── api.php                # API Routes

tests/
├── Feature/               # Feature Tests
└── Unit/                  # Unit Tests
```

---

## 🔐 Authentication

DUITKU menggunakan **Laravel Sanctum** untuk API authentication:

### Token-Based Auth
```bash
# Get token via login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# Use token for protected routes
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Protected Routes
Routes di `/api` yang membutuhkan authentication:
- `GET /api/me` - Get current user
- `POST /api/logout` - Logout

---

## 🛠️ Development

### Available Commands

```bash
# Development server dengan auto-reload
php artisan serve

# Watch assets changes
npm run dev

# Run migrations
php artisan migrate

# Fresh migration (⚠️ clears data)
php artisan migrate:fresh

# Run tests
php artisan test

# Check code style
composer pint

# Format code
composer pint --fix

# Run tinker REPL
php artisan tinker

# List all routes
php artisan route:list
```

### Development Workflow

```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Asset watcher
npm run dev

# Terminal 3: Queue listener (if using jobs)
php artisan queue:listen

# Terminal 4: Laravel Pail (logs)
php artisan pint --timeout=0
```

---

## 🧪 Testing

### Run Tests
```bash
# All tests
php artisan test

# Spesifik test class
php artisan test tests/Feature/AuthTest.php

# With coverage
php artisan test --coverage
```

### Test Structure
```
tests/
├── Feature/
│   ├── AuthTest.php           # Authentication flow tests
│   └── RegistrationTest.php   # Registration specific tests
└── Unit/
    └── Services/
        └── AuthServiceTest.php # Unit tests untuk service
```

---

## 📊 Database

### Schema Management

```bash
# Create new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all & re-run
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

### Users Table
```sql
- id: bigint (PK)
- name: string
- email: string (unique)
- password: string (hashed)
- currency: string (default: IDR)
- initial_balance: decimal
- email_verified_at: timestamp
- timestamps (created_at, updated_at)
```

---

## 🔒 Security

### Best Practices Implemented

✅ **Password Security**
- Bcrypt hashing (BCRYPT_ROUNDS=12)
- Minimum 8 characters
- Password confirmation required on register

✅ **API Security**
- Sanctum token authentication
- CORS configuration
- CSRF protection available

✅ **Data Validation**
- Server-side validation dengan FormRequest
- Email unique constraint
- Type casting & database constraints

✅ **Exception Handling**
- Proper HTTP status codes
- Consistent error responses
- No sensitive data in errors

---

## 🚀 Deployment

### Production Checklist

```bash
# Pre-deployment
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Environment
APP_ENV=production
APP_DEBUG=false
```

### Environment Variables (.env)
```env
APP_NAME=DUITKU
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxx
APP_URL=https://api.duitku.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_DATABASE=db_duitku
DB_USERNAME=postgres
DB_PASSWORD=your-secure-password

SANCTUM_STATEFUL_DOMAINS=duitku.com,www.duitku.com
```

---

## 📝 API Response Format

### Success Response (201 Created)
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
            "created_at": "2024-05-12T10:00:00.000000Z"
        },
        "token": "1|abcdefghijk..."
    }
}
```

### Error Response (401 Unauthorized)
```json
{
    "message": "Login gagal",
    "errors": {
        "email": ["Kredensial tidak sesuai"]
    }
}
```

---

## 🔗 Related Documentation

- [API Reference](./API_DOCUMENTATION.md) - Complete endpoint documentation
- [Architecture Guide](./ARCHITECTURE.md) - System design & clean architecture
- [Laravel Documentation](https://laravel.com/docs) - Official Laravel docs
- [Sanctum Docs](https://laravel.com/docs/sanctum) - Authentication docs
- [PostgreSQL Docs](https://www.postgresql.org/docs) - Database docs

---

## 🤝 Contributing

1. Create feature branch: `git checkout -b feature/feature-name`
2. Commit changes: `git commit -am 'Add feature'`
3. Push to branch: `git push origin feature/feature-name`
4. Create Pull Request

### Code Standards
- Follow PSR-12 coding standard
- Use type hints for all parameters
- Add docstring untuk public methods
- Write tests untuk new features

---

## 📧 Support

For questions or issues:
1. Check [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
2. Check [ARCHITECTURE.md](./ARCHITECTURE.md)
3. Open an issue on GitHub

---

## 📄 License

This project is licensed under the MIT License - see [LICENSE](../../LICENSE) file for details.

---

## 🎯 Next Features

Roadmap untuk masa depan:

```
Phase 2 - Core Features
├── Transaction management (income/expense)
├── Budget management
├── Category system
└── Dashboard analytics

Phase 3 - Advanced Features
├── AI-powered insights
├── Predictive analytics
├── Gamification
└── Mobile app sync

Phase 4 - Enterprise
├── Multi-user family accounts
├── Expense splitting
├── Invoice generation
└── Export reports
```

---

**Made with ❤️ for personal finance management**

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

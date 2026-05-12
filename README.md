# Duitku - Personal Finance Management App

<div align="center">

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Flutter](https://img.shields.io/badge/Flutter-3.11+-blue.svg?logo=flutter)
![Laravel](https://img.shields.io/badge/Laravel-13.7+-red.svg?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-purple.svg?logo=php)

**Duitku** adalah aplikasi manajemen keuangan pribadi yang modern, dengan backend API yang robust dan aplikasi mobile cross-platform yang user-friendly.

[Documentation](#dokumentasi) • [Getting Started](#getting-started) • [Architecture](#arsitektur) • [Contributing](#kontribusi)

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Struktur Proyek](#struktur-proyek)
- [Getting Started](#getting-started)
- [Arsitektur](#arsitektur)
- [Development](#development)
- [Testing](#testing)
- [Dokumentasi](#dokumentasi)
- [Kontribusi](#kontribusi)
- [License](#license)

---

## 🎯 Overview

Duitku adalah solusi terpadu untuk mengelola keuangan pribadi Anda. Aplikasi ini menyediakan:

- **Dashboard finansial** yang komprehensif
- **Tracking pengeluaran dan pemasukan** secara real-time
- **Laporan dan analisis** keuangan mendalam
- **Kategori dan budget** yang dapat dikustomisasi
- **Sinkronisasi multi-device** seamless

Dibangun menggunakan teknologi terkini dengan fokus pada performa, keamanan, dan user experience.

---

## ✨ Fitur Utama

- ✅ **Multi-Platform Support**
  - iOS & Android (Native performance)
  - Web Browser
  - Windows, macOS, Linux Desktop

- ✅ **User Management**
  - Authentication & Authorization
  - Secure password handling
  - Profile management

- ✅ **Financial Tracking**
  - Income & Expense tracking
  - Budget management
  - Category organization

- ✅ **Real-Time Sync**
  - Queue system untuk background jobs
  - Real-time data synchronization
  - Offline support (planned)

- ✅ **Developer Friendly**
  - Well-documented API
  - Shared DTOs dan Contracts
  - Hot reload development

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 13.7
- **Language:** PHP 8.3
- **Database:** (Configured in environment)
- **Queue:** Laravel Queue System
- **Testing:** PHPUnit 12.5

### Mobile & Frontend
- **Framework:** Flutter
- **Language:** Dart 3.11+
- **Platforms:** iOS, Android, Web, Windows, macOS, Linux
- **UI:** Material Design 3

### Styling & Build
- **CSS:** Tailwind CSS 4.0
- **Build Tool:** Vite 8.0
- **Package Manager:** NPM (Frontend), Composer (Backend)

### Development Tools
- **Version Control:** Git
- **Task Runner:** Composer Scripts, Flutter Commands
- **Linting:** PintPHP, Flutter Lints
- **Development Server:** Vite Dev Server, Laravel Artisan

---

## 📁 Struktur Proyek

```
duitku/
├── apps/
│   ├── backend/                 # Laravel API Server
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   └── Controllers/ # API Controllers
│   │   │   ├── Models/          # Eloquent Models
│   │   │   └── Providers/       # Framework Providers
│   │   ├── config/              # Configuration Files
│   │   ├── database/
│   │   │   ├── migrations/      # Database Migrations
│   │   │   ├── factories/       # Model Factories
│   │   │   └── seeders/         # Database Seeders
│   │   ├── routes/              # API Routes
│   │   │   ├── web.php          # Web Routes
│   │   │   └── console.php      # Console Commands
│   │   ├── resources/           # Views & Assets
│   │   ├── tests/               # Test Suites
│   │   ├── storage/             # App Storage
│   │   ├── composer.json        # Backend Dependencies
│   │   └── vite.config.js       # Vite Configuration
│   │
│   └── mobile/                  # Flutter Mobile App
│       ├── lib/
│       │   └── main.dart        # App Entry Point
│       ├── android/             # Android Native Code
│       ├── ios/                 # iOS Native Code
│       ├── web/                 # Web Assets
│       ├── windows/             # Windows Native Code
│       ├── macos/               # macOS Native Code
│       ├── linux/               # Linux Native Code
│       ├── test/                # Flutter Tests
│       ├── pubspec.yaml         # Flutter Dependencies
│       └── analysis_options.yaml # Dart Analysis Config
│
├── shared/                      # Shared Code
│   ├── contracts/               # Service Contracts
│   ├── dto/                     # Data Transfer Objects
│   ├── enums/                   # Enumerations
│   └── utils/                   # Utility Functions
│
├── docs/                        # Documentation
│   ├── api.md                   # API Documentation
│   ├── architecture.md          # Architecture Guide
│   └── database.md              # Database Schema
│
├── scripts/                     # Automation Scripts
│   ├── setup.sh                 # Initial Setup
│   ├── deploy.sh                # Deployment
│   └── seed.sh                  # Database Seeding
│
└── README.md                    # This File
```

---

## 🚀 Getting Started

### Prerequisites

Sebelum memulai, pastikan Anda telah menginstal:

- **PHP 8.3+** ([Download](https://www.php.net/downloads))
- **Composer** ([Download](https://getcomposer.org/download/))
- **Node.js 18+** & **npm** ([Download](https://nodejs.org/))
- **Flutter 3.11+** ([Download](https://flutter.dev/docs/get-started/install))
- **Dart SDK** (Included with Flutter)

### Installation

#### 1. Clone Repository

```bash
git clone https://github.com/yourusername/duitku.git
cd duitku
```

#### 2. Automatic Setup

Jalankan script setup otomatis untuk mengkonfigurasi backend:

```bash
bash scripts/setup.sh
```

Script ini akan:
- Install Composer dependencies
- Generate `.env` file
- Setup application key
- Run database migrations
- Install npm dependencies
- Build frontend assets

#### 3. Manual Backend Setup (Jika diperlukan)

```bash
cd apps/backend

# Install dependencies
composer install

# Setup environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Install frontend dependencies
npm install

# Build frontend assets
npm run build
```

#### 4. Manual Mobile Setup (Jika diperlukan)

```bash
cd apps/mobile

# Install Flutter dependencies
flutter pub get

# Get available devices
flutter devices

# Run on device/emulator
flutter run
```

---

## 🏗️ Arsitektur

### Arsitektur Keseluruhan

```
┌─────────────────────────────────────────────────────┐
│         Mobile & Web Frontend Layer                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  Flutter App (iOS, Android, Web, Desktop)     │  │
│  └───────────────────────────────────────────────┘  │
└────────────┬────────────────────────────────────────┘
             │ HTTP/REST API
┌────────────┴────────────────────────────────────────┐
│         API Server Layer (Laravel)                  │
│  ┌───────────────────────────────────────────────┐  │
│  │  Controllers  → Routes → Models → Database    │  │
│  └───────────────────────────────────────────────┘  │
└────────────┬────────────────────────────────────────┘
             │ Database Queries
┌────────────┴────────────────────────────────────────┐
│         Data Persistence Layer                      │
│  ┌───────────────────────────────────────────────┐  │
│  │  Database + Storage + Queue System            │  │
│  └───────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

### Backend Architecture

**MVC Pattern:**
- **Models** - Eloquent ORM models di `app/Models/`
- **Views** - Blade templates di `resources/views/`
- **Controllers** - Request handlers di `app/Http/Controllers/`

**API Organization:**
- Routes terstruktur di `routes/` folder
- Shared DTOs di `shared/dto/` untuk type safety
- Contracts di `shared/contracts/` untuk abstraksi

### Mobile Architecture

**Layered Architecture:**
- **Presentation Layer** - Flutter Widgets
- **Business Logic Layer** - State management & repositories  
- **Data Layer** - API clients & local storage

---

## 💻 Development

### Running Development Server

#### Backend Development

```bash
cd apps/backend

# Run development server dengan live reload, queue, dan logging
npm run dev
```

Ini akan menjalankan:
- Laravel Artisan Server (Port 8000)
- Queue listener untuk background jobs
- Application logs streaming
- Vite dev server untuk frontend assets

#### Mobile Development

```bash
cd apps/mobile

# Development mode dengan hot reload
flutter run

# Untuk target platform spesifik
flutter run -d chrome          # Web
flutter run -d "emulator-5554" # Android
flutter run -d "iPhone 14"     # iOS
```

### Code Organization

**Backend:**
- Follow Laravel conventions
- Use models untuk database abstraction
- Implement services untuk business logic
- Validate input dengan Form Requests

**Mobile:**
- Use widgets untuk UI components
- Implement providers untuk state management
- Organize screens dalam folder terstruktur
- Follow Material Design 3 guidelines

---

## 🧪 Testing

### Backend Testing

```bash
cd apps/backend

# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage

# Run only unit tests
php artisan test --filter Unit

# Run only feature tests
php artisan test --filter Feature
```

**Test Structure:**
- `tests/Unit/` - Unit tests untuk logic terpisah
- `tests/Feature/` - Feature tests untuk workflows

### Mobile Testing

```bash
cd apps/mobile

# Run widget tests
flutter test

# Run dengan code coverage
flutter test --coverage

# Run test untuk flavor/platform spesifik
flutter test -d chrome # Web testing
```

---

## 📚 Dokumentasi

Dokumentasi proyek tersedia di folder `/docs/`:

- **[api.md](docs/api.md)** - API Endpoints & Usage
- **[architecture.md](docs/architecture.md)** - Architecture deep-dive
- **[database.md](docs/database.md)** - Database schema & relationships

### Generating API Documentation

```bash
cd apps/backend

# Generate API docs menggunakan Laravel
php artisan make:docs
```

---

## 🤝 Kontribusi

Kami menyambut kontribusi dari komunitas! Silakan ikuti langkah-langkah berikut:

### Branch Convention

```
feature/description          # Fitur baru
bugfix/description           # Bug fixes
refactor/description         # Code refactoring
docs/description             # Documentation updates
test/description             # Test improvements
```

### Commit Message Convention

```
feat: Add transaction filtering
fix: Correct user authentication
docs: Update API documentation
test: Add unit tests for models
refactor: Simplify validation logic
```

### Pull Request Process

1. Fork repository
2. Buat branch dari `main`
3. Commit changes dengan descriptive messages
4. Push ke fork Anda
5. Buat Pull Request dengan deskripsi detail

### Code Standards

**Backend (PHP/Laravel):**
- PintPHP untuk code formatting
- PSR-12 coding standards
- PHPDoc untuk documentation

**Mobile (Dart/Flutter):**
- Flutter Lints untuk static analysis
- Effective Dart guidelines
- Documentation comments

### Running Code Quality Tools

```bash
# Backend
cd apps/backend
php artisan pint              # Format code
php artisan pint --test       # Check formatting

# Mobile
cd apps/mobile
dart analyze                  # Static analysis
dart format . --set-exit-if-changed
```

---

## 📝 License

Proyek ini dilisensikan di bawah **MIT License** - lihat file [LICENSE](LICENSE) untuk detail.

---

## 📞 Support & Contact

Untuk pertanyaan, issues, atau feedback:

- **GitHub Issues:** [Report bugs](../../issues)
- **Discussions:** [Start a discussion](../../discussions)
- **Email:** [your-email@example.com]

---

## 🎓 Learning Resources

### Documentation Resmi
- [Laravel Documentation](https://laravel.com/docs)
- [Flutter Documentation](https://flutter.dev/docs)
- [Dart Documentation](https://dart.dev/guides)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### Video Tutorials
- [Laravel Crash Course](https://youtu.be/)
- [Flutter Complete Course](https://youtu.be/)

---

## 🗺️ Roadmap

- [x] Project structure setup
- [x] Backend skeleton dengan Laravel
- [x] Mobile skeleton dengan Flutter
- [ ] Authentication & Authorization
- [ ] User dashboard
- [ ] Transaction management
- [ ] Budget tracking
- [ ] Reports & Analytics
- [ ] Mobile notifications
- [ ] Offline support
- [ ] Data export (PDF, CSV)

---

<div align="center">

**Made with ❤️ for better financial management**

[⬆ Back to top](#duitku---personal-finance-management-app)

</div>

# Dokumentasi API Duitku

## Base URL

```text
http://localhost:8000/api
```

## Authentication

Semua endpoint selain `/health`, `/register`, dan `/login` membutuhkan Bearer Token.

```http
Authorization: Bearer {token}
```

## Response Shape

Backend menggunakan beberapa bentuk response:

```json
{
  "success": true,
  "message": "Optional message",
  "data": {}
}
```

Laravel Resource Collection seperti `/categories`, `/budgets`, `/badges`, dan `/insights` mengembalikan:

```json
{
  "data": []
}
```

Endpoint paginated seperti `/transactions` dan `/notifications` mengembalikan:

```json
{
  "success": true,
  "data": [],
  "links": {
    "first": "http://localhost:8000/api/transactions?page=1",
    "last": "http://localhost:8000/api/transactions?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 0
  }
}
```

Format tanggal utama:

| Field | Format |
| --- | --- |
| Tanggal transaksi dan periode | `YYYY-MM-DD` |
| Bulan budget | `YYYY-MM` |
| Timestamp resource | `YYYY-MM-DD HH:mm:ss` |

## Endpoints

### Health Check

`GET /health`

Response:

```json
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "2026-06-06T02:00:00.000000Z"
}
```

### Authentication

`POST /register`

Payload:

```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "currency_code": "IDR",
  "initial_balance": 1000000,
  "onboarding_template": "standard"
}
```

Validasi penting: `currency_code`, `initial_balance`, dan `onboarding_template` opsional. `onboarding_template` hanya menerima `standard`, `freelancer`, atau `mahasiswa`.

`POST /login`

Payload:

```json
{
  "email": "john.doe@example.com",
  "password": "password123"
}
```

Response login/register:

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "uuid",
      "name": "John Doe",
      "email": "john.doe@example.com",
      "currency_code": "IDR",
      "initial_balance": 1000000,
      "current_balance": 1000000,
      "streak_days": 0,
      "is_premium": false,
      "premium_until": null,
      "avatar": null,
      "settings": null,
      "created_at": "2026-06-06 09:00:00"
    },
    "token": "plain-text-token"
  }
}
```

`GET /user`

Mengembalikan user resource untuk user yang sedang login.

`POST /logout`

Response:

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Profile & Settings

`GET /profile`

Mengembalikan `UserResource`.

`PUT /profile`

Payload:

```json
{
  "name": "John Updated",
  "email": "john.updated@example.com",
  "currency_code": "USD",
  "avatar": "https://example.com/avatar.jpg",
  "settings": {
    "theme": "dark",
    "language": "id"
  }
}
```

Field yang didukung backend: `name`, `email`, `password`, `password_confirmation`, `currency_code`, `avatar`, `settings`, dan `notification_token`.

`PUT /profile/settings`

Payload:

```json
{
  "theme": "dark",
  "language": "id",
  "notifications_enabled": true,
  "daily_reminder": true
}
```

Validasi penting: `theme` hanya menerima `light` atau `dark`.

`POST /profile/notification-token`

Payload:

```json
{
  "notification_token": "fcm-or-device-token"
}
```

### Dashboard

`GET /dashboard`

Response `data`:

```json
{
  "summary": {
    "total_income": 5000000,
    "total_expense": 1500000,
    "balance": 3500000,
    "savings_rate": 70,
    "top_categories": [],
    "transaction_count": 12
  },
  "comparison": {
    "income_change": 10.5,
    "expense_change": -3.2
  },
  "budget": {
    "overall": {
      "total_budget": 3000000,
      "total_spent": 1500000,
      "remaining": 1500000,
      "categories_at_risk": 1,
      "categories_overspent": 0
    },
    "at_risk": []
  },
  "streak": {},
  "recent_transactions": [],
  "currency": "IDR"
}
```

### Categories

`GET /categories?type=expense`

Query `type` opsional: `income` atau `expense`.

Response:

```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Makan & Minum",
      "type": "expense",
      "icon": "FaUtensils",
      "color": "#FF5722",
      "budget_default": 1500000,
      "is_default": false,
      "user_id": "uuid"
    }
  ]
}
```

`POST /categories`

Payload:

```json
{
  "name": "Groceries",
  "type": "expense",
  "icon": "FaShoppingCart",
  "color": "#FF6B6B",
  "budget_default": 2000000
}
```

`GET /categories/{categoryId}`

`PUT /categories/{categoryId}`

Payload update memakai validasi yang sama dengan create, sehingga `name` dan `type` tetap wajib.

`DELETE /categories/{categoryId}`

Kategori tidak dapat dihapus jika masih memiliki transaksi.

### Transactions

`GET /transactions?per_page=15&page=1`

Response `data` item:

```json
{
  "id": "uuid",
  "amount": 150000,
  "formatted_amount": "Rp 150.000",
  "type": "expense",
  "date": "2026-06-06",
  "note": "Weekly groceries shopping",
  "photo_url": "https://example.com/receipt.jpg",
  "location_name": "Supermarket ABC",
  "is_duplicate": false,
  "category": {},
  "created_at": "2026-06-06 09:00:00"
}
```

`POST /transactions`

Payload:

```json
{
  "amount": 150000,
  "type": "expense",
  "category_id": "{{categoryId}}",
  "date": "2026-06-06",
  "note": "Weekly groceries shopping",
  "photo_url": "https://example.com/receipt.jpg",
  "location_name": "Supermarket ABC"
}
```

Validasi penting: `amount` minimal `1`, `category_id` UUID valid, `date` opsional, dan `photo_url` harus URL valid.

`GET /transactions/{transactionId}`

`PUT /transactions/{transactionId}`

Payload update memakai validasi yang sama dengan create.

`DELETE /transactions/{transactionId}`

`GET /transactions/summary/by-category`

Response `data`:

```json
{
  "total_income": 5000000,
  "total_expense": 1500000,
  "balance": 3500000,
  "savings_rate": 70,
  "top_categories": [
    {
      "category_id": "uuid",
      "category_name": "Makan & Minum",
      "category_icon": "FaUtensils",
      "category_color": "#FF5722",
      "amount": 750000
    }
  ],
  "transaction_count": 12
}
```

### Budgets

`GET /budgets?month_year=2026-06`

Response memakai Resource Collection:

```json
{
  "data": [
    {
      "id": "uuid",
      "category": {},
      "month_year": "2026-06",
      "limit_amount": 2000000,
      "spent_amount": 500000,
      "remaining_amount": 1500000,
      "percentage": 25,
      "is_overspent": false,
      "status": "good"
    }
  ]
}
```

`GET /budgets/current`

Response `data`:

```json
{
  "overall": {
    "total_budget": 3000000,
    "total_spent": 1500000,
    "remaining": 1500000,
    "categories_at_risk": 1,
    "categories_overspent": 0
  },
  "budgets": []
}
```

`POST /budgets`

Payload:

```json
{
  "category_id": "{{categoryId}}",
  "month_year": "2026-06",
  "limit_amount": 5000000
}
```

`GET /budgets/{budgetId}`

`PUT /budgets/{budgetId}`

Payload update juga mewajibkan `category_id`, `month_year`, dan `limit_amount`.

`DELETE /budgets/{budgetId}`

### Reports

`GET /reports/weekly`

`GET /reports/monthly`

`GET /reports/custom?start_date=2026-06-01&end_date=2026-06-30`

Custom report juga mendukung query opsional `format`, `include_charts`, `categories[]`, dan `type`. Nilai `type`: `income`, `expense`, atau `both`.

Response `data`:

```json
{
  "user": {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "currency": "IDR"
  },
  "period": {
    "start_date": "2026-06-01",
    "end_date": "2026-06-30"
  },
  "summary": {
    "total_income": 5000000,
    "total_expense": 1500000,
    "balance": 3500000,
    "savings_rate": 70,
    "total_transactions": 12,
    "top_categories": []
  },
  "transactions": [],
  "generated_at": "2026-06-06 09:00:00"
}
```

`POST /reports/export`

Payload:

```json
{
  "start_date": "2026-06-01",
  "end_date": "2026-06-30",
  "format": "csv"
}
```

Validasi format: `pdf`, `excel`, atau `csv`. Response berupa file stream dengan header `Content-Type` dan `Content-Disposition`, bukan JSON.

### Badges

`GET /badges`

Response item:

```json
{
  "id": "uuid",
  "name": "Budget Master",
  "slug": "budget-master",
  "description": "Maintain budget discipline",
  "icon": "FaTrophy",
  "color": "#FFD700",
  "points": 100,
  "awarded_at": "2026-06-06 09:00:00"
}
```

`GET /badges/all`

Response item menambahkan `is_earned`.

### Insights

`GET /insights?type=weekly`

Query `type` opsional: `daily`, `weekly`, atau `monthly`.

Response item:

```json
{
  "id": "uuid",
  "period_type": "weekly",
  "period_start": "2026-06-01",
  "period_end": "2026-06-07",
  "period_label": "Mingguan",
  "summary": "Pengeluaran minggu ini stabil.",
  "insights": [],
  "recommendations": [],
  "is_read": false,
  "generated_at": "2026-06-06 09:00:00",
  "generated_at_human": "2 jam lalu",
  "insights_count": 0
}
```

`GET /insights/latest`

`POST /insights/{insightId}/read`

Response:

```json
{
  "success": true,
  "message": "Insight marked as read"
}
```

`POST /insights/generate`

Payload opsional:

```json
{
  "period_type": "weekly"
}
```

Response:

```json
{
  "success": true,
  "message": "Insight generation (weekly) has been queued.",
  "period_type": "weekly"
}
```

### Notifications

`GET /notifications?per_page=20&page=1`

Response paginated.

Notification item:

```json
{
  "id": "uuid",
  "type": "budget_alert",
  "title": "Budget Menipis",
  "body": "Pengeluaran sudah mendekati limit.",
  "data": {},
  "is_read": false,
  "read_at": null,
  "created_at": "2026-06-06 09:00:00"
}
```

Tipe notifikasi: `budget_alert`, `budget_warning`, `budget_overspent`, `streak_milestone`, `badge_earned`, `reminder`, `insight_ready`.

`GET /notifications/unread`

`GET /notifications/unread-count`

Response:

```json
{
  "success": true,
  "data": {
    "count": 3
  }
}
```

`POST /notifications/{notificationId}/read`

`POST /notifications/read-all`

Keduanya mengembalikan `success` dan `message`.

## Variables

| Variable | Description |
| --- | --- |
| `{{baseUrl}}` | `http://localhost:8000/api` |
| `{{token}}` | Bearer token dari login/register |
| `{{categoryId}}` | UUID kategori |
| `{{transactionId}}` | UUID transaksi |
| `{{budgetId}}` | UUID budget |
| `{{insightId}}` | UUID insight |
| `{{notificationId}}` | UUID notifikasi |


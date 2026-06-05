# API Documentation - Duitku

## Base URL
http://localhost:8000/api

## Authentication
Semua endpoint (kecuali login/register/health) memerlukan Bearer Token:

Authorization: Bearer {token}

---

## Endpoints

### Health Check
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/health` | Check API status |

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/register` | Register new user |
| POST | `/login` | User login |
| GET | `/user` | Get authenticated user |
| POST | `/logout` | User logout |

### Profile & Settings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/profile` | Get user profile |
| PUT | `/profile` | Update profile |
| PUT | `/profile/settings` | Update settings |
| POST | `/profile/notification-token` | Update FCM token |

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard` | Get dashboard data |

### Categories
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories` | List categories |
| POST | `/categories` | Create category |
| GET | `/categories/{id}` | Get category |
| PUT | `/categories/{id}` | Update category |
| DELETE | `/categories/{id}` | Delete category |

### Transactions
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/transactions` | List transactions |
| POST | `/transactions` | Create transaction |
| GET | `/transactions/{id}` | Get transaction |
| PUT | `/transactions/{id}` | Update transaction |
| DELETE | `/transactions/{id}` | Delete transaction |
| GET | `/transactions/summary/by-category` | Summary by category |

### Budgets
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/budgets` | List budgets |
| GET | `/budgets/current` | Current month budgets |
| POST | `/budgets` | Create budget |
| GET | `/budgets/{id}` | Get budget |
| PUT | `/budgets/{id}` | Update budget |
| DELETE | `/budgets/{id}` | Delete budget |

### Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports/weekly` | Weekly report |
| GET | `/reports/monthly` | Monthly report |
| GET | `/reports/custom` | Custom range report |
| POST | `/reports/export` | Export report |

### Badges
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/badges` | Get earned badges |
| GET | `/badges/all` | Get all badges with status |

### Insights
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/insights` | List insights |
| GET | `/insights/latest` | Latest unread insight |
| POST | `/insights/generate` | Generate new insights |
| POST | `/insights/{id}/read` | Mark insight as read |

### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/notifications` | List notifications |
| GET | `/notifications/unread` | Get unread notifications |
| GET | `/notifications/unread-count` | Get unread count |
| POST | `/notifications/{id}/read` | Mark as read |
| POST | `/notifications/read-all` | Mark all as read |

---

## Variables

| Variable | Description |
|----------|-------------|
| `{{baseUrl}}` | `http://localhost:8000/api` |
| `{{token}}` | Bearer token from login/register |
| `{{categoryId}}` | UUID of category |
| `{{transactionId}}` | UUID of transaction |
| `{{budgetId}}` | UUID of budget |
| `{{insightId}}` | ID of insight |
| `{{notificationId}}` | ID of notification |
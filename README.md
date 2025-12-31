# EcoEats Backend API

Production-grade Laravel API backend for EcoEats, designed to work alongside a separate React frontend.

## 🏗️ Architecture

- **Laravel 12** (latest stable)
- **API-first design** - JSON responses only
- **Service layer pattern** - Business logic in Services, thin Controllers
- **Dockerized** - Complete local/dev environment with Docker Compose
- **Authentication** - Laravel Sanctum with SPA cookie-based auth
- **Authorization** - Spatie Laravel Permission (RBAC)
- **Queues** - Redis queue driver with Horizon dashboard
- **Monitoring** - Laravel Pulse for application metrics
- **Testing** - Pest PHP for testing

## 📋 Prerequisites

- Docker & Docker Compose
- Composer (for local development)
- PHP 8.3+ (for local development)

## 🚀 Quick Start

### 1. Clone and Setup

```bash
cd ecoeats-backend
make install
```

This will:
- Install Composer dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run migrations and seeders

### 2. Start Services

```bash
make up
```

This starts all Docker containers:
- **App** (PHP-FPM 8.3) - Laravel application
- **Caddy** - Web server/reverse proxy (port 8000)
- **MySQL 8.0** - Database (port 3306)
- **Redis** - Cache/session/queue (port 6379)
- **Queue Worker** - Processes queued jobs
- **Scheduler** - Runs scheduled tasks
- **Horizon** - Queue dashboard and monitoring
- **Mailpit** - Email testing (port 8025)

### 3. Access Points

- **API**: http://localhost:8000
- **Health Check**: http://localhost:8000/health
- **Horizon Dashboard**: http://localhost:8000/horizon (admin only)
- **Pulse Dashboard**: http://localhost:8000/pulse (admin only)
- **Mailpit**: http://localhost:8025

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/        # Versioned API controllers
│   │   └── Auth/           # Authentication controllers
│   ├── Middleware/         # Custom middleware
│   ├── Requests/           # Form request validation
│   └── Resources/          # API resource transformers
├── Models/                 # Eloquent models
├── Policies/               # Authorization policies
├── Services/               # Business logic layer
│   ├── Auth/
│   └── Projects/
└── Jobs/                    # Queue jobs
```

## 🔐 Authentication

### Sanctum SPA Authentication

The API uses Laravel Sanctum for SPA authentication with cookie-based sessions.

**Frontend Setup (React):**
1. First, fetch CSRF cookie: `GET /sanctum/csrf-cookie`
2. Then login: `POST /api/v1/auth/login` with credentials
3. Subsequent requests automatically include session cookie

**Endpoints:**
- `GET /sanctum/csrf-cookie` - Get CSRF cookie
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout
- `GET /api/v1/auth/me` - Get current user

### CORS Configuration

Configured for React frontend at `http://localhost:3000`. Update `CORS_ALLOWED_ORIGINS` in `.env` for production.

## 🔑 Authorization (RBAC)

Uses Spatie Laravel Permission for role-based access control.

### Default Roles
- **admin** - Full access to all permissions
- **user** - Limited permissions (projects.read, projects.create)

### Default Permissions
- `projects.read`
- `projects.create`
- `projects.update`
- `projects.delete`

### Usage in Routes

```php
Route::middleware(['auth:sanctum', 'permission:projects.create'])->group(function () {
    // Protected routes
});
```

### Default Users (from seeder)
- **Admin**: admin@ecoeats.local / password
- **User**: user@ecoeats.local / password

## 📡 API Endpoints

### Versioning
All API routes are versioned under `/api/v1/`

### Response Format

**Success:**
```json
{
  "data": { ... },
  "message": "Success message"
}
```

**Error:**
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Error details"]
  }
}
```

### Projects API

- `GET /api/v1/projects` - List projects (with filtering, sorting, pagination)
- `GET /api/v1/projects/{id}` - Get project
- `POST /api/v1/projects` - Create project
- `PUT /api/v1/projects/{id}` - Update project
- `DELETE /api/v1/projects/{id}` - Delete project

**Query Parameters:**
- `filter[name]=...` - Filter by name
- `sort=created_at,-name` - Sort (prefix with `-` for descending)
- `page[number]=1` - Page number
- `page[size]=15` - Items per page

## 🎯 Queue Jobs

### Configuration
- **Driver**: Redis
- **Default Queue**: `default`
- **Retries**: 3 attempts
- **Backoff**: Exponential (60s, 120s, 240s)
- **Timeout**: Job-specific

### Example Jobs

**SendWelcomeEmailJob** - Example async job with retries
```php
SendWelcomeEmailJob::dispatch($user);
```

**ProcessProjectBatchJob** - Example batch job
```php
Bus::batch([
    new ProcessProjectBatchJob($project1),
    new ProcessProjectBatchJob($project2),
])->dispatch();
```

### Failed Jobs
Failed jobs are stored in `failed_jobs` table. Retry with:
```bash
php artisan queue:retry all
```

## 📊 Monitoring

### Horizon
Queue dashboard and monitoring at `/horizon` (admin only)

### Pulse
Application metrics dashboard at `/pulse` (admin only)

## 🧪 Testing

Uses Pest PHP for testing.

```bash
make test
# or
php artisan test
```

**Test Coverage:**
- Authentication flows
- RBAC authorization
- CRUD operations
- Queue jobs

## 🔧 Configuration

### Environment Variables

Key configuration in `.env`:

```env
# App
APP_URL=http://localhost:8000
APP_ENV=local

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=ecoeats
DB_USERNAME=ecoeats
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
SESSION_DOMAIN=localhost

# CORS
CORS_ALLOWED_ORIGINS=http://localhost:3000
CORS_SUPPORTS_CREDENTIALS=true

# Queue
QUEUE_CONNECTION=redis

# Features
FEATURE_REGISTRATION_ENABLED=false
```

## 🛠️ Makefile Commands

```bash
make install      # Initial setup
make up           # Start all services
make down         # Stop all services
make test         # Run tests
make migrate       # Run migrations
make seed          # Run seeders
make fresh         # Fresh migration with seeding
make shell         # Open shell in app container
make logs          # View logs
make horizon       # Open Horizon dashboard
make pulse         # Open Pulse dashboard
```

## 📝 Code Standards

- **PSR-12** coding standards
- **Service layer** for business logic
- **Form Requests** for validation
- **API Resources** for response transformation
- **Policies** for authorization
- **Type hints** throughout

## 🔒 Security

- CSRF protection for stateful requests
- Rate limiting on API endpoints
- Secure session cookies (configure for production)
- Trusted proxy configuration
- Security headers via Caddy

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Horizon](https://laravel.com/docs/horizon)
- [Laravel Pulse](https://laravel.com/docs/pulse)

## 🐳 Docker Services

- **app**: PHP 8.3-FPM with required extensions
- **caddy**: Web server with PHP-FPM proxy
- **mysql**: MySQL 8.0 database
- **redis**: Redis for cache/session/queue
- **queue**: Queue worker container
- **scheduler**: Scheduled task runner
- **horizon**: Horizon dashboard
- **mailpit**: Email testing tool

## 📄 License

MIT

# Task Management App - Laravel & Nuxt Integration Setup Guide

This guide documents all the steps taken to configure the connection between the Laravel backend and Nuxt frontend.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Backend Configuration (Laravel)](#backend-configuration-laravel)
3. [Frontend Configuration (Nuxt)](#frontend-configuration-nuxt)
4. [Running the Application](#running-the-application)
5. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- Docker & Docker Compose (for Laravel Sail)
- Node.js & pnpm (for Nuxt)
- Git

---

## Backend Configuration (Laravel)

### 1. Install Laravel Dependencies
```bash
cd backend
composer install
```

### 2. Set Up Environment Variables
```bash
cp .env.example .env
```

### 3. Start Laravel Sail
```bash
sail up -d
```

The Laravel application will run on **http://localhost** (port 80)

### 4. Run Database Migrations
```bash
sail artisan migrate
```

### 5. Seed the Database with Test Data
```bash
sail artisan db:seed
```

This creates 5 test users and 5 tasks per user. Default credentials:
- **Password**: `password`
- **Sample Email**: `lenna.legros@example.org` (or any other seeded email)

### 6. Create CORS Configuration

Create or update `/backend/config/cors.php`:

```php
<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### 7. Verify API Routes

Check that the following routes are available:

```bash
sail artisan route:list
```

Key routes:
- `POST /api/login` - User authentication (no auth required)
- `GET /api/tasks` - Fetch user's tasks (requires auth)
- `POST /api/tasks` - Create a new task (requires auth)
- `GET /api/tasks/{id}` - Get specific task (requires auth)
- `PUT /api/tasks/{id}` - Update task (requires auth)
- `DELETE /api/tasks/{id}` - Delete task (requires auth)
- `POST /api/logout` - Logout user (requires auth)

### 8. Verify Authentication Works

Test login endpoint:

```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"lenna.legros@example.org","password":"password"}'
```

Expected response:
```json
{
  "user": {...},
  "token": "1|your_token_here"
}
```

---

## Frontend Configuration (Nuxt)

### 1. Install Nuxt Dependencies
```bash
cd frontend
pnpm install
```

### 2. Configure API Base URL

Update `/frontend/nuxt.config.ts` to set the API base URL:

```typescript
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss'],
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost'  // Points to Laravel backend
    }
  },
  app: {
    head: {
      title: 'Task Management App',
      bodyAttrs: {
        class: 'white bg-gray-100 dark:bg-gray-900'
      }
    }
  }
})
```

**Important**: The API base URL is `http://localhost` (port 80), not `http://localhost:8000`

### 3. Set Up Authentication State Management

The `/frontend/app/app.vue` component handles:
- Login page with email/password authentication
- Token storage in localStorage
- API calls with Bearer token authentication
- Automatic logout functionality

Key storage keys:
- `auth_token` - Stores the API token
- `user` - Stores authenticated user data

### 4. Configure API Calls

All API calls use the runtime config:

```typescript
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// Example: Login
const response = await fetch(`${apiBase}/api/login`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ email, password })
})

// Example: Fetch tasks (with authentication)
const response = await fetch(`${apiBase}/api/tasks`, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
```

---

## Running the Application

### Terminal 1: Start Laravel Backend
```bash
cd /home/prince-raza/task-management/backend
sail up -d
```

### Terminal 2: Start Nuxt Development Server
```bash
cd /home/prince-raza/task-management/frontend
pnpm run dev
```

The app will be available at **http://localhost:3000** (or next available port)

---

## Application Features

### Login Page
- Email and password authentication
- Credentials stored in localStorage for persistence
- Error handling for failed login attempts
- Demo credentials displayed on login page

### Task Management
- View tasks grouped by date (Today, Recent, Last week, Older)
- Create new tasks with description, date, status, and priority
- Select task date from sidebar
- Automatic date list refresh after creating tasks
- User logout functionality

### Data Flow
1. User logs in → receives API token
2. Token stored in localStorage
3. All subsequent requests include token in Authorization header
4. Tasks are fetched and displayed grouped by date
5. New tasks created with selected date
6. Logout clears token and returns to login page

---

## Troubleshooting

### Issue: Connection Refused (ERR_CONNECTION_REFUSED)

**Cause**: Laravel backend not running or on wrong port

**Solution**:
```bash
# Check if Laravel container is running
docker ps | grep laravel

# Start if not running
cd backend && sail up -d

# Verify port 80 is accessible
curl http://localhost/api/login
```

### Issue: CORS Errors

**Cause**: CORS middleware not configured

**Solution**: Ensure `/backend/config/cors.php` exists with proper configuration

### Issue: Dates Not Displaying

**Cause**: Wrong field being used for dates

**Solution**: The app uses the `date` field from tasks (not `created_at`). Ensure tasks have dates in YYYY-MM-DD format.

### Issue: Login Fails After Backend Restart

**Cause**: Token invalidated or corrupted localStorage

**Solution**:
```bash
# Clear browser localStorage manually
# Or use browser DevTools Console:
localStorage.clear()
```

### Issue: Nuxt Dev Server Won't Start

**Cause**: Port already in use or environment issue

**Solution**:
```bash
# Kill existing processes
pkill -f "nuxt dev"

# Clear node_modules cache
rm -rf node_modules/.vite

# Reinstall dependencies
pnpm install

# Start again
pnpm run dev
```

---

## Key Configuration Files

| File | Purpose |
|------|---------|
| `/backend/bootstrap/app.php` | Laravel app configuration & middleware setup |
| `/backend/config/cors.php` | CORS allowed origins and methods |
| `/backend/routes/api.php` | API endpoint definitions |
| `/frontend/nuxt.config.ts` | Nuxt configuration including API base URL |
| `/frontend/app/app.vue` | Main Vue component with auth & task management |

---

## Database Schema

### Tasks Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `description` - Task description
- `date` - Task date (YYYY-MM-DD format)
- `status` - Task status (pending, in_progress, completed, cancelled)
- `priority` - Task priority (low, medium, high)
- `created_at` - Task creation timestamp
- `updated_at` - Task update timestamp

### Users Table
- `id` - Primary key
- `name` - User full name
- `email` - User email address
- `password` - Hashed password
- `created_at` - Account creation timestamp
- `updated_at` - Account update timestamp

---

## API Endpoints Summary

### Authentication (No Auth Required)
- `POST /api/login` - Authenticate user
- `POST /api/logout` - Logout user (auth required)

### Tasks (All Require Auth)
- `GET /api/tasks` - Get all user's tasks
- `POST /api/tasks` - Create new task
- `GET /api/tasks/{id}` - Get specific task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task

### Request Headers
All authenticated requests require:
```
Authorization: Bearer <token>
Content-Type: application/json
```

---

## Next Steps

- Implement task filtering and search
- Add task editing functionality
- Implement task deletion with confirmation
- Add task status management
- Create user profile management
- Implement real-time updates with WebSockets
- Add comprehensive error handling and notifications

---

## References

- [Laravel Documentation](https://laravel.com/docs)
- [Nuxt Documentation](https://nuxt.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Tailwind CSS](https://tailwindcss.com)

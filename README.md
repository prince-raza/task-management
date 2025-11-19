## Project Structure

```
task-management/
├── backend/          # Laravel API backend
└── frontend/         # Nuxt frontend application
```

## Backend (Laravel)

### Technologies

- **Framework**: Laravel 12
- **PHP Version**: 8.2 or higher
- **Database**: MySQL (via Docker) or SQLite (default)
- **Authentication**: Laravel Sanctum
- **Asset Bundling**: Vite
- **Styling**: TailwindCSS
- **Testing**: Pest PHP

### Prerequisites

- **Docker** and **Docker Compose** (required for Laravel Sail)
- **Composer** (for initial dependency installation)

> Note: PHP and MySQL are provided by the Docker containers, so you don't need to install them locally.

### Setup

1. **Navigate to the backend directory**:
   ```bash
   cd backend
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   ```

4. **Start Laravel Sail**:
   ```bash
   ./vendor/bin/sail up -d
   ```
   The `-d` flag runs containers in detached mode (background).

5. **Generate application key**:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Install Node.js dependencies** (if not already done):
   ```bash
   ./vendor/bin/sail npm install
   ```

7. **Run database migrations**:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

   Or use the setup script:
   ```bash
   ./vendor/bin/sail composer run setup
   ```

### Running the Backend

**Using Laravel Sail** (recommended):

Start all services:
```bash
./vendor/bin/sail up
```

Or run in detached mode:
```bash
./vendor/bin/sail up -d
```

The API will be available at `http://localhost` (or the port specified in your `.env` file via `APP_PORT`, default is port 80).

**Running development commands with Sail**:

All Laravel artisan commands should be prefixed with `./vendor/bin/sail`:
```bash
# Start queue worker
./vendor/bin/sail artisan queue:listen

# Run migrations
./vendor/bin/sail artisan migrate

# Access tinker
./vendor/bin/sail artisan tinker

# Run tests
./vendor/bin/sail artisan test

# View logs
./vendor/bin/sail logs

# Install npm packages
./vendor/bin/sail npm install

# Run npm scripts
./vendor/bin/sail npm run dev
```

**Using the dev script** (starts server, queue, logs, and vite):
```bash
./vendor/bin/sail composer run dev
```

This will start:
- Laravel development server (usually on `http://localhost`)
- Queue worker
- Log viewer (Pail)
- Vite development server for assets

**Stopping Sail**:
```bash
./vendor/bin/sail down
```

**Stopping and removing volumes** (fresh start):
```bash
./vendor/bin/sail down -v
```

### Building Assets for Production

```bash
./vendor/bin/sail npm run build
```

### Running Tests

```bash
./vendor/bin/sail artisan test
# or
./vendor/bin/sail composer run test
```

#### Pest Testing

Laravel is configured to use [Pest](https://pestphp.com) for expressive testing. The default test suite already uses Pest syntax, so you can keep writing tests with Pest’s expectations and dataset helpers.

- **Run the full Pest suite**:
  ```bash
  ./vendor/bin/sail ./vendor/bin/pest
  ```
- **Running a specific Pest test file**:
  ```bash
  ./vendor/bin/sail ./vendor/bin/pest tests/Feature/TaskTest.php
  ```
- **Filtering by test name**:
  ```bash
  ./vendor/bin/sail ./vendor/bin/pest --filter="tasks_can_be_completed"
  ```
- **Watching tests** (reruns on file changes, requires `symfony/process` which is already included):
  ```bash
  ./vendor/bin/sail ./vendor/bin/pest --watch
  ```

You can generate new Pest tests with artisan:
```bash
./vendor/bin/sail artisan pest:test TaskStatusTest
```

Refer to `tests/Feature` and `tests/Unit` for examples of how to structure Pest tests in this project.

### Sanctum & CORS Configuration

Ensure backend and frontend env values match so Sanctum cookies work when the Nuxt app runs on `http://localhost:3000`.

1. Copy `.env.example` to `.env` and set:
   ```env
   APP_URL=http://localhost
   FRONTEND_URL=http://localhost:3000
   SESSION_DOMAIN=localhost
   SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000
   ```
   Adjust host/port if you expose Laravel on `http://localhost:8000` or the frontend on another port.
2. Clear cached config:
   ```bash
   ./vendor/bin/sail artisan config:clear
   ```
3. Make sure the frontend HTTP client sends credentials (e.g. Axios `withCredentials: true`) so Sanctum can attach cookies.

With these values and the default `config/cors.php` in this repo, `http://localhost:3000` can talk to the API without CORS errors.

### API Endpoints

The backend provides a RESTful API. Make sure to configure your frontend to point to the correct API base URL (default with Sail: `http://localhost` or the port specified in `APP_PORT` environment variable).

---

## Frontend (Nuxt)

### Technologies

- **Framework**: Nuxt 4
- **Language**: TypeScript
- **UI Library**: Vue 3
- **State Management**: Pinia
- **Styling**: TailwindCSS
- **Icons**: Lucide Vue
- **Drag & Drop**: Vuedraggable

### Prerequisites

- Node.js 18 or higher
- pnpm (preferred package manager)

### Setup

1. **Navigate to the frontend directory**:
   ```bash
   cd frontend
   ```

2. **Install dependencies**:
   ```bash
   pnpm install
   ```

3. **Configure API endpoint** (if needed):

   Edit `nuxt.config.ts` and update the `runtimeConfig.public.apiBase`:
   ```typescript
   runtimeConfig: {
     public: {
       apiBase: 'http://localhost:8000'
   }
   ```

### Running the Frontend

**Development Mode**:
```bash
pnpm dev
```

The application will be available at `http://localhost:3000` (or the next available port).

**Production Build**:
```bash
pnpm build
```

**Preview Production Build**:
```bash
pnpm preview
```

---

## Running Both Applications

### Separate Terminals

**Terminal 1 - Backend (with Sail)**:
```bash
cd backend
./vendor/bin/sail up
```

**Terminal 2 - Frontend**:
```bash
cd frontend
pnpm run dev
```

The backend `composer run dev` script uses `concurrently` to run multiple services (server, queue, logs, vite). Make sure your frontend is running in a separate terminal.

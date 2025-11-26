# Syncio

A full-stack application combining a Laravel backend API with a Vue.js frontend.

## Project Structure

```
syncio/
├── syncio-test-be/     # Laravel backend API
├── syncio-test-fe/     # Vue.js frontend application
├── package.json        # Root package.json for running both projects
└── README.md          # This file
```

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **Node.js** (v20.19.0 or >=22.12.0)
- **PHP** (^8.2)
- **Composer** (PHP dependency manager)
- **npm** or **yarn** (Node package manager)

## Quick Start

### 1. Install Dependencies

Install all dependencies for both projects:

```bash
pnpm run install:all
```

Or install them separately:

```bash
# Backend dependencies
pnpm run install:backend

# Frontend dependencies
pnpm run install:frontend
```

### 2. Backend Setup (Laravel)

Navigate to the backend directory and complete the Laravel setup:

```bash
cd syncio-test-be
```

#### Environment Configuration

1. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

2. Generate the application key:
   ```bash
   php artisan key:generate
   ```

3. Configure your database settings in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

#### Database Migration

Run the database migrations:

```bash
php artisan migrate
```

If you need to seed the database:

```bash
php artisan db:seed
```

### 3. Frontend Setup (Vue.js)

The frontend dependencies should already be installed from step 1. If you need to install them separately:

```bash
cd syncio-test-fe
pnpm install
```

## Running the Application

### Run Both Projects Simultaneously

From the root directory, run:

```bash
pnpm run dev
```

This will start:
- **Backend API**: http://localhost:8000
- **Frontend App**: http://localhost:5173 (or the port shown in the terminal)

### Run Projects Individually

#### Backend Only

```bash
pnpm run dev:backend
```

Or from the backend directory:

```bash
cd syncio-test-be
php artisan serve
```

#### Frontend Only

```bash
pnpm run dev:frontend
```

Or from the frontend directory:

```bash
cd syncio-test-fe
pnpm run dev
```

## Building for Production

Build both projects:

```bash
pnpm run build
```

Or build individually:

```bash
# Build backend
pnpm run build:backend

# Build frontend
pnpm run build:frontend
```

## Development

### Backend (Laravel)

- **API Routes**: `syncio-test-be/routes/api.php`
- **Web Routes**: `syncio-test-be/routes/web.php`
- **Controllers**: `syncio-test-be/app/Http/Controllers/`
- **Models**: `syncio-test-be/app/Models/`
- **Migrations**: `syncio-test-be/database/migrations/`

#### Useful Laravel Commands

```bash
# Create a new migration
php artisan make:migration create_table_name

# Create a new controller
php artisan make:controller ControllerName

# Create a new model
php artisan make:model ModelName

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Frontend (Vue.js)

- **Source Code**: `syncio-test-fe/src/`
- **Components**: `syncio-test-fe/src/components/`
- **Views**: `syncio-test-fe/src/views/`
- **Router**: `syncio-test-fe/src/router/`
- **Store**: `syncio-test-fe/src/stores/`

#### Useful Frontend Commands

```bash
cd syncio-test-fe

# Type checking
pnpm run type-check

# Linting
pnpm run lint

# Formatting
pnpm run format

# Preview production build
pnpm run preview
```

## Troubleshooting

### Port Already in Use

If port 8000 (backend) or 5173 (frontend) is already in use:

**Backend**: Specify a different port
```bash
cd syncio-test-be
php artisan serve --port=8001
```

**Frontend**: Vite will automatically use the next available port, or you can configure it in `vite.config.ts`

### Database Connection Issues

1. Ensure your database server is running
2. Verify your `.env` file has the correct database credentials
3. Check that the database exists:
   ```bash
   php artisan migrate:status
   ```

### Node Version Issues

Make sure you're using a compatible Node.js version:
```bash
node --version  # Should be ^20.19.0 || >=22.12.0
```

If you need to switch Node versions, consider using `nvm` (Node Version Manager).

### PHP Version Issues

Ensure you're using PHP 8.2 or higher:
```bash
php --version  # Should be ^8.2
```

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Vite Documentation](https://vitejs.dev/)



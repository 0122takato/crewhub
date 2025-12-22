# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**CrewHub** is a mobile application for live event staff management companies.

**Tech Stack:**
- Backend: Laravel 12 (PHP 8.4) + Laravel Boost MCP server
- Frontend: React Native (Expo) + TypeScript
- Database: MySQL 8.0
- Cache: Redis
- Infrastructure: Docker (nginx, php, mysql, redis, node services)

## Development Environment

### Initial Setup

```bash
# Copy environment file
cp .env.example .env

# Build and start Docker containers
docker-compose up -d --build

# Install backend dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Install frontend dependencies
cd frontend && npm install
```

### Common Docker Commands

```bash
make up              # Start containers
make down            # Stop containers
make logs            # View all logs
make shell           # Access PHP container (bash)
make migrate         # Run migrations
make fresh           # Fresh migrations with seeding
make test            # Run Laravel tests
```

### Backend (Laravel) Commands

All Laravel commands run inside the PHP container:

```bash
# Database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan db:seed

# Testing
docker-compose exec app php artisan test
docker-compose exec app php artisan test --filter=testName
docker-compose exec app vendor/bin/pint --dirty  # Format code (required before finalizing)

# Debugging
docker-compose exec app php artisan tinker
```

### Frontend (React Native) Commands

```bash
cd frontend

npm start            # Start Expo development server
npm run android      # Run on Android
npm run ios          # Run on iOS (macOS only)
npm run web          # Run on web browser
npm run lint         # Run ESLint
```

### Laravel Boost MCP Server

Laravel Boost provides AI-assisted development tools via MCP:

```bash
# Setup
claude mcp add -s local laravel-boost -- docker-compose exec app php artisan boost:mcp
```

**Key Boost Tools:**
- `list-artisan-commands` - List available Artisan commands with parameters
- `search-docs` - Search Laravel ecosystem documentation (version-specific)
- `tinker` - Execute PHP code for debugging
- `database-query` - Read from database
- `browser-logs` - Read browser console logs/errors

## Architecture

### Backend Structure (Laravel 12)

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # HTTP controllers
│   │   └── Requests/       # Form Request validation
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic
│   └── Providers/          # Service providers
├── bootstrap/
│   └── app.php             # Central configuration
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   ├── factories/          # Model factories
│   └── seeders/            # Database seeders
├── routes/
│   ├── api.php             # API routes
│   └── web.php             # Web routes
└── tests/                  # PHPUnit tests
```

### Frontend Structure (React Native)

```
frontend/
├── app/                    # Expo Router app directory
├── components/             # Reusable components
├── hooks/                  # Custom hooks
├── services/               # API services
├── types/                  # TypeScript types
└── utils/                  # Utility functions
```

### Docker Architecture

- **nginx** (port 8080) → serves Laravel backend
- **app** (PHP 8.3-FPM) → runs Laravel
- **mysql** (port 3306) → database
- **redis** (port 6379) → cache & queue
- **mobile** (ports 8081, 19000-19002) → Expo dev server

All services communicate via `crewhub_network` bridge network.

## Key Development Patterns

### Laravel Conventions

- **Models:** Use `casts()` method instead of `$casts` property
- **Validation:** Create Form Request classes, not inline validation
- **Database:** Prefer Eloquent relationships; use eager loading to prevent N+1
- **Testing:** PHPUnit only; run `php artisan make:test {name}` for feature tests
- **Code Style:** Run `vendor/bin/pint --dirty` before finalizing changes

### React Native Conventions

- TypeScript strict mode
- Expo Router for navigation
- React Query for API state management
- Functional components with hooks

### API Design

- RESTful endpoints under `/api/v1/`
- JSON API format for responses
- Laravel Sanctum for mobile authentication

## Coding Rules

@.claude/coding-rules.md

## Performance

@.claude/performance.md

## Testing Strategy

- Most tests should be **feature tests** (test user workflows)
- Use factories for model creation in tests
- Check factory states before manual setup
- Run minimal tests with `--filter` after changes
- Run full suite before finalizing major features

## URLs

- Backend API: http://localhost:8080
- Expo Dev Server: exp://localhost:19000
- Expo Web: http://localhost:19002

## Important Notes

### Before Making Changes

1. Search Laravel Boost docs (`search-docs` tool) for version-specific guidance
2. Check sibling files for existing patterns and conventions
3. Verify with `list-artisan-commands` before running Artisan commands

### Database Migrations

When modifying columns, **include all previous attributes** or they will be dropped.

### Creating Files

- Use `php artisan make:*` commands with `--no-interaction` flag
- For generic PHP classes: `php artisan make:class`
- When creating models, also create factories and seeders

### Environment Variables

- **Never** use `env()` outside config files
- Always use `config('key')` in application code
- Configuration values belong in `config/` directory

## Workflow Best Practices

1. **Start services:** `make up` or `docker-compose up -d`
2. **Make code changes** following existing conventions
3. **Run relevant tests:** `php artisan test --filter=testName`
4. **Format code:** `vendor/bin/pint --dirty`
5. **Test on device/simulator** (if frontend changes)
6. **Run full test suite** before finalizing

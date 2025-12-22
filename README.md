# CrewHub

ライブスタッフの運営会社のためのスマホアプリ

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.3)
- **Frontend:** React Native (Expo) + TypeScript
- **Database:** MySQL 8.0
- **Cache:** Redis
- **Infrastructure:** Docker

## Project Structure

```
crewhub/
├── backend/          # Laravel API
├── frontend/         # React Native (Expo)
├── docker/           # Docker configuration
│   ├── nginx/
│   └── php/
├── docker-compose.yml
├── Makefile
├── CLAUDE.md         # AI development guide
└── .claude/          # Coding rules & guidelines
```

## Getting Started

### Prerequisites

- Docker & Docker Compose
- Node.js 20+ (for local React Native development)

### Setup

```bash
# 1. Clone the repository
git clone <repository-url>
cd crewhub

# 2. Copy environment file
cp .env.example .env

# 3. Build and start Docker containers
docker-compose up -d --build

# 4. Install backend dependencies
docker-compose exec app composer install

# 5. Generate application key
docker-compose exec app php artisan key:generate

# 6. Run migrations
docker-compose exec app php artisan migrate

# 7. Install frontend dependencies
cd frontend && npm install
```

### Development

#### Using Make commands

```bash
make up              # Start containers
make down            # Stop containers
make logs            # View logs
make shell           # Enter PHP container
make migrate         # Run migrations
make fresh           # Fresh migrations with seeds
make test            # Run tests
```

#### Backend (Laravel)

```bash
# Run tests
docker-compose exec app php artisan test

# Code formatting
docker-compose exec app vendor/bin/pint --dirty
```

#### Frontend (React Native)

```bash
cd frontend

# Start Expo development server
npm start

# Run on specific platform
npm run android
npm run ios
npm run web
```

## API Endpoints

Base URL: `http://localhost:8080/api`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /health  | Health check |

## Docker Services

| Service | Port | Description |
|---------|------|-------------|
| nginx   | 8080 | Web server |
| app     | 9000 | PHP-FPM |
| mysql   | 3306 | Database |
| redis   | 6379 | Cache & Queue |
| mobile  | 8081, 19000-19002 | Expo dev server |

## Development Guidelines

See [CLAUDE.md](./CLAUDE.md) for detailed development guidelines and coding rules.

## License

Private - All rights reserved

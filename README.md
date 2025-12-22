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

### Quick Start

```bash
# Clone and setup (first time)
git clone <repository-url>
cd crewhub
cp .env.example .env
make init
```

### Manual Setup

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Build and start Docker containers
docker-compose up -d --build

# 3. Install backend dependencies
docker-compose exec app composer install

# 4. Generate application key
docker-compose exec app php artisan key:generate

# 5. Run migrations
docker-compose exec app php artisan migrate

# 6. Install frontend dependencies
cd frontend && npm install
```

## Make Commands

`make help` で全コマンドのヘルプを表示できます。

### Setup

| Command | Description |
|---------|-------------|
| `make init` | 初回セットアップ（build, install, migrate を一括実行） |
| `make setup` | クイックセットアップ（up, install） |
| `make build` | Dockerコンテナをビルド |

### Docker

| Command | Description |
|---------|-------------|
| `make up` | コンテナ起動 |
| `make down` | コンテナ停止 |
| `make restart` | コンテナ再起動 |
| `make ps` | 実行中コンテナ表示 |
| `make clean` | コンテナとボリューム削除 |

### Laravel

| Command | Description |
|---------|-------------|
| `make shell` | PHPコンテナに入る |
| `make artisan cmd="..."` | Artisanコマンド実行 |
| `make tinker` | Laravel Tinker起動 |

### Database

| Command | Description |
|---------|-------------|
| `make migrate` | マイグレーション実行 |
| `make fresh` | Fresh migration + seed |
| `make seed` | Seeder実行 |
| `make rollback` | 最後のマイグレーションをロールバック |
| `make db-reset` | データベース完全リセット |

### Testing & Code Quality

| Command | Description |
|---------|-------------|
| `make test` | 全テスト実行 |
| `make test-filter name="UserTest"` | 特定テスト実行 |
| `make pint` | コードフォーマット（Laravel Pint） |
| `make lint` | コードチェック（dry-run） |

### Cache

| Command | Description |
|---------|-------------|
| `make cache-clear` | アプリケーションキャッシュクリア |
| `make config-clear` | 設定キャッシュクリア |
| `make route-clear` | ルートキャッシュクリア |
| `make view-clear` | ビューキャッシュクリア |
| `make optimize` | アプリケーション最適化 |
| `make optimize-clear` | 全キャッシュクリア |

### Logs

| Command | Description |
|---------|-------------|
| `make logs` | 全Dockerログ表示 |
| `make logs-app` | Laravelログ表示 |
| `make logs-nginx` | Nginxログ表示 |
| `make logs-mysql` | MySQLログ表示 |

### Composer

| Command | Description |
|---------|-------------|
| `make composer-install` | 依存関係インストール |
| `make composer-update` | 依存関係更新 |
| `make composer-require pkg="vendor/package"` | パッケージ追加 |
| `make composer-require-dev pkg="vendor/package"` | 開発パッケージ追加 |

### Mobile (React Native / Expo)

| Command | Description |
|---------|-------------|
| `make mobile-install` | npm依存関係インストール |
| `make mobile-start` | Expo開発サーバー起動 |
| `make mobile-android` | Androidで実行 |
| `make mobile-ios` | iOSで実行（macOSのみ） |
| `make mobile-web` | Webブラウザで実行 |
| `make mobile-lint` | ESLint実行 |

### Shortcuts

| Command | Description |
|---------|-------------|
| `make m` | `make migrate` のショートカット |
| `make t` | `make test` のショートカット |
| `make s` | `make shell` のショートカット |
| `make l` | `make logs` のショートカット |

## Docker Services

| Service | Port | Description |
|---------|------|-------------|
| nginx | 8080 | Web server |
| app | 9000 | PHP-FPM |
| mysql | 3306 | Database |
| redis | 6379 | Cache & Queue |
| mobile | 8081, 19000-19002 | Expo dev server |

## API Endpoints

Base URL: `http://localhost:8080/api`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /health | Health check |

## Development Guidelines

See [CLAUDE.md](./CLAUDE.md) for detailed development guidelines and coding rules.

## License

Private - All rights reserved

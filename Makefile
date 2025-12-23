# ============================================
# CrewHub Development Makefile
# ============================================
# Usage: make [command]
# Run 'make help' to see all available commands
# ============================================

.PHONY: help init env-setup setup up down restart logs shell \
        migrate fresh seed rollback tinker \
        test test-filter pint lint \
        cache-clear config-clear route-clear view-clear optimize \
        logs-app logs-nginx logs-mysql \
        mobile-install mobile-start mobile-android mobile-ios mobile-web \
        composer-install composer-update artisan \
        db-reset ps clean

# Default target
.DEFAULT_GOAL := help

# Colors for output
CYAN := \033[36m
GREEN := \033[32m
YELLOW := \033[33m
RESET := \033[0m

# ============================================
# Help
# ============================================

help: ## Show this help message
	@echo ""
	@echo "$(GREEN)CrewHub Development Commands$(RESET)"
	@echo "=============================="
	@echo ""
	@echo "$(YELLOW)Setup:$(RESET)"
	@grep -E '^(init|setup|build):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Docker:$(RESET)"
	@grep -E '^(up|down|restart|ps|clean):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Laravel:$(RESET)"
	@grep -E '^(shell|artisan|tinker):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Database:$(RESET)"
	@grep -E '^(migrate|fresh|seed|rollback|db-reset):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Testing & Quality:$(RESET)"
	@grep -E '^(test|test-filter|pint|lint):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Cache:$(RESET)"
	@grep -E '^(cache-clear|config-clear|route-clear|view-clear|optimize):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Logs:$(RESET)"
	@grep -E '^(logs|logs-app|logs-nginx|logs-mysql):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Mobile (React Native):$(RESET)"
	@grep -E '^(mobile-install|mobile-start|mobile-android|mobile-ios|mobile-web):.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# ============================================
# Initial Setup
# ============================================

init: ## First-time project setup (build, install, migrate)
	@echo "$(GREEN)Starting initial setup...$(RESET)"
	@make env-setup
	@make build
	@make up
	@sleep 5
	@make composer-install
	@make key-generate
	@make migrate
	@make mobile-install
	@echo "$(GREEN)Setup complete! Run 'make up' to start development.$(RESET)"

env-setup: ## Copy .env.example to .env if .env doesn't exist
	@if [ ! -f backend/.env ]; then \
		echo "$(YELLOW)Creating .env file from .env.example...$(RESET)"; \
		cp backend/.env.example backend/.env; \
		echo "$(GREEN).env file created!$(RESET)"; \
	else \
		echo "$(CYAN).env file already exists, skipping...$(RESET)"; \
	fi

setup: ## Quick setup (up, install dependencies)
	@make up
	@sleep 3
	@make composer-install
	@make mobile-install

build: ## Build Docker containers
	docker-compose build

key-generate: ## Generate Laravel application key
	docker-compose exec app php artisan key:generate

# ============================================
# Docker Commands
# ============================================

up: ## Start all Docker containers
	docker-compose up -d
	@echo "$(GREEN)Containers started!$(RESET)"
	@echo "  Backend API: http://localhost:8080"
	@echo "  MySQL:       localhost:3306"
	@echo "  Redis:       localhost:6379"

down: ## Stop all Docker containers
	docker-compose down

restart: ## Restart all Docker containers
	docker-compose restart

ps: ## Show running containers
	docker-compose ps

clean: ## Stop containers and remove volumes
	docker-compose down -v --remove-orphans
	@echo "$(YELLOW)All containers and volumes removed.$(RESET)"

# ============================================
# Laravel Commands
# ============================================

shell: ## Enter the PHP container shell
	docker-compose exec app bash

artisan: ## Run artisan command (usage: make artisan cmd="migrate:status")
	docker-compose exec app php artisan $(cmd)

tinker: ## Open Laravel Tinker (REPL)
	docker-compose exec app php artisan tinker

# ============================================
# Database Commands
# ============================================

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

fresh: ## Fresh migration with seeds
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

rollback: ## Rollback last migration
	docker-compose exec app php artisan migrate:rollback

db-reset: ## Reset database (fresh + seed)
	@echo "$(YELLOW)Resetting database...$(RESET)"
	docker-compose exec app php artisan migrate:fresh --seed
	@echo "$(GREEN)Database reset complete!$(RESET)"

# ============================================
# Testing & Code Quality
# ============================================

test: ## Run all Laravel tests
	docker-compose exec app php artisan test

test-filter: ## Run filtered tests (usage: make test-filter name="UserTest")
	docker-compose exec app php artisan test --filter=$(name)

pint: ## Run Laravel Pint (code formatter)
	docker-compose exec app vendor/bin/pint

lint: ## Run Pint in dry-run mode (check only)
	docker-compose exec app vendor/bin/pint --test

# ============================================
# Cache Commands
# ============================================

cache-clear: ## Clear application cache
	docker-compose exec app php artisan cache:clear
	@echo "$(GREEN)Cache cleared!$(RESET)"

config-clear: ## Clear config cache
	docker-compose exec app php artisan config:clear

route-clear: ## Clear route cache
	docker-compose exec app php artisan route:clear

view-clear: ## Clear view cache
	docker-compose exec app php artisan view:clear

optimize: ## Optimize application (cache config, routes, views)
	docker-compose exec app php artisan optimize

optimize-clear: ## Clear all cached data
	docker-compose exec app php artisan optimize:clear
	@echo "$(GREEN)All caches cleared!$(RESET)"

# ============================================
# Logs
# ============================================

logs: ## View all Docker logs
	docker-compose logs -f

logs-app: ## View Laravel app logs
	docker-compose logs -f app

logs-nginx: ## View Nginx logs
	docker-compose logs -f nginx

logs-mysql: ## View MySQL logs
	docker-compose logs -f mysql

# ============================================
# Composer Commands
# ============================================

composer-install: ## Install composer dependencies
	docker-compose exec app composer install

composer-update: ## Update composer dependencies
	docker-compose exec app composer update

composer-require: ## Require a package (usage: make composer-require pkg="vendor/package")
	docker-compose exec app composer require $(pkg)

composer-require-dev: ## Require a dev package (usage: make composer-require-dev pkg="vendor/package")
	docker-compose exec app composer require --dev $(pkg)

# ============================================
# Mobile (React Native / Expo) Commands
# ============================================

mobile-install: ## Install mobile dependencies
	cd frontend && npm install

mobile-start: ## Start Expo development server
	cd frontend && npm start

mobile-android: ## Run on Android emulator/device
	cd frontend && npm run android

mobile-ios: ## Run on iOS simulator (macOS only)
	cd frontend && npm run ios

mobile-web: ## Run in web browser
	cd frontend && npm run web

mobile-lint: ## Run ESLint on mobile code
	cd frontend && npm run lint

# ============================================
# Shortcuts
# ============================================

m: migrate ## Shortcut for migrate
t: test ## Shortcut for test
s: shell ## Shortcut for shell
l: logs ## Shortcut for logs

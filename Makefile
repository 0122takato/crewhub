.PHONY: help build up down restart logs shell migrate fresh seed test

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker containers
	docker-compose build

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

restart: ## Restart Docker containers
	docker-compose restart

logs: ## View Docker logs
	docker-compose logs -f

shell: ## Enter the app container shell
	docker-compose exec app bash

migrate: ## Run Laravel migrations
	docker-compose exec app php artisan migrate

fresh: ## Fresh migration with seeds
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run Laravel seeders
	docker-compose exec app php artisan db:seed

test: ## Run Laravel tests
	docker-compose exec app php artisan test

composer-install: ## Install composer dependencies
	docker-compose exec app composer install

npm-install: ## Install npm dependencies for mobile
	docker-compose exec mobile npm install

mobile-start: ## Start Expo development server
	docker-compose exec mobile npx expo start

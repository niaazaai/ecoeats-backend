.PHONY: help install build up down restart shell composer test migrate seed fresh horizon pulse

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Initial setup - install dependencies and setup environment
	@echo "🚀 Setting up EcoEats backend..."
	@docker-compose run --rm app composer install
	@cp .env.example .env
	@docker-compose run --rm app php artisan key:generate
	@docker-compose run --rm app php artisan migrate --seed
	@echo "✅ Setup complete! Run 'make up' to start services."

build: ## Build Docker images
	docker-compose build

up: ## Start all services
	docker-compose up -d
	@echo "✅ Services started. Backend: http://localhost:8000"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo "📊 Horizon: http://localhost:8000/horizon"
	@echo "📈 Pulse: http://localhost:8000/pulse"

down: ## Stop all services
	docker-compose down

restart: ## Restart all services
	docker-compose restart

shell: ## Open shell in app container
	docker-compose exec app sh

composer: ## Run composer command (usage: make composer CMD="require package")
	docker-compose run --rm app composer $(CMD)

test: ## Run tests
	docker-compose run --rm app php artisan test

migrate: ## Run migrations
	docker-compose run --rm app php artisan migrate

seed: ## Run seeders
	docker-compose run --rm app php artisan db:seed

fresh: ## Fresh migration with seeding
	docker-compose run --rm app php artisan migrate:fresh --seed

horizon: ## Open Horizon dashboard
	@echo "📊 Horizon dashboard: http://localhost:8000/horizon"

pulse: ## Open Pulse dashboard
	@echo "📈 Pulse dashboard: http://localhost:8000/pulse"

logs: ## View logs
	docker-compose logs -f

queue-logs: ## View queue worker logs
	docker-compose logs -f queue

horizon-logs: ## View Horizon logs
	docker-compose logs -f horizon

scheduler-logs: ## View scheduler logs
	docker-compose logs -f scheduler


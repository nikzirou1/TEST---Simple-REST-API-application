## ─────────────────────────────────────────────────────────────────────────────
##  Book Library API (Laravel) — convenience commands
##  Usage: make <target>
## ─────────────────────────────────────────────────────────────────────────────

.PHONY: help install key migrate seed setup serve test docker-up docker-down docker-setup docker-test

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ── Local (without Docker) ────────────────────────────────────────────────────

install: ## Install PHP dependencies via Composer
	composer install

key: ## Generate the Laravel application key
	php artisan key:generate

migrate: ## Run database migrations
	php artisan migrate --force

seed: ## Load sample book data (seeders)
	php artisan db:seed --force

setup: install key migrate seed ## Full local setup in one command

serve: ## Start the built-in PHP server on port 8000
	php artisan serve

test: ## Run the PHPUnit test suite
	php artisan test --colors=always

# ── Docker ────────────────────────────────────────────────────────────────────

docker-up: ## Build images and start all containers
	docker compose up --build -d

docker-down: ## Stop and remove all containers
	docker compose down

docker-setup: ## Run migrations + seeders inside the running app container
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan db:seed --force

docker-test: ## Run PHPUnit inside the app container
	docker compose exec app php artisan test --colors=always

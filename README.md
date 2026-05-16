# Book Library API

A REST API built with **Laravel 11 / PHP 8.3** for managing a book library.  
Books can be created, listed (with filtering), retrieved, partially updated, and deleted.

---

## Tech Stack

| Layer    | Technology                                  |
|----------|---------------------------------------------|
| Language | PHP 8.3                                     |
| Framework | Laravel 11                                 |
| ORM      | Eloquent ORM + Laravel Migrations           |
| Database | MySQL 8 (Docker) / SQLite (tests)           |
| Docs     | L5-Swagger → Swagger UI                     |
| Tests    | PHPUnit 11 (via `php artisan test`)         |
| Container | Docker + Docker Compose + Nginx            |

---

## Book Model

| Field              | Type           | Description              |
|--------------------|----------------|--------------------------|
| `id`               | integer        | Auto-generated primary key |
| `title`            | string (255)   | Book title               |
| `publisher`        | string (255)   | Publishing company       |
| `author`           | string (255)   | Author's full name       |
| `genre`            | string (100)   | Literary genre           |
| `publication_date` | date (Y-m-d)   | First publication date   |
| `word_count`       | integer        | Total number of words    |
| `price`            | decimal (10,2) | Price in US Dollars      |

---

## API Endpoints

| Method   | Path              | Description                          |
|----------|-------------------|--------------------------------------|
| `GET`    | `/api/books`      | List all books (filterable)          |
| `POST`   | `/api/books`      | Create a new book                    |
| `GET`    | `/api/books/{id}` | Get a single book by ID              |
| `PATCH`  | `/api/books/{id}` | Partially update a book              |
| `DELETE` | `/api/books/{id}` | Delete a book                        |
| `GET`    | `/api/doc`        | Swagger UI (interactive docs)        |
| `GET`    | `/api/doc.json`   | OpenAPI 3.0 JSON spec                |

### Query filters for `GET /api/books`

| Parameter   | Description                           |
|-------------|---------------------------------------|
| `title`     | Partial, case-insensitive title match |
| `author`    | Partial, case-insensitive match       |
| `genre`     | Partial, case-insensitive match       |
| `publisher` | Partial, case-insensitive match       |

---

## Quick Start — Docker (recommended)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) with WSL2 enabled

### 1. Clone the repository
```bash
git clone https://github.com/<your-username>/book-library-api.git
cd book-library-api
```

### 2. Start all containers
```bash
docker compose up --build -d
```

### 3. Run migrations and load sample data
```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

### 4. Open the API

| URL                                   | What                  |
|---------------------------------------|-----------------------|
| http://localhost:8080/api/books       | JSON list of books    |
| http://localhost:8080/api/doc         | Swagger UI            |

### 5. Stop containers
```bash
docker compose down
```

---

## Quick Start — Local PHP (without Docker)

### Prerequisites
- PHP ≥ 8.3 (with `pdo_sqlite`, `pdo_mysql`, `mbstring`, `zip` extensions)
- [Composer](https://getcomposer.org/) ≥ 2

### 1. Install dependencies and generate key
```bash
composer install
php artisan key:generate
```

### 2. Configure SQLite (no MySQL needed locally)
```bash
# Edit .env — set DB_CONNECTION and DB_DATABASE:
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/book-library-api/database/database.sqlite

touch database/database.sqlite
```

### 3. Migrate and seed
```bash
php artisan migrate
php artisan db:seed
```

### 4. Start server
```bash
php artisan serve
# API at http://localhost:8000/api/books
# Swagger at http://localhost:8000/api/doc
```

---

## Running Tests

Tests use **SQLite in-memory** (configured in `phpunit.xml`) — no database container needed.

```bash
# Local
php artisan test --colors=always

# Docker
docker compose exec app php artisan test --colors=always
```

The test suite covers 20 cases across all endpoints:
- List (empty, populated, filters by title/author/genre/publisher)
- Create (success, missing fields, blank title, negative values, bad date format)
- Show (found, not found)
- Update (partial update, price change, not found, validation failures)
- Delete (success, subsequent 404, not found)
- Response shape (all required fields present)

---

## Example Requests

### Create a book
```bash
curl -X POST http://localhost:8080/api/books \
  -H "Content-Type: application/json" \
  -d '{
    "title":            "Dune",
    "publisher":        "Chilton Books",
    "author":           "Frank Herbert",
    "genre":            "Science Fiction",
    "publication_date": "1965-08-01",
    "word_count":       187000,
    "price":            15.99
  }'
```

### List books filtered by author
```bash
curl "http://localhost:8080/api/books?author=Herbert"
```

### Partially update a book
```bash
curl -X PATCH http://localhost:8080/api/books/1 \
  -H "Content-Type: application/json" \
  -d '{"price": 19.99}'
```

### Delete a book
```bash
curl -X DELETE http://localhost:8080/api/books/1
```

---

## Project Structure

```
book-library-api/
├── app/
│   ├── Http/Controllers/
│   │   └── BookController.php     ← 5 REST endpoints + @OA Swagger annotations
│   ├── Models/
│   │   └── Book.php               ← Eloquent model with casts
│   └── Providers/AppServiceProvider.php
├── bootstrap/app.php              ← Laravel 11 application bootstrap
├── config/l5-swagger.php          ← Swagger UI configuration
├── database/
│   ├── migrations/
│   │   └── 2024_01_01_..._create_books_table.php
│   └── seeders/
│       ├── BookSeeder.php         ← 5 classic books
│       └── DatabaseSeeder.php
├── docker/nginx/default.conf      ← Nginx vhost
├── routes/api.php                 ← apiResource('books', BookController)
├── tests/Feature/
│   └── BookTest.php               ← 20 PHPUnit functional tests
├── .env                           ← MySQL (Docker) configuration
├── Dockerfile                     ← PHP 8.3-FPM multi-stage (dev/prod)
├── docker-compose.yml             ← app + nginx + MySQL 8
├── Makefile                       ← convenience commands
└── phpunit.xml                    ← SQLite in-memory for tests
```

---

## License

MIT

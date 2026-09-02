# Health API

Health API is a Laravel 13 backend for healthcare workflows including users,
doctors, appointments, hospitals, medicines, pharmacies, blood donation, and
ambulance services.

## Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum for bearer-token authentication
- Scramble for OpenAPI documentation
- SQLite by default; MySQL and other Laravel database drivers are supported
- PHPUnit for feature and unit tests
- Vite and Tailwind CSS for frontend assets

## Requirements

- PHP 8.3 or newer with the extensions required by Laravel
- Composer
- Node.js and npm
- A database supported by Laravel

The default local configuration uses SQLite, database-backed sessions, cache,
and queues. Redis and mail settings are also available through `.env`.

## Installation

Clone the repository and enter the project directory:

```bash
git clone <repo-url>
cd health-api
```

Install dependencies and create the environment file:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

On Windows PowerShell, use this environment-file command instead:

```powershell
Copy-Item .env.example .env
```

Configure `DB_*`, `APP_URL`, mail, storage, and queue settings in `.env` as
needed. For the default SQLite setup, create the database file if it does not
exist, then run migrations:

```bash
php artisan migrate
```

The included seeders can be run with:

```bash
php artisan db:seed
```

`composer setup` is also available for a fresh environment. It installs PHP
and Node dependencies, creates `.env`, generates the application key, runs
migrations, and builds frontend assets. It does not run the seeders.

If profile images are served from the public disk, create the storage link:

```bash
php artisan storage:link
```

## Running Locally

Run the complete local development workflow with:

```bash
composer run dev
```

This starts the Laravel server, queue listener, log viewer, and Vite process.
To run services separately:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
npm run dev
```

The API is normally available at `http://127.0.0.1:8000`. The health endpoint
is `GET /up`.

## Architecture

The codebase is moving toward Clean Architecture, but it is currently hybrid:

```text
HTTP Request
    |
    +-- Form Request / controller validation
    |
    +-- API Controller
    |
    +-- Use Case and DTOs (where implemented)
    |
    +-- Repository interface and implementation (where implemented)
    |
    +-- Eloquent model and database
```

Important directories:

```text
app/
  Domain/             Business entities and repository contracts
  Application/        DTOs and use cases
  Infrastructure/     Eloquent models and repository implementations
  Http/               Controllers, requests, and API resources
  Providers/          Dependency injection bindings
database/             Migrations, factories, and seeders
tests/                Feature and unit tests
routes/api.php        Public and authenticated API routes
```

Repository and use-case layers are used most extensively by the user,
medicine, ambulance, blood donation, store, product, and stock modules. Some
older or smaller modules still use controllers and Eloquent directly. New
work should follow the layered path and gradually move existing business rules
out of controllers.

## API Overview

All API routes are prefixed with `/api`.

### Public routes

- `POST /api/register`
- `POST /api/login`
- `POST /api/auth/otp-send`
- `POST /api/auth/otp-verify`
- `POST /api/auth/reset-password`
- `GET /api/posts` and `GET /api/posts/{id}`
- `GET /api/doctors/public` and `GET /api/doctors/public/{id}`
- `GET /api/medicine-companies/public` and `GET /api/medicine-companies/public/{id}`
- `GET /api/medicines/public` and `GET /api/medicines/public/{id}`
- `GET /api/blood-donors/public` and `GET /api/blood-donors/public/{id}`
- `GET /api/ambulances/public` and `GET /api/ambulances/public/{id}`

### Authenticated modules

Send a Sanctum token with protected requests:

```http
Authorization: Bearer <token>
```

Authenticated routes currently cover:

- Profile, users, and phone lookup
- Doctors, education, professional experience, chambers, and schedules
- Hospitals
- Appointments and appointment prescriptions
- Posts, comments, and ratings
- Blood donor interest and donations
- Ambulances
- Stores, store products, and stock transactions

Use `php artisan route:list --path=api` for the authoritative route list. CRUD
modules generally use Laravel API resource conventions. Special workflow
routes include:

```text
GET   /api/my-appointments
GET   /api/appointments/upcoming
GET   /api/my-prescriptions
GET   /api/my-blood-donations
PATCH /api/blood-donors/interest
GET   /api/stores/{storeId}/stocks/summary
GET   /api/stores/{storeId}/products/{productId}/stocks
```

Public doctor listings support `designation`, `department`, `address`,
`search`, `random`, and `per_page` query parameters. Store product listings
support `search`, `is_active`, `low_stock`, and `per_page`.

### Authentication examples

Register or log in, then use the returned token for protected requests:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"identifier":"user@example.com","password":"password"}'
```

```bash
curl http://127.0.0.1:8000/api/my-appointments \
  -H "Authorization: Bearer <token>"
```

OTP delivery is currently configured for local development. The current
implementation returns the generated OTP in the response for testing; this
must be replaced with a real delivery provider and verified reset state before
production use.

## API Documentation

Scramble exposes interactive documentation at:

```text
http://127.0.0.1:8000/docs/api
```

The generated OpenAPI file is written to `storage/api-docs.json`. Keep
controller, request, and resource descriptions current so the generated
documentation remains useful.

## Testing And Quality

Run the full test suite:

```bash
composer test
```

Equivalent commands:

```bash
php artisan test
php artisan test --filter=StoreApiTest
```

Before opening a pull request, run:

```bash
php vendor/bin/pint --test
php artisan route:list --path=api
npm run build
composer test
```

Use `php vendor/bin/pint` to apply PHP formatting. There is currently no
configured JavaScript linter or static-analysis command.

## Development Standards

For a new feature:

1. Define the domain behavior and data constraints.
2. Add or update migrations, factories, and seeders where necessary.
3. Add a repository contract under `app/Domain/Interfaces` when persistence
   needs an abstraction.
4. Implement the use case and DTO under `app/Application`.
5. Add a repository implementation and Eloquent model under
   `app/Infrastructure/Persistence`.
6. Keep the controller focused on HTTP orchestration.
7. Validate input with a Form Request and shape output with an API Resource.
8. Bind interfaces in `AppServiceProvider`.
9. Add feature tests for the endpoint and unit tests for isolated business
   rules.
10. Update this README and Scramble metadata when the public API changes.

Prefer dependency injection, descriptive names, small classes, explicit
authorization, database transactions for multi-write operations, and
idempotent seeders. Do not place business rules, raw queries, or authorization
decisions in controllers when they can live in the owning application or
domain abstraction.

## Current Improvement Roadmap

- Move direct Eloquent workflows into use cases and repositories.
- Add policies and role-aware authorization to user, appointment, prescription,
  and hospital operations.
- Add focused tests for post visibility, OTP reset security, and authorization
  boundaries.
- Replace development OTP responses with email or SMS delivery.
- Define stock arithmetic and concurrency rules for every transaction type.
- Add static analysis and JavaScript linting to the CI quality gate.

## Useful Commands

```bash
php artisan tinker
php artisan migrate:fresh --seed
php artisan config:clear
php artisan cache:clear
php artisan route:list --path=api
php artisan scramble:export
```

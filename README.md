# Health API

Health API is a modular backend platform for healthcare services built with Laravel 13 and Clean Architecture principles. The project is designed to grow into a multi-domain system for patients, doctors, hospitals, ambulances, diagnostic centers, and pharmacy operations.

## Project Goals

- Build a scalable and maintainable API foundation
- Keep business logic independent from framework-specific code
- Support future modules without tightly coupling features
- Provide a clean structure for onboarding new developers quickly

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum for authentication
- Scramble for API documentation
- Vite + Tailwind for frontend assets
- PHPUnit for automated testing

## Architecture Overview

This project follows a layered Clean Architecture approach:

Request -> Controller -> UseCase -> Repository -> Model/Database

### Recommended folder structure

```text
app/
├── Domain/
│   ├── Entities/
│   └── Interfaces/
├── Application/
│   ├── DTOs/
│   └── UseCases/
├── Infrastructure/
│   └── Persistence/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── Providers/
```

## Development Principles

All contributors should follow these rules:

- Keep controllers thin and focused on request handling
- Put business logic in Use Cases
- Use Form Requests for validation
- Use DTOs for input/output transfer between layers
- Put repository interfaces in the Domain layer and implementations in Infrastructure
- Avoid mixing framework code with core business rules
- Prefer dependency injection over hard-coded dependencies
- Write tests for new features and bug fixes

## Getting Started

### 1. Clone and enter the project

```bash
git clone <repo-url>
cd health-api
```

### 2. Install PHP and Node dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database setup

```bash
php artisan migrate
```

### 5. Run the application

For local development, use:

```bash
composer run dev
```

Or run the backend and frontend separately:

```bash
php artisan serve
npm run dev
```

## Useful Commands

```bash
composer test
php artisan migrate
php artisan route:list
php artisan tinker
php artisan pint
```

## API Documentation

API documentation is generated automatically with Scramble.

Open the docs at:

```text
http://127.0.0.1:8000/docs/api
```

## Authentication

The current authentication flow uses Laravel Sanctum.

### Expected login payload example

```json
{
  "identifier": "email or username or phone",
  "password": "your-password"
}
```

### Auth endpoint notes

- Registration is handled in the auth controller at [app/Http/Controllers/Api/Auth/AuthController.php](app/Http/Controllers/Api/Auth/AuthController.php).
- A new user can register with name, email or phone, password, and password confirmation.
- At least one of email or phone must be provided.
- Profile fields such as gender, date_of_birth, profile_image, address, blood_group, and marital_status are not part of initial registration and should be updated later after login.
- The user update API is available at /api/users/{id} and supports the profile fields above.

## Coding Guidelines for Future Developers

### Feature development workflow

1. Create a feature branch from the main branch
2. Add or update the relevant Use Case, DTO, repository interface, and implementation
3. Keep controllers focused on HTTP concerns only
4. Add validation with Form Requests
5. Write tests before finalizing the feature
6. Run the relevant test suite before pushing changes

### Naming and structure

- Use descriptive names for modules and classes
- Follow Laravel naming conventions
- Keep each feature self-contained where possible
- Group related routes, controllers, requests, and use cases by domain/module

### Avoid

- Fat controllers
- Business logic inside controllers
- Direct database logic inside HTTP layer
- Repeated code across modules
- Large mixed-purpose classes

## Testing Expectations

Every new feature or bug fix should include tests when practical.

Run tests with:

```bash
composer test
```

## Module Roadmap

Planned or ongoing modules include:

- User / Patient management
- Doctor management
- Hospital management
- Ambulance services
- Diagnostic center management
- Pharmacy / medicine store workflows
- Blood donor services

## AI and Contributor Guidance

When working with AI tools or asking for implementation help, share this README and describe the module or feature you want to build. Prefer prompts such as:

> Implement the Hospital module using the existing Clean Architecture structure and follow the project conventions in this README.

## Final Note

This repository is intended to be a long-term, scalable, and maintainable platform. New features should be added in a way that preserves the project’s architecture and keeps the codebase easy for future developers to understand.

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

## Next-Level Developer Guidelines

Use this project structure when adding new modules:

- app/Domain/Entities for domain models
- app/Domain/Interfaces for repository contracts
- app/Application/DTOs for request/response objects
- app/Application/UseCases for business workflows
- app/Infrastructure/Persistence for repository implementations and Eloquent models
- app/Http/Controllers/Api for HTTP controllers and response resources

Best practices for new features:

- Add only one responsibility per class
- Preserve a single source of truth for business rules in Use Cases
- Keep API validation inside Form Requests or dedicated validation classes
- Use API Resources for consistent JSON output
- Register interface bindings in app/Providers/AppServiceProvider.php
- Keep routes in routes/api.php, and separate public APIs from authenticated routes
- Add seeders using idempotent statements (`updateOrInsert` or factories)
- Keep documentation updated alongside code changes
- Add tests for endpoints, use cases, and persistence logic

Use these commands for development workflow:

- `php artisan migrate --seed` to build schema and sample data
- `php artisan route:list` to verify route registration
- `composer test` to run automated tests
- `php artisan tinker` for quick database checks

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

## Doctor, Education, Hospital, and Appointment APIs

The API now supports authenticated profile modules for doctors, hospitals, and appointment scheduling:

### Doctor endpoints

- GET /api/doctors
- POST /api/doctors
- GET /api/doctors/{id}
- PUT /api/doctors/{id}
- DELETE /api/doctors/{id}

### Education endpoints

- GET /api/educations
- POST /api/educations
- GET /api/educations/{id}
- PUT /api/educations/{id}
- DELETE /api/educations/{id}

### Professional experience endpoints

- GET /api/professional-experiences
- POST /api/professional-experiences
- GET /api/professional-experiences/{id}
- PUT /api/professional-experiences/{id}
- DELETE /api/professional-experiences/{id}

### Doctor schedule endpoints

- GET /api/doctor-schedules
- POST /api/doctor-schedules
- GET /api/doctor-schedules/{id}
- PUT /api/doctor-schedules/{id}
- DELETE /api/doctor-schedules/{id}

### User endpoints

- GET /api/users
- POST /api/users
- GET /api/users/{id}
- PUT /api/users/{id}
- DELETE /api/users/{id}
- GET /api/users/phone/{phone}
- POST /api/users/phone/{phone}

### Hospital endpoints

- GET /api/hospitals
- POST /api/hospitals
- GET /api/hospitals/{id}
- PUT /api/hospitals/{id}
- DELETE /api/hospitals/{id}

### User phone lookup endpoints

- GET /api/users/phone/{phone}
- POST /api/users/phone/{phone}

### User phone lookup notes

- `GET /api/users/phone/{phone}` checks whether a user exists with that phone number.
- If the user exists, it returns their details.
- `POST /api/users/phone/{phone}` creates a new patient user when the phone number is not already registered.
- This supports doctor workflows where a doctor can enter a patient phone and continue to the next appointment step.
- These lookup routes are separate from standard `/api/users/{id}` to avoid route conflicts and clearly separate lookup vs. create behavior.

### Appointment endpoints

- GET /api/appointments — doctor sees their assigned appointments
- GET /api/my-appointments — patient sees own appointments
- POST /api/appointments
- GET /api/appointments/{id}
- PUT /api/appointments/{id}
- DELETE /api/appointments/{id}
- GET /api/appointments/upcoming — doctor sees upcoming assigned appointments

### Public doctor listing endpoint

- GET /api/doctors/public
- GET /api/doctors/public/{id}

Query parameters:
- `designation` => filter doctor title
- `department` => filter doctor specialization
- `address` => search doctor user address
- `search` => search across doctor name, title, specialization, and address
- `random` => `true` to randomize results
- `per_page` => pagination page size

This endpoint is public and does not require a bearer token.

### Public doctor details endpoint

- GET /api/doctors/public/{id}

This returns the selected doctor profile along with related chambers and available schedules in a nested structure. It is intended for patient-facing doctor detail pages.

### Prescription endpoints

- GET /api/appointment-prescriptions — doctor sees prescriptions they wrote
- GET /api/my-prescriptions — patient sees own prescriptions
- POST /api/appointment-prescriptions
- GET /api/appointment-prescriptions/{id}
- PUT /api/appointment-prescriptions/{id}
- DELETE /api/appointment-prescriptions/{id}

### Prescription endpoint notes

- Prescriptions are created by the doctor after appointment consultation.
- Each prescription links `doctor_user_id`, `patient_user_id`, `appointment_id`, `schedule_id`, `chamber_id`, and `appointment_type`.
- Doctors record vitals and symptoms, including blood pressure, smoking status, sugar level, and general symptoms.
- The `medicines` field is stored as JSON and supports medicine schedule strings like `1+0+1`, `1+0+0`, or `0+1+0`.
- The prescription also captures `diagnosis`, `prescription_date`, and optional `notes`.

### Current implementation notes

- All profile-related and scheduling modules are protected by Sanctum authentication.
- Hospitals are associated with the authenticated user and can be managed from the hospital resource.
- Doctor schedules include `consultation_fee`, `max_patients`, and optional chamber association.
- Appointments link a patient, doctor, optional hospital or chamber, and optional doctor schedule.
- The appointment workflow supports consultation fee (`consultation_fee`), discount, appointment type, status, date, and optional time.
- Controllers use the current request validation pattern; future improvements may move validation into dedicated Form Requests for reusability.
- New modules should follow the existing Laravel convention of routes, controllers, models, and tests in the same domain structure.

## Developer Guidelines for Public Doctor Listing

- The public doctor listing endpoint is `GET /api/doctors/public`.
- This endpoint is intentionally public and does not require a bearer token.
- It is implemented in `app/Http/Controllers/Api/DoctorController.php` as `publicIndex`.
- The endpoint supports optional filters:
  - `designation` for doctor title
  - `department` for doctor specialization
  - `address` for doctor user address
  - `search` for doctor name, title, specialization, or address
  - `random=true` to randomize results
  - `per_page` for pagination size
- The response includes `data` and pagination metadata for frontend listing.
- Use this endpoint for homepage doctor lists and the doctor directory page.
- If new homepage fields are needed later, extend the controller query and include them in the payload carefully.

### Recommended approach for future changes

- Keep the public listing endpoint separate from authenticated doctor profile routes.
- Add new filters in the controller using query validation and `where` clauses.
- Keep the controller action focused on request handling and query building.
- Avoid adding authentication requirements to this endpoint unless the feature explicitly needs it.

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
4. Add validation with Form Requests when the feature grows beyond a simple CRUD flow
5. Write tests before finalizing the feature
6. Run the relevant test suite before pushing changes
7. Document new endpoints and module behavior in this README

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

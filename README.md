# 📘 Health API

A scalable, modular, and enterprise-ready API built with **Laravel 13** using **Clean Architecture** principles.

---

## 🚀 Project Overview

**Health API** is a large-scale backend system designed to support multiple domains and modules such as:

* User Management (all user are patient)
* Doctor
* Hospital
* Medicine Store
* Ambulance

## Project Summary 
 A web based project api. This project will like - User can browse a website and can see the doctor, ambulance, blood donor, hospital, Diagonostic center. brief below - 
 * Doctor: user can find a doctor and see the doctor meeting schedule. and join into a available schedule after that can visit the doctor. and the doctor can assign the test list for a patient. and can see the patient test report and can create a prescription. doctor can see the old prescription and reports of a patient etc. 
 * Patient: user can create a patient profile. can join a doctor meeting schedule, can see him/her prescription, diagonos reports etc. 
 * Ambulance: user can add ambulance for rent. and patient can see the information of ambulance to rent. 
 * Hospital: user can create hospital as owner. can assign employee, room, bed, ot, doctor etc. (total hospital management)
 * Medicine Store: User can create a medicine store. can galleriesed his shop product price from system provided medicine list. can sale, can manage store and stock. can manage order, can manage due etc POS of medicine shop features. 
 * Diagonstic Center: user can create diagonostic center profile. can update the price list of entire test service of this centre. can manage order, can update the patient simple collection, reports, employees etc. 

> This is a project for entire medical system Sass Type project. 

---

## 🧱 Architecture

This project follows **Clean Architecture (Layered Approach)**:

```
Request → Controller → UseCase → Repository → Model (DB)
```

### 📂 Folder Structure

```
app/
├── Domain/
│   ├── Entities/
│   ├── Interfaces/
│
├── Application/
│   ├── UseCases/
│   ├── DTOs/
│
├── Infrastructure/
│   ├── Persistence/
│   │   ├── Models/
│   │   ├── Repositories/
│
├── Http/
│   ├── Controllers/
│   ├── Requests/
│
├── Providers/
```

---

## 🧠 Core Principles

* ❌ No business logic in Controllers
* ✅ Business logic inside UseCases
* ❌ Direct Eloquent usage in Controller
* ✅ Repository pattern enforced
* ✅ Dependency Injection used
* ✅ Scalable & testable structure

---

## 🔐 Authentication System

### ✔ Features Implemented

* Login with:

  * Email
  * Username
  * Phone
* Password-based authentication
* Token-based authentication using **Laravel Sanctum**

### 📥 Login Request Example

```json
{
  "identifier": "email বা username বা phone",
  "password": "123456"
}
```

---

## 🧩 Modules (Planned & Ongoing)

| Module       | Status        |
| ------------ | ------------- |
| User         | ✅ Basic Ready |
| Doctor | ⏳ Planned     |
| Patient  | ⏳ Planned     |
| Hospital | ⏳ Planned     |
| Diagonstic Center | ⏳ Planned     |
| Ambulance service | ⏳ Planned     |
| Blood Donor | ⏳ Planned     |

---

## 📄 API Documentation

### ⚡ Tool Used: Scramble (Auto Documentation)

* No manual annotation required
* Auto-generates API docs from:

  * Routes
  * Controllers
  * Form Requests

### 🔗 Access Docs

```
http://127.0.0.1:8000/docs/api
```

---

## ❌ Removed Tools

* Swagger (L5-Swagger) ❌ removed
* Reason:

  * Requires manual annotation
  * Not suitable for fast development

---

## ⚙️ Installation Guide

### 1. Clone Project

```bash
git clone <repo-url>
cd health-api
```

---

### 2. Install Dependencies

```bash
composer install
```

---

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

---

### 4. Database Setup

```bash
php artisan migrate
```

---

### 5. Install Sanctum

```bash
composer require laravel/sanctum
php artisan migrate
```

---

### 6. Run Server

```bash
php artisan serve
```

---

## 🔌 Service Container Binding

Example:

```php
$this->app->bind(
    UserRepository::class,
    UserRepositoryImpl::class
);
```

---

## 🧪 API Testing

Use:

* Scramble UI (Recommended)
* OR Postman (Optional)

---

## 🚀 Development Guidelines

### ✔ Must Follow

* Use **UseCase per feature**
* Use **FormRequest for validation**
* Use **Repository Interface**
* Keep **Domain independent**

---

### ❌ Avoid

* Fat Controllers
* Direct DB calls inside Controller
* Mixing business logic with framework code

---

## 🔮 Future Roadmap

* Role-based Access Control (RBAC)
* Multi-tenant support
* Microservice-ready structure
* Queue & Job system
* Notification system (Email/SMS)
* Audit logs
* API versioning

---

## 🧠 AI Usage Instruction (IMPORTANT)

When using AI tools (ChatGPT, etc.):

👉 Always provide this README.md
👉 Ask for **step-by-step implementation based on Clean Architecture**

### Example Prompt:

> "Based on this README, implement Organization Module with Clean Architecture"

---

## 🎯 Goal

To build a **scalable, maintainable, enterprise-grade API system** that can support multiple domains and grow over time.

---

## 👨‍💻 Author

Islam Hossain
Full Stack Developer (Laravel + .NET + React)

---

## ⭐ Final Note

This project is designed for:

* Long-term scalability
* Clean and maintainable codebase
* Real-world enterprise usage

---

🚀 Happy Coding!

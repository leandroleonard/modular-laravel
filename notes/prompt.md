Act as a Senior Laravel Architect. Generate a complete, modular Laravel 11+ project structure implementing Domain-Driven Design (DDD) with the Service-Repository pattern. The application must serve both a JSON API and Blade views while sharing the same business logic.


## 1. Directory Structure & Autoloading
Restructure the `app/` directory into three distinct layers. Update `composer.json` to autoload these new namespaces:
- `Domain/`: Pure business logic (Entities, Value Objects, Repository Interfaces, Domain Services). Must NOT depend on Laravel framework classes (no Eloquent, no Request).
- `Application/`: Use Cases, DTOs, and Response builders. Orchestrates the Domain layer.
- `Infrastructure/`: Framework specifics (Eloquent Models, Repository Implementations, Controllers, Middleware, Providers).

Simulate a user CRUD, folowwind the example structure to implement:
app/
├── Domain/
│   └── User/
│       ├── Entities/User.php
│       ├── ValueObjects/Email.php
│       ├── Repositories/UserRepositoryInterface.php
│       └── Services/UserAuthService.php
├── Application/
│   └── User/
│       ├── DTOs/LoginDTO.php
│       ├── UseCases/AuthenticateUser.php
│       └── Responses/ApiResponseTrait.php
└── Infrastructure/
    ├── Database/Eloquent/UserModel.php
    ├── Database/Repositories/EloquentUserRepository.php
    ├── Http/Controllers/Api/UserController.php
    ├── Http/Controllers/Web/UserController.php
    └── Providers/RepositoryServiceProvider.php

## 2. Core Requirements

### A. Authentication (Dual Guard)
- Implement a unified authentication flow in `Domain/User/Services`.
- Configure `config/auth.php` with two guards:
  1. `web`: Session-based for Blade views.
  2. `api`: Sanctum token-based for API.
- The `AuthenticateUser` Use Case should accept a DTO and return a User Entity. The Infrastructure layer handles the actual login/token creation based on the request type.

### B. Standardized API Responses
- Create `Application/Responses/ApiResponseTrait`.
- All API controllers must use this trait to return a consistent JSON structure:
  ```json
  {
    "status": true,
    "message": "Operation successful",
    "data": { ... }
  }
  ```
- Include methods for `success()`, `error()`, and `validationError()`.

### C. Service-Repository Pattern
- Define `UserRepositoryInterface` in the Domain layer.
- Implement `EloquentUserRepository` in Infrastructure, injecting the Eloquent Model there.
- Bind the interface to the implementation in `RepositoryServiceProvider`.
- Inject the Interface into the Domain Service, never the Eloquent model.

### D. Modular Controllers
- Create two controllers for the "User" module:
  1. `Infrastructure/Http/Controllers/Api/UserController`: Returns JSON using the Trait.
  2. `Infrastructure/Http/Controllers/Web/UserController`: Returns `view()` with data for Blade.
- Both controllers must call the SAME `Application/UseCases/AuthenticateUser` class.

## 3. Deliverables
1. The updated `composer.json` with PSR-4 autoload paths.
2. The code for the `User` entity, `UserRepositoryInterface`, and `EloquentUserRepository`.
3. The `AuthenticateUser` Use Case and `LoginDTO`.
4. The `ApiResponseTrait` implementation.
5. Both API and Web Controllers demonstrating the shared logic.
6. The `RouteServiceProvider` or route file snippets showing how to route `/api/login` and `/login` to the respective controllers.

Ensure strict separation of concerns: The Domain layer must not know about HTTP, Eloquent, or JSON.
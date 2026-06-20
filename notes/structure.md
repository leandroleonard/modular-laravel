app/
├── Domain/
│   └── User/
│       ├── Entities/
│       │   └── User.php
│       ├── Exceptions/
│       │   └── InvalidCredentialsException.php
│       ├── Repositories/
│       │   └── UserRepositoryInterface.php
│       ├── Services/
│       │   └── UserAuthService.php
│       └── ValueObjects/
│           └── Email.php
│
├── Application/
│   ├── Responses/
│   │   └── ApiResponseTrait.php
│   └── User/
│       ├── DTOs/
│       │   └── LoginDTO.php
│       └── UseCases/
│           └── AuthenticateUser.php
│
└── Infrastructure/
    ├── Database/
    │   ├── Eloquent/
    │   │   └── UserModel.php
    │   └── Repositories/
    │       └── EloquentUserRepository.php
    ├── Http/
    │   └── Controllers/
    │       ├── Api/
    │       │   └── UserController.php
    │       └── Web/
    │           └── UserController.php
    └── Providers/
        └── RepositoryServiceProvider.php
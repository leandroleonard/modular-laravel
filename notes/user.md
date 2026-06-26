app/
├── Domain/
│   └── User/
│       ├── Entities/
│       │   └── User.php
│       ├── Exceptions/
│       │   ├── InvalidCredentialsException.php
│       │   └── UnauthorizedException.php
│       ├── Repositories/
│       │   └── UserRepositoryInterface.php
│       ├── Services/
│       │   └── UserAuthService.php
│       └── ValueObjects/
│           ├── Email.php
│           ├── HashedPassword.php
│           └── Role.php
│
├── Application/
│   ├── Responses/
│   │   └── ApiResponseTrait.php
│   └── User/
│       ├── DTOs/
│       │   ├── LoginDTO.php
│       │   ├── RegisterDTO.php
│       │   └── RecoverPasswordDTO.php
│       └── UseCases/
│           ├── AuthenticateUser.php
│           ├── RegisterUser.php
│           └── RecoverPassword.php
│
└── Infrastructure/
    ├── Database/
    │   ├── Eloquent/
    │   │   ├── UserModel.php
    │   │   └── RoleModel.php
    │   └── Repositories/
    │       └── EloquentUserRepository.php
    ├── Http/
    │   └── Controllers/
    │       ├── Api/
    │       │   └── AuthController.php
    │       └── Web/
    │           └── AuthController.php
    └── Providers/
        └── AuthRepositoryServiceProvider.php
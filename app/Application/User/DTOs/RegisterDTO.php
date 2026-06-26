<?php

namespace App\Application\User\DTOs;
use App\Domain\User\ValueObjects\Role;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public Role $role = Role::USER,
    ){}
}
<?php

namespace App\Application\User\DTOs;

readonly class RecoverPasswordDTO
{
    public function __construct(
        public string $email
    ){}
}
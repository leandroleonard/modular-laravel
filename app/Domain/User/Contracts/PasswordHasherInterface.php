<?php

namespace App\Domain\User\Contracts;
use App\Domain\User\ValueObjects\HashedPassword;

interface PasswordHasherInterface 
{
    public function hash(string $plainPassword): HashedPassword;
    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool;
}
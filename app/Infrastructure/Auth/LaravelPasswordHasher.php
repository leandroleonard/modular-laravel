<?php

namespace App\Infrastructure\Auth;
use App\Domain\User\Contracts\PasswordHasherInterface;
use App\Domain\User\ValueObjects\HashedPassword;
use Illuminate\Support\Facades\Hash;

class LaravelPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): HashedPassword
    {
        $hashed = Hash::make($plainPassword);

        return new HashedPassword($hashed);
    }

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword->value);
    }
}
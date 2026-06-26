<?php

namespace App\Domain\User\ValueObjects;
use InvalidArgumentException;

readonly class Email
{
    public string $value;

    public function __construct(string $email)
    {
        $trimmed = trim($email);

        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: [{$email}].");
        }

        $this->value = strtolower($trimmed);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
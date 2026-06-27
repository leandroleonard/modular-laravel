<?php

namespace App\Domain\User\ValueObjects;
use InvalidArgumentException;
readonly class HashedPassword
{
    public function __construct(public string $value){
        if(trim($value) === ""){
            throw new InvalidArgumentException("Hashed password cannot be empty");
        }
    }
}
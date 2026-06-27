<?php

namespace App\Domain\User\Exceptions;
use Exception;
class InvalidCredentialsException extends Exception
{
    protected $message = "The provided credentials do not match our records.";
}
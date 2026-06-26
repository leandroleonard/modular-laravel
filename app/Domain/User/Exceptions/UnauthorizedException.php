<?php

namespace App\Domain\User\Exceptions;
use Exception;
class UnauthorizedException extends Exception
{
    protected $message = "You do not have permission to perform this action.";
}
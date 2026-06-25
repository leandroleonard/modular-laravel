<?php

namespace App\Domain\Notification\ValueObjects;

use InvalidArgumentException;
readonly class Recipient
{
    /**
     * Summary of __construct
     * @param string $target Email address, phone number, or User ID
     * @param mixed $name Recipient name
     * @param mixed $id System identification
     * @throws InvalidArgumentException
     */
    public function __construct(
        public string $target,
        public ?string $name = null,
        public ?string $id = null,
    ){
        if(empty($target))
            throw new InvalidArgumentException("Recipient target cannot be empty!");
    }
}
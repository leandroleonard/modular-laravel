<?php

namespace App\Domain\User\Contracts;

interface EventDispatcherInterface
{
    /**
     * Dispatch a domain event to all registered listeners.
     */
    public function dispatch(object $event): void;
}
<?php

namespace App\Infrastructure\Auth;

use App\Domain\User\Contracts\EventDispatcherInterface;
use Illuminate\Support\Facades\Event;

class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        Event::dispatch($event);
    }
}
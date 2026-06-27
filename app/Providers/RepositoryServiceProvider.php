<?php

namespace App\Providers;

use App\Domain\User\Contracts\EventDispatcherInterface;
use App\Domain\User\Contracts\PasswordHasherInterface;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\Auth\LaravelEventDispatcher;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Infrastructure\User\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            PasswordHasherInterface::class,
            LaravelPasswordHasher::class
        );

        $this->app->bind(
            EventDispatcherInterface::class,
            LaravelEventDispatcher::class,
        );
    }
}
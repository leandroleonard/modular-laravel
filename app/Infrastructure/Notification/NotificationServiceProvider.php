<?php

namespace App\Infrastructure\Notification;

use App\Domain\Notification\Contracts\ChannelResolverInterface;
use App\Domain\Notification\Contracts\NotificationQueueDispatcherInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Infrastructure\Notification\Persistence\EloquentNotificationRepository;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Domain Repositories
        $this->app->bind(
            NotificationRepositoryInterface::class,
            EloquentNotificationRepository::class
        );

        // Bind Channel Resolver
        $this->app->bind(
            ChannelResolverInterface::class,
            ChannelResolver::class
        );

        // Bind Queue Dispatcher Gateway
        $this->app->bind(
            NotificationQueueDispatcherInterface::class,
            LaravelQueueNotificationDispatcher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}

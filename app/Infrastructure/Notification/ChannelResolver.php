<?php

namespace App\Infrastructure\Notification;

use App\Domain\Notification\Contracts\ChannelProviderInterface;
use App\Domain\Notification\Contracts\ChannelResolverInterface;
use App\Domain\Notification\ValueObjects\ChannelType;
use App\Infrastructure\Notification\Providers\DatabaseChannelProvider;
use App\Infrastructure\Notification\Providers\EmailChannelProvider;
use App\Infrastructure\Notification\Providers\SmsChannelProvider;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

class ChannelResolver implements ChannelResolverInterface
{
    /**
     * Map of ChannelType string values to concrete provider class names.
     */
    private array $providerMap = [
        ChannelType::EMAIL->value => EmailChannelProvider::class,
        ChannelType::SMS->value => SmsChannelProvider::class,
        ChannelType::DATABASE->value => DatabaseChannelProvider::class,
    ];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function resolve(ChannelType $channelType): ChannelProviderInterface
    {
        $providerClass = $this->providerMap[$channelType->value] ?? null;

        if ($providerClass === null) {
            throw new RuntimeException("No channel provider registered for channel type [{$channelType->value}].");
        }

        return $this->container->make($providerClass);
    }
}

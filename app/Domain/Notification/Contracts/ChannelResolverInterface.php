<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\ValueObjects\ChannelType;
use RuntimeException;

interface ChannelResolverInterface
{
    /**
     * Resolve the appropriate provider for the given channel type.
     * @throws RuntimeException if provider is not registered.
     */public function resolve(ChannelType $channelType): ChannelProviderInterface;
}
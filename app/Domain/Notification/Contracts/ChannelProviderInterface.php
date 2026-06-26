<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\ChannelType;

interface ChannelProviderInterface
{
    /**
     * The channel type this provider supports.
     * @return ChannelType
     */
    public function supports(): ChannelType;

    /**
     * Send the notification message through this channel.
     * Must throw an exception if sending fails.
     * @return void
     */
    public function send(NotificationMessage $message): void;
}
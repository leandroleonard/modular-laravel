<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Entities\NotificationMessage;

interface NotificationQueueDispatcherInterface
{
    /**
     * Push the notification message onto the asynchronous message queue.
     */
    public function dispatch(NotificationMessage $message): void;
}
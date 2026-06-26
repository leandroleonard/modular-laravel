<?php

namespace App\Infrastructure\Notification;

use App\Domain\Notification\Contracts\NotificationQueueDispatcherInterface;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Infrastructure\Notification\Jobs\SendNotificationJob;
use RuntimeException;

class LaravelQueueNotificationDispatcher implements NotificationQueueDispatcherInterface
{
    public function dispatch(NotificationMessage $message): void
    {
        $id = $message->getId();

        if ($id === null) {
            throw new RuntimeException('Cannot queue an unpersisted notification message. Message ID is missing.');
        }

        SendNotificationJob::dispatch($id);
    }
}
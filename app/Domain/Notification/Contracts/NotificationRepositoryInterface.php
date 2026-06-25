<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Entities\NotificationMessage;

interface NotificationRepositoryInterface
{
    public function saved(NotificationMessage $notification): NotificationMessage;

    public function fingById(string $id): ?NotificationMessage;
}
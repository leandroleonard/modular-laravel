<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Entities\NotificationMessage;

interface NotificationRepositoryInterface
{
    public function save(NotificationMessage $notification): NotificationMessage;

    public function findById(string $id): ?NotificationMessage;
    public function findAll(?string $userId = null, array $filters = []): array;
}
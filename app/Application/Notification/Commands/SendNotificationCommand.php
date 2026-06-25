<?php

declare(strict_types=1);

namespace App\Application\Notification\Commands;

use App\Domain\Notification\ValueObjects\ChannelType;

readonly class SendNotificationCommand
{
    public function __construct(
        public string $target,
        public string $subject,
        public string $body,
        public ChannelType $channel,
        public ?string $recipientName = null,
        public ?int $userId = null,
        public array $payload = [],
        public bool $shouldPersist = true,
        public bool $async = false, // Flag to decide whether to push to message queue
    ) {
    }
}

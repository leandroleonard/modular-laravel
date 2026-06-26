<?php

namespace App\Domain\Notification\ValueObjects;

readonly class NotificationContent
{
    /**
     * Summary of __construct
     * @param string $subject The subject of notification
     * @param string $body The notification content
     * @param array $payload Optional extra information
     */
    public function __construct(
        public string $subject,
        public string $body,
        public array $payload = []
    ){}
}
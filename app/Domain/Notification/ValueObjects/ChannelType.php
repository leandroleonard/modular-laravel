<?php

namespace App\Domain\Notification\ValueObjects;

enum ChannelType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case DATABASE = 'database';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::DATABASE => 'In-App Database',
        };
    }
}
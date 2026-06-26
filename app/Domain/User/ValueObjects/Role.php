<?php

namespace App\Domain\User\ValueObjects;

enum Role: string
{
    case ADMIN = "admin";
    case MANAGER = "manager";
    case USER = "user";
    case GUEST = "guest";

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => ['manage_users', 'view_reports', 'edit_content', 'delete_content'],
            self::MANAGER => ['view_reports', 'edit_content'],
            self::USER => ['view_content'],
            self::GUEST => ['view_content'],
        };
    }
}
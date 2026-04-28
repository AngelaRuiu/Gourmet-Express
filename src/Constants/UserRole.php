<?php

namespace App\Constants;

enum UserRole: string
{
    case ADMIN   = 'admin';
    case MANAGER = 'manager';
    case STAFF   = 'staff';

    public function canAccessAdmin(): bool
    {
        return match($this) {
            self::ADMIN, self::MANAGER => true,
            self::STAFF                => false,
        };
    }

    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }
}
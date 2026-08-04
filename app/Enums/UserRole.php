<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Vendor = 'vendor';
    case Admin = 'admin';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Vendor => 'Vendor',
            self::Admin => 'Admin',
            self::Staff => 'Staff',
        };
    }
}

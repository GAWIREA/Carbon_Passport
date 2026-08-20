<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Seller = 'seller';
    case Admin = 'admin';

    /**
     * Human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::User => 'Karyawan',
            UserRole::Seller => 'Seller',
            UserRole::Admin => 'Admin HR',
        };
    }

    /**
     * The named route for the role's dashboard.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            UserRole::User => 'user.dashboard',
            UserRole::Seller => 'seller.dashboard',
            UserRole::Admin => 'admin.dashboard',
        };
    }
}

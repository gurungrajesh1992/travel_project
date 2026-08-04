<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * One Spatie permission per admin module (see requirement doc §6).
     * Coarse-grained on purpose for this pass — split per action
     * (create/edit/delete) later if a module needs it.
     */
    private const PERMISSIONS = [
        'manage destinations', 'manage categories', 'manage difficulty levels',
        'manage tours', 'manage coupons', 'manage guides', 'manage customers',
        'manage inquiries', 'manage reviews', 'manage bookings', 'view reports',
        'manage roles', 'manage blogs', 'manage pages', 'manage gallery',
        'manage faqs', 'manage settings', 'manage newsletter',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(self::PERMISSIONS);

        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'manage tours', 'manage coupons', 'manage guides',
            'manage inquiries', 'manage reviews', 'manage bookings', 'view reports',
        ]);

        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@travel-tour.test'],
            [
                'name' => 'Site Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@travel-tour.test'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => UserRole::Customer,
                'email_verified_at' => now(),
            ]
        );
        $customerUser->assignRole('customer');
    }
}

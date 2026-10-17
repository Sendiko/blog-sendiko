<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.name');
        $email = config('admin.email');
        $password = config('admin.password');

        if (! is_string($name) || trim($name) === '') {
            throw new RuntimeException('Set ADMIN_NAME to a non-empty name before seeding the admin user.');
        }

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException('Set ADMIN_EMAIL to a valid email address before seeding the admin user.');
        }

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_PASSWORD to a password of at least 12 characters before seeding the admin user.');
        }

        User::query()->firstOrNew(['email' => $email])
            ->forceFill([
                'name' => $name,
                'password' => $password,
                'email_verified_at' => now(),
            ])
            ->save();
    }
}

<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Hash;

test('the admin seeder creates a verified admin and safely updates it on repeat runs', function () {
    config([
        'admin.name' => 'Blog Admin',
        'admin.email' => 'owner@example.com',
        'admin.password' => 'a-long-test-password',
    ]);

    $seeder = app(AdminUserSeeder::class);
    $seeder->run();
    $seeder->run();

    $this->assertDatabaseCount('users', 1);
    $admin = User::query()->where('email', 'owner@example.com')->firstOrFail();

    expect($admin->name)->toBe('Blog Admin')
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and(Hash::check('a-long-test-password', $admin->password))->toBeTrue();
});

test('the admin seeder requires an explicitly configured strong password', function () {
    config([
        'admin.name' => 'Blog Admin',
        'admin.email' => 'owner@example.com',
        'admin.password' => null,
    ]);

    expect(fn () => app(AdminUserSeeder::class)->run())
        ->toThrow(RuntimeException::class, 'Set ADMIN_PASSWORD');

    $this->assertDatabaseCount('users', 0);
});

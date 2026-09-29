<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ownerPassword = config('auth.owner_password') ?: env('OWNER_PASSWORD');

        if (! $ownerPassword) {
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@diskominfo.go.id'],
            [
                'name' => 'Admin Diskominfo',
                'password' => Hash::make($ownerPassword),
                'is_active' => true,
            ]
        );
    }
}

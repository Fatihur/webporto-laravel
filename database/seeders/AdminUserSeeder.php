<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Skipping AdminUserSeeder: set ADMIN_EMAIL and ADMIN_PASSWORD in environment.');

            return;
        }

        User::updateOrCreate([
            'email' => $email,
        ], [
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
    }
}

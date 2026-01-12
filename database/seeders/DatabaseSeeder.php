<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Categories, Authors, Publishers first
        $this->call([
            CategorySeeder::class,
            AuthorSeeder::class,
            PublisherSeeder::class,
        ]);

        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Sample User
        $user = User::create([
            'name' => 'User Demo',
            'email' => 'user@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create Member for Sample User
        Member::create([
            'user_id' => $user->id,
            'member_number' => 'MBR-2026-0001',
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia',
            'join_date' => now(),
            'status' => 'active',
        ]);
    }
}

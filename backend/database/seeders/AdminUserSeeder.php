<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user with ID = 1
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'System Administrator',
                'email' => 'admin@email.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create a sample moderator user
        User::updateOrCreate(
            ['email' => 'moderator@email.com'],
            [
                'name' => 'Sample Moderator',
                'email' => 'moderator@email.com',
                'password' => Hash::make('password'),
                'role' => 'moderator',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Admin and moderator users created successfully!');
        $this->command->info('📧 Admin credentials: admin@email.com / password');
        $this->command->info('📧 Moderator credentials: moderator@email.com / password');
    }
}

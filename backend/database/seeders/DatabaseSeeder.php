<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        
        // Seed users first
        $this->command->info('👥 Seeding users...');
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Create additional moderator users for testing
        $this->command->info('👤 Creating additional test users...');
        User::factory()
            ->count(3)
            ->moderator()
            ->create();

        // Seed products
        $this->command->info('📦 Seeding products...');
        $this->call([
            ProductSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('🔑 Test Credentials:');
        $this->command->info('  Admin: admin@email.com / password');
        $this->command->info('  Moderator: moderator@email.com / password');
        $this->command->info('');
        $this->command->info('🧪 Ready for stakeholder testing!');
    }
}

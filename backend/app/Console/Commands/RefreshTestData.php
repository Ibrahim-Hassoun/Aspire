<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:refresh {--fresh : Run fresh migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh database with test data for stakeholder testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Refreshing test data for stakeholder testing...');

        if ($this->option('fresh')) {
            $this->info('🗃️ Running fresh migrations...');
            $this->call('migrate:fresh');
        } else {
            $this->info('🔄 Rolling back and re-running migrations...');
            $this->call('migrate:refresh');
        }

        $this->info('🌱 Seeding database with test data...');
        $this->call('db:seed');

        $this->info('');
        $this->info('✅ Test data refresh completed!');
        $this->info('');
        $this->info('🔑 Login Credentials:');
        $this->info('  👑 Admin: admin@email.com / password');
        $this->info('  👤 Moderator: moderator@email.com / password');
        $this->info('');
        $this->info('🎯 Test Features:');
        $this->info('  ✓ Role-based access control (admin can delete, moderator cannot)');
        $this->info('  ✓ Product CRUD operations');
        $this->info('  ✓ Search and filtering');
        $this->info('  ✓ Inventory statistics');
        $this->info('  ✓ Low stock alerts');
        $this->info('  ✓ Chatbot integration');
        $this->info('');
        $this->info('📊 Database contains:');
        
        // Get counts
        $totalProducts = \App\Models\Product::count();
        $totalUsers = \App\Models\User::count();
        $categories = \App\Models\Product::distinct()->count('category');
        
        $this->info("  📦 {$totalProducts} products across {$categories} categories");
        $this->info("  👥 {$totalUsers} users (1 admin, " . ($totalUsers - 1) . " moderators)");
        
        $this->info('');
        $this->info('🚀 Ready for testing!');

        return Command::SUCCESS;
    }
}

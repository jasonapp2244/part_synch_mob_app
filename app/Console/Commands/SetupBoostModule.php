<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\BoostPackage;
use App\Models\BoostPosition;
use App\Models\VendorBoost;
use App\Models\BoostedProduct;
use App\Models\Product;
use App\Models\User;

class SetupBoostModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:setup {--test : Run module tests after setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Boost Module: Run migrations, seeders, and optionally test the module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Boost Module Setup...');
        $this->newLine();

        // Step 1: Run Migrations
        $this->info('📦 Step 1: Running Migrations...');
        $this->line('   Running: php artisan migrate');
        
        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->info('   ✅ Migrations completed successfully!');
        } catch (\Exception $e) {
            $this->error('   ❌ Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        $this->newLine();

        // Step 2: Run Seeders
        $this->info('🌱 Step 2: Running Seeders...');
        
        // Boost Package Seeder
        $this->line('   Running: BoostPackageSeeder');
        try {
            Artisan::call('db:seed', [
                '--class' => 'BoostPackageSeeder',
                '--force' => true
            ]);
            $this->info('   ✅ BoostPackageSeeder completed!');
        } catch (\Exception $e) {
            $this->error('   ❌ BoostPackageSeeder failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Boost Position Seeder
        $this->line('   Running: BoostPositionSeeder');
        try {
            Artisan::call('db:seed', [
                '--class' => 'BoostPositionSeeder',
                '--force' => true
            ]);
            $this->info('   ✅ BoostPositionSeeder completed!');
        } catch (\Exception $e) {
            $this->error('   ❌ BoostPositionSeeder failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        $this->newLine();

        // Step 3: Verify Setup
        $this->info('🔍 Step 3: Verifying Setup...');
        
        $packageCount = BoostPackage::count();
        $positionCount = BoostPosition::count();
        
        $this->line("   Boost Packages: {$packageCount}");
        $this->line("   Boost Positions: {$positionCount}");
        
        if ($packageCount >= 2 && $positionCount >= 3) {
            $this->info('   ✅ Setup verified successfully!');
        } else {
            $this->warn('   ⚠️  Some data might be missing. Please check seeders.');
        }
        $this->newLine();

        // Step 4: Display Created Data
        $this->info('📊 Step 4: Created Data Summary');
        $this->newLine();
        
        $packages = BoostPackage::all();
        $this->line('   Boost Packages:');
        foreach ($packages as $package) {
            $this->line("     • {$package->name}: \${$package->price} for {$package->product_limit} products ({$package->duration_days} days)");
        }
        $this->newLine();
        
        $positions = BoostPosition::all();
        $this->line('   Boost Positions:');
        foreach ($positions as $position) {
            $this->line("     • {$position->name} (Priority: {$position->priority}, Limit: {$position->display_limit})");
        }
        $this->newLine();

        // Step 5: Test Module (if --test flag)
        if ($this->option('test')) {
            $this->info('🧪 Step 5: Testing Module...');
            $this->testModule();
        } else {
            $this->info('💡 Tip: Run with --test flag to test the module');
            $this->line('   Command: php artisan boost:setup --test');
        }

        $this->newLine();
        $this->info('✨ Boost Module Setup Completed Successfully!');
        $this->newLine();
        $this->line('📝 Next Steps:');
        $this->line('   1. Setup cron job: * * * * * cd /path-to-project && php artisan schedule:run');
        $this->line('   2. Test expiry command: php artisan boost:expire');
        $this->line('   3. Test APIs using Postman or your frontend');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Test the boost module
     */
    protected function testModule()
    {
        $this->newLine();
        
        // Test 1: Check Tables
        $this->line('   Test 1: Checking Database Tables...');
        try {
            $tables = ['boost_packages', 'boost_positions', 'vendor_boosts', 'boosted_products'];
            foreach ($tables as $table) {
                $exists = DB::getSchemaBuilder()->hasTable($table);
                if ($exists) {
                    $this->info("     ✅ Table '{$table}' exists");
                } else {
                    $this->error("     ❌ Table '{$table}' missing");
                }
            }
        } catch (\Exception $e) {
            $this->error("     ❌ Error checking tables: " . $e->getMessage());
        }
        $this->newLine();

        // Test 2: Check Products Table Fields
        $this->line('   Test 2: Checking Products Table Fields...');
        try {
            $columns = ['is_top', 'top_start_date', 'top_expire_date'];
            foreach ($columns as $column) {
                $exists = DB::getSchemaBuilder()->hasColumn('products', $column);
                if ($exists) {
                    $this->info("     ✅ Column 'products.{$column}' exists");
                } else {
                    $this->error("     ❌ Column 'products.{$column}' missing");
                }
            }
        } catch (\Exception $e) {
            $this->error("     ❌ Error checking columns: " . $e->getMessage());
        }
        $this->newLine();

        // Test 3: Check Models
        $this->line('   Test 3: Checking Models...');
        $models = [
            'App\Models\BoostPackage',
            'App\Models\BoostPosition',
            'App\Models\VendorBoost',
            'App\Models\BoostedProduct'
        ];
        
        foreach ($models as $model) {
            if (class_exists($model)) {
                $this->info("     ✅ Model '{$model}' exists");
            } else {
                $this->error("     ❌ Model '{$model}' missing");
            }
        }
        $this->newLine();

        // Test 4: Check Controllers
        $this->line('   Test 4: Checking Controllers...');
        $controllers = [
            'App\Http\Controllers\Vendor\VendorBoostController',
            'App\Http\Controllers\User\BoostedProductsController'
        ];
        
        foreach ($controllers as $controller) {
            if (class_exists($controller)) {
                $this->info(" Controller '{$controller}' exists");
            } else {
                $this->error("     ❌ Controller '{$controller}' missing");
            }
        }
        $this->newLine();

        // Test 5: Check Cron Command
        $this->line('   Test 5: Checking Cron Command...');
        try {
            Artisan::call('boost:expire', ['--help' => true]);
            $this->info("     ✅ Command 'boost:expire' exists");
        } catch (\Exception $e) {
            $this->warn("     ⚠️  Command 'boost:expire' might not be registered");
        }
        $this->newLine();

        // Test 6: Check Routes
        $this->line('   Test 6: Checking API Routes...');
        $routes = [
            'vendor/boost/packages',
            'vendor/boost/purchase',
            'user/boosted-products/slider'
        ];
        
        $routeList = Artisan::call('route:list', ['--path' => 'boost']);
        $output = Artisan::output();
        
        if (strpos($output, 'boost') !== false) {
            $this->info("Boost routes are registered");
        } else {
            $this->warn("Boost routes might not be registered");
        }
        $this->newLine();

        $this->info('Module Testing Completed!');
    }
}


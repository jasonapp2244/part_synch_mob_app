<?php

namespace Database\Seeders;

use App\Models\BoostPackage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BoostPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Weekly Package: $5 for 10 products for 1 week (7 days)
        BoostPackage::create([
            'name' => 'Weekly Boost',
            'slug' => 'weekly-boost',
            'price' => 5.00,
            'product_limit' => 10,
            'duration_days' => 7,
            'currency' => 'usd',
            'status' => true,
            'description' => 'Boost 10 products for 1 week (7 days)'
        ]);

        // Monthly Package: $10 for 10 products for 1 month (30 days)
        BoostPackage::create([
            'name' => 'Monthly Boost',
            'slug' => 'monthly-boost',
            'price' => 10.00,
            'product_limit' => 10,
            'duration_days' => 30,
            'currency' => 'usd',
            'status' => true,
            'description' => 'Boost 10 products for 1 month (30 days)'
        ]);
    }
}

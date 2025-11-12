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
        BoostPackage::create(['name' => 'Starter', 'slug' => 'starter', 'price' => 10.00, 'product_limit' => 10, 'duration_days' => 30, 'currency' => 'usd', 'status' => 1, 'description' => 'Boost 10 products for 30 days']);
        BoostPackage::create(['name' => 'Pro', 'slug' => 'pro', 'price' => 20.00, 'product_limit' => 20, 'duration_days' => 30, 'currency' => 'usd', 'status' => 1]);
    }
}

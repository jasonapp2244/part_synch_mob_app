<?php

namespace Database\Seeders;

use App\Models\BoostPosition;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BoostPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BoostPosition::create(['name' => 'Top Section', 'priority' => 1, 'display_limit' => 6, 'status' => 1]);
        BoostPosition::create(['name' => 'Middle Section', 'priority' => 2, 'display_limit' => 8, 'status' => 1]);
        BoostPosition::create(['name' => 'Bottom Section', 'priority' => 3, 'display_limit' => 10, 'status' => 1]);
        BoostPosition::create(['name' => 'Category Section', 'priority' => 4, 'display_limit' => 6, 'status' => 1]);
    }
}

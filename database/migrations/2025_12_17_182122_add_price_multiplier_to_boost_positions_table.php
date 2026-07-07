<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boost_positions', function (Blueprint $table) {
            $table->decimal('price_multiplier', 3, 2)->default(1.00)->after('display_limit');
        });

        // Set default multipliers: Top = 1.5, Middle = 1.0, Bottom = 0.7
        DB::table('boost_positions')->where('name', 'Top Section')->update(['price_multiplier' => 1.50]);
        DB::table('boost_positions')->where('name', 'Middle Section')->update(['price_multiplier' => 1.00]);
        DB::table('boost_positions')->where('name', 'Bottom Section')->update(['price_multiplier' => 0.70]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boost_positions', function (Blueprint $table) {
            $table->dropColumn('price_multiplier');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_top')->default(0)->after('is_active');
            $table->dateTime('top_start_date')->nullable()->after('is_top');
            $table->dateTime('top_expire_date')->nullable()->after('is_top');


        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_top');
            $table->dropColumn('top_start_date');
            $table->dropColumn('top_expire_date');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products','is_promoted')) {
                $table->boolean('is_promoted')->default(false)->after('name');
            }
            if (!Schema::hasColumn('products','promoted_until')) {
                $table->dateTime('promoted_until')->nullable()->after('is_promoted');
            }
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_promoted','promoted_until']);
        });
    }
};

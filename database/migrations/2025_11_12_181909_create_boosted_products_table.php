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
        Schema::create('boosted_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_boost_id')->constrained('vendor_boosts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->dateTime('boost_start')->nullable();
            $table->dateTime('boost_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['vendor_boost_id','product_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('boosted_products'); }
};

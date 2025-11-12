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
        Schema::create('boost_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->decimal('price',10,2);
            $table->unsignedInteger('product_limit')->default(1);
            $table->unsignedInteger('duration_days')->default(30);
            $table->string('currency',10)->default('usd');
            $table->boolean('status')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('boost_packages'); }
};

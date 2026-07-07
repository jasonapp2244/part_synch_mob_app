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
        Schema::create('boost_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('priority')->default(0); // lower = higher on homepage
            $table->unsignedInteger('display_limit')->default(10);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('boost_positions'); }
};
